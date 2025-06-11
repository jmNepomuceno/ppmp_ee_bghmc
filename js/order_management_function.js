let modal_viewRequest = new bootstrap.Modal(document.getElementById('modal-view-request'));
let modal_notif = new bootstrap.Modal(document.getElementById('modal-notif'));
let modal_imiss_update = new bootstrap.Modal(document.getElementById('modal-view-update'));
// modal_imiss_update.show()
// modal_notif.show()

let selectedRequest_data = {}
let incoming_orderID_clicked = ""
let history_update_response = {}
let orig_quantity_before_update_user = []
let remarks_arr = []

const notifID_session = sessionStorage.getItem("highlightOrderID");
console.log(notifID_session)

const dataTable = (filter) =>{
    try {
        $.ajax({
            url: '../php/fetch_orderReqUser.php',
            method: "POST",
            data : {
                filter : filter
            },
            dataType : "json",
            success: function(response) {
                try {
                    let dataSet = [];
                    
                    for(let i = 0; i < response.length; i++){
                        let imiss_update = (response[i].history_update.length > 0) ? "VIEW" : "NONE"
                        let status_style = "";
                        if(response[i].order_status === "Pending"){
                            status_style = "background:#ffcc33;"
                        }else if(response[i].order_status === "Approved"){
                            status_style = "background:#5bd778";
                        }else if(response[i].order_status === "Cancelled" || response[i].order_status === "Rejected"){
                            status_style = "background:#e05260;"
                        }

                        dataSet.push([
                            `<span>${i + 1}</span>`,
                            `<span>${response[i].order_date}</span>`,
                            `<span class="request-section-span" id="${response[i].order_by_sectionName}" style="${status_style}">${response[i].order_status}</span>`,
                            `<span class='view-request-span' id='${response[i].orderID}'> VIEW </span>`,
                            `<span class='imiss-update-span'> ${imiss_update} </span>`,
                        ])

                        history_update_response[i] = response[i].history_update
                        remarks_arr.push(response[i].order_remarks)
                    }  

                    if ($.fn.DataTable.isDataTable('#cart-table')) {
                        $('#cart-table').DataTable().destroy();
                        $('#cart-table tbody').empty(); // Clear previous table body
                        
                    }

                    $('#cart-table').DataTable({
                        data: dataSet,
                        columns: [
                            { title: "REQUEST NO.", data:0 },
                            { title: "DATE", data:1 },
                            { title: "STATUS", data:2 },
                            { title: "REQUEST ITEM", data:3 },
                            { title: "IMISS UPDATE", data:4 },
                        ],
                        // "paging": false,
                        // "info": false,
                        // "ordering": false,
                        // "stripeClasses": [],
                        // "search": false
                    });

                     // notification highlighter
                     setTimeout(() => {
                        if (notifID_session) {
                            // Find the matching row and highlight it
                            $('#cart-table tbody tr').each(function() {
                                const rowText = $(this).find('.view-request-span').attr('id'); // get orderID in the row
                                console.log(rowText , notifID_session)
                                if (rowText === notifID_session) {
                                    $(this).css({
                                        backgroundColor: '#ffeaa7',
                                        transition: 'background-color 0.5s ease'
                                    });
                                    // Optionally scroll into view
                                    this.scrollIntoView({ behavior: "smooth", block: "center" });
                                }
                            });
                    
                            // Clear it after use
                            sessionStorage.removeItem("highlightOrderID");
                        }
                    }, 300); // Delay to ensure table is fully rendered

                } catch (innerError) {
                    console.error("Error processing response:", innerError);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error);
            }
        });
    } catch (ajaxError) {
        console.error("Unexpected error occurred:", ajaxError);
    }
   
}

