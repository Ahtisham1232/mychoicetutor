<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppService
{
    public function __construct()
    {
        // Constructor can be used for dependency injection if needed
    }

    /**
     * Send a WhatsApp message using the VeevoTech API.
     *
     * @param string $to The recipient's phone number in E.164 format.
     * @param array $bodyVariables The dynamic variables for the message template.
     * @param int $templateId The ID of the message template.
     * @return array The API response as an associative array.
     */
    public function sendMessage(string $to, array $bodyVariables, int $templateId): bool
    {
        try {
            $hash = config('services.veevotech.hash');

            if (empty($hash)) {
                Log::critical('VeevoTech hash missing');
                return false;
            }

            $response = Http::withHeaders([
                'hash' => $hash,
            ])->post('https://wa-api.veevotech.com/wa/v1/send_message', [
                'to' => $to,
                'type' => 'template',
                'template_id' => $templateId,
                'header_variables' => [],
                'body_variables' => $bodyVariables,
                'media_url' => [],
                'priority' => 1,
            ]);

            if ($response->successful() && ($response->json('STATUS') === 'SUCCESS')) {
                Log::info('WhatsApp sent', [
                    'to' => $to,
                    'template_id' => $templateId,
                ]);
                return true;
            }

            Log::error('WhatsApp API error', [
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
