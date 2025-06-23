let modal_notif = new bootstrap.Modal(document.getElementById('modal-notif'));
let viewModal = new bootstrap.Modal(document.getElementById('modal-view-request'));

const dataTable = () => {
    $.ajax({
        url: '../php/fetch_adminSide_request.php',
        method: "POST",
        data: {
            filter: "all" // optional filter, you can customize this in your PHP later
        },
        dataType: "json",
        success: function(response) {
            console.log(response)
            try {
                let dataSet = [];

                for (let i = 0; i < response.length; i++) {
                    let row = response[i];

                    dataSet.push([
                        `<span>${row.request_date}</span>`,
                        `<span class="requestor-info"> <b> ${row.request_userName ?? "Unknown"} </b> </span>`,
                        `<span class="requestor-info"> <b> ${row.request_userSection ?? "Unknown"} </b> </span>`,
                        `
                        <button class="btn btn-success view-btn" data-id="${row.itemReqID}">View</button>
                        `
                    ]);
                }

                if ($.fn.DataTable.isDataTable('#cart-table')) {
                    $('#cart-table').DataTable().destroy();
                    $('#cart-table tbody').empty();
                }

                $('#cart-table').DataTable({
                    data: dataSet,
                    columns: [
                        { title: "DATE REQUESTED" },
                        { title: "REQUESTOR NAME" },
                        { title: "REQUESTOR SECTION" },
                        { title: "ACTION" }
                    ],
                    columnDefs: [
                        { targets: 0, createdCell: td => $(td).addClass('item-date') },
                        { targets: 1, createdCell: td => $(td).addClass('item-user') },
                        { targets: 2, createdCell: td => $(td).addClass('item-section') },
                        { targets: 3, createdCell: td => $(td).addClass('item-actions') },
                    ]
                });

            } catch (innerError) {
                console.error("Error processing response:", innerError);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX request failed:", error);
        }
    });
};




$(document).ready(function(){
    dataTable()

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

    $(document).on('click', '.view-btn', function () {
        const reqID = $(this).data('id');
        console.log(reqID)
        $.ajax({
            url: '../php/fetch_itemRequest.php',
            method: 'POST',
            data: { itemReqID: reqID },
            dataType: 'json',
            success: function (data) {
                console.log(data)
                $('#view-item-name').text(data.request_itemName || 'N/A');
                $('#view-item-image').attr('src', '../' + (data.request_itemImage || 'source/request_image/default.jpg'));
                $('#view-item-link')
                    .attr('href', data.request_itemLink || '#')
                    .text(data.request_itemLink ? 'View Reference' : 'No Link');
                $('#view-item-reason').text(data.request_itemReason || 'N/A');

                // Show the modal
                viewModal.show();
            },
            error: function (err) {
                console.error("Error fetching item request:", err);
            }
        });
    });

    // close-viewModal-btn
    $(document).off('click', '.close-viewModal-btn').on('click', '.close-viewModal-btn', function() {
        viewModal.hide();
    });
})