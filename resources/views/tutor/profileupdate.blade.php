@extends('tutor.layouts.main')
@section('main-section')

    <div class="main-content">
        <style>
            .profile-pic-wrapper {
                position: relative;
                width: 150px;
                margin: auto;
            }

            .card {
                border: 2px solid #6d6ddf !important;
            }

            .profile-pic-wrapper img {
                width: 150px;
                height: 150px;
                object-fit: cover;
                border-radius: 50%;
                border: 2px solid #ccc;
                display: block;
            }

            #file {
                display: none;
            }

            #uploadBtn {
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);

                background: rgba(0, 0, 0, 0.7);
                color: #fff;

                padding: 8px 14px;
                border-radius: 20px;

                font-size: 14px;
                cursor: pointer;

                display: none;
                white-space: nowrap;
            }

            @media (max-width: 768px) {
                #uploadBtn {
                    display: block !important;
                    position: static;
                    width: auto;
                    background-color: #007bff;
                    color: white;
                    text-align: center;
                }
            }

            .alert-dismissible {
                width: auto;
                margin: 5px;
            }
        </style>

        <div class="page-content">
            <div class="container-fluid">

                {{-- ================= PROFILE CARD ================= --}}
                <div class="card mb-4 mx-auto" style="max-width: 900px;">

                    <div class="card-header bg-white">
                        <h4 class="mb-0">Profile Information</h4>
                    </div>

                    <form action="{{ route('tutor.updateprofiledata') }}" enctype="multipart/form-data" method="POST">
                        @csrf

                        <div class="card-body">

                            {{-- Profile Image --}}
                            <div class="text-center mb-4">
                                <div class="profile-pic-wrapper position-relative">
                                    <img src="{{ url('images/tutors/profilepics', $tutorpd->profile_pic ?? '1703078631.png') }}"
                                        id="photo" alt="Profile Photo">

                                    <input type="file" id="file" name="file" onchange="validateImage()">

                                    <label for="file" id="uploadBtn" class="btn btn-sm btn-primary mt-2">
                                        <i class="ri-camera-line"></i> Change Photo
                                    </label>

                                    <span class="text-danger d-block mt-2" id="file-error"></span>
                                </div>

                                <h5 class="mt-3">
                                    {{ $tutorpd->name ?? session('userid')->name }}
                                </h5>
                            </div>

                            <div class="row g-3">

                                <div class="form-group col-md-6">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control"
                                        value="{{ $tutorpd->name ?? session('userid')->name }}" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Gender</label>
                                    <select class="form-control" name="gender">
                                        <option value="1" {{ $tutorpd->gender == '1' ? 'selected' : '' }}>
                                            Male
                                        </option>

                                        <option value="2" {{ $tutorpd->gender == '2' ? 'selected' : '' }}>
                                            Female
                                        </option>

                                        <option value="3" {{ $tutorpd->gender == '3' ? 'selected' : '' }}>
                                            Other
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Headline</label>
                                    <input type="text" class="form-control" name="headline"
                                        value="{{ $tutorpd->headline ?? '' }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Qualification</label>
                                    <input type="text" class="form-control" name="qualification"
                                        value="{{ $tutorpd->qualification ?? '' }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Experience</label>
                                    <input type="text" class="form-control" name="experience"
                                        value="{{ $tutorpd->experience ?? '' }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Certification</label>
                                    <input type="text" class="form-control" name="certification"
                                        value="{{ $tutorpd->certification ?? '' }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Mobile</label>
                                    <input type="text" class="form-control"
                                        value="{{ $tutorpd->mobile ?? session('userid')->mobile }}" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Secondary Mobile</label>
                                    <input type="number" class="form-control" name="secmobile"
                                        value="{{ $tutorpd->secondary_mobile ?? '' }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control"
                                        value="{{ $tutorpd->email ?? session('userid')->email }}" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Rate Per Hour (£)</label>
                                    <input type="text" class="form-control" value="{{ $tutorpd->rateperhour ?? 0 }}"
                                        disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>About Me</label>
                                    <textarea class="form-control" name="goals" rows="4">{{ $tutorpd->goal ?? '' }}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Other Details</label>
                                    <textarea class="form-control" name="details1" rows="4">{{ $tutorpd->detail_1 ?? '' }}</textarea>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Intro Video Link</label>
                                    <input type="text" class="form-control" name="introvideolink"
                                        value="{{ $tutorpd->intro_video_link ?? '' }}">
                                </div>

                            </div>

                        </div>

                        <div class="card-footer text-center bg-white">
                            <button type="submit" class="btn btn-success px-5">
                                <i class="fa fa-check"></i> Save Profile
                            </button>
                        </div>

                    </form>
                </div>



                {{-- ================= CLASS MAPPING CARD ================= --}}
                <div class="card mb-4 mx-auto" style="max-width: 900px;">

                    <div class="card-header bg-white">
                        <h4 class="mb-0">Class / Grade Mapping</h4>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('tutor.classmapping') }}" method="POST">
                            @csrf

                            <div class="row align-items-end">

                                <div class="form-group col-md-4">
                                    <label>Class</label>

                                    <select class="form-control" id="classname" name="classname"
                                        onchange="fetchSubjects()" required>

                                        <option value="">-- Select Class --</option>

                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Subject</label>

                                    <select class="form-control" id="subject" name="subject" required>

                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <button class="btn btn-success w-50" type="submit">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>

                            </div>

                        </form>

                        <hr>

                        <div class="table-responsive">

                            <table class="table table-hover table-striped">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($tutorsub as $classmapping)
                                        <tr>

                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $classmapping->class }}</td>

                                            <td>{{ $classmapping->subject }}</td>

                                            <td>
                                                <a href="{{ url('tutor/classmappingdelete') }}/{{ $classmapping->id }}">
                                                    <button class="btn btn-danger btn-sm" type="button">
                                                        Delete
                                                    </button>
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                {{-- ================= ACHIEVEMENT CARD ================= --}}
                <div class="card mb-4 mx-auto" style="max-width: 900px;">

                    <div class="card-header bg-white">
                        <h4 class="mb-0">Add Achievements</h4>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('tutor.tutoracadd') }}" method="POST">
                            @csrf

                            <div class="row align-items-end">

                                <div class="form-group col-md-3">
                                    <label>Name</label>

                                    <input type="text" class="form-control" name="achievementName" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Description</label>

                                    <input type="text" class="form-control" name="achievementDesc" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Date</label>

                                    <input type="date" class="form-control" name="achDate">
                                </div>

                                <div class="form-group col-md-2">
                                    <button class="btn btn-success w-100" type="submit">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>

                            </div>

                        </form>

                        <hr>

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($achievement as $item)
                                        <tr>

                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $item->name }}</td>

                                            <td>{{ $item->description }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($item->date)->format('j-F-Y') }}
                                            </td>

                                            <td>
                                                <a href="{{ url('tutor/tutoracdel') }}/{{ $item->id }}">
                                                    <button class="btn btn-danger btn-sm" type="button">
                                                        Delete
                                                    </button>
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                {{-- ================= SKILLS CARD ================= --}}
                <div class="card mb-5 mx-auto" style="max-width: 900px;">

                    <div class="card-header bg-white">
                        <h4 class="mb-0">Add Skills</h4>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('tutor.update-skills') }}" method="POST">
                            @csrf

                            <div class="row align-items-end">

                                <div class="col-md-9">
                                    <label>Skills</label>

                                    <input type="text" class="form-control" name="skills"
                                        value="{{ $tutorpd->keywords ?? '' }}" placeholder="Java Expert, Python Expert">
                                </div>

                                <div class="col-md-3">
                                    <button class="btn btn-success w-50" type="submit">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>

                            </div>

                        </form>

                        <p class="text-danger mt-3 mb-3" style="font-size: 13px;">
                            Enter skills separated with commas.
                        </p>

                        <div class="d-flex flex-wrap gap-2">

                            @if ($tutorpd->keywords ?? '')
                                @foreach ($skillsArray as $item)
                                    <div class="badge bg-primary p-2">
                                        {{ $item }}
                                    </div>
                                @endforeach
                            @endif

                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- JS Scripts --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            const imgDiv = document.querySelector('.profile-pic-wrapper');
            const img = document.querySelector('#photo');
            const file = document.querySelector('#file');
            const uploadBtn = document.querySelector('#uploadBtn');

            // Only show on hover for desktop
            if (window.innerWidth > 768) {
                imgDiv.addEventListener('mouseenter', function() {
                    uploadBtn.style.display = "block";
                });

                imgDiv.addEventListener('mouseleave', function() {
                    uploadBtn.style.display = "none";
                });
            }

            // Preview uploaded image
            file.addEventListener('change', function() {
                const choosedFile = this.files[0];
                if (choosedFile) {
                    const reader = new FileReader();
                    reader.addEventListener('load', function() {
                        img.setAttribute('src', reader.result);
                    });
                    reader.readAsDataURL(choosedFile);
                }
            });

            function validateImage() {
                const fileInput = document.getElementById('file');
                const filePath = fileInput.value;
                const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
                const file = fileInput.files[0];
                const maxSize = 2 * 1024 * 1024;
                const errorElement = document.getElementById('file-error');

                errorElement.textContent = '';

                if (!allowedExtensions.exec(filePath)) {
                    errorElement.textContent = 'Only .jpg, .jpeg, and .png files are allowed';
                    fileInput.value = '';
                    return false;
                }

                if (file.size > maxSize) {
                    errorElement.textContent = 'File size must not exceed 2MB';
                    fileInput.value = '';
                    return false;
                }

                return true;
            }

            function fetchSubjects() {

                var classId = $('#classname option:selected').val();
                $("#subject").html('');
                $.ajax({
                    url: "{{ url('fetchsubjects') }}",
                    type: "POST",
                    data: {
                        class_id: classId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#subject').html('<option value="">-- Select Type --</option>');
                        $.each(result.subjects, function(key, value) {
                            $("#subject").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                    }
                });
            };
        </script>

        <script>
            var achievementArray = [];

            function addNewAchievement() {
                achieveObj = {};
                achieveObj.achieveName = $("#achievementName").val();
                achieveObj.achieveDesc = $("#achievementDesc").val();
                achieveObj.achieveDate = $("#achDate").val();
                achievementArray.push(achieveObj);

                bindAchieveArray();

                $("#achievementName").val("");
                $("#achievementDesc").val("");
                $("#achDate").val("");

            }

            function bindAchieveArray() {
                var p = 0;
                var strRow = "";
                for (var c = 0; c < achievementArray.length; c++) {
                    p++;
                    strRow += `<tr>`;
                    strRow += `<td >${p}</td>`;
                    strRow += `<td>${achievementArray[c].achieveName}</td>`;
                    strRow += `<td>${achievementArray[c].achieveDesc}</td>`;
                    strRow += `<td>${achievementArray[c].achieveDate}</td>`;
                    strRow += `<td><button class="btn-danger" href="#" onclick="removeAchievement(${p})" ></i>Remove</a></td>`;
                    strRow += `</tr>`;
                }
                document.getElementById("achievementGrid").innerHTML = strRow;
            }

            function removeAchievement(objToRemove) {


            }
        </script>

        <script>
            function validateInput(inputElement) {
                // Get the input value
                var inputValue = inputElement.value;


                // Remove any characters that are not alphabets, numbers, or #
                var sanitizedValue = inputValue.replace(/[^a-zA-Z0-9#, ]/g, '');

                // Update the input field with the sanitized value
                inputElement.value = sanitizedValue;
            }
        </script>
        <script>
            $(document).ready(function() {
                $(".remove-skill").click(function() {
                    var skillToRemove = $(this).closest(".skill-preview").data(
                        "skill"); // Get the skill name from data attribute
                    var currentSkills = $("input[name='skills']")
                        .val(); // Get the current skills from the input field
                    var skillsArray = currentSkills.split(","); // Split the skills into an array

                    // Remove the skill from the array
                    var index = skillsArray.indexOf(skillToRemove);
                    if (index !== -1) {
                        skillsArray.splice(index, 1);
                    }
                    $("input[name='skills']").val(skillsArray.join(", "));
                });
            });
        </script>
        <script>
            // Flag to track changes in any form
            let isFormDirty = false;

            // Select all forms on the page
            const forms = document.querySelectorAll('form');

            // Loop through each form
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, select, textarea');

                // Attach 'input' event listeners to each form's inputs
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        isFormDirty = true;
                        // alert("Form has unsaved changes!"); // Just to test that the event is triggered
                    });
                });

                // Reset the flag when any form is submitted
                form.addEventListener('submit', () => {
                    isFormDirty = false;
                });
            });

            // Warn user before they leave the page if any form has unsaved changes
            window.addEventListener('beforeunload', (event) => {
                if (isFormDirty) {
                    event.preventDefault();
                    event.returnValue = 'You have unsaved changes, do you really want to leave?';
                }
            });
        </script>
        <script>
            document.querySelectorAll('.word-limit').forEach(function(field) {
                const limit = parseInt(field.getAttribute('data-limit'));
                let feedback;

                // Create feedback element if not already present
                if (field.nextElementSibling && field.nextElementSibling.classList.contains('word-count-feedback')) {
                    feedback = field.nextElementSibling;
                } else {
                    feedback = document.createElement('small');
                    feedback.classList.add('text-muted', 'word-count-feedback');
                    field.parentNode.appendChild(feedback);
                }

                const updateWordCount = () => {
                    let words = field.value.trim().split(/\s+/).filter(word => word.length > 0);
                    if (words.length > limit) {
                        // Trim to limit
                        field.value = words.slice(0, limit).join(' ');
                        words = words.slice(0, limit);
                    }

                    feedback.textContent = `${words.length}/${limit} words`;
                };

                // Listen to input and paste
                field.addEventListener('input', updateWordCount);
                field.addEventListener('paste', function(e) {
                    // Delay so value is available
                    setTimeout(updateWordCount, 0);
                });

                // Initialize on page load
                updateWordCount();
            });
        </script>

    </div>
@endsection
