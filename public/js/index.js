$(document).ready(function () {

    $("#formSignup").validate({
        rules: {
            name: "required",
            email: {
                required: true,
                email: true,
                remote: "/account/validateEmail"
            },
            password: {
                required: true,
                minlength: 6,
                validPassword: true
            }
        },
        messages: {
            email: {
                remote: "Email already exists in database"
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "name")
                error.insertAfter("#signupName")
            else if (element.attr("name") == "email")
                error.insertAfter("#signupEmail")
            else if (element.attr("name") == "password")
                error.insertAfter("#signupPassword")
        }
    });

    $("#showPassword").on("click", function () {
        var passInput = $("#floatingPasswordSignup");
        if (passInput.attr("type") === "password") {
            passInput.attr("type", "text");
        } else {
            passInput.attr("type", "password");
        }
    });

});