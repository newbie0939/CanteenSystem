document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("registerForm");

    const message =
        document.getElementById("registerMessage");

    const button =
        document.getElementById("registerButton");


    form.addEventListener("submit", async function (event) {

        event.preventDefault();


        const studentId =
            document.getElementById("student_id")
                .value
                .trim();

        const name =
            document.getElementById("name")
                .value
                .trim();

        const email =
            document.getElementById("email")
                .value
                .trim();

        const password =
            document.getElementById("password")
                .value;

        const confirmPassword =
            document.getElementById("confirm_password")
                .value;


        if (password !== confirmPassword) {

            message.className = "message error";

            message.textContent =
                "Passwords do not match.";

            return;
        }




        if (password.length < 6) {

            message.className = "message error";

            message.textContent =
                "Password must be at least 6 characters.";

            return;
        }




        button.disabled = true;

        button.textContent =
            "Creating account...";

        message.className = "message";

        message.textContent = "";


        try {

            const response = await fetch(
                "api/register_student.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({

                        student_id: studentId,

                        name: name,

                        email: email,

                        password: password

                    })
                }
            );



            const text =
                await response.text();

            console.log(
                "Register server response:",
                text
            );



            let data;

            try {

                data = JSON.parse(text);

            } catch (error) {

                console.error(
                    "Invalid JSON:",
                    text
                );

                message.className =
                    "message error";

                message.textContent =
                    "Server returned an invalid response.";

                button.disabled = false;

                button.textContent =
                    "Create Student Account";

                return;
            }

            if (!data.success) {

                message.className =
                    "message error";

                message.textContent =
                    data.message;

                button.disabled = false;

                button.textContent =
                    "Create Student Account";

                return;
            }



            message.className =
                "message success";

            message.textContent =
                data.message;


            form.reset();




            setTimeout(function () {

                window.location.href =
                    "login.html";

            }, 1500);


        } catch (error) {

            console.error(
                "Registration error:",
                error
            );

            message.className =
                "message error";

            message.textContent =
                "Unable to connect to the server.";

            button.disabled = false;

            button.textContent =
                "Create Student Account";
        }

    });

});