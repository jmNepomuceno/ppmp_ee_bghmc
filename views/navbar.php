<div class="nav-div">
    <div class="content-name-div">
        <i class="fa-solid fa-circle-dot"></i>
        <span> <?= $navbar_view ?> </span>
    </div>
    <!-- <img src="../source/home_img/logout.png" alt="logout-img"> -->

    <div class="navbar-function-icon">
        <i id="navbar-bell" class="fa-solid fa-bell"> 
            <span id="navbar-span-val">3</span>
        </i>

        <div class="navbar-notif-div">
            <div class="navbar-notif-row unread">
                <i class="fa-solid fa-circle-check"></i>
                <div class="navbar-notif-main-container">
                    <span class="navbar-notif-time">12:00 PM</span>
                    <div class="navbar-notif-sub-main-container">
                        <span class="navbar-notif-title">Incoming Item</span>
                        <span class="navbar-notif-desc">You have an incoming item</span>    
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="navbar-pagination"></div> -->
        <img src="../source/home_css/logout.png" alt="log-out-btn" id="logout-btn" />
    </div>
</div>

<script src="../js/navbar.js?v=<?php echo time(); ?>"></script>