const dataTable_viewRequest = (orderID, sectionName) =>{
    $.ajax({
        url: '../php/view_orderRequest.php',
        method: "POST",
        data : {
            view_orderID : orderID
        },
        dataType : 'JSON',
        success: function(response) {
            
            if(response != false){
                let order_status = ""

                for (let i = 0; i < response.order_item.length; i++) {
                    if (response.order_item[i].itemName.length > 75) {
                        response.order_item[i].itemName = response.order_item[i].itemName.substring(0, 35) + "...";
                    }
                }

                if ($.fn.DataTable.isDataTable('#cart-table-request')) {
                    $('#cart-table-request').DataTable().destroy();
                    $('#cart-table-request tbody').empty(); // Clear previous table body
                }

                // populate the data set
                let dataSet = [], total_subtotal = 0;

                for (let i = 0; i < response.order_item.length; i++) {
                // for (let i = response.order_item.length - 1; i >= 0; i--) {
                    let item = response.order_item[i];

                    // Remove "P" and commas, then convert to a float
                    let cleanPrice = parseFloat(item.itemPrice.replace(/P|\s|,/g, '')) * parseInt(item.itemQuantity);
                    let formattedPrice = "P " + cleanPrice.toLocaleString();
                    total_subtotal += cleanPrice;

                    orig_quantity_before_update_user.push(parseInt(item.itemQuantity))
                    let rowData = [
                        // `<img src="../images/${item.itemImage}" alt="item-image" class="img-fluid" style="width: 100px; height: 100px;" />`,
                        // item.itemID,
                        `<span class='item-id-span'>${item.itemID}</span>`,
                        `<span class='item-image-span'><img src="${item.itemImage}" alt="item-1-img"/></span>`,
                        `<span class='item-name-span'>${item.itemName}</span>`,
                        `<span class='item-price-span'>${"P " + parseFloat(item.itemPrice.replace(/P|\s|,/g, '')).toLocaleString()}</span>`,
                        `<input class='item-quantity-span' type='number' value='${item.itemQuantity}' />`,
                        `<span class="item-subtotal-span">${formattedPrice}</span>`
                    ];
                    
                    if (response.order_status === "Pending") {
                        order_status = "Pending"
                        rowData.push(`
                            <div class="action-btn-div"> 
                                <button class='btn btn-danger remove-item-btn'>Cancel</button>
                                <button class='btn btn-success update-item-btn'>Update</button>
                            </div>
                        `);
                    }
                    else if (response.order_status === "Cancelled") {
                        order_status = "Cancelled"
                        rowData.push(`
                            <span class="item-subtotal-span">Cancelled</span>
                        `);
                    }
                    
                    dataSet.push(rowData);
                }
                

                dataSet.push([
                    "<span style='visibility:hidden;'>asdf</span> ",
                    "<span style='visibility:hidden;'>asdf</span> ",
                    "<span style='visibility:hidden;'>asdf</span> ",
                    "<span style='visibility:hidden;'>asdf</span> ",
                    `<span class="total-subtotal-span">P ${total_subtotal.toLocaleString()}</span>`,
                    "<span style='visibility:hidden;'>asdf</span> ",
                    "<span style='visibility:hidden;'>asdf</span> ",
                ]);



                let table_column = [
                    { title: "ITEM ID", data:0},
                    { title: "IMAGE", data: 1 },
                    { title: "PRODUCT", data: 2 },
                    { title: "PRICE", data: 3 },
                    { title: "QUANTITY", data: 4 },
                    { title: "SUBTOTAL", data: 5 },
                ];
                
                let columnDefs = [
                    { targets: 0, createdCell: function(td) { $(td).addClass('item-id-td'); } },
                    { targets: 1, createdCell: function(td) { $(td).addClass('item-image-td'); } },
                    { targets: 2, createdCell: function(td) { $(td).addClass('item-name-td'); } },
                    { targets: 3, createdCell: function(td) { $(td).addClass('item-price-td'); } },
                    { targets: 4, createdCell: function(td) { $(td).addClass('item-quantity-td'); } },
                    { targets: 5, createdCell: function(td) { $(td).addClass('item-subtotal-td'); } }
                ];
                
                // 🔹 Add "ACTION" column **only if** order_status is "Pending"
                if (order_status === "Pending") {
                    table_column.push({ title: "ACTION", data: 6 });
                    columnDefs.push({ targets: 6, createdCell: function(td) { $(td).addClass('action-btn-td'); } });
                    document.getElementById("action-header").style.display = "table-cell"; // Show ACTION header
                }
                else if (order_status === "Cancelled") {
                    table_column.push({ title: "ACTION", data: 6 });
                    columnDefs.push({ targets: 6, createdCell: function(td) { $(td).addClass('action-btn-td'); } });
                    document.getElementById("action-header").style.display = "table-cell"; // Show ACTION header
                }
                
                $('#cart-table-request').DataTable({
                    data: dataSet,
                    columns: table_column,
                    columnDefs: columnDefs
                    // "paging": false,
                    // "info": false,
                    // "ordering": false,
                    // "stripeClasses": []
                });
                
                $('#modal-view-request #modal-title-incoming').text(`${sectionName} Request`)
            }
            else{
                if ($.fn.DataTable.isDataTable('#cart-table-request')) {
                    $('#cart-table-request').DataTable().destroy();
                    $('#cart-table-request tbody').empty(); // Clear previous table body
                    dataTable("Pending")
                }
            }
            
        }
    });
}

