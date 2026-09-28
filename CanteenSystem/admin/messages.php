<?php
session_start();

if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'admin' ||
    !isset($_SESSION['user_id'])
) {
    header("Location: ../login.html");
    exit;
}

require_once "../api/db_connect.php";

$adminId = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT id, admin_id, name, email, profile_image
    FROM admins
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $adminId);
$stmt->execute();

$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$stmt->close();

if (!$admin) {
    session_destroy();
    header("Location: ../login.html");
    exit;
}

$adminName = htmlspecialchars($admin['name'], ENT_QUOTES, 'UTF-8');


$profileImage = !empty($admin['profile_image'])
    ? "../" . htmlspecialchars($admin['profile_image'], ENT_QUOTES, 'UTF-8')
    : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Messages | Admin Panel</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin_messages.css"
    >

    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .sidebar-header span {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            color: #9ca3af;
        }

        .sidebar-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .sidebar-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .sidebar-profile strong {
            display: block;
            font-size: 13px;
            color: #111827;
        }

        .sidebar-profile small {
            color: #9ca3af;
            font-size: 11px;
        }

        .sidebar-nav {
            padding: 15px 10px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px 13px;
            margin-bottom: 4px;
            border-radius: 9px;
            text-decoration: none;
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .sidebar-nav a:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .sidebar-nav a.active {
            background: #111827;
            color: #ffffff;
        }

        .sidebar-nav .logout {
            margin-top: 20px;
            color: #dc2626;
        }

        .sidebar-nav .logout:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        .topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            z-index: 900;
            align-items: center;
            padding: 0 15px;
        }

        .hamburger {
            width: 40px;
            height: 40px;
            border: none;
            background: #f3f4f6;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
        }

        .topbar-title {
            margin-left: 12px;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 999;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.active {
                display: block;
            }

            .topbar {
                display: flex;
            }
        }
    </style>
</head>

<body>

    

    <aside class="sidebar" id="adminSidebar">

        <div class="sidebar-header">
            <h2>Canteen System</h2>
            <span>Admin Panel</span>
        </div>

        <div class="sidebar-profile">

            <?php if ($profileImage): ?>

                <img
                    src="<?= $profileImage ?>"
                    alt="Admin Profile"
                >

            <?php else: ?>

                <div
                    style="
                        width:40px;
                        height:40px;
                        border-radius:50%;
                        background:#111827;
                        color:#ffffff;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-weight:700;
                        flex-shrink:0;
                    "
                >
                    <?= strtoupper(substr($admin['name'], 0, 1)) ?>
                </div>

            <?php endif; ?>

            <div>
                <strong><?= $adminName ?></strong>
                <small>Administrator</small>
            </div>

        </div>

        <nav class="sidebar-nav">

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="orders.php">
                Orders
            </a>

            <a href="messages.php" class="active">
                Messages
                <span
                    id="messageUnreadCount"
                    class="message-unread-count"
                    style="display:none;"
                >0</span>
            </a>

            <a href="notifications.php">
                Notifications
            </a>

            <a href="menu.php">
                Menu
            </a>

            <a href="queue.php">
                Queue
            </a>

            <a href="reports.php">
                Reports
            </a>

            <a href="../api/logout.php" class="logout">
                Logout
            </a>

        </nav>

    </aside>


    

    <header class="topbar">

        <button
            type="button"
            class="hamburger"
            id="hamburgerBtn"
            aria-label="Open menu"
        >
            ☰
        </button>

        <span class="topbar-title">
            Messages
        </span>

    </header>


    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    

    <main class="main-content">

        <div class="page-header">

            <div>
                <h1>Messages</h1>

                <p>
                    Communicate with students about their orders and concerns.
                </p>
            </div>

        </div>


        <div class="messages-card">

            <div class="messages-header">

                <div>
                    <h2>
                        Send Message
                    </h2>
                </div>

                <div class="message-actions">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="markAllMessagesBtn"
                    >
                        Mark All Read
                    </button>

                    <button
                        type="button"
                        class="btn btn-danger"
                        id="deleteAllMessagesBtn"
                    >
                        Delete All
                    </button>

                </div>

            </div>


            <div
                id="messageAlert"
                class="message-alert"
            ></div>


           

            <form
                id="sendMessageForm"
                class="message-form"
            >

                <input
                    type="hidden"
                    name="receiver_type"
                    value="student"
                >


                <div class="form-row">

                    <div class="form-group">

                        <label for="receiver_id">
                            Student
                        </label>

                        <select
                            id="receiver_id"
                            name="receiver_id"
                            required
                        >

                            <option value="">
                                Select student
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="order_id">
                            Order
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

                </div>


                <div class="form-group">

                    <label for="message">
                        Message
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        placeholder="Write your message..."
                        maxlength="2000"
                        required
                    ></textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Send Message
                </button>

            </form>


            <!-- TOOLBAR -->

            <div class="messages-toolbar">

                <span
                    id="messageUnreadCount"
                    class="message-unread-count"
                    style="display:none;"
                >
                    0
                </span>

            </div>



            <div
                id="messagesContainer"
                class="messages-container"
            >

                <div class="empty-messages">

                    <h3>
                        Loading messages...
                    </h3>

                    <p>
                        Please wait.
                    </p>

                </div>

            </div>

        </div>

    </main>


    <script>
        window.currentUserType = "admin";
        window.currentUserId = <?= $adminId ?>;
    </script>


    <script src="../assets/js/messages.js"></script>


    <script>

