<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_type"]) ||
    $_SESSION["user_type"] !== "student"
) {
    header("Location: ../login.html");
    exit;
}

$name = $_SESSION["name"] ?? "Student";

$student_id = $_SESSION["student_id"] ?? "";

$email = $_SESSION["email"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Dashboard - Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/student_dashboard.css"
    >

</head>

<body>

    <!-- SIDEBAR -->

    <aside
        class="sidebar"
        id="sidebar"
    >

        <div class="sidebar-header">

            <h2>
                Lorenzo Canteen
            </h2>

            <button
                type="button"
                class="close-menu"
                id="closeMenu"
            >
                ×
            </button>

        </div>


        <nav class="sidebar-nav">

            <a
                href="dashboard.php"
                class="nav-item active"
            >
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>


            <a
                href="menu.php"
                class="nav-item"
            >
                <span class="nav-icon">🍔</span>
                <span>Menu</span>
            </a>


            <a
                href="orders.php"
                class="nav-item"
            >
                <span class="nav-icon">📦</span>
                <span>My Orders</span>
            </a>


            <a
                href="notifications.php"
                class="nav-item"
            >
                <span class="nav-icon">🔔</span>
                <span>Notifications</span>
            </a>


            <a
                href="messages.php"
                class="nav-item"
            >
                <span class="nav-icon">💬</span>
                <span>Messages</span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <button
                type="button"
                class="logout-button"
                id="logoutButton"
            >
                <span class="nav-icon">↪</span>
                <span>Logout</span>
            </button>

        </div>

    </aside>



    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>




    <main class="main-content">

        <header class="topbar">

            <button
                type="button"
                class="hamburger"
                id="hamburger"
            >
                ☰
            </button>


            <div class="page-title">

                <h1>
                    Dashboard
                </h1>

                <p>
                    Welcome back,
                    <?php echo htmlspecialchars($name); ?>!
                </p>

            </div>


            <div class="profile-area">

                <div class="profile-icon">

                    <?php
                    echo strtoupper(
                        substr($name, 0, 1)
                    );
                    ?>

                </div>


                <div class="profile-info">

                    <strong>
                        <?php
                        echo htmlspecialchars($name);
                        ?>
                    </strong>

                    <span>
                        Student
                    </span>

                </div>

            </div>

        </header>


        <section class="dashboard-content">


            <!-- WELCOME -->

            <div class="welcome-card">

                <div>

                    <span class="welcome-label">
                        Student Portal
                    </span>

                    <h2>
                        Welcome,
                        <?php
                        echo htmlspecialchars($name);
                        ?>!
                    </h2>

                    <p>
                        Order your favorite canteen meals
                        and track your orders here.
                    </p>

                </div>


                <div class="welcome-icon">
                    🍽️
                </div>

            </div>


            <div class="summary-grid">


                <div class="summary-card">

                    <div class="summary-icon">
                        🛒
                    </div>

                    <div>

                        <span>
                            Active Orders
                        </span>

                        <strong>
                            0
                        </strong>

                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-icon">
                        ✓
                    </div>

                    <div>

                        <span>
                            Completed Orders
                        </span>

                        <strong>
                            0
                        </strong>

                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-icon">
                        🔔
                    </div>

                    <div>

                        <span>
                            Notifications
                        </span>

                        <strong>
                            0
                        </strong>

                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-icon">
                        ✉
                    </div>

                    <div>

                        <span>
                            Messages
                        </span>

                        <strong>
                            0
                        </strong>

                    </div>

                </div>

            </div>


            <!-- CONTENT -->

            <div class="content-grid">



                <section class="dashboard-card">

                    <div class="card-header">

                        <div>

                            <h2>
                                Recent Orders
                            </h2>

                            <p>
                                Your latest canteen orders
                            </p>

                        </div>


                        <a
                            href="orders.php"
                            class="view-link"
                        >
                            View All
                        </a>

                    </div>


                    <div class="empty-state">

                        <div class="empty-icon">
                            🛒
                        </div>

                        <h3>
                            No orders yet
                        </h3>

                        <p>
                            Your recent orders will appear here.
                        </p>

                    </div>

                </section>


                <section class="dashboard-card">

                    <div class="card-header">

                        <div>

                            <h2>
                                My Profile
                            </h2>

                            <p>
                                Account information
                            </p>

                        </div>

                    </div>


                    <div class="profile-details">


                        <div class="detail-row">

                            <span>
                                Student ID
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $student_id
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-row">

                            <span>
                                Name
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $name
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-row">

                            <span>
                                Email
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $email
                                );
                                ?>
                            </strong>

                        </div>


                    </div>

                </section>

            </div>

        </section>

    </main>


    <script src="../assets/js/student_dashboard.js"></script>

</body>

</html>