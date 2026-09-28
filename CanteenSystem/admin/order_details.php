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

require_once "../api/db_connect.php";

$order_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($order_id <= 0) {
    header("Location: orders.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT
        o.id,
        o.order_number,
        o.student_id,
        o.total_amount,
        o.status,
        o.queue_position,
        o.created_at,
        o.updated_at,
        s.student_id AS student_number,
        s.name AS student_name,
        s.email AS student_email
    FROM orders o
    INNER JOIN students s
        ON s.id = o.student_id
    WHERE o.id = ?
    LIMIT 1
");

$stmt->bind_param("i", $order_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: orders.php");
    exit;
}

$order = $result->fetch_assoc();

$item_stmt = $conn->prepare("
    SELECT
        oi.quantity,
        oi.price,
        oi.subtotal,
        mi.name
    FROM order_items oi
    INNER JOIN menu_items mi
        ON mi.id = oi.menu_item_id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
");

$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();

$item_result = $item_stmt->get_result();

$items = [];

while ($item = $item_result->fetch_assoc()) {
    $items[] = $item;
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

<title>
    Order Details - Canteen System
</title>

<link
    rel="stylesheet"
    href="../assets/css/admin_order_details.css"
>

</head>

<body>

<aside class="sidebar" id="sidebar">

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
            <span>⌂</span>
            Dashboard
        </a>

        <a
            href="orders.php"
            class="nav-item active"
        >
            <span>▣</span>
            Orders
        </a>

        <a
            href="menu.php"
            class="nav-item"
        >
            <span>☷</span>
            Menu
        </a>

        <a
            href="notifications.php"
            class="nav-item"
        >
            <span>♢</span>
            Notifications
        </a>

        <a
            href="messages.php"
            class="nav-item"
        >
            <span>✉</span>
            Messages
        </a>

        <a
            href="reports.php"
            class="nav-item"
        >
            <span>▤</span>
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
                Order Details
            </h1>

            <p>
                View complete information about this order
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

    <section class="order-details-content">

        <div class="back-section">

            <a
                href="orders.php"
                class="back-button"
            >
                ← Back to Orders
            </a>

        </div>

        <div class="order-header-card">

            <div>

                <span class="order-label">
                    Order Number
                </span>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $order["order_number"]
                    );
                    ?>
                </h2>

            </div>

            <div class="order-status">

                <span class="status-label">
                    Status
                </span>

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

        </div>

        <div class="details-grid">

            <div class="detail-card">

                <h3>
                    Student Information
                </h3>

                <div class="detail-row">

                    <span>
                        Student ID
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $order["student_number"]
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
                            $order["student_name"]
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
                            $order["student_email"]
                        );
                        ?>
                    </strong>

                </div>

            </div>

            <div class="detail-card">

                <h3>
                    Order Information
                </h3>

                <div class="detail-row">

                    <span>
                        Queue Position
                    </span>

                    <strong>

                        <?php

                        if (
                            $order["queue_position"] !== null
                        ) {
                            echo htmlspecialchars(
                                $order["queue_position"]
                            );
                        } else {
                            echo "—";
                        }

                        ?>

                    </strong>

                </div>

                <div class="detail-row">

                    <span>
                        Date
                    </span>

                    <strong>

                        <?php
                        echo date(
                            "M d, Y",
                            strtotime(
                                $order["created_at"]
                            )
                        );
                        ?>

                    </strong>

                </div>

                <div class="detail-row">

                    <span>
                        Time
                    </span>

                    <strong>

                        <?php
                        echo date(
                            "h:i A",
                            strtotime(
                                $order["created_at"]
                            )
                        );
                        ?>

                    </strong>

                </div>

            </div>

        </div>

        <div class="items-card">

            <div class="section-heading">

                <div>

                    <h3>
                        Ordered Items
                    </h3>

                    <p>
                        Food items included in this order
                    </p>

                </div>

            </div>

            <?php if (count($items) > 0): ?>

                <div class="items-list">

                    <?php foreach ($items as $item): ?>

                        <div class="order-item">

                            <div class="item-info">

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $item["name"]
                                    );
                                    ?>
                                </strong>

                                <span>
                                    ₱<?php
                                    echo number_format(
                                        (float) $item["price"],
                                        2
                                    );
                                    ?>
                                    ×
                                    <?php
                                    echo (int) $item["quantity"];
                                    ?>
                                </span>

                            </div>

                            <strong class="item-subtotal">

                                ₱<?php
                                echo number_format(
                                    (float) $item["subtotal"],
                                    2
                                );
                                ?>

                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty-items">

                    No items found for this order.

                </div>

            <?php endif; ?>

            <div class="order-total">

                <span>
                    Total Amount
                </span>

                <strong>

                    ₱<?php
                    echo number_format(
                        (float) $order["total_amount"],
                        2
                    );
                    ?>

                </strong>

            </div>

        </div>

        <div class="order-actions-card">

            <h3>
                Order Status
            </h3>

            <p>
                Current order status:
                <strong>
                    <?php
                    echo htmlspecialchars(
                        $order["status"]
                    );
                    ?>
                </strong>
            </p>

            <div class="action-buttons">

                <?php if ($order["status"] === "Pending"): ?>

                    <button
                        type="button"
                        class="action-button primary"
                        id="startPreparingButton"
                        data-order-id="<?php echo $order["id"]; ?>"
                    >
                        Start Preparing
                    </button>

                    <button
                        type="button"
                        class="action-button danger"
                        id="cancelOrderButton"
                        data-order-id="<?php echo $order["id"]; ?>"
                    >
                        Cancel Order
                    </button>

                <?php elseif ($order["status"] === "Preparing"): ?>

                    <button
                        type="button"
                        class="action-button primary"
                        id="markReadyButton"
                        data-order-id="<?php echo $order["id"]; ?>"
                    >
                        Mark Ready
                    </button>

                <?php elseif ($order["status"] === "Ready"): ?>

                    <button
                        type="button"
                        class="action-button primary"
                        id="completeOrderButton"
                        data-order-id="<?php echo $order["id"]; ?>"
                    >
                        Complete Order
                    </button>

                <?php elseif ($order["status"] === "Completed"): ?>

                    <span class="completed-message">
                        This order has been completed.
                    </span>

                <?php elseif ($order["status"] === "Cancelled"): ?>

                    <span class="cancelled-message">
                        This order has been cancelled.
                    </span>

                <?php endif; ?>

            </div>

        </div>

    </section>

</main>

<script>

window.orderDetailsId =
    <?php echo (int) $order["id"]; ?>;

</script>

<script src="../assets/js/admin_order_details.js"></script>

</body>

</html>