async function loadStudents() {

    const select = document.getElementById("receiver_id");

    if (!select) {
        return;
    }

    try {

        const response = await fetch(
            "../api/students.php",
            {
                credentials: "same-origin",
                cache: "no-store"
            }
        );

        const data = await response.json();

        if (!data.success) {

            select.innerHTML =
                '<option value="">Unable to load students</option>';

            return;
        }

        select.innerHTML =
            '<option value="">Select student</option>';

        (data.students || []).forEach(function(student) {

            const option = document.createElement("option");

            option.value = student.id;

            option.textContent =
                `${student.name} (${student.student_id})`;

            select.appendChild(option);

        });

    } catch (error) {

        console.error(
            "Unable to load students:",
            error
        );

        select.innerHTML =
            '<option value="">Unable to load students</option>';
    }
}


async function loadStudentOrdersForAdmin() {

    const studentSelect =
        document.getElementById("receiver_id");

    const orderSelect =
        document.getElementById("order_id");

    if (!studentSelect || !orderSelect) {
        return;
    }

    const studentId = studentSelect.value;

    if (!studentId) {

        orderSelect.innerHTML =
            '<option value="">No specific order</option>';

        return;
    }

    orderSelect.innerHTML =
        '<option value="">Loading orders...</option>';

    try {

        const response = await fetch(
            "../api/admin_student_orders.php?student_id=" +
            encodeURIComponent(studentId),
            {
                credentials: "same-origin",
                cache: "no-store"
            }
        );

        const data = await response.json();

        if (!data.success) {

            orderSelect.innerHTML =
                '<option value="">Unable to load orders</option>';

            return;
        }

        orderSelect.innerHTML =
            '<option value="">No specific order</option>';

        (data.orders || []).forEach(function(order) {

            const option =
                document.createElement("option");

            option.value = order.id;

            const total =
                Number(order.total_amount || 0).toFixed(2);

            option.textContent =
                `#${order.order_number} - ${order.status} - ₱${total}`;

            orderSelect.appendChild(option);

        });

        if (!data.orders || data.orders.length === 0) {

            orderSelect.innerHTML =
                '<option value="">No orders found</option>';
        }

    } catch (error) {

        console.error(
            "Unable to load student orders:",
            error
        );

        orderSelect.innerHTML =
            '<option value="">Unable to load orders</option>';
    }
}


document.addEventListener(
    "DOMContentLoaded",
    function() {

        const studentSelect =
            document.getElementById("receiver_id");

        const sidebar =
            document.querySelector(".sidebar");

        const hamburger =
            document.getElementById("hamburgerBtn");

        const overlay =
            document.getElementById("sidebarOverlay");


        loadStudents();


        if (studentSelect) {

            studentSelect.addEventListener(
                "change",
                loadStudentOrdersForAdmin
            );

        }


        if (hamburger && sidebar) {

            hamburger.addEventListener(
                "click",
                function() {

                    sidebar.classList.toggle("open");

                    if (overlay) {
                        overlay.classList.toggle("active");
                    }

                }
            );

        }


        if (overlay && sidebar) {

            overlay.addEventListener(
                "click",
                function() {

                    sidebar.classList.remove("open");

                    overlay.classList.remove("active");

                }
            );

        }


        document
            .querySelectorAll(".sidebar-nav a")
            .forEach(function(link) {

                link.addEventListener(
                    "click",
                    function() {

                        if (sidebar) {
                            sidebar.classList.remove("open");
                        }

                        if (overlay) {
                            overlay.classList.remove("active");
                        }

                    }
                );

            });

    }
);

</script>

</body>

</html>