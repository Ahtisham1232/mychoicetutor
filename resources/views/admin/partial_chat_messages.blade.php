<div class="position-relative chatarea" id="chatbox">
    <div class="chat-messages p-4">

        @if (empty($messages))
            <div class="chat-message-center pb-4">

                Please select anyone from the list to start chat
            </div>
        @else
            @foreach ($messages as $message)
                @if ($message->from_id === session('userid')->id && $message->from_role_id === 1)
                    <div class="chat-message-right pb-4">
                        <div style="margin-right: 12px;">

                            <div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-1.webp"
                                    class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40">
                                <div class="text-muted small text-nowrap mt-2">
                                    {{ $message->created_at }}</div>
                            </div>
                            <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                <div class="font-weight-bold mb-1">You</div>
                                {{ $message->body }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="chat-message-left pb-4">
                        <div style="margin-right: 12px;">
                            @if ($header->role_id == 3)
                                <img src="{{ url('images/students/profilepics') }}/{{ $header->profile_pic }}"
                                    class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40">
                            @elseif ($header->role_id == 2)
                                <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                    class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40">
                            @elseif ($header->role_id == 1)
                                <img src="{{ asset('images/students/profilepics/no-img.jpg') }}"
                                    class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40">
                            @endif

                            <div class="text-muted small text-nowrap mt-2">
                                {{ $message->created_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                            </div>
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
