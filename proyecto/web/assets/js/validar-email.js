$(document).ready(function () {
    $(".form-email").blur(function () {
        var email_form = this.value;
        $.ajax({
            url: URL + '/email-test',
            data: {email: email_form},
            type: 'POST',
            success: function (response) {
                if (response == "used") {
                    $(".form-email").css("border", "2px solid red");
                } else {
                    $(".form-email").css("border", "2px solid green");
                }
            }
        });
    });
});