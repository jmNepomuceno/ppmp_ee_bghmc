let modal_addItem = new bootstrap.Modal(document.getElementById('modal-add-item'));
let modal_notif = new bootstrap.Modal(document.getElementById('modal-notif'));
// var item_data = JSON.parse(document.getElementById('item-data').textContent);

// $('#modal-add-item').on('hidden.bs.modal', function () {
//     modal_notif.show();
// });

const dataTable = () =>{
    $.ajax({
        url: '../php/checkCurrentCart.php',
        method: "POST",
        dataType : 'JSON',
        success: function(response) {

            // Truncate long product names
            for (let i = 0; i < response.cart.length; i++) {
                if (response.cart[i]['itemName'].length > 75) {
                    response.cart[i]['itemName'] = response.cart[i]['itemName'].substring(0, 35) + "...";
                }
            }

            // comnspole.log()

            if ($.fn.DataTable.isDataTable('#cart-table')) {
                $('#cart-table').DataTable().destroy();
                $('#cart-table tbody').empty(); // Clear previous table body
            }

            // populate the data set
            let dataSet = [], total_subtotal = 0;
            for (let i = 0; i <  response.cart.length; i++) {
                let item =  response.cart[i];

                // Remove "P" and commas, then convert to a float
                let cleanPrice = parseFloat(item.itemPrice.replace(/P|\s|,/g, '')) * parseInt(item.itemQuantity);
                let formattedPrice = "P " + cleanPrice.toLocaleString();
                total_subtotal += cleanPrice;

                dataSet.push([
                    `<span class='item-id-span' >${item.itemID}</span>`,
                    `<span class='item-image-span'><img src="${item.itemImage}" alt="item-1-img"/></span>`,
                    `<span class='item-name-span'>${item.itemName}</span>`,
                    `<span class='item-price-span'>${ "P " + parseFloat(item.itemPrice.replace(/P|\s|,/g, '')).toLocaleString()}</span>`,
                    `<input class='item-quantity-span' type='number' value='${item.itemQuantity}' />`,
                    `<span class="item-subtotal-span">${formattedPrice}</span>`, 
                    `<div class="action-btn-div"> 
                        <button class='btn btn-danger remove-item-btn'>Remove</button>
                        <button class='btn btn-success update-item-btn'>Update</button>
                    </div>`
                ]);
            }
            

            dataSet.push([
                "<span style='visibility:hidden;'>asdf</span> ",
                "<span style='visibility:hidden;'>asdf</span> ",
                "<span style='visibility:hidden;'>asdf</span> ",
                "<span style='visibility:hidden;'>asdf</span> ",
                "<span style='visibility:hidden;'>asdf</span> ",
                `<span class="total-subtotal-span">P ${total_subtotal.toLocaleString()}</span>`,
                "",
                ""
            ]);

            $('#cart-table').DataTable({
                data: dataSet,
                columns: [
                    { title: "ITEM ID", data:0},
                    { title: "IMAGE", data:1 },
                    { title: "PRODUCT", data:2 },
                    { title: "PRICE", data:3 },
                    { title: "QUANTITY", data:4 },
                    { title: "SUBTOTAL", data:5 },
                    { title: "ACTION", data:6 },
                ],
                columnDefs: [
                    { targets: 0, createdCell: function(td) { $(td).addClass('item-id-td'); } },
                    { targets: 1, createdCell: function(td) { $(td).addClass('item-image-td'); } },
                    { targets: 2, createdCell: function(td) { $(td).addClass('item-name-td'); } },
                    { targets: 3, createdCell: function(td) { $(td).addClass('item-price-td'); } },
                    { targets: 4, createdCell: function(td) { $(td).addClass('item-quantity-td'); } },
                    { targets: 5, createdCell: function(td) { $(td).addClass('item-subtotal-td'); } },
                    { targets: 6, createdCell: function(td) { $(td).addClass('action-btn-td'); } }
                ],
                "paging": false,
                "info": false,
                "ordering": false,
                "stripeClasses": []
            });
        }
    });
}


