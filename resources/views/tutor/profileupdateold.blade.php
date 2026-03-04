@extends('tutor.layouts.main')
@section('main-section')
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->

<div class="main-content">
    <style>
    .listHeader {
        display: flex;
        justify-content: space-between;
    }

    /* change profile pic */

    .profile-pic-div {
        height: 150px;
        width: 150px;
        position: absolute;
        top: 65%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        overflow: hidden;
        border: 1px solid grey;
    }

    #photo {
        object-fit: cover;
        height: 150px;
        width: 150px;
    }

    #file {
        display: none;
    }

    #uploadBtn {
        height: 40px;
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        background: rgba(0, 0, 0, 0.7);
        color: wheat;
        line-height: 30px;
        font-family: sans-serif;
        font-size: 15px;
        cursor: pointer;
        display: none;
    }

    .profile-pic-div label {
        font-size: 12px !important;
    }

    .alert-dismissible{
        width: auto;
        margin:5px;
    }
    #uploadBtn {
        cursor: pointer !important;
    }
    </style>

    <div class="page-content">
        <div class="container-fluid">

            @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            @if (Session::has('fail'))
            <div class="alert alert-danger">{{ Session::get('fail') }}</div>
            @endif
            <div class="card" style="width: 50rem; margin: 0 auto;">
                <form action="{{ route('tutor.updateprofiledata') }}" enctype="multipart/form-data" method="POST">
                    @csrf

                    <span class="text-danger" id="file-error"></span>
                    <div class="card-header bg-white">

                        <div class="row mb-5">
                            <div class="col-md-3 col-12">
                                <!-- <div class="profile-pic-div">
                                    <img src="{{url('images/tutors/profilepics','/')}}{{ $tutorpd->profile_pic ?? '1703078631.png'}}" id="photo">
                                    <input type="file" id="file" name="file" onchange="validateImage()">
                                    <label for="file" id="uploadBtn"><span class="ri-camera-line">&nbsp;Choose Photo</span></label>
                                </div> -->
                                <div class="profile-pic-div text-center">
                                    <img src="{{ url('images/tutors/profilepics', $tutorpd->profile_pic ?? '1703078631.png') }}"
                                        id="photo"
                                        class="img-fluid rounded-circle mb-2"
                                        alt="Profile Photo">

                                    <input type="file" id="file" name="file" onchange="validateImage()" style="display: none;">
                                    
                                    <label for="file" id="uploadBtn" class="btn btn-sm btn-outline-primary d-inline-block">
                                        <i class="ri-camera-line"></i> Choose Photo
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <h2 style="margin-top: 60px; color: black; padding-left: 30px;">
                                    {{ $tutorpd->name ?? session('userid')->name }}</h2>


                            </div>

                        </div>

                    </div>





                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Full Name<i style="color:red">*</i></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    value="{{ $tutorpd->name ?? session('userid')->name }}" required disabled>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="name">Gender</label>
                                <select type="text" class="form-control" id="gender" name="gender" value="">
                                    @if (empty($tutorpd->gender))
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                    @else
                                    <option value="1" {{ ( $tutorpd->gender == "1") ? 'selected' : '' }}>Male</option>
                                    <option value="2" {{ ( $tutorpd->gender == "2") ? 'selected' : '' }}>Female</option>
                                    <option value="3" {{ ( $tutorpd->gender == "3") ? 'selected' : '' }}>Other</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Qualification</label>
                                <input type="text" class="form-control" id="qualification" name="qualification"
                                    placeholder="qualification" value="{{ $tutorpd->qualification ?? '' }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Experience</label>
                                <input type="text" class="form-control" id="experience" name="experience"
                                    placeholder="experience" value="{{ $tutorpd->experience ?? '' }}" >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Cetification</label>
                                <input type="text" class="form-control" id="certification" name="certification"
                                    placeholder="certification" value="{{ $tutorpd->certification ?? ''}}">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="">Mobile</label>
                                <input type="text" class="form-control" id="primarymobile" name="primarymobile"
                                    placeholder="" value="{{ $tutorpd->mobile ?? session('userid')->mobile }}" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Secondary Mobile</i></label>
                                <input type="text" class="form-control" id="secmobile" name="secmobile"
                                    placeholder="Enter Secondary Mobile" value="{{ $tutorpd->secondary_mobile ?? '' }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $tutorpd->email ?? session('userid')->email }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                        </div>
                        <div class="row">

                            <div class="form-group col-md-6">
                                <label for="">Headline</label>
                                <input type="text" class="form-control" id="headline" name="headline"
                                    placeholder="Enter Headline" value="{{ $tutorpd->headline ?? '' }}" >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">About Me</label>
                                <input type="text" class="form-control" id="goals" name="goals"
                                    placeholder="Enter Goals " value="{{ $tutorpd->goal ?? '' }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Other Details</label>
                                <input type="text" class="form-control" id="details1" name="details1"
                                    placeholder="Enter Details" value="{{ $tutorpd->detail_1 ?? '' }}" >
                            </div>
                            <div class="form-group col-md-6" hidden>
                                <label for="name">Details 2</label>
                                <input type="text" class="form-control" id="details2" name="details2"
                                    placeholder="Enter Details" value="{{ $tutorpd->detail_2 ?? '' }}">
                            </div>
                            <div class="form-group col-md-6" hidden>
                                <label for="name">Details 3</label>
                                <input type="text" class="form-control" id="details3" name="details3"
                                    placeholder="Enter Details" value="{{ $tutorpd->detail_3 ?? '' }}">
                            </div>
                            {{-- Added new field for rate per hour based on profile starts here --}}
                            <div class="form-group col-md-6">
                                <label for="name">Rate Per Hour({{ config('common.currency.symbol') }})<i style="color:red">*</i></label>
                                <input type="text" class="form-control" id="rateperhour" name="rateperhour"
                                    placeholder="0" disabled readonly
                                    value="{{ $tutorpd->rateperhour ?? 0}}" required>
                            </div>
                            {{-- Added new field for rate per hour based on profile ends here --}}

                            <div class="form-group col-md-6">
                                <label for="name">Intro Video Link<span style="font-size: 8px;color:red"> (Ex : https://www.youtube.com/embed/1Yg5GOospcE)</span></label>
                                <input type="text" class="form-control" id="introvideolink" name="introvideolink"
                                    placeholder="https://youtube.com/abZpqYUppz"
                                    value="{{ $tutorpd->intro_video_link ?? ''}}">
                            </div>



                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <div class="text-center mt-4">
                                    <button type="submit" id="" class="btn btn-lg btn-success"><span
                                            class="fa fa-check"></span> Update</button>

                                    {{-- <a type="button" class="btn btn-danger mr-1 moveRight" --}}
                                    {{-- style="background-color:#DC3545;" href="editstudentprofile.html">Back</a> --}}
                                    <br>
                                    <p style="color:red; font-size:12px"> * Make sure to update the data before going down.</p>
                                </div>
                            </div>
                        </div>


                </form>
                <hr>

                {{-- Achievement Add/Update --}}
                <h3 class="text-center my-5"><u>Class/Grade Mapping</u></h3>

                <form action="{{ route('tutor.classmapping') }}" method="POST" name="classmapping">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="">Class<i style="color:red">*</i></label>
                            <select type="text" class="form-control" id="classname" name="classname"
                                onchange="fetchSubjects();" required>
                                <option value="">--Select--</option>
                                @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="hidden" id="id" name="id" class="form-group">
                            <label for="">Subject<i style="color:red">*</i></label>
                            <select type="text" class="form-control" id="subject" name="subject" required>

                            </select>
                        </div>


                        <div class="form-group col-md-3 text-right" style="margin-top: 33px;">
                            <button class=" btn btn-sm btn-success text-white" type="submit"><span
                                    class="fa fa-plus"></span> Add</button>
                        </div>
                    </div>
                </form>
                <hr>

                <table class="table table-hover table-striped align-middlemb-0 table-responsive">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tutorclassmappinggrid">
                        @foreach ($tutorsub as $classmapping)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-wrap">{{ $classmapping->class }}</td>
                            <td class="text-wrap">{{ $classmapping->subject }}</td>
                            <td><a href="{{url('tutor/classmappingdelete')}}/{{$classmapping->id}}"><button
                                        class="btn-sm btn btn-danger" type="button"><span class="fa fa-trash"></span>
                                        Delete</button></a></td>
                        </tr>
                        @endforeach
                        <tr>

                        </tr>

                    </tbody>
                </table>

                <hr>

                {{-- Achievement Add/Update --}}
                <h3 class="text-center my-5"><u>Achievement</u></h3>

                <form action="{{ route('tutor.tutoracadd') }}" method="POST" name="achie">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="">Name<i style="color:red">*</i></label>
                            <input type="text" class="form-control" id="achievementName" name="achievementName"
                                placeholder="Enter Details" required>
                            <span class="text-danger">
                                @error('achievementName')
                                {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="">Description<i style="color:red">*</i></label>
                            <input type="text" class="form-control" id="achievementDesc" name="achievementDesc"
                                placeholder="Enter Details" required>
                            <span class="text-danger">
                                @error('achievementDesc')
                                {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="">Date</label>
                            <input type="date" class="form-control" id="achDate" name="achDate">

                        </div>

                        <div class="form-group col-md-3 text-right" style="margin-top: 33px;">
                            <button class=" btn btn-sm btn-success text-white" type="submit"><span
                                    class="fa fa-plus"></span> Add</button>
                        </div>
                    </div>
                </form>
                <hr>

                <table class="table table-bordered table-responsive">
                    <thead class="">
                        <tr>
                            <th>S.No.</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="achievementGrid">
                        @foreach ($achievement as $achievement)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-wrap">{{ $achievement->name }}</td>
                            <td class="text-wrap">{{ $achievement->description }}</td>
                            {{-- <td class="text-wrap">{{$achievement->date}}</td> --}}
                            <td>{{ \Carbon\Carbon::parse($achievement->date)->format('j-F-Y') }}</td>
                            <td><a href="{{url('tutor/tutoracdel')}}/{{$achievement->id}}"><button
                                        class="btn-sm btn btn-danger" type="button"><span class="fa fa-trash"></span>
                                        Delete</button></a></td>
                        </tr>
                        @endforeach
                        <tr>

                        </tr>

                    </tbody>
                </table>

                <hr>

                <h3 class="text-center my-5"><u>Skills</u></h3>
                <div class="form">
                    <form action="{{route('tutor.update-skills')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-9">
                                <input type="text" class="form-control" oninput="validateInput(this)" name="skills" value="{{ $tutorpd->keywords ?? '' }}"placeholder="Enter skills separated with comma (Example: Java Expert, Python Expert)">

                                @error('skills')
                                  <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3 text-right" style="margin-top: 4px;">
                                <button class="btn btn-sm btn-success text-white" type="submit"><span
                                        class="fa fa-plus"></span> Add</button>
                            </div>
                        </div>
                    </form>
                    <p style="color:red; font-size:12px"> * Please enter all the skills separated with comma. After that click on <b>+Add</b> button</p>
                    <hr>

                    @if($tutorpd->keywords ?? '')
                    <div class="row">

                        @foreach ($skillsArray  as $item)
                            <div class="alert alert-primary alert-dismissible fade show skill-preview" data-skill="{{ $item }}">
                                <button type="button" class="btn-close remove-skill" data-bs-dismiss="alert"></button>
                                <strong>{{ $item }}</strong>
                            </div>
                        @endforeach
                   </div>

                    @endif
                </div>




            </div>
        </div>


<br>
<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="infoModalLabel">Profile Update Required</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Kindly update your profile & add Class/Grade Mapping.
        </div>
        <div class="modal-footer">
          <a href="{{ url('tutor/profileupdate') }}" class="btn btn-primary">Update Now</a>
        </div>
      </div>
    </div>
  </div>

    </div>
    <!-- content-wrapper ends -->

    <!-- partial -->
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
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
//declearing html elements

const imgDiv = document.querySelector('.profile-pic-div');
const img = document.querySelector('#photo');
const file = document.querySelector('#file');
const uploadBtn = document.querySelector('#uploadBtn');

//if user hover on img div

imgDiv.addEventListener('mouseenter', function() {
    uploadBtn.style.display = "block";
});

//if we hover out from img div

imgDiv.addEventListener('mouseleave', function() {
    uploadBtn.style.display = "none";
});

//lets work for image showing functionality when we choose an image to upload

//when we choose a foto to upload

file.addEventListener('change', function() {
    //this refers to file
    const choosedFile = this.files[0];

    if (choosedFile) {

        const reader = new FileReader(); //FileReader is a predefined function of JS

        reader.addEventListener('load', function() {
            img.setAttribute('src', reader.result);
        });

        reader.readAsDataURL(choosedFile);
    }
});


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
    function validateInput(inputElement) {
        // Get the input value
        var inputValue = inputElement.value;


        // Remove any characters that are not alphabets, numbers, or #
        var sanitizedValue = inputValue.replace(/[^a-zA-Z0-9#, ]/g, '');

        // Update the input field with the sanitized value
        inputElement.value = sanitizedValue;
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".remove-skill").click(function () {
            var skillToRemove = $(this).closest(".skill-preview").data("skill"); // Get the skill name from data attribute
            var currentSkills = $("input[name='skills']").val(); // Get the current skills from the input field
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
    function validateImage() {

    const fileInput = document.getElementById('file');
    const filePath = fileInput.value;
    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
    const file = fileInput.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB in bytes
    const errorElement = document.getElementById('file-error');

    // Reset previous error message
    errorElement.textContent = '';

    // Check file extension
    if (!allowedExtensions.exec(filePath)) {
        errorElement.textContent = 'Only .jpg, .jpeg, and .png files are allowed';
        fileInput.value = '';
        return false;
    }

    // Check file size
    if (file.size > maxSize) {
        errorElement.textContent = 'File size must not exceed 2MB';
        fileInput.value = '';
        return false;
    }

    return true;
}
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



@endsection
