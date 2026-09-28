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

    <title>Queue - Admin | Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin_orders.css"
    >

    <style>

        .queue-content {
            padding: 30px;
        }

        .queue-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .queue-heading h2 {
            margin: 0;
        }

        .queue-heading p {
            margin: 6px 0 0;
        }

        .queue-refresh-button {
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .queue-columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .queue-column {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        }

        .queue-column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .queue-column-header h3 {
            margin: 0;
        }

        .queue-count {
            min-width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f1f1f1;
            font-size: 13px;
            font-weight: 600;
        }

        .queue-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .queue-empty {
            padding: 25px 10px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }

        .queue-item {
            border: 1px solid #eeeeee;
            border-radius: 10px;
            padding: 15px;
        }

        .queue-position {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .queue-order-number {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .queue-total {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .queue-date {
            font-size: 12px;
            color: #777;
        }


        @media (max-width: 900px) {

            .queue-columns {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 600px) {

            .queue-content {
                padding: 20px 15px;
            }

            .queue-heading {
                align-items: flex-start;
                flex-direction: column;
            }

        }

    </style>

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
                href="queue.php"
                class="nav-item active"
            >
                <span>🔢</span>
                Queue
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

                <h1>Queue</h1>

                <p>
                    Monitor the current order queues
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


        

        <section class="queue-content">


            <div class="queue-heading">


                <div>

                    <h2>
                        Order Queue
                    </h2>

                    <p>
                        Orders are arranged according to FIFO queue position.
                    </p>

                </div>


                <button
                    type="button"
                    class="queue-refresh-button"
                    id="queueRefreshButton"
                >
                    ↻ Refresh
                </button>


            </div>


            

            <div class="queue-columns">


                

                <div class="queue-column">


                    <div class="queue-column-header">

                        <h3>
                            Pending
                        </h3>

                        <span
                            class="queue-count"
                            id="pendingCount"
                        >
                            0
                        </span>

                    </div>


                    <div
                        class="queue-list"
                        id="pendingQueue"
                    >

                        <div class="queue-empty">
                            Loading...
                        </div>

                    </div>


                </div>


               

                <div class="queue-column">


                    <div class="queue-column-header">

                        <h3>
                            Preparing
                        </h3>

                        <span
                            class="queue-count"
                            id="preparingCount"
                        >
                            0
                        </span>

                    </div>


                    <div
                        class="queue-list"
                        id="preparingQueue"
                    >

                        <div class="queue-empty">
                            Loading...
                        </div>

                    </div>


                </div>


               

                <div class="queue-column">


                    <div class="queue-column-header">

                        <h3>
                            Ready
                        </h3>

                        <span
                            class="queue-count"
                            id="readyCount"
                        >
                            0
                        </span>

                    </div>


                    <div
                        class="queue-list"
                        id="readyQueue"
                    >

                        <div class="queue-empty">
                            Loading...
                        </div>

                    </div>


                </div>


            </div>


        </section>


    </main>


    <script src="../assets/js/admin_queue.js"></script>


</body>

</html>