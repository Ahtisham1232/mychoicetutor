<!DOCTYPE html>
<html>
<head>
    <title>Test Jibri Recording</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/jibri-recording.js') }}"></script>
</head>
<body>
    <h1>Jibri Recording Test</h1>
    
    <div id="status">Checking Jibri status...</div>
    
    <button id="startBtn" onclick="startRecording()" disabled>Start Recording</button>
    <button id="stopBtn" onclick="stopRecording()" disabled>Stop Recording</button>
    
    <div id="result"></div>
    
    <script>
        const manager = new JibriRecordingManager();
        
        // Check status on load
        manager.checkStatus().then(available => {
            document.getElementById('status').textContent = available ? 
                '✅ Jibri is available' : '❌ Jibri is not available';
            document.getElementById('startBtn').disabled = !available;
        });
        
        async function startRecording() {
            document.getElementById('startBtn').disabled = true;
            document.getElementById('result').textContent = 'Starting recording...';
            
            const result = await manager.startRecording('testroom');
            
            if (result.success) {
                document.getElementById('result').textContent = `✅ Recording started! Session: ${result.sessionId}`;
                document.getElementById('stopBtn').disabled = false;
            } else {
                document.getElementById('result').textContent = `❌ Error: ${result.error}`;
                document.getElementById('startBtn').disabled = false;
            }
        }
        
        async function stopRecording() {
            document.getElementById('stopBtn').disabled = true;
            document.getElementById('result').textContent = 'Stopping recording...';
            
            const result = await manager.stopRecording();
            
            if (result.success) {
                document.getElementById('result').textContent = '✅ Recording stopped! Check /var/www/html/mychoicetutor/public/recordings/';
                document.getElementById('startBtn').disabled = false;
            } else {
                document.getElementById('result').textContent = `❌ Error: ${result.error}`;
                document.getElementById('stopBtn').disabled = false;
            }
        }
    </script>
</body>
</html>
