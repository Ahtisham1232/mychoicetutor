@extends('student.layouts.main')
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

            /* css chat */
            .chat-online {
                color: #34ce57;
            }

            .chat-offline {
                color: #e4606d;
            }

            /* .chat-messages {
                display: flex;
                flex-direction: column;
                max-height: 500px;
                overflow-y: scroll;
            } */
            .chat-messages {
            display: flex;
            max-height: 300px;
            flex-direction: column-reverse; /* Reverse message order */
            overflow-y: scroll; /* Enable scrolling */
            }

            .chat-message-left,
            .chat-message-right {
                display: flex;
                flex-shrink: 0;
            }

            .chat-message-left {
                margin-right: auto;
            }

            .chat-message-right {
                flex-direction: row-reverse;
                margin-left: auto;
            }

            .py-3 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            .px-4 {
                padding-right: 1.5rem !important;
                padding-left: 1.5rem !important;
            }

            .flex-grow-0 {
                flex-grow: 0 !important;
            }

            .border-top {
                border-top: 1px solid #dee2e6 !important;
            }

            .border-right {
                border-right: 1px solid lightgrey !important;
            }

            .adminTutorBtn {
                display: flex;
            }

            .adminTutorBtn a {
                margin-right: 3px;
            }

            .userlists div {
                display: flex;
                justify-content: start
            }
        </style>

        <div class="page-content">
            <div class="container-fluid">
                <div class="card chatPannel">


                    <div class="row g-0">
                        <div class="col-12 col-lg-5 col-xl-3 border-right">
                            <div class="m-4 adminTutorBtn">
                                <a href="{{ route('student.messages.tutor') }}"> <button
                                        class="badge bg-primary">Tutors</button></a>
                                <a href="{{ route('student.messages.admins') }}"> <button
                                        class="badge bg-primary">Admin</button></a>
                            </div>
                            <hr>
                            {{-- <div class="px-4 d-none d-md-block">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <input type="text" class="form-control my-3" placeholder="Search...">
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div style=" display: flex; flex-direction: column;   max-height: 500px;overflow-y:scroll; "> --}}
                            {{-- Populating chat user list --}}
                            @foreach ($userlists as $userlist)
                                @if ($userlist->role_id == 1)
                                    <a href="{{ url('student/adminmessages') }}/{{ $userlist->id }}"
                                        class="list-group-item list-group-item-action border-0">
                                    @elseif ($userlist->role_id == 2)
                                        <a href="{{ url('student/tutormessages') }}/{{ $userlist->id }}"
                                            class="list-group-item list-group-item-action border-0">
                                        @elseif ($userlist->role_id == 3)
                                            <a href="{{ url('tutor/studentmessages') }}/{{ $userlist->id }}"
                                                class="list-group-item list-group-item-action border-0">
                                @endif

                                <div class="d-flex align-items-start">

                                    @if (empty($userlist->profile_pic))
                                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                            class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                    @else
                                        @if ($userlist->role_id == 2)
                                            <img src="{{ url('images/tutors/profilepics') }}/{{ $userlist->profile_pic }}"
                                                class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                        @elseif ($userlist->role_id == 3)
                                            <img src="{{ url('images/students/profilepics') }}/{{ $userlist->profile_pic }}"
                                                class="rounded-circle mr-1" alt="Richard" width="40" height="40">
                                        @endif
                                    @endif

                                    <div class="userlists" style="margin-left:10px">
                                        <div class=""> {{ $userlist->name }}</div>
                                        <div class="small"><span class="fa fa-circle chat-online"></span>
                                            Online</div>
                                    </div>


                                </div>


                                </a>
                            @endforeach
                            {{-- </div> --}}
                            <hr class="d-block d-lg-none mt-1 mb-0">
                        </div>

                        <div class="col-12 col-lg-7 col-xl-9">
                            @if ($header->name ?? '')
                                <div class="py-2 px-4 border-bottom d-none d-lg-block">
                                    <div class="d-flex align-items-center py-1">
                                        <div class="position-relative">
                                            @if (empty($header->profile_pic))
                                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                                    class="rounded-circle mr-1" alt="Richard" width="40"
                                                    height="40">
                                            @else
                                                <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                                    class="rounded-circle mr-1" alt="Richard" width="40"
                                                    height="40">
                                            @endif

                                        </div>
                                        <div class="" style="margin-left:10px;">
                                            <strong>{{ $header->name }}</strong>
                                            {{-- <div class="text-muted small"><em>Typing...</em></div> --}}
                                        </div>

                                    </div>
                                </div>
                            @endif
                            <div class="position-relative" id="chatbox">
                                <div class="chat-messages p-4">

                                    @if (empty($messages))
                                        <div class="chat-message-center pb-4">

                                            Please select anyone from the list to start chat
                                        </div>
                                    @else
                                        @foreach ($messages as $message)
                                            @if ($message->from_id === session('userid')->id && $message->from_role_id === 3)
                                                {{-- <div class="my-message">
                                            <p>{{ $message->content }}</p>
                                <span>{{ $message->created_at->format('H:i') }}</span>
                            </div> --}}
                                                <div class="chat-message-right pb-4">
                                                    <div>
                                                        <img src="{{ url('images/students/profilepics') }}/{{ $profile_pics->profile_pic ?? ''}}"
                                                            class="rounded-circle mr-1" alt="Chris Wood" width="40"
                                                            height="40">
                                                        <div class="text-muted small text-nowrap mt-2">
                                                            {{ $message->created_at }}</div>
                                                    </div>
                                                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                        <div class="font-weight-bold mb-1">You</div>
                                                        {{ $message->body }}
                                                    </div>
                                                </div>
                                            @else
                                                {{-- <div class="their-message">
                                            <p>{{ $message->content }}</p>
                            <span>{{ $message->created_at->format('H:i') }}</span>
                        </div> --}}
                                                <div class="chat-message-left pb-4">
                                                    <div>
                                                        @if ($header->role_id == 2)
                                                            <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                                                class="rounded-circle mr-1" alt="Chris Wood" width="40"
                                                                height="40">
                                                        @elseif ($header->role_id == 1)
                                                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                                                class="rounded-circle mr-1" alt="Chris Wood" width="40"
                                                                height="40">
                                                        @endif

                                                        <div class="text-muted small text-nowrap mt-2">
                                                            {{ $message->created_at }}</div>
                                                    </div>
                                                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                        <div class="font-weight-bold mb-1">{{ $header->name }}</div>
                                                        {{ $message->body }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                            @if ($header->name ?? '')
                                {{-- @else --}}
                                <div class="flex-grow-0 py-3 px-4 border-top">
                                    @if (session('userid')->role_id == 1)
                                        <form action="{{ route('admin.messages.send') }}" method="POST">
                                        @elseif (session('userid')->role_id == 2)
                                            <form action="{{ route('tutor.messages.send') }}" method="POST">
                                            @elseif (session('userid')->role_id == 3)
                                                <form action="{{ route('student.messages.send') }}" method="POST">
                                                    {{-- @elseif (session('userid')->role_id == 4) --}}
                                                    {{-- <form action="{{ route('parent.messages.send') }}" method="POST"> --}}
                                    @endif
                                    @csrf
                                    <div class="input-group">


                                        <input type="hidden" id="receiver_role_id" name="receiver_role_id"
                                            placeholder="reole id" value="{{ $header->role_id }}">
                                        <input type="hidden" id="receiver_id" name="receiver_id"
                                            placeholder="receiver id" value="{{ $header->id }}">

                                        <input type="text" id="message" name="message" class="form-control"
                                            placeholder="Type your message here ...">


                                        <button type="submit" class="btn btn-sm btn-success ml-1"><span
                                                class="fa fa-paper-plane">
                                            </span> Send</i></button>
                                    </div>
                                    <span class="text-danger" style="float: left !important;">
                                        @error('message')
                                            {{ "Can't send empty message!" }}
                                        @enderror
                                    </span>
                                    </form>

                                </div>
                            @endif

                        </div>
                    </div>
                </div>



            </div>
            <script>
                // Function to reload chat messages using AJAX
                function reloadChat() {
                var RoleId = <?php echo isset($header->role_id) ? json_encode($header->role_id) : '""'; ?>;
            console.log(RoleId);
            var UrlId = <?php echo isset($header->id) ? json_encode($header->id) : '""'; ?>;
            // AJAX request to fetch updated chat messages
            var url = "";
            @if(isset($header) && $header !== null)
            // Set the URL based on the RoleId
            if (RoleId == 1) {
                url = "/student/adminmessagesload/" + UrlId;
            } else {
                url = "/student/tutormessagesload/" + UrlId;
            }
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    // Update the chat messages section with the fetched content
                    $('#chatbox').html(response);

                }
            });
            @endif
                }

                // Reload chat messages every 10 seconds
                setInterval(reloadChat, 10000);
            </script>

            <!-- content-wrapper ends -->
        @endsection
