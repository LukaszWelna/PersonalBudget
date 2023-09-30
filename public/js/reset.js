$(document).ready(function () {

    $("#formResetPassword").validate({
        rules: {
            password: {
                required: true,
                minlength: 6,
                validPassword: true
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "password")
                error.insertAfter("#divInputPassword")
        }
    });

    $("#showPassword").on("click", function () {
        var passInput = $("#inputPassword");
        if (passInput.attr("type") === "password") {
            passInput.attr("type", "text");
        } else {
            passInput.attr("type", "password");
        }
    });
    
});