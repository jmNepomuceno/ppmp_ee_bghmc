let main_data = null;

const fetchIncomingOrder_navBar = () => {
    $.ajax({
        url: '../php/fetch_incoming_notif.php',
        method: "GET",
        dataType: "json",
        success: function(response) {
            // Inject HTML into container
            $('.navbar-notif-div').html(response.html);
    
            // Access raw data
            main_data = response.data;
            console.log("🔍 Raw notification data:", main_data);
    
            const notifCount = response.count;
    
            if (notifCount > 0) {
                $('#navbar-bell').css('opacity', '1');
                $('#navbar-span-val').text(notifCount);
            } else {
                $('#navbar-bell').css('opacity', '0.5');
                $('#navbar-span-val').text('');
            }
        },
        error: function(xhr, status, error) {
            console.error("❌ Error fetching notifications:", error);
        }
    });
};

// Run the function every 5 minutes (300000ms)
// setInterval(fetchIncomingOrder, 300000);

// Run immediately on page load
fetchIncomingOrder_navBar();


document.addEventListener("websocketMessage", function(event) {
    let data = event.detail;

    if (data.action === "refreshNavbar") {
        fetchIncomingOrder_navBar()
    }
});

$(document).ready(function(){
    // $(document).off('click', '.update-item-btn').on('click', '.update-item-btn', function() {        

    $(document).off('click', '#navbar-bell').on('click', '#navbar-bell', function() {  
        if($('.navbar-notif-div').css('display') == 'flex'){
            $('.navbar-notif-div').css('display', 'none');
        }else{
            $('.navbar-notif-div').css('display', 'flex');
        }

        fetchIncomingOrder_navBar()
    });

    $(document).off('click', '.navbar-notif-row').on('click', '.navbar-notif-row', function() {  
        let $row = $(this);
        let index = $row.index();
        let rowData = main_data[index];
    
        console.log(rowData);
    
        // Store orderID in sessionStorage
        sessionStorage.setItem("highlightOrderID", rowData.orderID);
    
        $.ajax({
            url: '../php/update_notification.php',
            method: "POST",
            data : {notifID : rowData['notifID']},
            success: function(response) {
                if(rowData['notifReceiver'] === 'admin'){
                    if(rowData['notifStatus'] === 'incoming_request' || 
                       rowData['notifStatus'] === 'updated' || 
                       rowData['notifStatus'] === 'cancelled'){
                        window.location.href = "../views/incoming_order.php?status=" + rowData.notifStatus;
                    }
                } else {
                    if(['updated', 'rejected', 'approved', 'cancelled'].includes(rowData['notifStatus'])){
                        window.location.href = "../views/order_management.php?status=" + rowData.notifStatus;
                    }
                }
    
                fetchIncomingOrder_navBar();
            },
            error: function(xhr, status, error) {
                console.error("❌ Error fetching notifications:", error);
            }
        });
    
        // Styling effect
        $row.removeClass('unread').addClass('read');
        $row.css({ transform: 'scale(0.97)' });
        setTimeout(() => {
            $row.css({ transform: 'scale(1)' });
        }, 150);
    });
    
})