@extends('front-cms.layouts.main')
@section('main-section')

<section class="bannerSec tutBann">
        <div class="container-fluid">
            <div class="tutorHeader">
                <h1 class="mb-3">
                Terms and Conditions for My Choice Tutor
                </h1>

                <div class="text-center">
                    <p class="charcol">Guidelines for a Seamless Learning Experience</p>

                </div>

            </div>
        </div>

    </section>


    <section class="container findtutSec-ex ">
    <h5 calss="mb-2">1. Acceptance of Terms</h5>
    <p class="mb-5">By accessing and using My Choice Tutor, you acknowledge that you have read, understood, and agree to be bound by these Terms, as well as our Privacy Policy. If you do not agree to these Terms, please do not use our platform.</p>


    <h5 calss="mb-2">2. Eligibility</h5>
    <p class="mb-5">You must be at least 13 years old to use My Choice Tutor. By using our platform, you represent and warrant that you meet this age requirement.</p>

    <h5 calss="mb-2">3. Account Registration</h5>
    <p class="mb-5">To access certain features of My Choice Tutor, you may need to create an account. You agree to provide accurate, current, and complete information during the registration process and to update such information as necessary. You are responsible for safeguarding your account credentials and for all activities that occur under your account.</p>

    <h5 calss="mb-2">4. Course Enrollment and Participation</h5>

    <p class="mt-4"><b>a. Enrollment:</b></p>
    <p class="">Students can enroll in courses offered on My Choice Tutor. By enrolling in a course, you agree to pay any applicable fees and to comply with the course requirements.</p>
    <p><b>b. Participation:</b></p> 
    <p class="mb-5">You agree to participate in live classes in a manner that is respectful and conducive to learning. Disruptive or inappropriate behavior may result in suspension or termination of your access to our services.</p>
    
    <h5 calss="mb-2">5. Payment and Refunds</h5>
    <p class="mt-4"><b>a. Payment:</b></p> 
    <p class="mb-3">All fees for courses are due at the time of enrollment. Payment must be made through our approved payment methods.</p>
    <p><b>b. Refunds:</b></p> 
    <p class="mb-5">Refund policies may vary by course and will be specified at the time of enrollment. If a course is canceled by My Choice Tutor, a full refund will be issued.   </p>
   
   
    
    <h5 calss="mb-2">6. Intellectual Property</h5>
    <p class="mt-4"><b>a. Ownership:</b></p> 
    <p class="mb-3">All content on My Choice Tutor, including course materials, text, graphics, logos, and software, is the property of My Choice Tutor or its content suppliers and is protected by intellectual property laws.</p>

    <p class=""><b>b. License:</b></p> 
    <p class="mb-5">You are granted a limited, non-exclusive, non-transferable license to access and use the content for personal, non-commercial purposes. You may not reproduce, distribute, modify, or create derivative works from the content without our prior written consent.</p>



    <h5 calss="mb-2">7. User Conduct</h5>
    <p class="mb-3">You agree not to use My Choice Tutor for any unlawful or prohibited activities, including but not limited to:</p>
    <ul class="mb-5">
        <li>Impersonating any person or entity</li>
        <li>Harassing, threatening, or defaming others</li>
        <li>Uploading or distributing viruses or harmful software</li>
        <li>Violating any applicable laws or regulations</li>
    </ul>



    <h5 calss="mb-2">8. Termination</h5>
    <p class="mb-5">We reserve the right to terminate or suspend your account and access to My Choice Tutor at our sole discretion, without prior notice, for conduct that we believe violates these Terms or is harmful to other users of the platform.</p>

    <h5 calss="mb-2">9. Disclaimers and Limitation of Liability</h5>
    <p class="mt-4"><b>a. Disclaimers:</b></p>
    <p>My Choice Tutor is provided on an "as is" and "as available" basis. We do not warrant that the platform will be uninterrupted, error-free, or free of viruses or other harmful components.</p>

    <p><b>b. Limitation of Liability:</b></p>
    <p class="mb-5">To the fullest extent permitted by law, My Choice Tutor and its affiliates, officers, directors, employees, and agents shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses resulting from <br>(i) your use of or inability to use the platform; <br>(ii) any unauthorized access to or use of our servers and/or any personal information stored therein;<br>(iii) any interruption or cessation of transmission to or from our platform.</p>




    <h5 calss="mb-2">10. Indemnification</h5>
    <p class="mb-5">You agree to indemnify, defend, and hold harmless My Choice Tutor and its affiliates, officers, directors, employees, and agents from and against any claims, liabilities, damages, losses, and expenses, including without limitation reasonable legal and accounting fees, arising out of or in any way connected with your access to or use of the platform or your violation of these Terms.</p>


    <h5 calss="mb-2">11. Governing Law</h5>
    <p class="mb-5">These Terms shall be governed by and construed in accordance with the laws of [Insert Jurisdiction], without regard to its conflict of law principles.</p>


    <h5 calss="mb-2">12. Changes to Terms</h5>
    <p class="mb-5">We reserve the right to modify these Terms at any time. We will notify you of any changes by posting the new Terms on our website. You are advised to review these Terms periodically for any changes. Your continued use of My Choice Tutor after the posting of changes constitutes your acceptance of such changes.</p>

    <h5 calss="mb-2">13. Contact Information</h5>
    <p class="mb-5">If you have any questions about these Terms, please contact us at <a href="mailto:{{config('mail.support_address')}}">{{config('mail.support_address')}}</a>.</p>

    <h5 calss="mb-2"></h5>
    <p class="mb-5"></p>
    <br><br>
    </section>

@endsection