<?php
session_start();

if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'student' ||
    !isset($_SESSION['user_id'])
) {
    header("Location: ../login.html");
    exit;
}

require_once "../api/db_connect.php";

$student_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT id, name, email, profile_image
    FROM students
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $student_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$stmt->close();
$conn->close();

if (!$student) {
    session_destroy();
    header("Location: ../login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Messages - Canteen System</title>

    <link
        rel="stylesheet"
        href="../assets/css/student_messages.css"
    >

</head>

<body>

<div class="student-layout">

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    <aside
        class="student-sidebar"
        id="studentSidebar"
    >

        <div class="sidebar-header">

            <div class="logo">
                Canteen 
            </div>

            <button
                type="button"
                class="close-sidebar"
                id="closeSidebar"
            >
                ×
            </button>

        </div>


<nav class="sidebar-nav">

    <a href="dashboard.php" class="nav-item">
        <span class="nav-icon">📊</span>
        <span class="nav-label">Dashboard</span>
    </a>

    <a href="menu.php" class="nav-item">
        <span class="nav-icon">🍔</span>
        <span class="nav-label">Menu</span>
    </a>

    <a href="cart.php" class="nav-item">
        <span class="nav-icon">🛒</span>
        <span class="nav-label">Cart</span>
    </a>

    <a href="orders.php" class="nav-item">
        <span class="nav-icon">📦</span>
        <span class="nav-label">Orders</span>
    </a>

    <a href="notifications.php" class="nav-item">
        <span class="nav-icon">🔔</span>
        <span class="nav-label">Notifications</span>
    </a>

    <a href="messages.php" class="nav-item active">
        <span class="nav-icon">💬</span>
        <span class="nav-label">Messages</span>

        <span
            class="messages-unread-count"
            id="messageUnreadCount"
            style="display:none;"
        >
            0
        </span>
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


    <main class="student-main">

        <!-- TOPBAR -->
        <header class="student-topbar">

            <button
                type="button"
                class="hamburger-button"
                id="hamburgerButton"
                aria-label="Open menu"
            >
                ☰
            </button>


            <div class="topbar-title">
                Messages
            </div>


            <div class="student-profile">

                <?php if (!empty($student['profile_image'])): ?>

                    <img
                        src="../<?php echo htmlspecialchars($student['profile_image']); ?>"
                        alt="Profile"
                        class="profile-image"
                    >

                <?php else: ?>

                    <div class="profile-placeholder">

                        <?php
                        echo strtoupper(
                            substr($student['name'], 0, 1)
                        );
                        ?>

                    </div>

                <?php endif; ?>


                <div class="profile-info">

                    <strong>
                        <?php echo htmlspecialchars($student['name']); ?>
                    </strong>

                    <span>
                        <?php echo htmlspecialchars($student['email']); ?>
                    </span>

                </div>

            </div>

        </header>

        <section class="messages-page">

            <div class="page-heading">

                <div>

                    <h1>
                        Messages
                    </h1>

                    <p>
                        Communicate with the canteen admin.
                    </p>

                </div>


                <div class="message-page-actions">

                    <button
                        type="button"
                        id="markAllMessagesBtn"
                        class="secondary-button"
                    >
                        Mark All as Read
                    </button>


                    <button
                        type="button"
                        id="deleteAllMessagesBtn"
                        class="danger-button"
                    >
                        Delete All
                    </button>

                </div>

            </div>


            <div
                id="messageAlert"
                class="message-alert"
                style="display:none;"
            ></div>

            <section class="message-compose-card">

                <div class="card-heading">

                    <div>

                        <h2>
                            Send a Message
                        </h2>

                        <p>
                            Send a message to the canteen admin.
                        </p>

                    </div>

                </div>


                <form
                    id="sendMessageForm"
                    class="message-form"
                >

                    <input
                        type="hidden"
                        name="receiver_type"
                        value="admin"
                    >


                    <div class="form-group">

                        <label for="receiver_id">
                            Admin
                        </label>

                        <select
                            id="receiver_id"
                            name="receiver_id"
                            required
                        >

                            <option value="">
                                Select Admin
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="order_id">
                            Related Order
                        </label>

                        <select
                            id="order_id"
                            name="order_id"
                        >

                            <option value="">
                                No specific order
                            </option>

                        </select>

                    </div>


                    <div class="form-group full-width">

                        <label for="message">
                            Message
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="4"
                            maxlength="1000"
                            placeholder="Type your message..."
                            required
                        ></textarea>

                    </div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            class="send-button"
                        >
                            Send Message
                        </button>

                    </div>

                </form>

            </section>


            <!-- MESSAGES -->
            <section class="messages-card">

                <div class="card-heading">

                    <div>

                        <h2>
                            Conversation
                        </h2>

                        <p>
                            Your messages and admin replies.
                        </p>

                    </div>


                    <button
                        type="button"
                        class="refresh-button"
                        id="refreshMessagesButton"
                        onclick="loadMessages()"
                    >
                        Refresh
                    </button>

                </div>


                <div
                    id="messagesContainer"
                    class="messages-container"
                >

                    <div class="message-empty">

                        <h3>
                            Loading messages...
                        </h3>

                        <p>
                            Please wait.
                        </p>

                    </div>

                </div>

            </section>

        </section>

    </main>

</div>


<script>
    window.currentUserType = "student";
    window.currentUserId = <?php echo $student_id; ?>;
</script>


<script src="../assets/js/messages.js"></script>


<script>

document.addEventListener("DOMContentLoaded", () => {

    const hamburgerButton =
        document.getElementById("hamburgerButton");

    const closeSidebar =
        document.getElementById("closeSidebar");

    const sidebar =
        document.getElementById("studentSidebar");

    const overlay =
        document.getElementById("sidebarOverlay");


    function openSidebar() {

        if (sidebar) {
            sidebar.classList.add("open");
        }

        if (overlay) {
            overlay.classList.add("active");
        }

    }


    function closeSidebarMenu() {

        if (sidebar) {
            sidebar.classList.remove("open");
        }

        if (overlay) {
            overlay.classList.remove("active");
        }

    }


    if (hamburgerButton) {

        hamburgerButton.addEventListener(
            "click",
            openSidebar
        );

    }


    if (closeSidebar) {

        closeSidebar.addEventListener(
            "click",
            closeSidebarMenu
        );

    }


    if (overlay) {

        overlay.addEventListener(
            "click",
            closeSidebarMenu
        );

    }


    const navItems =
        document.querySelectorAll(".nav-item");

    navItems.forEach(item => {

        item.addEventListener(
            "click",
            closeSidebarMenu
        );

    });


    const logoutButton =
        document.getElementById("logoutButton");


    if (logoutButton) {

        logoutButton.addEventListener(
            "click",
            async () => {

                try {

                    await fetch(
                        "../api/logout.php",
                        {
                            method: "POST",
                            credentials: "same-origin"
                        }
                    );

                } catch (error) {

                    console.error(error);

                }

                window.location.href =
                    "../login.html";

            }
        );

    }


    loadAdmins();

    loadStudentOrders();

});


async function loadAdmins() {

    const select =
        document.getElementById("receiver_id");


    if (!select) {
        return;
    }


    try {

        const response =
            await fetch(
                "../api/admins.php",
                {
                    credentials: "same-origin"
                }
            );


        const data =
            await response.json();


        if (!data.success) {
            return;
        }


        select.innerHTML =
            '<option value="">Select Admin</option>';


        (data.admins || []).forEach(admin => {

            const option =
                document.createElement("option");


            option.value =
                admin.id;


            option.textContent =
                admin.name;


            select.appendChild(option);

        });


    } catch (error) {

        console.error(
            "Unable to load admins:",
            error
        );

    }

}


async function loadStudentOrders() {

    const select =
        document.getElementById("order_id");


    if (!select) {
        return;
    }


    try {

        const response =
            await fetch(
                "../api/student_orders.php",
                {
                    credentials: "same-origin",
                    cache: "no-store"
                }
            );


        if (!response.ok) {

            throw new Error(
                "HTTP " + response.status
            );

        }


        const data =
            await response.json();


        if (!data.success) {

            console.error(
                data.message ||
                "Unable to load orders."
            );

            return;

        }


        select.innerHTML =
            '<option value="">No specific order</option>';


        (data.orders || []).forEach(order => {

            const option =
                document.createElement("option");


            option.value =
                order.id;


            const status =
                order.status || "Pending";


            const total =
                Number(
                    order.total_amount || 0
                ).toFixed(2);


            option.textContent =
                `#${order.order_number} - ${status} - ₱${total}`;


            select.appendChild(option);

        });


    } catch (error) {

        console.error(
            "Unable to load student orders:",
            error
        );

    }

}

</script>

</body>

</html>