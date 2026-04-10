<!DOCTYPE html>
<html>

<head>
    <title>My Choice Tutor - Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style type="text/css">
        body {
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        #card-element {
            height: 45px;
            padding: 12px;
            background: white;
        }

        .payment-summary {
            background: #f1f3f5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        #card-errors {
            color: #dc3545;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row">

            <div class="col-md-6 offset-md-3">
                <div class="card mt-5">
                    <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Secure Payment</h4>
                        <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary">
                            &larr; Back
                        </a>
                    </div>
                    <div class="card-body p-4">

                        <div class="payment-summary">
                            <div class="d-flex justify-content-between">
                                <span><strong>Total Amount:</strong></span>
                                <span class="text-success"><strong>{{ config('common.currency.symbol') }}
                                        {{ $amt }}</strong></span>
                            </div>
                        </div>

                        <form id='checkout-form' method='post' action="{{ route('stripe.post') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Name on card"
                                    required>
                            </div>

                            <input type='hidden' name='stripeToken' id='stripe-token-id'>
                            <input type='hidden' name='amt' value="{{ $amt }}">

                            <div class="mb-3">
                                <label class="form-label">Card Details</label>
                                <div id="card-element" class="form-control"></div>
                                <div id="card-errors" role="alert"></div>
                            </div>

                            <button id='pay-btn' class="btn btn-success w-100 py-2 mt-3" type="button"
                                onclick="createToken()">
                                PAY {{ config('common.currency.symbol') }} {{ $amt }}
                            </button>

                            <div class="text-center mt-3">
                                <small class="text-muted">🔒 Encrypted and Secure Payments</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        var stripe = Stripe('{{ $stripeKey }}');
        var elements = stripe.elements();

        // Style Stripe element to look like Bootstrap
        var style = {
            base: {
                fontSize: '16px',
                color: '#495057',
                fontFamily: 'inherit'
            }
        };

        var cardElement = elements.create('card', {
            style: style
        });
        cardElement.mount('#card-element');
        let processing = false;

        function createToken() {
            //  Prevent multiple clicks
            if (processing) return;

            processing = true;

            const btn = document.getElementById("pay-btn");
            const errorElement = document.getElementById('card-errors');

            btn.disabled = true;
            btn.innerHTML = "Processing...";
            errorElement.textContent = "";

            stripe.createToken(cardElement).then(function(result) {
                if (result.error) {

                    //  Reset flag on error
                    processing = false;

                    btn.disabled = false;
                    btn.innerHTML = "PAY {{ config('common.currency.symbol') }} {{ $amt }}";
                    errorElement.textContent = result.error.message;

                } else {
                    document.getElementById("stripe-token-id").value = result.token.id;

                    //  Do NOT reset processing here (form is submitting)
                    document.getElementById('checkout-form').submit();
                }
            });
        }
    </script>
</body>

</html>
