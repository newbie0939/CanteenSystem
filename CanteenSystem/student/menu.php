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

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        description,
        price,
        image,
        category
    FROM menu_items
    WHERE is_available = 1
    ORDER BY category ASC, name ASC
");

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Menu - Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/student_menu.css"
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
                class="nav-item active"
            >
                <span>🍔</span>
                Menu
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
                    Menu
                </h1>

                <p>
                    Choose your favorite food
                </p>

            </div>


            <div class="profile-area">

                <div class="profile-icon">

                    <?php
                    echo strtoupper(
                        substr(
                            $_SESSION["name"],
                            0,
                            1
                        )
                    );
                    ?>

                </div>


                <div class="profile-info">

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $_SESSION["name"]
                        );
                        ?>
                    </strong>

                    <span>
                        Student
                    </span>

                </div>

            </div>

        </header>


        <section class="menu-content">


            <div class="menu-heading">

                <div>

                    <h2>
                        Available Food
                    </h2>

                    <p>
                        Select an item to add to your order.
                    </p>

                </div>

            </div>


            <?php if ($result->num_rows > 0): ?>

                <div class="menu-grid">


                    <?php while ($item = $result->fetch_assoc()): ?>


                        <article class="food-card">


                            <div class="food-image">

                                <?php if (
                                    !empty($item["image"])
                                ): ?>

                                    <img
                                        src="../assets/image/menu/<?php
                                        echo htmlspecialchars(
                                            $item["image"]
                                        );
                                        ?>"
                                        alt="<?php
                                        echo htmlspecialchars(
                                            $item["name"]
                                        );
                                        ?>"
                                    >

                                <?php else: ?>

                                    <span>
                                        🍽️
                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="food-details">


                                <?php if (
                                    !empty($item["category"])
                                ): ?>

                                    <span class="food-category">
                                        <?php
                                        echo htmlspecialchars(
                                            $item["category"]
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>


                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $item["name"]
                                    );
                                    ?>
                                </h3>


                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $item["description"] ?? ""
                                    );
                                    ?>
                                </p>


                                <div class="food-bottom">


                                    <strong class="food-price">

                                        ₱<?php
                                        echo number_format(
                                            $item["price"],
                                            2
                                        );
                                        ?>

                                    </strong>


                                    <button
                                        type="button"
                                        class="add-button"
                                        data-id="<?php
                                        echo $item["id"];
                                        ?>"
                                    >
                                        Add
                                    </button>


                                </div>


                            </div>


                        </article>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <div class="empty-menu">

                    <div>
                        🍽️
                    </div>

                    <h3>
                        No food available
                    </h3>

                    <p>
                        There are currently no available
                        menu items.
                    </p>

                </div>


            <?php endif; ?>


        </section>

    </main>


    <script src="../assets/js/student_menu.js"></script>

</body>

</html>