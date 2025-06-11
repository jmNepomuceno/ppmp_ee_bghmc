const audio = new Audio('../source/sound/shopee.mp3'); // Load the notification sound
let modal_logout = new bootstrap.Modal(document.getElementById('modal-logout'));
let previousResponse = 0; // Store the previous count to prevent duplicate sounds

const fetchIncomingOrder = () => {
    $.ajax({
        url: '../php/fetch_incoming_order.php',
        method: "GET",
        success: function(response) {
            response = parseInt(response);

            if (response > 0) {
                $('#bell-notif').removeClass('hidden'); // Show bell notification
                // audio.play();
            } else {
                $('#bell-notif').addClass('hidden'); // Hide bell notification
            }

            previousResponse = response; // Update previous response count
        }
    });
};

// Run the function every 5 minutes (300000ms)
// setInterval(fetchIncomingOrder, 300000);

// Run immediately on page load
fetchIncomingOrder();

document.addEventListener("websocketMessage", function(event) {
    let data = event.detail;

    if (data.action === "refreshSideBar") {
        fetchIncomingOrder()
    }
});


