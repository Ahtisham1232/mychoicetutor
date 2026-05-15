@extends('front-cms.layouts.main')
@section('main-section')

<section class="bannerSec tutBann">
    <div class="container-fluid">
        <div class="tutorHeader">
            <h1 class="mb-3">
                Privacy Policy for My Choice Tutor
            </h1>
            <div class="text-center">
                <p class="charcol">Protecting Your Personal Information While You Learn</p>
            </div>
        </div>
    </div>
</section>

<section class="container findtutSec-ex">

    <p class="mb-4">My Choice Tutor (“we,” “our,” or “us”) respects your privacy and is committed to protecting the
personal information you share with us. This Privacy Policy explains how we collect, use, store, and
protect your information when you use our services, including when you submit information through
our Meta lead form ads.</p>

    <h5 class="mb-2">1. Information We Collect</h5>
    <p>When you interact with our services, we may collect the following types of personal information:</p>
    <ul>
        <li>Name of parent/guardian</li>
        <li>Contact details (phone number, email address)</li>
        <li>Child’s grade level and subjects required</li>
        <li>Preferred schedule and location (if applicable)</li>
    </ul>

    <h5 class="mb-2 mt-4">2. How We Use Your Information</h5>
    <p>We use your information to:</p>
    <ul>
        <li>Contact you regarding tutoring inquiries and trial lessons</li>
        <li>Schedule and manage tutoring sessions</li>
        <li>Personalize tutoring support for your child</li>
        <li>Provide customer support and respond to your questions</li>
        <li>Improve our services and communication</li>
    </ul>

    <h5 class="mb-2 mt-4">3. Data Sharing and Disclosure</h5>
    <p>We do not sell or rent your personal information to third parties. Your data may only be shared with:</p>
    <ul>
        <li>Assigned tutors for lesson scheduling and preparation</li>
        <li>Service providers who support our operations (e.g., communication tools)</li>
        <li>Authorities if required by law</li>
    </ul>

    <h5 class="mb-2 mt-4">4. Data Security</h5>
    <p>We implement reasonable security measures to protect your personal information against unauthorized access, alteration, or disclosure.</p>

    <h5 class="mb-2 mt-4">5. Your Rights</h5>
    <p>You have the right to:</p>
    <ul>
        <li>Request access to the information we hold about you</li>
        <li>Ask us to correct inaccurate information</li>
        <li>Request deletion of your personal data</li>
        <li>Opt-out of further communications at any time</li>
    </ul>

    <h5 class="mb-2 mt-4">6. Children’s Privacy</h5>
    <p>We only collect limited information about children (grade level, subjects) as provided by parents/guardians. We do not collect or process children’s personal data directly.</p>

    <h5 class="mb-2 mt-4">7. Updates to This Policy</h5>
    <p>We may update this Privacy Policy from time to time. Any changes will be posted with a revised effective date.</p>

    <h5 class="mb-2 mt-4">8. Contact Us</h5>
    <p>If you have any questions or concerns regarding this Privacy Policy, you can contact us at:</p>
    <ul>
       <strong>My Choice Tutor</strong>
        <li>Email: <a href="mailto:{{config('mail.support_address')}}">{{config('mail.support_address')}}</a></li>
        <li>Phone: +44 07761 975326</li>
    </ul>

    <br><br>
</section>

@endsection
