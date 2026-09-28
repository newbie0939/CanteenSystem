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

$name = $_SESSION["name"] ?? "Administrator";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard - Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin_dashboard.css"
    >

</head>

<body>


    

    <aside
        class="sidebar"
        id="sidebar"
    >

        <div class="sidebar-header">

            <h2>
                Canteen
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
                class="nav-item"
            >
                <span>🔔</span>
                Notifications
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
                    Admin Dashboard
                </h1>

                <p>
                    Manage incoming canteen orders
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


        

        <section class="dashboard-content">


            <!-- SUMMARY -->

            <div class="summary-grid">


                <div class="summary-card">

                    <div class="summary-icon">
                        🕐
                    </div>

                    <div>

                        <span>
                            Pending
                        </span>

                        <strong id="pendingCount">
                            0
                        </strong>

                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-icon">
                        👨‍🍳
                    </div>

                    <div>

                        <span>
                            Preparing
                        </span>

                        <strong id="preparingCount">
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
                            Ready
                        </span>

                        <strong id="readyCount">
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
                            Completed
                        </span>

                        <strong id="completedCount">
                            0
                        </strong>

                    </div>

                </div>


            </div>


            

            <div class="section-heading">

                <div>

                    <h2>
                        Order Queue
                    </h2>

                    <p>
                        Process orders in the order they were received.
                    </p>

                </div>


                <button
                    type="button"
                    class="refresh-button"
                    id="refreshButton"
                >
                    ↻ Refresh
                </button>

            </div>


            <div
                class="orders-container"
                id="ordersContainer"
            >

                <div class="loading-state">

                    Loading orders...

                </div>

            </div>


        </section>

    </main>


    <script src="../assets/js/admin_dashboard.js"></script>

</body>

</html>