const dataTable_viewUpdate = (orderID, sectionName , history_update, remarks) =>{
    let dataSet = []
    for(let i = 0; i < history_update.length; i++){
        
        let differences = [];
        let updatedMap = new Map(history_update[i].updatedOrder.map(item => [String(item.itemID), item]));

        // 🔹 Check for modified & removed items
        history_update[i].previousOrder.forEach(prevItem => {
            let updatedItem = updatedMap.get(String(prevItem.itemID));

            if (updatedItem) {
                let prevQty = prevItem.itemQuantity;
                let updatedQty = updatedItem.itemQuantity;

                if (prevQty !== updatedQty) {
                    differences.push({
                        itemID: prevItem.itemID,
                        changeType: "UPDATED",
                        prevQuantity: prevQty,
                        updatedQuantity: updatedQty,
                        itemName: prevItem.itemName,
                        itemPrice : prevItem.itemPrice,
                        itemImage : prevItem.itemImage,
                    });
                }
            } else {
                differences.push({
                    itemID: prevItem.itemID,
                    changeType: "REMOVED",
                    prevQuantity: prevItem.itemQuantity,
                    itemName: prevItem.itemName,
                    itemPrice : prevItem.itemPrice,
                    itemImage : prevItem.itemImage,
                });
            }
        });

        // 🔹 Check for newly added items
        let previousMap = new Map(history_update[i].previousOrder.map(item => [String(item.itemID), item]));

        history_update[i].updatedOrder.forEach(updatedItem => {
            if (!previousMap.has(String(updatedItem.itemID))) {
                differences.push({
                    itemID: updatedItem.itemID,
                    changeType: "ADDED",
                    updatedQuantity: updatedItem.itemQuantity,
                    itemName: updatedItem.itemName,
                    itemImage : prevItem.itemImage
                });
            }
        });


        if ($.fn.DataTable.isDataTable('#cart-table-update')) {
            let table = $('#cart-table-update').DataTable();
            table.clear().destroy(); // Clear data and destroy instance
            $('#cart-table-update tbody').empty(); // Remove old table rows
        }

        let style = ""
        if(differences[0].changeType === 'UPDATED'){
            style = "padding:10px;border-radius:7px;background:#ffc108;"
        }else{
            style = "padding:10px;border-radius:7px;background:#db3545;color:white;"
        }

        let cleanPrice = parseFloat(differences[0].itemPrice.replace(/P|\s|,/g, '')) * (differences[0].updatedQuantity ? differences[0].updatedQuantity : 0);
        let formattedPrice = "P " + cleanPrice.toLocaleString();
        dataSet.push([
            `<span class='item-id-span' style='display:none;'>${differences[0].itemID}</span>`,
            `<span class='item-last-update-span'>${history_update[i].dateEdited}</span>`,
            `<span class='item-image-span'> <img src='${differences[0].itemImage}' alt='item-image' /> </span>`, 
            `<span class='item-name-span'>${differences[0].itemName}</span>`, 
            `<span class='item-price-span'>${differences[0].itemPrice}</span>`,
            `<input class='item-quantity-span' type='number' value='${differences[0].prevQuantity}' disabled />`,
            `<input class='item-quantity-span' type='number' value='${(differences[0].updatedQuantity) ? differences[0].updatedQuantity : 0}' disabled />`,
            `<span class="item-subtotal-span"> ${formattedPrice}</span>`, 
            `<span class="item-subtotal-span" style='${style}'> ${differences[0].changeType}</span>`, 
        ]);

    }

    $('#cart-table-update').DataTable({
        destroy: true, // Ensure reinitialization is allowed
        data: dataSet,
        columns: [
            { title: "ITEM ID", data:0, visible: false },
            { title: "LAST MODIFIED", data:1 },
            { title: "IMAGE", data:2 },
            { title: "PRODUCT", data:3 },
            { title: "PRICE", data:4 },
            { title: "QUANTITY BEFORE", data:5 },
            { title: "QUANTITY UPDATED", data:6 },
            { title: "SUBTOTAL", data:7 },
            { title: "ACTION", data:8 },
        ],
        columnDefs: [
            { targets: 0, createdCell: function(td) { $(td).addClass('item-id-td'); } },
            { targets: 1, createdCell: function(td) { $(td).addClass('item-name-td'); } },
            { targets: 2, createdCell: function(td) { $(td).addClass('item-image-td'); } },
            { targets: 3, createdCell: function(td) { $(td).addClass('item-name-td'); } },
            { targets: 4, createdCell: function(td) { $(td).addClass('item-price-td'); } },
            { targets: 5, createdCell: function(td) { $(td).addClass('item-quantity-td'); } },
            { targets: 6, createdCell: function(td) { $(td).addClass('item-subtotal-td'); } },
            { targets: 7, createdCell: function(td) { $(td).addClass('action-btn-td'); } },
        ]


        // "paging": false,
        // "info": false,
        // "ordering": false,
        // "stripeClasses": []
    });
    // 
    $('#modal-view-update #modal-title-incoming').text(`${sectionName} Request`)
    $('#remark-textarea').val((remarks) ? remarks : "No Remarks")
}

