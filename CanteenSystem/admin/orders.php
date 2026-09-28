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

    <title>Orders - Admin | Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin_orders.css"
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
                class="nav-item active"
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

                <h1>Orders</h1>

                <p>
                    Manage and process student orders
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


        

        <section class="orders-content">


            <div class="orders-heading">

                <div>

                    <h2>
                        All Orders
                    </h2>

                    <p>
                        Orders are displayed according to the queue.
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


            

            <div class="filter-bar">

                <button
                    type="button"
                    class="filter-button active"
                    data-filter="All"
                >
                    All
                </button>

                <button
                    type="button"
                    class="filter-button"
                    data-filter="Pending"
                >
                    Pending
                </button>

                <button
                    type="button"
                    class="filter-button"
                    data-filter="Preparing"
                >
                    Preparing
                </button>

                <button
                    type="button"
                    class="filter-button"
                    data-filter="Ready"
                >
                    Ready
                </button>

                <button
                    type="button"
                    class="filter-button"
                    data-filter="Completed"
                >
                    Completed
                </button>

                <button
                    type="button"
                    class="filter-button"
                    data-filter="Cancelled"
                >
                    Cancelled
                </button>

            </div>


            

            <div
                class="orders-list"
                id="ordersList"
            >

                <div class="loading-state">
                    Loading orders...
                </div>

            </div>


        </section>

    </main>


    <script src="../assets/js/admin_orders.js"></script>

</body>

</html>