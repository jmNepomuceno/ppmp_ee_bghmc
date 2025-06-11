<?php 
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');
    
    $allowed_roles = ["admin"];
    if (!in_array($_SESSION["role"], $allowed_roles)) {
        if (!in_array($_SESSION["role"], $allowed_roles)) {
            die("<h2>Access Denied</h2><p>You do not have permission to access this page.</p>");
        }
        header("Location: ../views/home.php");
    }


    // $sql = "UPDATE ppmp_request SET order_status=Pending WHERE orderID='ORDER00032'";
    // $stmt = $pdo->prepare($sql);
    // // $stmt->execute([$todo,  json_encode($current_cart['order_item']), $orderID]);

    // $sql = "DELETE FROM request_history WHERE orderID='ORDER00038'";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute();

    // $sql = "DELETE FROM ppmp_request WHERE orderID='ORDER00038'";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute();

    // $sql = "DELETE FROM ppmp_notification WHERE orderID='ORDER00038'";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute();

    // $sql = "UPDATE ppmp_notification SET isRead=0 WHERE orderID='ORDER00036'";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute();

    $sql = "SELECT * FROM ppmp_notification WHERE notifReceiver='admin' ORDER BY isRead ASC, created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $admin_notif = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // echo '<pre>'; print_r($admin_notif); echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css/incoming_order.css">

    <?php require "../links/header_link.php" ?>
</head>
<body>
    <?php 
        $view = "incoming-order-sub-div";
        include("./sidebar.php")
    ?>

    <i class="fa-solid fa-bars" id="burger-icon"></i>
    <div class="right-container">
        <?php 
            $navbar_view = "INCOMING ORDER";
            include("./navbar.php");
        ?>
        <h1>Incoming PPMP Request</h1>
        <div class="table-div">
            <div class="filter-div">
                <span id="filter-span-text">Filter: </span>
                <button class="filter-buttons" id="pending-btn">Pending</button>
                <button class="filter-buttons" id="approved-btn">Approved</button>
                <button class="filter-buttons" id="rejected-btn">Rejected</button>
                <button class="filter-buttons" id="cancelled-btn">Cancelled</button>
                <button class="filter-buttons" id="all-btn">All</button>
            </div>
            <div class="table-container">
                <table id="cart-table" class="display">
                    <thead>
                        <tr >
                            <th>REQUEST NO.</th>
                            <th>NAME</th>
                            <th>SECTION</th>
                            <th>DATE</th>
                            <th>REQUEST ITEM</th>
                            <th>STATUS</th>
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
                            <tr >
                                <th>ITEM ID</th>
                                <th>IMAGE</th>
                                <th>PRODUCT</th>
                                <th>PRICE</th>
                                <th>QUANTITY</th>
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
                    <button id="close-modal-btn" type="button" type="button" data-bs-dismiss="modal">CLOSE</button>
                    <button id="approve-request-btn" type="button">APPROVE REQUEST</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-notif" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel">Your Cart</h5>
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
    <script src="../js-obf/incoming_order_function-obf.js?v=<?php echo time(); ?>"></script> -->
    
    <script src="../js/home_traverse.js?v=<?php echo time(); ?>"></script>
    <script src="../js/incoming_order_function.js?v=<?php echo time(); ?>"></script>
</body>
</html>