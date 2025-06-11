<?php 
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');
    
    $allowed_roles = ["admin" , "user"];
    if (!in_array($_SESSION["role"], $allowed_roles)) {
        header("Location: ../views/home.php"); // Redirect unauthorized users
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css/order_management.css">
    <?php require "../links/header_link.php" ?>
</head>
<body>
    <?php 
        $view = "order-management-sub-div";
        include("./sidebar.php")
    ?>

    <i class="fa-solid fa-bars" id="burger-icon"></i>

    <div class="right-container">
        <?php 
            $navbar_view = "Request Status";
            include("./navbar.php");
        ?>
        <h1>Order Request Dashboard</h1>
        <div class="table-div">
            <div class="table-container">
                <table id="cart-table" class="display">
                    <div class="filter-div">
                        <span id="filter-span-text">Filter: </span>
                        <button class="filter-buttons" id="pending-btn">Pending</button>
                        <button class="filter-buttons" id="approved-btn">Approved</button>
                        <button class="filter-buttons" id="rejected-btn">Rejected</button>
                        <button class="filter-buttons" id="cancelled-btn">Cancelled</button>
                        <button class="filter-buttons" id="all-btn">All</button>
                    </div>
                    
                    <thead>
                        <tr >
                            <th>REQUEST NO.</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                            <th>REQUEST ITEM</th>
                            <th>IMISS Update</th>
                        </tr>
                    </thead>

                    <tbody>
                    
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modal-view-request" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered custom-modal-width modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel"></h5>
                </div>
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">

                    <table id="cart-table-request" class="display">
                        <thead>
                            <tr>
                                <th>ITEM ID</th>
                                <th>IMAGE</th>
                                <th>PRODUCT</th>
                                <th>PRICE</th>
                                <th>QUANTITY</th>
                                <th>SUBTOTAL</th>
                                <!-- Conditionally add the "ACTION" column -->
                                <th id="action-header" style="display: none;">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- <tr >
                                <td><img src="../source/inventory_image/item_1.png" alt="item-1-img"></td>
                                <td>Brand New Desktop Computer</td>
                                <td>P 80,000.00</td>
                                <td>1</td>
                                <td>P 80,000.00</td>
                                <td><button id="remove-item-btn">Remove</button></td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button id="close-modal-btn-incoming" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-view-update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered custom-modal-width modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel"></h5>
                </div>
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">

                    <table id="cart-table-update" class="display">
                        <thead>
                            <tr>
                                <th style="display:none;">ITEM ID</th>
                                <th>LAST MODIFIED</th>
                                <th>IMAGE</th>
                                <th>PRODUCT</th>
                                <th>PRICE</th>
                                <th>QUANTITY BEFORE</th>
                                <th>QUANTITY UPDATED</th>
                                <th>SUBTOTAL</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- <tr >
                                <td><img src="../source/inventory_image/item_1.png" alt="item-1-img"></td>
                                <td>Brand New Desktop Computer</td>
                                <td>P 80,000.00</td>
                                <td>1</td>
                                <td>P 80,000.00</td>
                                <td><button id="remove-item-btn">Remove</button></td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
                
                <div class="remarks-div">
                    <textarea name="" id="remark-textarea" placeholder="Remarks/Comment"></textarea>
                </div>

                <div class="modal-footer">
                    <button id="close-modal-btn-incoming" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-notif" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel">Successfully Updated</h5>
                </div>
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">
                    
                </div>
                <div class="modal-footer">
                    <button id="close-modal-btn-incoming" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <?php require "../links/script_links.php" ?>
    <script> 
        var section = "<?php echo $section ?>";
    </script>
    <!-- <script src="../js-obf/home_traverse-obf.js?v=<?php echo time(); ?>"></script>
    <script src="../js-obf/order_management_function-obf.js?v=<?php echo time(); ?>"></script> -->

    <script src="../js/home_traverse.js?v=<?php echo time(); ?>"></script>
    <script src="../js/order_management_function.js?v=<?php echo time(); ?>"></script>
</body>
</html>