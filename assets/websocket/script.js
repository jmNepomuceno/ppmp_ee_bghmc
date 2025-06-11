// Only create the WebSocket if it hasn't been created yet
if (!window.socket) {
    window.socket = new WebSocket("ws://192.168.42.222:8081");

    window.socket.onopen = function() {
        // console.log("WebSocket Connected (Global)");
    };

    window.socket.onmessage = function(event) {
        // console.log("Global WebSocket received:", event.data);
        let data;

        try {
            data = JSON.parse(event.data);
        } catch (e) {
            // console.error("Invalid JSON:", event.data);
            return;
        }

        // Dispatch a custom event for other scripts to listen to
        document.dispatchEvent(new CustomEvent("websocketMessage", { detail: data }));
    };

    window.socket.onclose = function() {
        // console.log("WebSocket Closed");
    };

    window.socket.onerror = function(event) {
        // console.error("WebSocket Error:", event);
    };
}
