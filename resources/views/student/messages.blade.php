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
                        <div class="col-12 col-lg-5 col-xl-4 border-right {{ $header->name ?? '' ? 'd-none' : '' }}">
                            <div class="m-4 adminTutorBtn">
                                <a href="{{ route('student.messages.tutor') }}"> <button
                                        class="badge bg-primary buttons">Tutors</button></a>
                                <a href="{{ route('student.messages.admins') }}"> <button
                                        class="badge bg-primary buttons">Admin</button></a>
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

                                <div class="d-flex align-items-start my-3">

                                    @if (empty($userlist->profile_pic))
                                        <img src="{{ asset('images/students/profilepics/no-img.jpg') }}"
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
                                        <div class="small chat-status" data-chat-user="{{ $userlist->role_id }}_{{ $userlist->id }}">
                                            <span class="fa fa-circle {{ ($userlist->is_online ?? false) ? 'chat-online' : 'chat-offline' }}"></span>
                                            {{ ($userlist->is_online ?? false) ? 'Online' : 'Offline' }}
                                        </div>
                                    </div>


                                </div>


                                </a>
                            @endforeach
                            {{-- </div> --}}
                            <hr class="d-block d-lg-none mt-1 mb-0">
                        </div>

                        <div class="col-12 {{ $header->name ?? '' ? 'col-lg-12 col-xl-12' : 'col-lg-7 col-xl-8' }}">
                            @if ($header->name ?? '')
                                <div class="py-2 px-4 border-bottom d-none d-lg-block">
                                    <div class="d-flex align-items-center py-1">
                                        <div class="position-relative">
                                            @if (empty($header->profile_pic))
                                                <img src="{{ asset('images/students/profilepics/no-img.jpg') }}"
                                                    class="rounded-circle mr-1" alt="{{ $header->name }}" width="40"
                                                    height="40">
                                            @else
                                                <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                                    class="rounded-circle mr-1" alt="{{ $header->name }}" width="40"
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
                                @include('student.partials.chat-messages', ['messages' => $messages, 'studentProfile' => $studentProfile ?? null])
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
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 py-5">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-comments fa-3x text-muted"></i>
                                        </div>
                                        <h5 class="text-muted">Select a conversation</h5>
                                        <p class="text-muted">Choose someone from the list to start chatting</p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>



            </div>
            <script>
                // Pusher instance (used for presence and, when in a conversation, for chat/notifications)
                var pusher = new Pusher('{{ config("chatify.pusher.key") }}', {
                    cluster: '{{ config("chatify.pusher.options.cluster") }}',
                    encrypted: true,
                    authEndpoint: '{{ url("student/chat-presence-auth") }}',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': (document.querySelector('input[name="_token"]') && document.querySelector('input[name="_token"]').value) || (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || '',
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
                            el.innerHTML = '<span class="fa fa-circle ' + (isOnline ? 'chat-online' : 'chat-offline') + '"></span> ' + (isOnline ? 'Online' : 'Offline');
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
                    var RoleId = <?php echo isset($header->role_id) ? json_encode($header->role_id) : '""'; ?>;
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

                @if(isset($header) && $header !== null)
                // Real-time chat and notifications (same Pusher instance as presence)
                var channel = pusher.subscribe('chat.{{ session("userid")->id }}');
                channel.bind('new-message', function(data) {
                    console.log('New message received:', data);
                    // Reload chat when new message arrives
                    reloadChat();
                });

                var notificationChannel = pusher.subscribe('notifications.{{ session("userid")->id }}');
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
                        </button>
                    `;

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
                    console.log('DOM loaded, looking for message form...');
                    const messageForm = document.querySelector('form[action*="messages.send"]');
                    console.log('Message form found:', messageForm);

                    if (messageForm) {
                        console.log('Adding submit listener to form');
                        messageForm.addEventListener('submit', function(e) {
                            console.log('Form submit intercepted');
                            e.preventDefault(); // Prevent normal form submission
                            e.stopPropagation(); // Stop event bubbling

                            const formData = new FormData(this);
                            const messageInput = document.getElementById('message');

                            // Show loading state
                            const submitBtn = this.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = 'Sending...';
                            }

                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                console.log('Response received:', response);
                                return response.json();
                            })
                            .then(data => {
                                console.log('Success data:', data);
                                // Clear the message input
                                if (messageInput) messageInput.value = '';
                                // Reload chat to show the new message
                                reloadChat();

                                // Reset button
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = 'Send';
                                }
                            })
                            .catch(error => {
                                console.error('Error sending message:', error);
                                alert('Failed to send message. Please try again.');

                                // Reset button
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = 'Send';
                                }
                            });
                        });
                    } else {
                        console.log('Message form not found');
                    }
                });
            </script>

            <!-- content-wrapper ends -->
        @endsection
