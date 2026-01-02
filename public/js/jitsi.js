const domain = 'meet.mychoicetutor.com';
const options = {
    roomName: 'MyChoiceTutorMeeting-' + (new Date().getTime()),
    width: '100%',
    height: 600,
    parentNode: document.querySelector('#jitsi-container'),
    configOverwrite: {
        fileRecordingsEnabled: false,
        toolbarButtons: [
            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
            'fodeviceselection', 'hangup', 'profile', 'chat',
            'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
            'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
            'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
            'security'
        ],
    },
    interfaceConfigOverwrite: {
        TOOLBAR_BUTTONS: [
            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
            'fodeviceselection', 'hangup', 'profile', 'chat',
            'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
            'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
            'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
            'security'
        ],
    },
};

const api = new JitsiMeetExternalAPI(domain, options);

// Custom Recording Logic
let recordingSessionId = null;

async function startRecording() {
    const btnStart = document.getElementById('btn-start-recording');
    const btnStop = document.getElementById('btn-stop-recording');
    const status = document.getElementById('recording-status');
    
    btnStart.disabled = true;
    status.innerText = 'Starting recording...';
    
    try {
        const roomName = options.roomName;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch('/jibri/start-recording', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                room_jid: roomName
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            recordingSessionId = data.session_id;
            btnStart.style.display = 'none';
            btnStop.style.display = 'inline-block';
            status.innerText = 'Recording active';
            status.style.color = 'red';
        } else {
            alert('Failed to start recording: ' + (data.error || 'Unknown error'));
            btnStart.disabled = false;
            status.innerText = '';
        }
    } catch (error) {
        console.error('Recording error:', error);
        alert('Error starting recording');
        btnStart.disabled = false;
        status.innerText = '';
    }
}

async function stopRecording() {
    const btnStart = document.getElementById('btn-start-recording');
    const btnStop = document.getElementById('btn-stop-recording');
    const status = document.getElementById('recording-status');
    
    btnStop.disabled = true;
    status.innerText = 'Stopping recording...';
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch('/jibri/stop-recording', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            btnStop.style.display = 'none';
            btnStart.style.display = 'inline-block';
            btnStart.disabled = false;
            btnStop.disabled = false;
            status.innerText = 'Recording saved';
            status.style.color = 'green';
            
            setTimeout(() => {
                status.innerText = '';
            }, 3000);
        } else {
            alert('Failed to stop recording: ' + (data.error || 'Unknown error'));
            btnStop.disabled = false;
        }
    } catch (error) {
        console.error('Stop recording error:', error);
        alert('Error stopping recording');
        btnStop.disabled = false;
    }
}
