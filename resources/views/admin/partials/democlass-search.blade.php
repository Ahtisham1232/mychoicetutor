@if($type == 'admin' && isset($demos))
    @foreach ($demos as $demo)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td><a href="studentprofile/{{$demo->student_id}}">{{ $demo->student_name }}</a></td>
            <td>{{ $demo->student_mobile }}</td>
            <td><a href="tutorprofile/{{$demo->tutor_id}}">{{ $demo->tutor }}</a></td>
            <td>{{ $demo->tutor_mobile }}</td>
            <td>{{ $demo->class_name }}</td>
            <td>{{ $demo->subject }}</td>
            <td>
                @if ($demo->status == 1)
                    <span class="badge bg-info">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 2)
                    <span class="badge bg-primary">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 3)
                    <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 4)
                    <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 5)
                    <span class="badge bg-danger">{{ $demo->currentstatus }}</span>
                @endif
            </td>
            <td>
                @if ($demo->slot_1)
                    <span style="color: black;">{{ date('d-m-Y', strtotime($demo->slot_1)) }}</span>
                    <span style="color: #299CDB;">{{ date('h:i A', strtotime($demo->slot_1)) }}</span>
                @else
                    <span class="text-muted">Not selected</span>
                @endif
            </td>
            <td>
                @if ($demo->slot_2)
                    <span style="color: black;">{{ date('d-m-Y', strtotime($demo->slot_2)) }}</span>
                    <span style="color: #299CDB;">{{ date('h:i A', strtotime($demo->slot_2)) }}</span>
                @else
                    <span class="text-muted">Not selected</span>
                @endif
            </td>
            <td>
                @if ($demo->slot_3)
                    <span style="color: black;">{{ date('d-m-Y', strtotime($demo->slot_3)) }}</span>
                    <span style="color: #299CDB;">{{ date('h:i A', strtotime($demo->slot_3)) }}</span>
                @else
                    <span class="text-muted">Not selected</span>
                @endif
            </td>
            <td>{{ $demo->slot_confirmed }}</td>
            <td><a href="{{ $demo->demo_link }}">{{ $demo->demo_link }}</a></td>
            <td>{{ $demo->remarks }}</td>
            <td><button class="btn btn-sm btn-success"
                    onclick="openconfirmmodal({{ $demo->demo_id }});">Confirm</button>
                <button class="btn btn-sm btn-primary"
                    onclick="openupdatemodal({{ $demo->demo_id }})">Modify</button>
            </td>
        </tr>
    @endforeach
@endif
@if($type == 'student' && isset($demos))
    @foreach ($demos as $demo)
        <tr>
        <td>{{$loop->iteration}}</td>
        <td>{{$demo->tutor}}</td>
        <td>{{$demo->class_name}}</td>
        <td>{{$demo->subject}}</td>
        <td>
            @if($demo->status == 1)
            <span class="badge bg-info">{{$demo->currentstatus}}</span>
            @elseif ($demo->status == 2)
            <span class="badge bg-primary">{{$demo->currentstatus}}</span>
            @elseif ($demo->status == 3)
            <span class="badge bg-success">{{$demo->currentstatus}}</span>
            @elseif ($demo->status == 4)
            <span class="badge bg-success">{{$demo->currentstatus}}</span>
            @elseif ($demo->status == 5)
            <span class="badge bg-danger">{{$demo->currentstatus}}</span>

            @endif
        </td>
        <td>{{$demo->slot_1}}</td>
        <td>{{$demo->slot_2}}</td>
        <td>{{$demo->slot_3}}</td>
        <td>{{$demo->slot_confirmed}}</td>
        <td>{{$demo->remarks}}</td>
        {{-- <td><a href="{{$demo->demo_link}}">{{$demo->demo_link}}</a></td> --}}

        @if ($demo->status == 1)
        <td>
            {{-- <a href="demoreschedule"><button class="btn btn-sm mr-1 btn-primary"><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button></a> --}}
            <a href="democancel/{{$demo->demo_id}}"><button class="badge bg-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button></a></td>
        @elseif ($demo->status == 2)
        <td>
            {{-- <a href="demoreschedule"><button class="btn btn-sm mr-1 btn-primary"><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button></a> --}}
            <a href="democancel/{{$demo->demo_id}}"><button class="badge bg-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button></a></td>
        @elseif ($demo->status == 3)
        <td>
            {{-- <button class="btn btn-sm mr-1 btn-primary" disabled><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button> --}}
            <a href="{{$demo->demo_link}}"><button class="badge bg-success"><i class="fa fa-play-circle-o" aria-hidden="true"></i> Join Class</button></a></td>
        @else
        <td>
            {{-- <button class="btn btn-sm mr-1 btn-primary" disabled><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button> --}}
            <button class="badge bg-secondary" disabled><i class="fa fa-times" aria-hidden="true"></i> Cancelled</button></td>

            @endif


        </tr>
    @endforeach
@endif
@if($type == 'tutor' && isset($demos))
    @foreach ($demos as $demo)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td><a href="studentprofile/{{$demo->student_id}}">{{ $demo->student_name }}</a></td>
            {{-- <td>{{ $demo->student_mobile }}</td> --}}
            {{-- <td><a href="tutorprofile/{{$demo->tutor_id}}">{{ $demo->tutor }}</a></td>
            <td>{{ $demo->tutor_mobile }}</td> --}}
            <td>{{ $demo->class_name }}</td>
            <td>{{ $demo->subject }}</td>
            <td>
                @if ($demo->status == 1)
                    <span class="badge bg-info">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 2)
                    <span class="badge bg-primary">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 3)
                    <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 4)
                    <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                @elseif ($demo->status == 5)
                    <span class="badge bg-danger">{{ $demo->currentstatus }}</span>
                @endif
            </td>
            <td>{{ $demo->slot_1 }}</td>
            <td>{{ $demo->slot_2 }}</td>
            <td>{{ $demo->slot_3 }}</td>
            <td>{{ $demo->slot_confirmed }}</td>
            <td><a href="{{ $demo->demo_link }}">{{ $demo->demo_link }}</a></td>
            <td>{{ $demo->remarks }}</td>
            <td><button class="btn btn-sm btn-success"
                    onclick="openconfirmmodal({{ $demo->demo_id }});">Confirm</button>
                <button class="btn btn-sm btn-primary"
                    onclick="openupdatemodal({{ $demo->demo_id }})">Modify</button>
            </td>
        </tr>
    @endforeach
@endif

