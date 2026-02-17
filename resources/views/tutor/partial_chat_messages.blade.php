<div class="position-relative" id="chatbox">
    <div class="chat-messages p-4">

        @if (empty($messages))
            <div class="chat-message-center pb-4">

                Please select anyone from the list to start chat
            </div>
        @else
            @foreach ($messages as $message)
                @if ($message->from_id === session('userid')->id && $message->from_role_id === 2)
                    <div class="chat-message-right pb-4">
                        <div style="margin-right: 12px;">
                            @if (empty($tutorProfilePic))
                                <img src="{{ asset('images/tutors/profilepics/no-img.jpg') }}" class="rounded-circle mr-1"
                                    width="40" height="40">
                            @else
                                <img src="{{ asset('images/tutors/profilepics/' . $tutorProfilePic) }}"
                                    class="rounded-circle mr-1" width="40" height="40">
                            @endif

                            <div class="text-muted small text-nowrap mt-2">
                                {{ $message->created_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                            </div>
                        </div>
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 shadow-sm"
                            style="max-width: 80%; border: 1px solid #e9ecef;">
                            <div class="small text-muted font-weight-bold mb-1">You</div>
                            <div class="text-dark">
                                {{ $message->body }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="chat-message-left pb-4">
                        <div style="margin-right: 12px;">
                            @if (empty($header->profile_pic))
                                <img src="{{ asset('images/students/profilepics/no-img.jpg') }}"
                                    class="rounded-circle mr-1" width="40" height="40">
                            @else
                                <img src="{{ asset('images/students/profilepics/' . $header->profile_pic) }}"
                                    class="rounded-circle mr-1" width="40" height="40">
                            @endif
                            <div class="text-muted small text-nowrap mt-2">
                                {{ $message->created_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }}</div>
                        </div>

                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 shadow-sm"
                            style="max-width: 80%; border: 1px solid #e9ecef;">
                            <div class="small text-muted font-weight-bold mb-1">{{ $header->name }}</div>
                            <div class="text-dark">
                                {{ $message->body }}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

    </div>
</div>
