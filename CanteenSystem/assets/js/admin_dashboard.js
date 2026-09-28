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

    const refreshButton =
        document.getElementById("refreshButton");

    const ordersContainer =
        document.getElementById("ordersContainer");


    const pendingCount =
        document.getElementById("pendingCount");

    const preparingCount =
        document.getElementById("preparingCount");

    const readyCount =
        document.getElementById("readyCount");

    const completedCount =
        document.getElementById("completedCount");


    let isLoadingOrders = false;




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

        sidebar.classList.remove("open");

        overlay.classList.remove("show");

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




    async function loadOrders() {

        if (isLoadingOrders) {
            return;
        }


        isLoadingOrders = true;


        if (refreshButton) {

            refreshButton.disabled = true;

            refreshButton.textContent =
                "↻ Loading...";

        }


        try {

            const response =
                await fetch(
                    "../api/admin_orders.php",
                    {
                        method: "GET",
                        credentials: "same-origin",
                        cache: "no-store"
                    }
                );


            if (!response.ok) {

                throw new Error(
                    "HTTP " +
                    response.status
                );

            }


            const data =
                await response.json();


            if (!data.success) {

                ordersContainer.innerHTML = `
                    <div class="empty-state">
                        ${escapeHtml(
                            data.message ||
                            "Unable to load orders."
                        )}
                    </div>
                `;

                updateSummary([]);

                return;

            }


            const orders =
                Array.isArray(data.orders)
                    ? data.orders
                    : [];


            updateSummary(
                orders
            );


            renderOrders(
                orders
            );


        } catch (error) {

            console.error(
                "Dashboard orders error:",
                error
            );


            ordersContainer.innerHTML = `
                <div class="empty-state">
                    Unable to connect to the server.
                </div>
            `;

        } finally {

            isLoadingOrders = false;


            if (refreshButton) {

                refreshButton.disabled = false;

                refreshButton.textContent =
                    "↻ Refresh";

            }

        }

    }


    function updateSummary(orders) {

        let pending = 0;

        let preparing = 0;

        let ready = 0;

        let completed = 0;


        orders.forEach(function (order) {

            switch (order.status) {

                case "Pending":
                    pending++;
                    break;

                case "Preparing":
                    preparing++;
                    break;

                case "Ready":
                    ready++;
                    break;

                case "Completed":
                    completed++;
                    break;

            }

        });


        pendingCount.textContent =
            pending;

        preparingCount.textContent =
            preparing;

        readyCount.textContent =
            ready;

        completedCount.textContent =
            completed;

    }




    function renderOrders(orders) {

        if (
            !orders ||
            orders.length === 0
        ) {

            ordersContainer.innerHTML = `
                <div class="empty-state">
                    No orders have been received yet.
                </div>
            `;

            return;

        }


        ordersContainer.innerHTML = "";


        orders.forEach(function (order) {

            const card =
                document.createElement("article");


            card.className =
                "order-card";


            if (order.status === "Pending") {

                card.classList.add(
                    "priority"
                );

            }


            const statusClass =
                "status-" +
                String(
                    order.status || ""
                ).toLowerCase();


            let itemsHtml = "";


            const items =
                Array.isArray(order.items)
                    ? order.items
                    : [];


            items.forEach(
                function (item) {

                    itemsHtml += `
                        <div class="order-item">

                            <span class="item-name">

                                ${escapeHtml(
                                    item.name
                                )}

                                <span class="item-quantity">
                                    × ${Number(
                                        item.quantity || 0
                                    )}
                                </span>

                            </span>

                            <span class="item-price">
                                ₱${Number(
                                    item.subtotal || 0
                                ).toFixed(2)}
                            </span>

                        </div>
                    `;

                }
            );


            const queueText =
                order.queue_position !== null &&
                order.queue_position !== undefined
                    ? "#" + order.queue_position
                    : "—";


            const orderDate =
                formatDate(
                    order.created_at
                );


            const actionsHtml =
                getActions(
                    order
                );


            card.innerHTML = `

                <div class="order-header">

                    <div>

                        <h3 class="order-number">
                            ${escapeHtml(
                                order.order_number
                            )}
                        </h3>

                        <p class="student-name">

                            ${escapeHtml(
                                order.student_name ||
                                "Unknown Student"
                            )}

                            ·

                            ${escapeHtml(
                                order.student_number ||
                                ""
                            )}

                        </p>

                    </div>


                    <span
                        class="status-badge ${statusClass}"
                    >

                        ${escapeHtml(
                            order.status
                        )}

                    </span>

                </div>


                <div class="order-body">


                    <div class="order-items">

                        ${itemsHtml}

                    </div>


                    <div class="order-meta">

                        <span class="queue-number">
                            Queue ${queueText}
                        </span>


                        <span class="order-total">
                            ₱${Number(
                                order.total_amount || 0
                            ).toFixed(2)}
                        </span>


                        <span class="order-date">
                            ${orderDate}
                        </span>

                    </div>


                </div>


                ${actionsHtml}

            `;


            ordersContainer.appendChild(
                card
            );

        });


        attachActionButtons();

    }



    function getActions(order) {

        if (order.status === "Pending") {

            return `
                <div class="order-actions">

                    <button
                        type="button"
                        class="action-button prepare-button"
                        data-order-id="${order.id}"
                        data-status="Preparing"
                    >
                        Start Preparing
                    </button>


                    <button
                        type="button"
                        class="action-button cancel-button"
                        data-order-id="${order.id}"
                        data-status="Cancelled"
                    >
                        Cancel
                    </button>

                </div>
            `;

        }


        if (order.status === "Preparing") {

            return `
                <div class="order-actions">

                    <button
                        type="button"
                        class="action-button ready-button"
                        data-order-id="${order.id}"
                        data-status="Ready"
                    >
                        Mark Ready
                    </button>

                </div>
            `;

        }


        if (order.status === "Ready") {

            return `
                <div class="order-actions">

                    <button
                        type="button"
                        class="action-button complete-button"
                        data-order-id="${order.id}"
                        data-status="Completed"
                    >
                        Complete Order
                    </button>

                </div>
            `;

        }


        return "";

    }


    function attachActionButtons() {

        const buttons =
            document.querySelectorAll(
                ".action-button"
            );


        buttons.forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    updateOrderStatus(
                        button
                    );

                }
            );

        });

    }



    async function updateOrderStatus(button) {

        const orderId =
            Number(
                button.dataset.orderId
            );


        const status =
            button.dataset.status;


        if (!orderId || !status) {
            return;
        }


        if (status === "Cancelled") {

            const confirmed =
                confirm(
                    "Are you sure you want to cancel this order?"
                );


            if (!confirmed) {
                return;
            }

        }


        const originalText =
            button.textContent;


        button.disabled =
            true;


        button.textContent =
            "Updating...";


        try {

            const response =
                await fetch(
                    "../api/update_order_status.php",
                    {
                        method: "POST",

                        credentials: "same-origin",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        body: JSON.stringify({

                            order_id:
                                orderId,

                            status:
                                status

                        })

                    }
                );


            if (!response.ok) {

                throw new Error(
                    "HTTP " +
                    response.status
                );

            }


            const data =
                await response.json();


            if (!data.success) {

                alert(
                    data.message ||
                    "Unable to update order."
                );


                button.disabled =
                    false;


                button.textContent =
                    originalText;


                return;

            }


            await loadOrders();


        } catch (error) {

            console.error(
                "Update order error:",
                error
            );


            alert(
                "Unable to connect to the server."
            );


            button.disabled =
                false;


            button.textContent =
                originalText;

        }

    }



    if (refreshButton) {

        refreshButton.addEventListener(
            "click",
            loadOrders
        );

    }




    if (logoutButton) {

        logoutButton.addEventListener(
            "click",
            async function () {

                const confirmed =
                    confirm(
                        "Are you sure you want to logout?"
                    );


                if (!confirmed) {
                    return;
                }


                logoutButton.disabled =
                    true;


                try {

                    const response =
                        await fetch(
                            "../api/logout.php",
                            {
                                method: "POST",
                                credentials: "same-origin"
                            }
                        );


                    const data =
                        await response.json();


                    if (data.success) {

                        window.location.href =
                            "../login.html";

                        return;

                    }


                    window.location.href =
                        "../login.html";


                } catch (error) {

                    console.error(
                        "Logout error:",
                        error
                    );


                    window.location.href =
                        "../login.html";

                }

            }
        );

    }




    function escapeHtml(value) {

        const div =
            document.createElement("div");


        div.textContent =
            value ?? "";


        return div.innerHTML;

    }


    function formatDate(value) {

        if (!value) {
            return "—";
        }


        const date =
            new Date(
                String(value).replace(
                    " ",
                    "T"
                )
            );


        if (
            Number.isNaN(
                date.getTime()
            )
        ) {

            return value;

        }


        return date.toLocaleString(
            "en-PH",
            {
                month: "short",
                day: "numeric",
                year: "numeric",
                hour: "numeric",
                minute: "2-digit"
            }
        );

    }



    loadOrders();



    setInterval(
        function () {

            loadOrders();

        },
        10000
    );

});