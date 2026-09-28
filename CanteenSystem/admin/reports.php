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

    <title>Reports - Admin | Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin_reports.css"
    >

</head>

<body>


    <!-- SIDEBAR -->

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
                class="nav-item active"
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

                <h1>Reports</h1>

                <p>
                    View canteen order and sales reports
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

                    <span>Administrator</span>

                </div>

            </div>

        </header>


        

        <section class="reports-content">


            <div class="report-heading">

                <div>

                    <h2>Order Reports</h2>

                    <p>
                        Select a date range to view order statistics.
                    </p>

                </div>

            </div>


            
            <div class="filter-card">

                <div class="date-field">

                    <label for="startDate">
                        Start Date
                    </label>

                    <input
                        type="date"
                        id="startDate"
                    >

                </div>


                <div class="date-field">

                    <label for="endDate">
                        End Date
                    </label>

                    <input
                        type="date"
                        id="endDate"
                    >

                </div>


                <button
                    type="button"
                    id="generateReportButton"
                    class="generate-button"
                >
                    Generate Report
                </button>

            </div>


           

            <div class="summary-grid">

                <div class="summary-card">

                    <span>Total Orders</span>

                    <strong id="totalOrders">
                        0
                    </strong>

                </div>


                <div class="summary-card">

                    <span>Completed Orders</span>

                    <strong id="completedOrders">
                        0
                    </strong>

                </div>


                <div class="summary-card">

                    <span>Cancelled Orders</span>

                    <strong id="cancelledOrders">
                        0
                    </strong>

                </div>


                <div class="summary-card">

                    <span>Total Sales</span>

                    <strong id="totalSales">
                        ₱0.00
                    </strong>

                </div>

            </div>


            <!-- STATUS REPORT -->

            <div class="report-card">

                <div class="card-heading">

                    <div>

                        <h3>Order Status</h3>

                        <p>
                            Orders grouped by current status.
                        </p>

                    </div>

                </div>


                <div
                    class="status-grid"
                    id="statusGrid"
                >

                    <div class="status-row">

                        <span>Pending</span>

                        <strong id="pendingCount">
                            0
                        </strong>

                    </div>


                    <div class="status-row">

                        <span>Preparing</span>

                        <strong id="preparingCount">
                            0
                        </strong>

                    </div>


                    <div class="status-row">

                        <span>Ready</span>

                        <strong id="readyCount">
                            0
                        </strong>

                    </div>


                    <div class="status-row">

                        <span>Completed</span>

                        <strong id="completedStatusCount">
                            0
                        </strong>

                    </div>


                    <div class="status-row">

                        <span>Cancelled</span>

                        <strong id="cancelledStatusCount">
                            0
                        </strong>

                    </div>

                </div>

            </div>


            <!-- TOP FOOD ITEMS -->

            <div class="report-card">

                <div class="card-heading">

                    <div>

                        <h3>Top Food Items</h3>

                        <p>
                            Food items ordered during the selected period.
                        </p>

                    </div>

                </div>


                <div
                    class="food-report"
                    id="foodReport"
                >

                    <div class="loading-state">
                        Generate a report to view food statistics.
                    </div>

                </div>

            </div>


        </section>

    </main>


    <script src="../assets/js/admin_reports.js"></script>

</body>

</html>