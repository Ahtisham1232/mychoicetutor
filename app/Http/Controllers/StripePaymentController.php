<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class StripePaymentController extends Controller
{
    public function stripe(): View
    {
        return view('stripe');
    }

    public function stripePost(Request $request): RedirectResponse
    {
        Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $amt = $request->amt;

        try {
            $response = Stripe\Charge::create([
                "amount" => $amt * 100,
                "currency" => "gbp",
                "source" => $request->stripeToken,
                "description" => "My Choice Tutor"
            ]);

            if ($response->status == "succeeded") {
                $order_id = $response->id;
                Log::info('Stripe Payment Success', [
                    'order_id' => $order_id,
                    'amount' => $amt,
                    'currency' => 'gbp',
                    'user_id' => auth()->id() ?? null,
                ]);
                return redirect()->route('stripe.payment.success')->with('order_id', $order_id);
            } else {
                return $this->handleFailure('Payment Failed!');
            }
        }
        // Here we are logging each specific Stripe exception type with as much relevant information as possible to help with debugging and monitoring.
        // The handleFailure method is used to centralize the failure response and ensure the user is redirected appropriately with a clear message.
        catch (\Stripe\Exception\CardException $e) {
            $this->logStripeError('Stripe Card Exception', $e, $request, [
                'stripe_error_code' => $e->getError()->code ?? null,
            ]);
            return $this->handleFailure($e->getError()->message);
        } catch (\Stripe\Exception\RateLimitException $e) {
            $this->logStripeError(
                'Stripe Rate Limit Exceeded',
                $e,
                $request,
                [
                    'stripe_error_type' => $e->getError()->type ?? null,
                    'stripe_error_code' => $e->getError()->code ?? null,
                    'http_status' => $e->getHttpStatus(),
                    'endpoint' => 'Charge::create',
                ]
            );
            return $this->handleFailure("Too many requests made to the API too quickly.");
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->logStripeError(
                'Stripe Invalid Request Exception',
                $e,
                $request,
                [
                    'http_status' => $e->getHttpStatus(),
                    'stripe_error_type' => $e->getError()->type ?? null,
                    'stripe_error_code' => $e->getError()->code ?? null,
                    'param' => $e->getError()->param ?? null,
                    'request_id' => $e->getRequestId() ?? null,
                ]
            );
            return $this->handleFailure("Invalid parameters were supplied to Stripe's API.");
        } catch (\Stripe\Exception\AuthenticationException $e) {
            $this->logStripeError(
                'Stripe Authentication Exception',
                $e,
                $request,
                [
                    'http_status' => $e->getHttpStatus(),
                    'stripe_error_type' => $e->getError()->type ?? null,
                    'stripe_error_code' => $e->getError()->code ?? null,
                    'request_id' => $e->getRequestId() ?? null,
                    'note' => 'Check Stripe API Secret Key configuration',
                ]
            );
            return $this->handleFailure("Authentication with Stripe's API failed.");
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $this->logStripeError(
                'Stripe API Connection Exception',
                $e,
                $request,
                [
                    'http_status' => $e->getHttpStatus(),
                    'request_id' => $e->getRequestId() ?? null,
                    'note' => 'Network issue while communicating with Stripe',
                    'possible_causes' => [
                        'internet down',
                        'stripe server timeout',
                        'DNS issue',
                        'firewall blocking request',
                    ],
                ]
            );
            return $this->handleFailure("Network communication with Stripe failed.");
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $this->logStripeError(
                'Stripe API Error Exception',
                $e,
                $request,
                [
                    'http_status' => $e->getHttpStatus(),
                    'stripe_error_type' => $e->getError()->type ?? null,
                    'stripe_error_code' => $e->getError()->code ?? null,
                    'request_id' => $e->getRequestId() ?? null,
                ]
            );

            return $this->handleFailure("Error processing your payment with Stripe.");
        } catch (\Exception $e) {
            $this->logStripeError(
                'Unexpected Payment Exception',
                $e,
                $request,
                [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            return $this->handleFailure("Something went wrong processing your payment.");
        }
    }

    private function logStripeError(string $type, \Exception $e, Request $request, array $extra = [])
    {
        Log::error($type, array_merge([
            'message' => $e->getMessage(),
            'user_id' => auth()->id(),
            'amount' => $request->amt,
            'currency' => 'gbp',
        ], $extra));
    }

    private function handleFailure($message)
    {
        Log::warning('Stripe Payment Failed', [
            'message' => $message,
            'user_id' => auth()->id() ?? null,
        ]);

        $data = session('stripe_payload');

        // Clear the payload on failure so they don't get stuck
        session()->forget('stripe_payload');

        if ($data && isset($data['tutorenrollid'])) {
            return redirect()->route('student.admission', ['id' => $data['tutorenrollid']])
                ->with('fail', $message);
        }

        return redirect()->route('student.dashboard')->with('fail', $message);
    }
}
