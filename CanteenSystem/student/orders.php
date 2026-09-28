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

require_once "../api/db_connect.php";

$student_id = (int) $_SESSION["user_id"];

$name = $_SESSION["name"] ?? "Student";


$stmt = $conn->prepare("
    SELECT
        id,
        order_number,
        total_amount,
        status,
        queue_position,
        created_at,
        updated_at
    FROM orders
    WHERE student_id = ?
      AND is_archived = 0
    ORDER BY created_at DESC
");

$stmt->bind_param(
    "i",
    $student_id
);

$stmt->execute();

$orders = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders - Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/student_orders.css"
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
                class="nav-item"
            >
                <span>📊</span>
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
                class="nav-item active"
            >
                <span>📦</span>
                My Orders
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


    <!-- MAIN -->

    <main class="main-content">


        <!-- TOPBAR -->

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
                    My Orders
                </h1>

                <p>
                    Track your canteen orders
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


        <!-- ORDERS -->

        <section class="orders-content">


            <div class="orders-heading">

                <div>

                    <h2>
                        Your Orders
                    </h2>

                    <p>
                        View your current and previous orders.
                    </p>

                </div>


                <a
                    href="menu.php"
                    class="new-order-button"
                >
                    + New Order
                </a>

            </div>


            <?php if ($orders->num_rows > 0): ?>


                <div class="orders-list">


                    <?php while (
                        $order = $orders->fetch_assoc()
                    ): ?>


                        <article class="order-card">


                            <div class="order-top">


                                <div>

                                    <span class="order-label">
                                        Order Number
                                    </span>

                                    <h3>
                                        <?php
                                        echo htmlspecialchars(
                                            $order["order_number"]
                                        );
                                        ?>
                                    </h3>

                                </div>


                                <span
                                    class="status-badge status-<?php
                                    echo strtolower(
                                        $order["status"]
                                    );
                                    ?>"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $order["status"]
                                    );
                                    ?>
                                </span>


                            </div>


                            <div class="order-details">


                                <div class="detail-item">

                                    <span>
                                        Total
                                    </span>

                                    <strong>
                                        ₱<?php
                                        echo number_format(
                                            $order["total_amount"],
                                            2
                                        );
                                        ?>
                                    </strong>

                                </div>


                                <div class="detail-item">

                                    <span>
                                        Queue Position
                                    </span>

                                    <strong>

                                        <?php if (
                                            $order["queue_position"]
                                            !== null
                                        ): ?>

                                            #<?php
                                            echo (int)
                                                $order[
                                                    "queue_position"
                                                ];
                                            ?>

                                        <?php else: ?>

                                            —

                                        <?php endif; ?>

                                    </strong>

                                </div>


                                <div class="detail-item">

                                    <span>
                                        Date
                                    </span>

                                    <strong>
                                        <?php
                                        echo date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $order["created_at"]
                                            )
                                        );
                                        ?>
                                    </strong>

                                </div>


                            </div>


                            <div class="order-status-line">


                                <div
                                    class="status-step <?php
                                    echo in_array(
                                        $order["status"],
                                        [
                                            "Pending",
                                            "Preparing",
                                            "Ready",
                                            "Completed"
                                        ]
                                    )
                                        ? "active"
                                        : "";
                                    ?>"
                                >
                                    <span>1</span>
                                    <small>Pending</small>
                                </div>


                                <div
                                    class="status-line"
                                ></div>


                                <div
                                    class="status-step <?php
                                    echo in_array(
                                        $order["status"],
                                        [
                                            "Preparing",
                                            "Ready",
                                            "Completed"
                                        ]
                                    )
                                        ? "active"
                                        : "";
                                    ?>"
                                >
                                    <span>2</span>
                                    <small>Preparing</small>
                                </div>


                                <div
                                    class="status-line"
                                ></div>


                                <div
                                    class="status-step <?php
                                    echo in_array(
                                        $order["status"],
                                        [
                                            "Ready",
                                            "Completed"
                                        ]
                                    )
                                        ? "active"
                                        : "";
                                    ?>"
                                >
                                    <span>3</span>
                                    <small>Ready</small>
                                </div>


                                <div
                                    class="status-line"
                                ></div>


                                <div
                                    class="status-step <?php
                                    echo $order["status"]
                                        === "Completed"
                                        ? "active"
                                        : "";
                                    ?>"
                                >
                                    <span>4</span>
                                    <small>Completed</small>
                                </div>


                            </div>


                        </article>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <div class="empty-orders">

                    <div class="empty-icon">
                        🛒
                    </div>

                    <h3>
                        No orders yet
                    </h3>

                    <p>
                        Your orders will appear here
                        after you place an order.
                    </p>

                    <a
                        href="menu.php"
                        class="browse-button"
                    >
                        Browse Menu
                    </a>

                </div>


            <?php endif; ?>


        </section>

    </main>


    <script src="../assets/js/student_orders.js"></script>

</body>

</html>