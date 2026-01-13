@if($type=="students" && isset($stdlists))

    @foreach ($stdlists as $stdlist)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td><a href="studentprofile/{{$stdlist->student_id}}">{{ $stdlist->student_name }}</a></td>
            <td>{{ $stdlist->email }}</td>
            <td>{{ $stdlist->student_mobile }}</td>

            <td>{{ \Carbon\Carbon::parse($stdlist->created_at)->format('d-m-Y h:i A') }}</td>

            <td>
                <div class="form-check form-switch">
                    @if ($stdlist->student_status == 1)
                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                    @else
                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                    @endif
                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{$stdlist->student_id}}','{{$stdlist->student_status}}');" class="checkbox" @if ($stdlist->student_status == 1) then checked

                    @endif>
                </div>
            </td>
            <td><button class="btn btn-sm btn-danger" onclick="showDeleteWarning({{$stdlist->student_id}});">Delete</button></td>

            {{-- <td><button class="badge badge-success"
                    onclick="openconfirmmodal({{ $stdlist->demo_id }});">Confirm</button>
                <button class="badge badge-primary"
                    onclick="openupdatemodal({{ $stdlist->demo_id }})">Modify</button>
            </td> --}}
        </tr>
    @endforeach
@endif
@if($type=="tutors" && isset($ttrlists))
    @foreach ($ttrlists as $ttrlist)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td><a href="tutorprofile/{{ $ttrlist->tutor_id }}">{{ $ttrlist->tutor_name }}</a></td>
            <td>{{ $ttrlist->tutor_mobile }}</td>
            <td>{{ $ttrlist->class_name }}</td>
            <td>{{ $ttrlist->subject_name }}</td>
            <td>{{ $ttrlist->rate }}</td>
            <td><a href="#" onclick="updatecommission('{{$ttrlist->rate_id}}','{{$ttrlist->admin_commission}}')"> {{ $ttrlist->admin_commission }} <span class="badge bg-primary ml-3"> Update</span> </a>
            </td>
            <td>
                <div class="form-check form-switch">
                    @if ($ttrlist->tutor_status == 1)
                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                    @else
                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                    @endif
                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{ $ttrlist->tutor_id }}','{{ $ttrlist->tutor_status }}');"
                    class="checkbox" @if ($ttrlist->tutor_status == 1) then checked @endif>

                </div>
            </td>

        </tr>
    @endforeach
@endif

@if ($type=="contents" && isset($contents))
    @foreach ($contents as $content)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$content->classname}}</td>
            <td>{{$content->subjectname}}</td>
            <td>{{$content->topic_name}}</td>
            <td><div class="text-center">{{$content->content_description}}</div><br>
                @if ($content->content_link)
                <div class="text-center"><a href="{{url('uploads/documents/learningcontents')}}/{{$content->content_link}}" target="_blank" class="badge bg-primary mt-2 ">View</a></div>

                @endif
            </td>
            <td><div class="text-center">{{$content->video_description}}</div><br>
                @if ($content->video_link)

                <div class="text-center"><a href="{{url('uploads/videos/learningcontents')}}/{{$content->video_link}}" target="_blank" class="badge bg-primary">View</a></div>
                @endif
            </td>
            <td><div class="text-center">{{$content->blog_description}}</div><br>
                @if ($content->blog_link)

                <div class="text-center"><a href="{{$content->blog_link}}" target="_blank" class="badge bg-primary">View</a></div>
                @endif
            </td>
            <td>
                <div class="form-check form-switch">
                    @if ($content->contentstatus == 1)
                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                    @else
                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                    @endif
                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{$content->contentid}}','{{$content->contentstatus}}');" class="checkbox" @if ($content->contentstatus == 1) then checked

                    @endif>
                </div>
            </td>

            <td><a href="learningcontents/{{$content->contentid}}"><button class="badge bg-danger">Modify</button></a></td>
        </tr>
    @endforeach
@endif

@if($type=="assignments" && isset($data))
    @foreach ($data as $datalist)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$datalist->assignment_name}}</td>
            <td>{{$datalist->class_name}}</td>
            <td>{{$datalist->subject_name}}</td>
            <td>{{$datalist->topic_name}}</td>
            <td><a href="{{url('admin/tutorprofile').'/'.$datalist->tutor_id}}">{{$datalist->tutor_name}}</td>
            <td>{{$datalist->assignment_start_date}}</td>
            <td>{{$datalist->assignment_end_date}}</td>
            {{-- <td>{{$datalist->assigned_to}}</td> --}}
            <td><div class="text-center"> <a href="{{url('admin/assignments/').'/'.$datalist->assignment_id}}" class="badge bg-primary">View</a></div></td>
            <td>
                <div class="form-check form-switch">
                    @if ($datalist->assignment_status == 1)
                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                    @else
                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                    @endif
                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{$datalist->assignment_id}}','{{$datalist->assignment_status}}');" class="checkbox" @if ($datalist->assignment_status == 1) then checked

                    @endif>
                </div>
            </td>


        </tr>
    @endforeach
