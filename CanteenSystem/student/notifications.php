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

$name =
    $_SESSION["name"] ?? "Student";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Notifications - Student
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/student_notifications.css"
    >

</head>

<body>


<aside
    class="sidebar"
    id="sidebar"
>

    <div class="sidebar-header">

        <h2>Canteen</h2>

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
            class="nav-item"
        >
            <span></span>
            Dashboard
        </a>


        <a
            href="menu.php"
            class="nav-item"
        >
            <span>🍔</span>
            Menu
        </a>


        <a
            href="cart.php"
            class="nav-item"
        >
            <span>🛒</span>
            My Cart
        </a>


        <a
            href="orders.php"
            class="nav-item"
        >
            <span>📦</span>
            My Orders
        </a>


        <a
            href="notifications.php"
            class="nav-item active"
        >
            <span>🔔</span>
            Notifications

            <span
                class="nav-badge"
                id="sidebarUnreadCount"
            >
                0
            </span>

        </a>


        <a
            href="messages.php"
            class="nav-item"
        >
            <span>💬</span>
            Messages
        </a>

    </nav>


    <div class="sidebar-bottom">

        <button
            type="button"
            class="logout-button"
            id="logoutButton"
        >
            <span>↪</span>
            Logout
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
                Notifications
            </h1>

            <p>
                Stay updated with your orders
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


    <section class="notifications-content">


        <div class="notifications-heading">

            <div>

                <h2>
                    Your Notifications
                </h2>

                <p>
                    Order updates and system notifications.
                </p>

            </div>


            <div class="notification-actions">

                <button
                    type="button"
                    id="markAllButton"
                    class="secondary-button"
                >
                    Mark all as read
                </button>


                <button
                    type="button"
                    id="clearAllButton"
                    class="danger-button"
                >
                    Clear all
                </button>

            </div>

        </div>


        <div
            class="notification-list"
            id="notificationList"
        >

            <div class="loading-state">
                Loading notifications...
            </div>

        </div>


    </section>

</main>


<script src="../assets/js/student_notifications.js"></script>

</body>

</html>