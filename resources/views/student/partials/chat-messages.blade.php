<div class="chat-messages p-4">
    <style>
        .chat-message-right {
            display: flex;
            flex-direction: row-reverse;
            margin-left: auto;
            justify-content: flex-start;
            align-items: flex-start;
        }

        /* Standardizes spacing for avatars */
        .chat-message-right img,
        .chat-message-left img {
            flex-shrink: 0;
        }

        /* Fixes the gap specifically for right-side messages */
        .chat-message-right .flex-shrink-1 {
            margin-right: 1rem !important;
            /* Space between bubble and your avatar */
            margin-left: 0 !important;
        }

        /* Fixes the gap for left-side messages */
        .chat-message-left .flex-shrink-1 {
            margin-left: 1rem !important;
            /* Space between bubble and sender avatar */
            margin-right: 0 !important;
        }
    </style>
    @if (isset($messages))
        @foreach ($messages as $message)
            @if ($message->from_id === session('userid')->id && $message->from_role_id === session('userid')->role_id)
                <div class="chat-message-right pb-4">
                    {{-- Avatar and Time Container --}}
                    <div class="d-flex flex-column align-items-center">
                        @if (empty($studentProfile->profile_pic ?? ''))
                            <img src="{{ asset('images/students/profilepics/no-img.jpg') }}" class="rounded-circle"
                                alt="You" width="40" height="40">
                        @else
                            <img src="{{ url('images/students/profilepics') }}/{{ $studentProfile->profile_pic }}"
                                class="rounded-circle" alt="You" width="40" height="40">
                        @endif
                        <div class="text-muted small text-nowrap mt-2">
                            {{ $message->created_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                        </div>
                    </div>

                    {{-- Message Bubble --}}
                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 shadow-sm"
                        style="max-width: 80%; border: 1px solid #e9ecef;">
                        <div class="small text-muted font-weight-bold mb-1">You</div>
                        <div class="text-dark">
                            {{ $message->body }}
                        </div>
                    </div>
                </div>
            @else
                {{-- Incoming message --}}
                <div class="chat-message-left pb-4">
                    <div>
                        @if (isset($header) && $header)
                            @if ($header->role_id == 2)
                                @if (empty($header->profile_pic))
                                    <img src="{{ asset('images/students/profilepics/no-img.jpg') }}" class="rounded-circle mr-1"
                                        alt="{{ $header->name }}" width="40" height="40">
                                @else
                                    <img src="{{ url('images/tutors/profilepics') }}/{{ $header->profile_pic }}"
                                        class="rounded-circle mr-1" alt="{{ $header->name }}" width="40"
                                        height="40">
                                @endif
                            @elseif ($header->role_id == 1)
                                <img src="{{ asset('images/students/profilepics/no-img.jpg') }}" class="rounded-circle mr-1"
                                    alt="{{ $header->name }}" width="40" height="40">
                            @endif
                        @else
                            <img src="{{ asset('images/students/profilepics/no-img.jpg') }}" class="rounded-circle mr-1" alt="Sender"
                                width="40" height="40">
                        @endif
                        <div class="text-muted small text-nowrap mt-2">
                            {{ $message->created_at }}
                        </div>
                    </div>
                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 shadow-sm"
                        style="max-width: 80%; border: 1px solid #e9ecef;">
                        <div class="small font-weight-bold text-primary mb-1">
                            {{ isset($header) && $header ? $header->name : 'Sender' }}
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
