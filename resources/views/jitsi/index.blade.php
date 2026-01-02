@extends('layouts.app')

@section('content')
    <div class="container mb-3" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12 text-center">
                <button id="btn-start-recording" class="btn btn-danger" onclick="startRecording()">
                    <i class="fa fa-circle"></i> Start Recording
                </button>
                <button id="btn-stop-recording" class="btn btn-secondary" onclick="stopRecording()" style="display: none;">
                    <i class="fa fa-stop"></i> Stop Recording
                </button>
                <span id="recording-status" class="ml-3 font-weight-bold"></span>
            </div>
        </div>
    </div>

    <div id="jitsi-container"></div>

    <script src="https://meet.mychoicetutor.com/external_api.js"></script>
    <script src="{{ asset('js/jitsi.js') }}"></script>
@endsection
