const WebSocket = require('ws');

const ws = new WebSocket('ws://test-app.local/user-monitor');

ws.on('open', function open() {
    ws.send('something');
});

ws.on('message', function incoming(data) {
    console.log(data);
});