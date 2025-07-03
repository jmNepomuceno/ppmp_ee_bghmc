<?php 
    include ('../session.php');
    include('../assets/connection/sqlconnection.php');
    
    // $allowed_roles = ["admin"];
    // if (!in_array($_SESSION["role"], $allowed_roles)) {
    //     if (!in_array($_SESSION["role"], $allowed_roles)) {
    //         die("<h2>Access Denied</h2><p>You do not have permission to access this page.</p>");
    //     }
    //     header("Location: ../views/home.php");

    // }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css/incoming_request.css">
    <?php require "../links/header_link.php" ?>
</head>
<body>
    <?php 
        $view = "incoming-request-sub-div";
        include("./sidebar.php")
    ?>

    <i class="fa-solid fa-bars" id="burger-icon"></i>

    <div class="right-container">
        <?php 
            $navbar_view = "INCOMING ITEM REQUEST";
            include("./navbar.php");
        ?>
        <h1>Incoming Item Request</h1>
        <div class="table-div">
            <div class="table-container">
                <table id="cart-table" class="display">
                    <thead>
                        <tr>
                            <th>DATE REQUESTED</th>
                            <th>REQUESTOR NAME</th>
                            <th>REQUESTOR SECTION</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>

                    <tbody>
                    
                    </tbody>
                </table>
            </div>
        </div>
        <!-- <button id="exportExcelBtn" class="btn btn-success">Export to Excel</button> -->
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

    <div class="modal fade" id="modal-view-request" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewLabel">View Item Request</h5>
                    <!-- <button type="button" class="btn-close close-viewModal-btn" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>

                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name:</label>
                        <p id="view-item-name" class="mb-0">---</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Image:</label><br>
                        <img id="view-item-image" src="" alt="Item Image" class="img-fluid rounded border" style="max-height: 200px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Website Link:</label>
                        <p class="mb-0"><a id="view-item-link" href="#" target="_blank">View Reference</a></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Request:</label>
                        <p id="view-item-reason" class="mb-0">---</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-viewModal-btn" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>



    <?php require "../links/script_links.php" ?>
    <script> 
        var section = "<?php echo $section ?>";
    </script>
    <!-- <script src="../js-obf/home_traverse-obf.js?v=<?php echo time(); ?>"></script>
    <script src="../js-obf/item_distribution_function-obf.js?v=<?php echo time(); ?>"></script> -->

    <script src="../js/home_traverse.js?v=<?php echo time(); ?>"></script>
    <script src="../js/incoming_request_function.js?v=<?php echo time(); ?>"></script>
</body>
</html>