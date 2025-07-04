let totalScreenWidth = screen.width;
let totalScreenHeight = screen.height;

console.log("Total Screen Width: " + totalScreenWidth + ", Total Screen Height: " + totalScreenHeight);
//1920 / 1080


const side_bar_border_style = (view) =>{
    for(let i = 0; i < $('.side-bar-routes').length; i++){
        $('.side-bar-routes').eq(i).css('background', '#BA3912');
        $('.side-bar-routes').eq(i).css('border-left', '0');
    }

    document.getElementById(view).style.background = "#A23210"
    document.getElementById(view).style.borderLeft = "5px solid white"
}

$(document).ready(function(){
    side_bar_border_style(view)

    if(section.length <= 10){
        $('#user-section-span').css('font-size', '1.5em');
    }

    $('#inventory-list-sub-div').click(function(){
        window.location.href = "../views/home.php";
    });

    $('#order-management-sub-div').click(function(){
        window.location.href = "../views/order_management.php";
    });

    $('#incoming-item-sub-div').click(function(){
        window.location.href = "../views/incoming_item.php";
    });

    $('#imiss-inventory-sub-div').click(function(){
        window.location.href = "../views/imiss_inventory.php";
    });

    $('#incoming-order-sub-div').click(function(){
        window.location.href = "../views/incoming_order.php";
    });

    $('#imiss-ppmp-sub-div').click(function(){
        window.location.href = "../views/imiss_ppmp.php";
    });

    $('#item-distribution-sub-div').click(function(){
        window.location.href = "../views/item_distribution.php";
    });
    

    $('#item-request-sub-div').click(function(){
        window.location.href = "../views/item_request.php";
    });

    $('#incoming-request-sub-div').click(function(){
        window.location.href = "../views/incoming_request.php";
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
    

    $(document).off('click', '#burger-icon').on('click', '#burger-icon', function() {
        if($('#burger-icon').css('color') != 'rgb(255, 85, 33)'){
            $('body .left-container').css('display', 'none');
            $('#burger-icon').css('color', '#ff5521');
        }else{
            $('body .left-container').css('display', 'flex');
            $('#burger-icon').css('color', 'white');
        }
    });

    $(document).on('click', '.view-specs-btn', function () {
        const itemName = $(this).data('item-name');
        const itemPrice = $(this).data('item-price');
        const itemUnit = $(this).data('item-unit');
        const itemImage = $(this).data('item-image');
        const specs = $(this).data('item-specs');

        console.log(specs)

        $('#specs-item-name').text(itemName);
        $('#specs-item-price').text("P " + itemPrice);
        $('#specs-item-unit').text(itemUnit);
        $('#specs-item-img').attr('src', itemImage);
        $('#specs-list').html(specs);

        const modal = new bootstrap.Modal(document.getElementById('specsModal'));
        modal.show();
    });
})