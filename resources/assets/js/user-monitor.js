const WebSocket = require('ws');

const ws = new WebSocket('ws://www.host.com/path', {
    perMessageDeflate: false
});

ws.onopen = function() {
    console.log('[open]', ws.headers);
    ws.send('mic check');
};

ws.onclose = function(close) {
    console.log('[close]', close.code, close.reason);
};

ws.onerror = function(error) {
    console.log('[error]', error.message);
};

ws.onmessage = function(message) {
    console.log('[message]', message.data);
};