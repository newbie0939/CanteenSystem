document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("loginForm");
    const message = document.getElementById("loginMessage");

    form.addEventListener("submit", async function (event) {

        event.preventDefault();

        const login = document.getElementById("login").value.trim();
        const password = document.getElementById("password").value;

        message.textContent = "Logging in...";

        try {

            const response = await fetch("api/login.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    login: login,
                    password: password
                })

            });

            const data = await response.json();

            if (!data.success) {

                message.textContent = data.message;

                return;
            }


            if (data.role === "student") {

                window.location.href =
                    "student/dashboard.php";

            } else if (data.role === "admin") {

                window.location.href =
                    "admin/dashboard.php";
            }

        } catch (error) {

            console.error(error);

            message.textContent =
                "Unable to connect to the server.";

        }

    });

});