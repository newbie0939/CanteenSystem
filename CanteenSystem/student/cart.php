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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Cart - Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/student_cart.css"
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
                class="nav-item active"
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
                    My Cart
                </h1>

                <p>
                    Review your order before checkout
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

        <section class="cart-content">


            <div class="cart-heading">

                <div>

                    <h2>
                        Your Order
                    </h2>

                    <p>
                        Check your items and quantities.
                    </p>

                </div>


                <a
                    href="menu.php"
                    class="back-menu"
                >
                    ← Continue Shopping
                </a>

            </div>


            <div
                class="cart-layout"
                id="cartLayout"
            >



                <div class="cart-items-section">

                    <div
                        class="cart-items"
                        id="cartItems"
                    >
                    </div>


                    <div
                        class="empty-cart"
                        id="emptyCart"
                    >

                        <div class="empty-cart-icon">
                            🛒
                        </div>

                        <h3>
                            Your cart is empty
                        </h3>

                        <p>
                            Add some delicious food from
                            the menu.
                        </p>

                        <a
                            href="menu.php"
                            class="shop-button"
                        >
                            Browse Menu
                        </a>

                    </div>

                </div>



                <aside
                    class="order-summary"
                    id="orderSummary"
                >

                    <h2>
                        Order Summary
                    </h2>


                    <div class="summary-row">

                        <span>
                            Items
                        </span>

                        <strong id="summaryItems">
                            0
                        </strong>

                    </div>


                    <div class="summary-row">

                        <span>
                            Subtotal
                        </span>

                        <strong id="summarySubtotal">
                            ₱0.00
                        </strong>

                    </div>


                    <div class="summary-divider"></div>


                    <div class="total-row">

                        <span>
                            Total
                        </span>

                        <strong id="summaryTotal">
                            ₱0.00
                        </strong>

                    </div>


                    <button
                        type="button"
                        class="undo-last-button"
                        id="undoLastButton"
                    >
                         ↶ Undo Last Item
                    </button>

                    <button
                        type="button"
                        class="checkout-button"
                        id="checkoutButton"
                    >
                        Place Order
                    </button>

                </aside>


            </div>

        </section>

    </main>


    <script src="../assets/js/student_cart.js"></script>

</body>

</html>