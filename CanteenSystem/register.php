<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Registration - Canteen System</title>

    <link
        rel="stylesheet"
        href="assets/css/register.css"
    >

</head>

<body>

    <main class="register-container">

        <h1 class="title">
            Student Registration
        </h1>

        <p class="subtitle">
            Create your student account
        </p>


        <div
            id="registerMessage"
            class="message"
        ></div>


        <form id="registerForm">

            <div class="form-group">

                <label for="student_id">
                    Student ID
                </label>

                <input
                    type="text"
                    id="student_id"
                    name="student_id"
                    placeholder="Enter your Student ID"
                    autocomplete="username"
                    required
                >

            </div>


            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    autocomplete="name"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="new-password"
                    required
                >

            </div>


            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                id="registerButton"
                class="register-button"
            >
                Create Student Account
            </button>

        </form>


        <a
            href="login.html"
            class="login-link"
        >
            Already have an account? Login
        </a>

    </main>


    <script src="assets/js/register.js"></script>

</body>

</html>