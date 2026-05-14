@extends('front-cms.layouts.main')

@section('main-section')
    <style>
        .btn.collapsed .faq-icon i {
            transform: rotate(0deg);
            transition: 0.3s;
        }

        .btn .faq-icon i {
            transform: rotate(45deg);
            transition: 0.3s;
        }

        .faq-description {
            font-size: 22px;
            color: #666;
            text-align: center;
            margin-bottom: 40px;
            line-height: 1.8;
            font-weight: 400;
        }

        .faq-card {
            border: 1px solid #6a307d;
            border-radius: 10px;
            overflow: hidden;
        }

        .faq-card .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9dff0;
        }

        .faq-card .card-body {
            border-top: 1px solid #e9dff0;
            font-size: 16px;
            line-height: 1.8;
            color: #555;
        }

        .faq-card .btn {
            padding: 18px 20px;
            font-size: 18px;
            font-weight: 600;
        }

        .fa-plus {
            color: #6a307d;
        }

        .faq-stats {
            display: flex;
            justify-content: center;
            gap: 0;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0;
        }

        @media (max-width: 600px) {
            .faq-answer {
                padding-left: 24px;
            }

            .faq-stats {
                flex-wrap: wrap;
            }

            .stat-item {
                border-right: none;
                border-bottom: 1px solid var(--border);
                max-width: 100%;
            }
        }

        .section-label {
            font-family: var(--font-head);
            font-size: 13px;
            font-weight: 600;
            color: #6a307d;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 2px;
            background: #6a307d;
            border-radius: 20px;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .faq-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 30px 20px;
            flex-wrap: wrap;
            background: #f8f3fb;
        }

        .stat-item {
            background: linear-gradient(135deg, #6a307d, #8e44ad);
            color: white;
            border-radius: 14px;
            padding: 25px 30px;
            min-width: 220px;
            text-align: center;
            box-shadow: 0 6px 18px rgba(106, 48, 125, 0.2);
            transition: 0.3s ease;
            border: none;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-num {
            font-size: 30px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>

    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container-fluid">
            <div class="tutorHeader">
                <h1 class="mb-3">
                    Frequently Asked Questions
                </h1>

                <div class="text-center">
                    <p class="charcol">
                        Find answers to common questions about our services.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <div class="faq-stats">
        <div class="stat-item">
            <div class="stat-num">{{ $faqs->count() }}</div>
            <div class="stat-label">Questions answered</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">24/7</div>
            <div class="stat-label">Support available</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">&lt; 2 min</div>
            <div class="stat-label">Avg. read time</div>
        </div>
    </div>


    <!-- FAQ section -->
    <section class="mt-5 mb-5">
        <div class="container">

            <div class="section-label">All questions

            </div>


            <div class="row mt-5 justify-content-center">
                <div class="col-lg-10">

                    <div id="faqAccordion">

                        @foreach ($faqs as $faq)
                            <div class="card mb-3 faq-card shadow-sm">
                                <div class="card-header" id="heading{{ $faq->id }}">

                                    <h5 class="mb-0">

                                        <button
                                            class="btn btn-link text-left w-100 d-flex justify-content-between align-items-center {{ $loop->first ? '' : 'collapsed' }}"
                                            type="button" data-toggle="collapse" data-target="#collapse{{ $faq->id }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $faq->id }}"
                                            style="text-decoration: none; color: #000;">

                                            {{-- <h5>{{ $faq->question }}</h5> --}}
                                            <span class="faq-question">{{ $faq->question }}</span>

                                            <span class="faq-icon">
                                                <i class="fa fa-plus"></i>
                                            </span>

                                        </button>

                                    </h5>

                                </div>

                                <div id="collapse{{ $faq->id }}" class="collapse {{ $loop->first ? 'show' : '' }}"
                                    aria-labelledby="heading{{ $faq->id }}" data-parent="#faqAccordion">

                                    <div class="card-body">

                                        {!! $faq->answer !!}

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    @if ($faqs->isEmpty())
                        <div class="text-center py-5">

                            <p class="text-muted">
                                No FAQs available at the moment.
                            </p>

                        </div>
                    @endif

                </div>
            </div>

            <script>
                function redirect() {
                    window.location.href = "{{ url('/student/register') }}";
                }
            </script>

        </div>
    </section>
@endsection
