<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_type"]) ||
    $_SESSION["user_type"] !== "admin"
) {
    header("Location: ../login.html");
    exit;
}

$name =
    $_SESSION["name"] ?? "Administrator";

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
        Notifications - Admin
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/admin_notifications.css"
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
            <span>📊</span>
            Dashboard
        </a>


        <a
            href="orders.php"
            class="nav-item"
        >
            <span>📦</span>
            Orders
        </a>


        <a
            href="menu.php"
            class="nav-item"
        >
            <span>🍔</span>
            Menu
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


        <a
            href="reports.php"
            class="nav-item"
        >
            <span>📊</span>
            Reports
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
                System and order notifications
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
                    Administrator
                </span>

            </div>

        </div>

    </header>


    <section class="notifications-content">


        <div class="notifications-heading">

            <div>

                <h2>
                    Notifications
                </h2>

                <p>
                    Stay updated with system activity.
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


<script src="../assets/js/admin_notifications.js"></script>

</body>

</html>