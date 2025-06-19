let modal_addItem = new bootstrap.Modal(document.getElementById('modal-add-item'));
let modal_notif = new bootstrap.Modal(document.getElementById('modal-notif'));

let click_itemId = 0;
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
    console.log(item_data)

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

            if(instance === $('.tiles-div').length){
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

        const isEdit = $('#add-item-btn').text().trim() === 'EDIT ITEM';
        const form = $('#add-item-form')[0];
        const formData = new FormData(form);
        const endpoint = isEdit ? '../php/updateItem_mng.php' : '../php/addNewItem_mng.php';
        const successMessage = isEdit ? "Successfully Updated!" : "Successfully Added!";

        if (isEdit) {
            formData.append('item_id', click_itemId); // set globally from edit button
        }

        // ✅ Get the value from the textarea directly
        const rawSpecs = $('#item-specs').val().trim();
        formData.set('item_specs', rawSpecs); // no JSON conversion needed

        $.ajax({
            url: endpoint,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);

                if (response.status === 'success') {
                    const updatedInventory = response.updated_inventory;

                    $('.item-tile').remove(); // clear old

                    updatedInventory.forEach((item, index) => {
                        const formattedPrice = "P " + parseFloat(item.itemPrice).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        const imageSrc = item.itemImagePath && item.itemImagePath.trim() !== ''
                            ? `../${item.itemImagePath}`
                            : '../source/inventory_image/default.jpg';

                        const itemHTML = `
                            <div class="tiles-div item-tile" data-index="${index}">
                                <img class="item-img" src="${imageSrc}" alt="item-img" />
                                <p class="item-description">
                                    ${item.itemName}
                                    <span style="display:none" class="item-id">${item.itemID}</span>
                                </p>
                                <span class="item-price">${formattedPrice}</span>
                                <div class="function-div">
                                    <button class="edit-item-btn">Edit</button>
                                    <button class="delete-item-btn">Delete</button>
                                </div>
                            </div>
                        `;
                        $('.inventory-div').append(itemHTML);
                    });
                    paginationInstance = pagination(); 
                    $('#item-specs').val(""); // clear textarea
                }

                modal_addItem.hide();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                $('#modal-notif #modal-title-incoming').text(successMessage);
                modal_notif.show();
            },
            error: function (err) {
                console.error("Error:", err);
                $('#modal-notif #modal-title-incoming').text("Something went wrong.");
                modal_addItem.hide();
            }
        });
    });




    $('#add-new-item-btn').click(function () {
        $('#modal-add-item .modal-title-incoming').text("New Item");
        $('#add-item-btn').text("ADD NEW ITEM");
        $('#add-item-form')[0].reset(); 

        $('#img-preview-display').css('display' , 'none')
    });

    $('#add-item-form input, #add-item-form textarea').on('input change', function () {
        checkIfFormChanged();
    });

    $('#item-image').on('change', function () {
        checkIfFormChanged();
    });

    function checkIfFormChanged() {
        let changed = false;

        $('#add-item-form input[type="text"], #add-item-form input[type="number"], #add-item-form textarea').each(function () {
            const original = $(this).attr('data-original');
            const current = $(this).val();
            if (original !== current) {
                changed = true;
            }
        });

        // Image comparison
        const originalImg = $('#img-preview-display').attr('data-original');
        const currentImg = $('#img-preview-display').attr('src');
        if (originalImg !== currentImg) {
            changed = true;
        }

        $('#add-item-btn').prop('disabled', !changed);
        $('#add-item-btn').css('opacity', 1);

    }

    $(document).off('click', '.edit-item-btn').on('click', '.edit-item-btn', function () {
        const index = $('.edit-item-btn').index(this);
        const itemID = $('.item-id').eq(index).text().trim();
        click_itemId = itemID;

        $('#modal-add-item .modal-title-incoming').text("Edit Item");

        // Fetch item data via AJAX
        $.ajax({
            url: '../php/fetch_singleImage.php',
            method: 'POST',
            data: { item_id: itemID },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const item = response.data;

                    // Populate basic fields
                    $('#item-name').val(item.itemName).attr('data-original', item.itemName);
                    $('#item-price').val(item.itemPrice).attr('data-original', item.itemPrice);
                    $('#item-specs').val(item.itemSpecs).attr('data-original', item.itemSpecs); // plain text

                    // Show image preview
                    const imageSrc = item.itemImagePath
                        ? '../' + item.itemImagePath
                        : '../source/inventory_image/default.jpg';

                    $('#img-preview-display')
                        .attr('src', imageSrc)
                        .attr('data-original', imageSrc)
                        .show();

                    // Disable button initially
                    $('#add-item-btn').prop('disabled', true).css('opacity', 0.5);

                    // Set button label and show modal
                    $('#add-item-btn').text("EDIT ITEM");
                    modal_addItem.show();
                }
            },
            error: function () {
                alert("Failed to fetch item details.");
            }
        });
    });



    $(document).off('click', '.delete-item-btn').on('click', '.delete-item-btn', function () {
        const index = $('.delete-item-btn').index(this);
        const itemID = $('.item-id').eq(index).text().trim();

        if (!itemID) return;

        // Store the values on the Yes button
        $('#yes-modal-btn-notif').data('item-id', itemID);
        $('#yes-modal-btn-notif').data('item-index', index);

        // Update modal
        $('#modal-notif #modal-title-incoming').text("Are you sure you want to delete this item?");
        $('#modal-notif #yes-modal-btn-notif').css('display', 'block');
        $('#modal-notif #close-modal-btn-notif').text("No");

        modal_notif.show();
    });

    $(document).off('click', '#yes-modal-btn-notif').on('click', '#yes-modal-btn-notif', function () {
       $(document).off('click', '#yes-modal-btn-notif').on('click', '#yes-modal-btn-notif', function () {
        const itemID = $(this).data('item-id');
        const index = $(this).data('item-index');

        $.ajax({
            url: '../php/deleteItem_mng.php',
            type: 'POST',
            data: { itemID: itemID },
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('.item-tile').eq(index).remove();
                        modal_notif.hide();
                    } else {
                        alert('Delete failed: ' + res.message);
                    }
                } catch (e) {
                    console.error('Invalid JSON:', response);
                    alert('Unexpected error.');
                }
            },
            error: function () {
                alert('Error deleting item. Please try again.');
            }
        });
    });

    })

    $('#add-spec').click(function () {
        $('#item-specs-container').append(`
            <div class="spec-row input-group mb-2">
                <input type="text" class="form-control spec-key" placeholder="e.g., RAM">
                <input type="text" class="form-control spec-value" placeholder="e.g., 16GB DDR4">
                <button type="button" class="btn btn-danger remove-spec">×</button>
            </div>
        `);
    });

    $(document).on('click', '.remove-spec', function () {
        $(this).closest('.spec-row').remove();
        checkIfFormChanged()
    });


})