const pagination = () => {
    let currentPage = 1;

    function showPage(page) {
        let items = $(".item-tile").not(".hidden-item"); // Get only visible items
        totalPages = Math.ceil(items.length / itemsPerPage);
        
        $(".item-tile").hide(); // Hide all items
        let start = (page - 1) * itemsPerPage;
        let end = start + itemsPerPage;

        items.slice(start, end).show(); // Show only paginated items
        updatePagination(page, totalPages);
    }

    function updatePagination(page, totalPages) {
        let paginationHTML = "";
        let maxVisiblePages = 5;

        $("#prevPage").prop("disabled", page === 1);
        $("#nextPage").prop("disabled", page === totalPages);

        // Always show the first page
        if (page > 3) {
            paginationHTML += `<button class="pagination-btn" data-page="1">1</button> ... `;
        }

        // Generate page numbers dynamically
        let startPage = Math.max(1, page - 2);
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `<button class="pagination-btn ${i === page ? 'active-page' : ''}" data-page="${i}">${i}</button> `;
        }

        // Always show the last page
        if (page < totalPages - 2) {
            paginationHTML += ` ... <button class="pagination-btn" data-page="${totalPages}">${totalPages}</button>`;
        }

        $("#pagination-numbers").html(paginationHTML);
    }

    // Initial page load
    showPage(currentPage);

    // Pagination button events
    $("#prevPage").click(() => {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });

    $("#nextPage").click(() => {
        let totalPages = Math.ceil($(".item-tile").not(".hidden-item").length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    });

    $(document).on("click", ".pagination-btn", function() {
        currentPage = parseInt($(this).attr("data-page"));
        showPage(currentPage);
    });

    return { showPage }; // Return function to use in search
};

let paginationInstance = pagination();

$(document).ready(function(){
    // dataTable()

    $('#search-btn').on('click', function() {
        let searchInput = $('#search-input').val().toLowerCase();
    
        if (searchInput !== "") {
            let instance = 0;
            $('.item-tile').each(function() {
                var itemName = $(this).find('.item-description').text().toLowerCase();
    
                // If the item name includes the search input, show it; otherwise, mark it as hidden
                if (itemName.includes(searchInput)) {
                    $(this).removeClass("hidden-item");
                } else {
                    $(this).addClass("hidden-item");
                    instance++;
                }
            });

            if(instance === 171){
                $('#modal-notif #modal-title-incoming').text("No item found.")
                modal_notif.show()
            }

        } else {
            $(".item-tile").removeClass("hidden-item"); // Show all items if search is empty
        }
    
        paginationInstance.showPage(1); // Reset pagination after search
    });

    $('#search-input').on('keydown', function(e) {
        if (e.key === "Enter") {
            $('#search-btn').click();
        }
    });

    // if user click backspace and the value of the search-input is 0, show all items
    $('#search-input').on('keyup', function(e) {
        if (e.key === "Backspace" && $(this).val().length === 0) {
            $(".item-tile").removeClass("hidden-item");
            paginationInstance.showPage(1);
        }
    })
    
    

    // AJAX Submit on Add button
    $('#add-item-btn').click(function (event) {
        event.preventDefault();

        const form = $('#add-item-form')[0];
        const formData = new FormData(form);

        $.ajax({
            url: '../php/addNewItem_mng.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                modal_addItem.hide()
                $('.modal-backdrop').remove(); 
                $('body').removeClass('modal-open');

                $('#modal-notif #modal-title-incoming').text("Order Request Sent.")
                modal_notif.show()
            },
            error: function (err) {
                console.error("Error:", err);
                $('#modal-notif #modal-title-incoming').text("Something went wrong.");
                modalAddItem.hide(); // still hide so we show notif
            }
        });
    });

    $('#add-new-item-btn').click(function () {
        $('#modal-add-item .modal-title-incoming').text("New Item");
        $('#add-item-btn').text("ADD NEW ITEM");
        $('#add-item-form')[0].reset(); 
    });

    $(document).off('click', '.edit-item-btn').on('click', '.edit-item-btn', function() {        
        const index = $('.edit-item-btn').index(this);
        console.log(index)

        $('#modal-add-item .modal-title-incoming').text("Edit Item");
        // console.log(item_data)
        // $('#item-id').val(existingItem.id);
        // $('#item-name').val(existingItem.name);
        // $('#item-price').val(existingItem.price);
        // $('#item-specs').val(existingItem.specs);


        modal_addItem.show()
        
    });

})