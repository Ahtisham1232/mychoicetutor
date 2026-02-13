@extends('admin.layouts.main')
@section('main-section')
    <div class="main-content">
        <style>
            .listHeader {
                display: flex;
                justify-content: space-between;
            }

            .chat-online {
                color: #34ce57;
            }

            .chat-offline {
                color: #e4606d;
            }

            .chat-messages {
                display: flex;
                max-height: 300px;
                flex-direction: column-reverse;
                /* Reverse message order */
                overflow-y: scroll;
                /* Enable scrolling */
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
                border-right: 2px solid lightgrey !important;
            }

            .list-group-item {
                transition: all 0.2s ease;
            }

            .list-group-item:hover {
                background-color: #f8f9fa;
                transform: translateX(4px);
            }

            .list-group-item.active {
                background-color: #e9ecef;
            }

            .buttons {
                padding: 10px;
                border: none;
                font-size: 12px;
            }
        </style>

        <div class="page-content">
            <div class="container-fluid">
                <div class="card chatPannel">
                    <div class="row g-0">

                        @if ($activeTab === 'all')
                            <div class="col-12">
                                <div class="alert alert-dark text-center fw-bold" role="alert">
                                    This page displays all tutors and students. Use the buttons above to view a specific
                                    category.
                                </div>
                            </div>
                        @elseif($activeTab === 'student')
                            <div class="col-12">
                                <div class="alert alert-dark text-center fw-bold" role="alert">
                                    This page displays all students..
                                </div>
                            </div>
                        @elseif($activeTab === 'tutor')
                            <div class="col-12">
                                <div class="alert alert-dark text-center fw-bold" role="alert">
                                    This page displays all tutors..
                                </div>
                            </div>
                        @endif

                        @if (!empty($searchtext))
                            <div class="alert alert-info py-2 px-3 mb-2">
                                🔍 Showing search results for "<strong>{{ $searchtext }}</strong>"
                            </div>
                        @endif


                        <div class="col-12 col-lg-5 col-xl-4 border-right {{ optional($header)->name ? 'd-none' : '' }}">


                            <div class="m-4">
                                <a href="{{ route('admin.messages.students') }}"> <button
                                        class="badge bg-primary buttons">Students</button></a>
                                <a href="{{ route('admin.messages.tutors') }}"> <button
                                        class="badge bg-primary buttons">Tutors</button></a>
                                @if (!empty($searchtext))
                                    <a href="{{ route('admin.messages') }}"> <button class="badge bg-primary buttons">✖
                                            Remove Filter</button></a>
                                @endif

                            </div>
                            <hr>

                            @if ($activeTab === 'tutor')
                                <div class="px-4 d-none d-md-block">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 position-relative">
                                            <form action="{{ route('admin.chat.tutor.search') }}" method="POST">
                                                @csrf
                                                <input type="text" class="form-control my-3 pl-5" id="searchtext"
                                                    name="searchtext" placeholder="Search...">
                                                <button
                                                    class="btn btn-sm btn-primary position-absolute top-50 translate-middle-y"
                                                    style="right: 10px;" type="submit">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($activeTab === 'student')
                                <div class="px-4 d-none d-md-block">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 position-relative">
                                            <form action="{{ route('admin.chat.student.search') }}" method="POST">
                                                @csrf
                                                <input type="text" class="form-control my-3 pl-5" id="searchtext"
                                                    name="searchtext" placeholder="Search...">
                                                <button
                                                    class="btn btn-sm btn-primary position-absolute top-50 translate-middle-y"
                                                    style="right: 10px;" type="submit">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Populating chat user list --}}
                            @foreach ($userlists as $userlist)
                                @if ($userlist->role_id == 1)
                                    <a href="{{ url('admin/adminmessages') }}/{{ $userlist->id }}"
                                        class="list-group-item list-group-item-action border-0">
                                    @elseif ($userlist->role_id == 2)
                                        <div class="dropdown">
                                            <span type="button"
                                                style="  float:right; height: 50px; font-size:20px; margin-right:5px"
                                                class="" data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>

                                            </span>

                                            <ul class="dropdown-menu">
                                                <a href="{{ url('admin/chatClearAdmintutor') }}/{{ $userlist->id }}">
                                                    <li class="dropdown-item">Clear Chat</li>
                                                </a>
                                            </ul>
                                        </div>
                                        <a href="{{ url('admin/tutormessages') }}/{{ $userlist->id }}"
                                            class="list-group-item list-group-item-action border-0">
                                        @elseif ($userlist->role_id == 3)
                                            <div class="dropdown">
                                                <span type="button"
                                                    style="  float:right; height: 50px; font-size:20px; margin-right:5px"
                                                    class="" data-bs-toggle="dropdown">
                                                    <i class="ri-more-fill"></i>

                                                </span>

                                                <ul class="dropdown-menu">
                                                    <a
                                                        href="{{ url('admin/adminclearsstudentmessages') }}/{{ $userlist->id }}">
                                                        <li class="dropdown-item">Clear Chat</li>
                                                    </a>

                                                </ul>
                                            </div>

                                            <a href="{{ url('admin/studentmessages') }}/{{ $userlist->id }}"
                                                class="list-group-item list-group-item-action border-0">
                                @endif

                                <div class="d-flex align-items-start m-3">

                                    @if (empty($userlist->profile_pic))
                                        <img src="{{asset('images/students/profilepics/no-img.jpg')}}" class="rounded-circle mr-1"
                                            alt="Richard" width="40" height="40">
                                    @else
                                        @if ($userlist->role_id == 2)
                                            <img src="{{ url('images/tutors/profilepics') }}/{{ $userlist->profile_pic }}"
                                            class="rounded-circle mr-1"
                                            width="40" height="40"
                                            onerror="this.onerror=null;this.src='{{ asset('images/students/profilepics/no-img.jpg') }}';">

                                        @elseif ($userlist->role_id == 3)
                                            <img src="{{ url('images/students/profilepics') }}/{{ $userlist->profile_pic }}"
                                            class="rounded-circle mr-1"
                                            width="40" height="40"
                                            onerror="this.onerror=null;this.src='{{ asset('images/students/profilepics/no-img.jpg') }}';">

                                        @endif
                                    @endif



                                    <div class="flex-grow-1" style="margin-left:10px;">
                                        {{ $userlist->name }}


                                        <div class="small chat-status"
                                            data-chat-user="{{ $userlist->role_id }}_{{ $userlist->id }}">
                                            <span
                                                class="fa fa-circle {{ $userlist->is_online ?? false ? 'chat-online' : 'chat-offline' }}"></span>
                                            {{ $userlist->is_online ?? false ? 'Online' : 'Offline' }}
                                        </div>

                                    </div>


                                </div>

                                </a>
                            @endforeach
                        </div>

                        <hr class="d-block d-lg-none mt-1 mb-0">
                        {{-- </div> --}}

                        <div class="col-12 {{ optional($header)->name ? 'col-lg-12 col-xl-12' : 'col-lg-7 col-xl-8' }}">
                            @if (isset($header) && $header->name)
                                <div class="py-2 px-4 border-bottom d-none d-lg-block ">
                                    <div class="d-flex align-items-center py-1 ">
                                        <div class="position-relative">
                                            @if (empty($header->profile_pic))
                                                <img src="{{asset('images/students/profilepics/no-img.jpg')}}"
                                                    class="rounded-circle mr-1" alt="Richard" width="40"
                                                    height="40">
                                            @else
                                                @if ($header->role_id == 2)
                                                    <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                                        class="rounded-circle mr-1" alt="Tutor" width="40"
                                                        height="40">
                                                @elseif ($header->role_id == 3)
                                                    <img src="{{ url('images/students/profilepics') }}/{{ $header->profile_pic }}"
                                                        class="rounded-circle mr-1" alt="Student" width="40"
                                                        height="40">
                                                @endif
                                            @endif

                                        </div>
                                        <div class="flex-grow-1 pl-3" style="margin-left:30px !important; ">
                                            <strong>{{ $header->name }} </strong>
                                            {{-- <div class="text-muted small"><em>Typing...</em></div> --}}

                                        </div>

                                    </div>
                                </div>
                            @else
                                <div class="text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-comments fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted" style="font-size: 20px">Select a conversation</h5>
                                    <p class="text-muted" style="font-size: 17px">Choose someone from the list to start
                                        chatting</p>
                                </div>

                            @endif
                            <div class="position-relative chatarea" id="chatbox">
                                <div class="chat-messages p-4">

                                    @if (isset($messages))
                                        @foreach ($messages as $message)
                                            @if ($message->from_id === session('userid')->id && $message->from_role_id === 1)
                                                <div class="chat-message-right pb-4">
                                                    <div style="margin-left:12px">
                                                        <img src="{{asset('images/students/profilepics/no-img.jpg')}}"
                                                            class="rounded-circle mr-1" alt="Student" width="40"
                                                            height="40">
                                                        <div class="text-muted small text-nowrap mt-2">
                                                            {{ $message->created_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 shadow-sm"
                                                        style="max-width: 80%;">
                                                        <div class="small text-muted font-weight-bold mb-1">You</div>
                                                        <div class="text-dark">
                                                            {{ $message->body }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="chat-message-left pb-4">
                                                    <div>
                                                        @if ($header->role_id == 3)
                                                            <img src="{{ url('images/students/profilepics') }}/{{ $header->profile_pic }}"
                                                                class="rounded-circle mr-1" alt="Student"
                                                                width="40" height="40">
                                                        @elseif ($header->role_id == 2)
                                                            <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                                                class="rounded-circle mr-1" alt="Tutor"
                                                                width="40" height="40">
                                                        @elseif ($header->role_id == 1)
                                                            <img src="{{asset('images/students/profilepics/no-img.jpg')}}"
                                                                class="rounded-circle mr-1" alt="Admin"
                                                                width="40" height="40">
                                                        @endif

                                                        <div class="text-muted small text-nowrap mt-2">
                                                            {{ $message->created_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 shadow-sm"
                                                        style="max-width: 80%; border: 1px solid #e9ecef;">
                                                        <div class="small font-weight-bold text-primary mb-1">
                                                            {{ $header->name }}
                                                        </div>

                                                        <div class="text-dark" style="line-height: 1.4;">
                                                            {{ $message->body }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                            @if (isset($header) && $header->name)
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

                <!-- content-wrapper ends -->

            </div>
        </div>
        <script>
            // Pusher instance (used for presence and, when in a conversation, for chat/notifications)
            var pusher = new Pusher('{{ config('chatify.pusher.key') }}', {
                cluster: '{{ config('chatify.pusher.options.cluster') }}',
                encrypted: true,
                authEndpoint: '{{ url('admin/chat-presence-auth') }}',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': (document.querySelector('input[name="_token"]') && document.querySelector(
                            'input[name="_token"]').value) || (document.querySelector('meta[name="csrf-token"]') &&
                            document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }
            });

            // Presence channel: online/offline status (Pusher Presence Channels)
            (function() {
                function setUserOnlineStatus(userId, isOnline) {
                    var el = document.querySelector('.chat-status[data-chat-user="' + userId + '"]');
                    if (el) {
                        el.innerHTML = '<span class="fa fa-circle ' + (isOnline ? 'chat-online' : 'chat-offline') +
                            '"></span> ' + (isOnline ? 'Online' : 'Offline');
                    }
                }
                var presenceChannel = pusher.subscribe('presence-chat');
                presenceChannel.bind('pusher:subscription_succeeded', function() {
                    var members = presenceChannel.members.members;
                    document.querySelectorAll('.chat-status[data-chat-user]').forEach(function(el) {
                        var key = el.getAttribute('data-chat-user');
                        setUserOnlineStatus(key, !!members[key]);
                    });
                });
                presenceChannel.bind('pusher:member_added', function(member) {
                    setUserOnlineStatus(member.id, true);
                });
                presenceChannel.bind('pusher:member_removed', function(member) {
                    setUserOnlineStatus(member.id, false);
                });
            })();

            // Function to reload chat messages using AJAX
            function reloadChat() {
                var RoleId = <?php echo isset($header) && $header !== null && isset($header->role_id) ? json_encode($header->role_id) : '""'; ?>;
                console.log(RoleId);
                var UrlId = <?php echo isset($header) && $header !== null && isset($header->id) ? json_encode($header->id) : '""'; ?>;
                // AJAX request to fetch updated chat messages
                var url = "";
                @if (isset($header) && $header !== null)
                    // Set the URL based on the RoleId
                    if (RoleId == 2) {
                        url = "/admin/tutormessagesload/" + UrlId;
                    } else {
                        url = "/admin/studentmessagesload/" + UrlId;
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

            @if (isset($header) && $header !== null)
                // Real-time chat and notifications (same Pusher instance as presence)
                var channel = pusher.subscribe('chat.{{ session('userid')->id }}');
                channel.bind('new-message', function(data) {
                    console.log('New message received:', data);
                    // Reload chat when new message arrives
                    reloadChat();
                });

                var notificationChannel = pusher.subscribe('notifications.{{ session('userid')->id }}');
                notificationChannel.bind('message.notification', function(data) {
                    console.log('Notification received:', data);
                    // Show notification
                    showNotification(data.message);

                    // Reload chat to show new message
                    reloadChat();
                });
            @endif

            // Function to show notifications
            function showNotification(message) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                notification.innerHTML = `
                <strong>New Message!</strong> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>`;

                // Add to page
                document.body.appendChild(notification);

                // Auto remove after 5 seconds
                setTimeout(function() {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 5000);
            }

            // Fallback: Reload chat messages every 30 seconds (reduced frequency)
            setInterval(reloadChat, 30000);

            // Handle form submission via AJAX
            document.addEventListener('DOMContentLoaded', function() {
                const messageForm = document.querySelector('form[action*="messages.send"]');
                if (messageForm) {
                    messageForm.addEventListener('submit', function(e) {
                        e.preventDefault(); // Prevent normal form submission

                        const formData = new FormData(this);
                        const messageInput = document.getElementById('message');

                        fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Clear the message input
                                if (messageInput) messageInput.value = '';
                                // Reload chat to show the new message
                                reloadChat();
                            })
                            .catch(error => {
                                console.error('Error sending message:', error);
                                alert('Failed to send message. Please try again.');
                            });
                    });
                }
            });
        </script>
        <!-- content-wrapper ends -->
    @endsection
