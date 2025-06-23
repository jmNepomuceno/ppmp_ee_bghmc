let modal_notif = new bootstrap.Modal(document.getElementById('modal-notif'));


const fetch_userRequest = () =>{
    $.ajax({
        url: '../php/fetch_userSide_request.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            console.log(data)
            let html = '';

            if (data.length === 0) {
                html = `<div class="request-entry">No requests found.</div>`;
            } else {
                data.forEach(req => {
                    html += `
                        <div class="request-entry">
                            <div class="request-date">${req.formattedDate}</div>
                            <div class="request-name">${req.request_itemName}</div>
                            <div class="request-timeago">${req.timeAgo}</div>
                        </div>
                    `;
                });
            }

            $('#user-request-container').html(html);
        },
        error: function (err) {
            console.error("Failed to fetch user requests:", err);
        }
    });
}

$(document).ready(function(){
    fetch_userRequest()

    $('#logout-btn').click(function(){
        modal_logout.show()
        $(document).off('click', '#yes-modal-btn-logout').on('click', '#yes-modal-btn-logout', function() {
            $.ajax({
                url: '../php/logout.php',
                method: "GET",
                
                success: function(response) {
                    window.location.href = response;
                }
            });
        })
    });

    $(document).off('click', '#burger-icon').on('click', '#burger-icon', function() {
        if($('#burger-icon').css('color') != 'rgb(255, 85, 33)'){
            $('body .left-container').css('display', 'none');
            $('#burger-icon').css('color', '#ff5521');
        }else{
            $('body .left-container').css('display', 'flex');
            $('#burger-icon').css('color', 'white');
        }
    });

    $('#item-order-form').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '../php/add_itemRequest.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                // console.log(response)
                if (response.status === 'success') {
                    $('#modal-notif #modal-title-incoming').text("Request submitted successfully.");
                    $('#modal-notif #modal-body-incoming').text("Please wait up to 24 hours for the IMISS staff to review and add your requested item to the list.");
                    modal_notif.show();

                    // Reset form
                    // $('#item-request-form')[0].reset();
                    fetch_userRequest()
                } else {
                    $('#modal-notif #modal-title-incoming').text("Submission failed.");
                    modal_notif.show();
                }
            },
            error: function(err) {
                console.error("AJAX Error: ", err);
                $('#modal-notif #modal-title-incoming').text("An error occurred.");
                modal_notif.show();
            }
        });
    });

})