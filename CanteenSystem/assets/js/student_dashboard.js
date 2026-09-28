document.addEventListener("DOMContentLoaded", function () {

    const sidebar =
        document.getElementById("sidebar");

    const hamburger =
        document.getElementById("hamburger");

    const closeMenu =
        document.getElementById("closeMenu");

    const overlay =
        document.getElementById("sidebarOverlay");

    const logoutButton =
        document.getElementById("logoutButton");



    if (hamburger) {

        hamburger.addEventListener(
            "click",
            function () {

                sidebar.classList.add("open");

                overlay.classList.add("show");

            }
        );

    }


    function closeSidebar() {

        if (sidebar) {
            sidebar.classList.remove("open");
        }

        if (overlay) {
            overlay.classList.remove("show");
        }

    }


    if (closeMenu) {

        closeMenu.addEventListener(
            "click",
            closeSidebar
        );

    }


    if (overlay) {

        overlay.addEventListener(
            "click",
            closeSidebar
        );

    }


    if (logoutButton) {

        logoutButton.addEventListener(
            "click",
            async function () {

                try {

                    const response =
                        await fetch(
                            "../api/logout.php",
                            {
                                method: "POST"
                            }
                        );


                    const data =
                        await response.json();


                    if (data.success) {

                        window.location.href =
                            "../login.html";

                    }

                } catch (error) {

                    console.error(error);

                    alert(
                        "Unable to connect to the server."
                    );

                }

            }
        );

    }



    async function loadDashboard() {

        try {



            const dashboardResponse =
                await fetch(
                    "../api/student_dashboard.php",
                    {
                        cache: "no-store"
                    }
                );


            const dashboardData =
                await dashboardResponse.json();


            if (
                dashboardData.success
            ) {

                updateOrderCounts(
                    dashboardData
                );

                renderRecentOrders(
                    dashboardData.recent_orders || []
                );

            }


            const notificationResponse =
                await fetch(
                    "../api/notifications.php",
                    {
                        cache: "no-store"
                    }
                );


            const notificationData =
                await notificationResponse.json();


            if (
                notificationData.success
            ) {

                updateNotificationCount(
                    notificationData.unread_count
                );

            }



            const messageResponse =
                await fetch(
                    "../api/messages.php",
                    {
                        method: "GET",
                        credentials: "same-origin",
                        cache: "no-store"
                    }
                );


            const messageData =
                await messageResponse.json();


            if (
                messageData.success
            ) {

                updateMessageCount(
                    messageData.unread_count
                );

            }

        } catch (error) {

            console.error(
                "Dashboard update error:",
                error
            );

        }

    }



    function updateOrderCounts(data) {

        const cards =
            document.querySelectorAll(
                ".summary-card"
            );


        if (cards.length > 0) {

            const activeValue =
                cards[0].querySelector("strong");


            if (activeValue) {

                activeValue.textContent =
                    Number(
                        data.active_orders || 0
                    );

            }

        }


        if (cards.length > 1) {

            const completedValue =
                cards[1].querySelector("strong");


            if (completedValue) {

                completedValue.textContent =
                    Number(
                        data.completed_orders || 0
                    );

            }

        }

    }



    function updateNotificationCount(count) {

        const cards =
            document.querySelectorAll(
                ".summary-card"
            );


        if (cards.length < 3) {
            return;
        }


        const notificationValue =
            cards[2].querySelector("strong");


        if (notificationValue) {

            notificationValue.textContent =
                Number(count || 0);

        }

    }


    function updateMessageCount(count) {

        const cards =
            document.querySelectorAll(
                ".summary-card"
            );


        if (cards.length < 4) {
            return;
        }


        const messageValue =
            cards[3].querySelector("strong");


        if (messageValue) {

            messageValue.textContent =
                Number(count || 0);

        }

    }


    function renderRecentOrders(orders) {

        const container =
            document.querySelector(
                ".dashboard-orders-list"
            );


        if (!container) {
            return;
        }


        if (
            !orders ||
            orders.length === 0
        ) {

            container.innerHTML = `
                <div class="empty-state">
                    No orders yet
                </div>
            `;

            return;

        }


        container.innerHTML =
            orders.map(
                function (order) {

                    const status =
                        order.status || "Pending";


                    const total =
                        Number(
                            order.total_amount || 0
                        ).toFixed(2);


                    const queue =
                        order.queue_position !== null &&
                        order.queue_position !== undefined
                            ? `#${Number(order.queue_position)}`
                            : "";


                    return `
                        <div class="dashboard-order-item">

                            <div class="dashboard-order-info">

                                <strong>
                                    Order #${escapeHtml(
                                        order.order_number
                                    )}
                                </strong>

                                <span>
                                    ${escapeHtml(status)}
                                    ${queue ? ` • Queue ${queue}` : ""}
                                </span>

                            </div>


                            <div class="dashboard-order-total">

                                ₱${total}

                            </div>

                        </div>
                    `;

                }
            ).join("");

    }


    function escapeHtml(value) {

        const div =
            document.createElement("div");


        div.textContent =
            value ?? "";


        return div.innerHTML;

    }


    loadDashboard();



    setInterval(
        loadDashboard,
        5000
    );

});