@endif
@if($type=="questions" && isset($questions))
    @foreach ($questions as $question)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $question->class }}</td>
            <td>{{ $question->subject }}</td>
            <td>{{ $question->topic }}</td>
            <td>{{ $question->question }}</td>
            {{-- <td><div ><textarea class="form-control">{{$question->question}}</textarea></div></td> --}}
            <td>
                <div class="form-check form-switch">
                    @if ($question->question_status == 1)
                        <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                    @else
                        <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                    @endif
                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                        onclick="changestatus('{{ $question->question_id }}','{{ $question->question_status }}');"
                        class="checkbox" @if ($question->question_status == 1) then checked @endif>
                </div>
            </td>


            <td>
                <div class="text-center"><a class="badge bg-primary"
                        href="{{ url('admin/questionupdate') . '/' . $question->question_id }}">View/Update</a>
                </div>
            </td>
        </tr>
    @endforeach
@endif
@if($type=="testlists" && isset($testlists))
    @foreach ($testlists as $testlist)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $testlist->test_name }}</td>
            <td>{{ $testlist->test_description }}</td>
            <td>{{ $testlist->class_name }}</td>
            <td>{{ $testlist->subject_name }}</td>
            <td>{{ $testlist->topic_name }}</td>
            <td>{{ $testlist->test_duration }}</td>
            <td>{{ $testlist->max_attempt }}</td>
            <td>{{ $testlist->test_start_date }}</td>
            <td>{{ $testlist->test_end_date }}</td>
            <td>
                <div class="form-check form-switch">
                    @if ($testlist->test_status == 1)
                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                    @else
                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                    @endif
                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{ $testlist->test_id }}','{{ $testlist->test_status }}');"
                    class="checkbox" @if ($testlist->test_status == 1) then checked @endif>
                </div>
            </td>

            <td>
                <div class="text-center"><a class="badge bg-primary"
                        href="{{ url('admin/onlinetests') . '/' . $testlist->test_id }}">View/Update</a>
                </div>
            </td>
        </tr>
    @endforeach
@endif
@if($type=="tutor-testlists" && isset($onlinetestlists))
    @foreach ($onlinetestlists as $testlist)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $testlist->test_name }}</td>
            <td>{{ $testlist->test_description }}</td>
            <td>{{ $testlist->class_name }}</td>
            <td>{{ $testlist->subject_name }}</td>
            <td>{{ $testlist->topic_name }}</td>
            <td>{{ $testlist->test_duration }}</td>
            <td>{{ $testlist->max_attempt }}</td>
            <td>{{ $testlist->test_start_date }}</td>
            <td>{{ $testlist->test_end_date }}</td>
            <td>
                <div class="form-check form-switch">
                    @if ($testlist->test_status == 1)
                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                    @else
                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                    @endif
                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{ $testlist->test_id }}','{{ $testlist->test_status }}');"
                    class="checkbox" @if ($testlist->test_status == 1) then checked @endif>
                </div>
            </td>

            <td>
                <div class="text-center"><a class="badge bg-primary p-1"
                        href="{{ url('tutor/onlinetests') . '/' . $testlist->test_id }}">View/Update</a>
                </div>
            </td>
        </tr>
    @endforeach
@endif
@if($type=="tutor_subjective_tests" && isset($onlineTests))
    @foreach ($onlineTests as $test)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$test->class_name}}</td>
            <td>{{$test->sub_name}}</td>
            <td>{{$test->topic_name}}</td>
            <td>{{$test->name}}</td>
            <td>{{ \Carbon\Carbon::parse($test->test_start_date)->format('d M Y') }}</td>
            <td>
                <a href="{{url('tutor/onlinetests/responses')}}/{{$test->id}}"><button class="btn btn-sm btn-primary">View</button></a>
            </td>
        </tr>
    @endforeach
@endif
@if($type=="tutor_subjective_responses" && isset($responses))
    @foreach ($responses as $response)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$response->std_name}}</td>
            <td>{{ \Carbon\Carbon::parse($response->test_attempted_on)->format('d M Y') }}</td>
            <td>
                @if($response->status ==1 )
                    <a href="#"><button class="btn btn-sm btn-primary" disabled>completed</button></a>
                @else
                    <a href="{{url('tutor/onlinetests/responses/student')}}/{{$response->id}}"><button class="btn btn-sm btn-primary">View</button></a>
                @endif
            </td>
        </tr>
    @endforeach
@endif



