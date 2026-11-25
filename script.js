
$(document).ready(function () {

    $("#contactForm").on("submit", function (event) {

        event.preventDefault();

        const name = $("#name").val().trim();
        const email = $("#email").val().trim();
        const message = $("#message").val().trim();
        const contactMethod =
            $("input[name='contact_method']:checked").val();
        const subject = $("#subject").val();

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Check all required fields
        if (
            name === "" ||
            email === "" ||
            message === "" ||
            !contactMethod ||
            subject === ""
        ) {
            $("#formResponse")
                .text("Please fill in all required fields.")
                .css("color", "red");

            return;
        }

        // Validate email
        if (!emailPattern.test(email)) {
            $("#formResponse")
                .text("Please enter a valid email address.")
                .css("color", "red");

            return;
        }

        // AJAX submission
        $.ajax({

            url: "php/contact.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",

            beforeSend: function () {
                $("#formResponse")
                    .text("Sending your message...")
                    .css("color", "#245b91");

                $("#contactForm button[type='submit']")
                    .prop("disabled", true);
            },

            success: function (response) {
                $("#formResponse")
                    .text(response.message)
                    .css("color", "green");

                $("#contactForm")[0].reset();
            },

            error: function (xhr) {
                let errorMessage =
                    "Something went wrong. Please try again.";

                if (xhr.responseJSON &&
                    xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $("#formResponse")
                    .text(errorMessage)
                    .css("color", "red");
            },

            complete: function () {
                $("#contactForm button[type='submit']")
                    .prop("disabled", false);
            }

        });

    });

});
