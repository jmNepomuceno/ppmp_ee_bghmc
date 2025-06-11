<?php 

    $section;
    try {
        $sql = "SELECT sectionName FROM pgssection WHERE sectionID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['section']]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $section = $data[0]['sectionName'];
        $_SESSION["sectionName"] = $section;

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    $sql = "SELECT permission FROM permission WHERE role=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['role']]);
    $permission_account = $stmt->fetch(PDO::FETCH_ASSOC);
    $permissions = json_decode($permission_account['permission'], true);
        
    
?>
    <div class="left-container">
        <div class="home-name-div">
            <img id="home-img" src="../source/landing_css/logo.PNG" alt="logo-img" >
        </div>

        <div class="side-bar-route">
            <div class="side-bar-routes" id="inventory-list-sub-div">
                <i class="fa-solid fa-box"></i>
                <span>Inventory List</span>
            </div>

            <div class="side-bar-routes" id="order-management-sub-div">
                <i class="fa-solid fa-clipboard"></i>
                <span>Request Status</span>
            </div>

            <div class="side-bar-routes" id="incoming-item-sub-div">
                <i class="fa-solid fa-truck-loading"></i>
                <span>Incoming Item</span>
            </div>

            <?php if ($permissions['admin_function'] != false) { ?>
                <div class="side-bar-routes" id="incoming-order-sub-div">
                    <i class="fa-solid fa-box-open"></i>
                    <span>Incoming Order</span>
                    <i class="fa-solid fa-bell hidden" id="bell-notif"></i>
                </div>
                
                <div class="side-bar-routes" id="imiss-inventory-sub-div">
                    <i class="fa-solid fa-warehouse"></i>
                    <span>IMISS Inventory</span>
                </div>

                <div class="side-bar-routes" id="imiss-ppmp-sub-div">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>IMISS PPMP Draft</span>
                </div>

                <div class="side-bar-routes" id="item-distribution-sub-div">
                    <i class="fa-solid fa-truck"></i>
                    <span>Item Distribution</span>
                </div>
            <?php } ?>

        </div>

        <div class="user-acc-div">
            <span id="user-section-span"><?php echo $section ?></span>
            <div class="vl"></div>
            <span id="user-name-span"><?php echo $_SESSION["name"] ?></span>
            <!-- <i class="fa-solid fa-right-from-bracket" id="logout-btn"></i> -->
            <!-- <img src="../source/home_css/logout.png" alt="log-out-btn" id="logout-btn" /> -->
        </div>
    </div>

    <div class="modal fade" id="modal-logout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title-incoming" class="modal-title-incoming" id="exampleModalLabel">Are you sure you want to logout?</h5>
                </div>
                <div id="modal-body-incoming" class="modal-body-incoming ml-2">
                    
                </div>
                <div class="modal-footer">
                    <button id="yes-modal-btn-logout" type="button" type="button" data-bs-dismiss="modal">YES</button>
                    <button id="no-modal-btn-logout" type="button" type="button" data-bs-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="../assets/websocket/script.js"></script> -->
    <script> 
        var view = "<?php echo $view ?>";
    </script>

<script src="../js/sidebar.js?v=<?php echo time(); ?>"></script>

    