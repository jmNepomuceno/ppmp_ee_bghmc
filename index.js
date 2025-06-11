$(document).ready(function(){
    // Function to handle login
    let modal_notif = new bootstrap.Modal(document.getElementById('modal-notif'));

    function handleLogin() {
        const username_input = $('#username-txt').val();
        const password_input = $('#password-txt').val();

        $.ajax({
            url: '../php/login.php',
            method: "POST",
            data: {
                username: username_input,
                password: password_input
            },
            success: function(response) {
                if(response === "invalid") {
                    $('#modal-notif .modal-content .modal-header .modal-title-incoming').text("Invalid Credentials")
                    modal_notif.show()
                }else{
                    window.location.href = response;
                }
            }
        });
    }
    
    // Trigger login on button click
    $('#login-btn').click(function() {
        handleLogin();
    });

    // Trigger login on Enter key press
    $('#username-txt, #password-txt').keydown(function(event) {
        if (event.key === "Enter" || event.keyCode === 13) {
            handleLogin();
        }
    });

})