const getQueryParam = (param) => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

const clearFilterStyleBtns = () => {
    for(let i = 0; i < $('.filter-buttons').length; i++){
        $('.filter-buttons').eq(i).css('background' , '#ba3a13')
        $('.filter-buttons').eq(i).css('opacity' , '0.5')
    }
}

document.addEventListener("websocketMessage", function(event) {
    let data = event.detail;

    if (data.action === "refreshImissUpdate") {
        dataTable("Pending")
    }
});


$(document).ready(function(){
    const notifStatus = getQueryParam("status");
    clearFilterStyleBtns()
    switch(notifStatus) {
        case "updated": 
            dataTable("Pending"); 
            $('#pending-btn').css('background' , '#ba3a13')
            $('#pending-btn').css('opacity' , '1')    
            break;
        case "approved": 
            dataTable("Approved"); 
            $('#approved-btn').css('background' , '#ba3a13')
            $('#approved-btn').css('opacity' , '1')    
            break;
        case "rejected": 
            dataTable("Rejected"); 
            $('#rejected-btn').css('background' , '#ba3a13')
            $('#rejected-btn').css('opacity' , '1')    
            break;
        case "cancelled": 
            dataTable("Cancelled"); 
            $('#cancelled-btn').css('background' , '#ba3a13')
            $('#cancelled-btn').css('opacity' , '1')    
            break;
        default : 
            dataTable("Pending"); 
            $('#pending-btn').css('background' , '#ba3a13')
            $('#pending-btn').css('opacity' , '1')    
            break;
    }

    $('#inventory-list-sub-div').click(function(){
        window.location.href = "../views/home.php";
    });

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

    $(document).off('click', '.view-request-span').on('click', '.view-request-span', function() {      
        const index = $('.view-request-span').index(this);
        const orderID = $('.view-request-span').eq(index).attr('id')
        const sectionName = $('.request-section-span').eq(index).attr('id')
        incoming_orderID_clicked = orderID
        modal_viewRequest.show(
            dataTable_viewRequest(orderID, sectionName)
        )
    });

    $(document).off('click', '.imiss-update-span').on('click', '.imiss-update-span', function() {      
        const index = $('.imiss-update-span').index(this);

        const orderID = $('.view-request-span').eq(index).attr('id')
        const sectionName = $('.request-section-span').eq(index).attr('id')
        incoming_orderID_clicked = orderID
        modal_imiss_update.show(
            dataTable_viewUpdate(orderID, sectionName, history_update_response[index], remarks_arr[index])  
        )
    });

    // approve-request-btn
    $(document).off('click', '#approve-request-btn').on('click', '#approve-request-btn', function() {    
        // try {
        //     $.ajax({
        //         url: '../php/approve_request.php',
        //         data : selectedRequest_data,
        //         method: "POST",
        //         dataType : "json",
        //         success: function(response) {
        //             console.log(response)

        //             let dataSet = [];
        //             for(let i = 0; i < response.length; i++){
        //                 dataSet.push([
        //                     `<span>${i + 1}</span>`,
        //                     `<span>${response[i].order_by_name}</span>`,
        //                     `<span class="request-section-span" id='${response[i].order_by_sectionName}'>${response[i].order_by_sectionName}</span>`,
        //                     `<span class='view-request-span' id='${response[i].orderID}'> VIEW </span>`,
        //                 ])
        //             }

        //             if ($.fn.DataTable.isDataTable('#cart-table')) {
        //                 $('#cart-table').DataTable().destroy();
        //                 $('#cart-table tbody').empty(); // Clear previous table body
        //             }

        //             $('#cart-table').DataTable({
        //                 data: dataSet,
        //                 columns: [
        //                     { title: "REQUEST NO.", data:0 },
        //                     { title: "NAME", data:1 },
        //                     { title: "SECTION", data:2 },
        //                     { title: "REQUEST ITEM", data:3 },
        //                 ]
        //             })

        //             modal_viewRequest.hide()
        //             try {
        //             } catch (innerError) {
        //                 console.error("Error processing response:", innerError);
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error("AJAX request failed:", error);
        //         }
        //     });
        // } catch (ajaxError) {
        //     console.error("Unexpected error occurred:", ajaxError);
        // }
    }) 

    $(document).off('change', '.item-quantity-span').on('change', '.item-quantity-span', function() {        
        const index = $('.item-quantity-span').index(this);

        if (parseInt($(this).val()) < 1 || $(this).val() === '') {
            $(this).val(1); // Reset to 0 if negative
        }

        if($('.item-quantity-span').eq(index).val() != orig_quantity_before_update_user[index]){
            $('.update-item-btn').eq(index).css('opacity', '1');
            $('.update-item-btn').eq(index).css('pointer-events', 'auto');
        }else{
            $('.update-item-btn').eq(index).css('opacity', '0.3');
            $('.update-item-btn').eq(index).css('pointer-events', 'none');
        }
    });

    $(document).off('click', '.update-item-btn').on('click', '.update-item-btn', function() {        
        const index = $('.update-item-btn').index(this);
        
        try {
            $.ajax({
                url: '../php/update_pending_request.php',
                method: "POST",
                data: {
                    orderID : incoming_orderID_clicked,
                    itemID: $('.item-id-span').eq(index).text(),
                    itemQuantity: $('.item-quantity-span').eq(index).val(),
                    action : "update",
                    from : "user"
                },
                success: function(response) {
                    try {
                        $('#modal-notif .modal-content .modal-header .modal-title-incoming').text("Successfully Updated")
                        modal_notif.show()
                        // console.log(response)
                        // const orderID = $('.view-request-span').eq(index).attr('id')
                        // const sectionName = $('.request-section-span').eq(index).attr('id')
                        // dataTable_viewRequest(orderID, sectionName)

                    } catch (innerError) {
                        console.error("Error processing response:", innerError);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed:", error);
                }
            });
        } catch (ajaxError) {
            console.error("Unexpected error occurred:", ajaxError);
        }
    });

    $(document).off('click', '.remove-item-btn').on('click', '.remove-item-btn', function() {        
        const index = $('.remove-item-btn').index(this);
        try {
            $.ajax({
                url: '../php/update_pending_request.php',
                method: "POST",
                data: {
                    orderID : incoming_orderID_clicked,
                    itemID: $('.item-id-span').eq(index).text(),
                    itemQuantity: $('.item-quantity-span').eq(index).val(),
                    action : "delete",
                    from : "user"
                },
                success: function(response) {
                    try {
                        const orderID = incoming_orderID_clicked
                        const sectionName = $('.request-section-span').eq(index).attr('id')
                        dataTable_viewRequest(orderID, sectionName)
                        dataTable("Pending")

                        $('#modal-notif .modal-content .modal-header .modal-title-incoming').text("Successfully Cancelled")
                        modal_notif.show()
                    } catch (innerError) {
                        console.error("Error processing response:", innerError);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed:", error);
                }
            });
        } catch (ajaxError) {
            console.error("Unexpected error occurred:", ajaxError);
        }
    });

    // close-modal-btn-incoming
    // $(document).off('click', '#close-modal-btn-incoming').on('click', '#close-modal-btn-incoming', function() {        
    //     dataTable("Pending")
    // });

    $(document).off('click', '#pending-btn').on('click', '#pending-btn', function() {
        clearFilterStyleBtns()

        $('#pending-btn').css('background' , '#ba3a13')
        $('#pending-btn').css('opacity' , '1')

        dataTable("Pending")
    });

    $(document).off('click', '#approved-btn').on('click', '#approved-btn', function() {      
        clearFilterStyleBtns()

        $('#approved-btn').css('background' , '#ba3a13')
        $('#approved-btn').css('opacity' , '1')

        dataTable("Approved")
    });

    $(document).off('click', '#rejected-btn').on('click', '#rejected-btn', function() {  
        clearFilterStyleBtns()

        $('#rejected-btn').css('background' , '#ba3a13')
        $('#rejected-btn').css('opacity' , '1')      
        dataTable("Rejected")
    });

    $(document).off('click', '#cancelled-btn').on('click', '#cancelled-btn', function() {    
        clearFilterStyleBtns()

        $('#cancelled-btn').css('background' , '#ba3a13')
        $('#cancelled-btn').css('opacity' , '1')    
        dataTable("Cancelled")
    });

    $(document).off('click', '#all-btn').on('click', '#all-btn', function() {      
        clearFilterStyleBtns()

        $('#all-btn').css('background' , '#ba3a13')
        $('#all-btn').css('opacity' , '1')      
        dataTable("All")
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
})