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

    const ordersList =
        document.getElementById("ordersList");

    const filterButtons =
        document.querySelectorAll(".filter-button");


    let orders = [];

    let currentFilter = "All";

    let isLoading = false;




    hamburger.addEventListener(
        "click",
        function () {

            sidebar.classList.add("open");

            overlay.classList.add("show");

            document.body.classList.add(
                "menu-open"
            );

        }
    );


    function closeSidebar() {

        sidebar.classList.remove("open");

        overlay.classList.remove("show");

        document.body.classList.remove(
            "menu-open"
        );

    }


    closeMenu.addEventListener(
        "click",
        closeSidebar
    );


    overlay.addEventListener(
        "click",
        closeSidebar
    );



    async function loadOrders() {

        if (isLoading) {
            return;
        }


        isLoading = true;


        refreshButton.disabled = true;

        refreshButton.textContent =
            "↻ Loading...";


        if (orders.length === 0) {

            ordersList.innerHTML = `
                <div class="loading-state">
                    Loading orders...
                </div>
            `;

        }


        try {

            const response =
                await fetch(
                    "../api/admin_orders.php",
                    {
                        method: "GET",
                        cache: "no-store",
                        credentials: "same-origin"
                    }
                );


            if (!response.ok) {

                throw new Error(
                    "Server returned " +
                    response.status
                );

            }


            const data =
                await response.json();


            if (!data.success) {

                ordersList.innerHTML = `
                    <div class="empty-state">
                        ${escapeHtml(
                            data.message ||
                            "Unable to load orders."
                        )}
                    </div>
                `;

                return;

            }


            orders =
                Array.isArray(data.orders)
                    ? data.orders
                    : [];




            orders.sort(function (a, b) {

                const statusOrder = {

                    "Pending": 1,
                    "Preparing": 2,
                    "Ready": 3,
                    "Completed": 4,
                    "Cancelled": 5

                };


                const statusA =
                    statusOrder[a.status] || 99;

                const statusB =
                    statusOrder[b.status] || 99;


                if (statusA !== statusB) {

                    return statusA - statusB;

                }


                const positionA =
                    a.queue_position === null
                        ? Number.MAX_SAFE_INTEGER
                        : Number(a.queue_position);

                const positionB =
                    b.queue_position === null
                        ? Number.MAX_SAFE_INTEGER
                        : Number(b.queue_position);


                if (positionA !== positionB) {

                    return positionA - positionB;

                }


                return (
                    Number(a.id) -
                    Number(b.id)
                );

            });


            renderOrders();


        } catch (error) {

            console.error(
                "Load orders error:",
                error
            );


            ordersList.innerHTML = `
                <div class="empty-state">
                    Unable to connect to the server.
                </div>
            `;

        } finally {

            isLoading = false;

            refreshButton.disabled = false;

            refreshButton.textContent =
                "↻ Refresh";

        }

    }




    function renderOrders() {

        let filteredOrders;


        if (currentFilter === "All") {

            filteredOrders =
                orders;

        } else {

            filteredOrders =
                orders.filter(
                    function (order) {

                        return (
                            order.status ===
                            currentFilter
                        );

                    }
                );

        }


        if (
            filteredOrders.length === 0
        ) {

            const message =
                currentFilter === "All"
                    ? "No orders found."
                    : "No " +
                      currentFilter.toLowerCase() +
                      " orders found.";


            ordersList.innerHTML = `
                <div class="empty-state">
                    ${escapeHtml(message)}
                </div>
            `;

            return;

        }


        ordersList.innerHTML = "";


        filteredOrders.forEach(
            function (order) {

                const card =
                    document.createElement(
                        "article"
                    );


                card.className =
                    "order-card";


                card.classList.add(
                    "status-card-" +
                    order.status.toLowerCase()
                );


                if (
                    order.status ===
                    "Pending"
                ) {

                    card.classList.add(
                        "pending"
                    );

                }




                let itemsHtml = "";


                if (
                    Array.isArray(
                        order.items
                    ) &&
                    order.items.length > 0
                ) {

                    order.items.forEach(
                        function (item) {

                            itemsHtml += `

                                <div class="order-item">

                                    <div class="item-left">

                                        <span class="item-quantity">
                                            ${Number(
                                                item.quantity
                                            )}×
                                        </span>

                                        <span class="item-name">
                                            ${escapeHtml(
                                                item.name
                                            )}
                                        </span>

                                    </div>

                                    <span class="item-price">
                                        ₱${Number(
                                            item.subtotal
                                        ).toFixed(2)}
                                    </span>

                                </div>

                            `;

                        }
                    );

                } else {

                    itemsHtml = `
                        <div class="no-items">
                            No order items found.
                        </div>
                    `;

                }



                const statusClass =
                    "status-" +
                    order.status.toLowerCase();


                let queue = "—";


                if (
                    
                    order.queue_position !== null
                ) {

                    queue =
                        "#" +
                        Number(
                            order.queue_position
                        );

                }



                card.innerHTML = `

                    <div class="order-header">

                        <div>

                            <h3 class="order-number">
                                ${escapeHtml(
                                    order.order_number
                                )}
                            </h3>

                            <p class="student-info">
                                ${escapeHtml(
                                    order.student_name
                                )}
                                ·
                                Student ID:
                                ${escapeHtml(
                                    order.student_number
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


                        <div class="order-information">

                            <div class="info-box">

                                <span>
                                    Queue Position
                                </span>

                                <strong>
                                    ${queue}
                                </strong>

                            </div>


                            <div class="info-box">

                                <span>
                                    Total Amount
                                </span>

                                <strong>
                                    ₱${Number(
                                        order.total_amount
                                    ).toFixed(2)}
                                </strong>

                            </div>


                            <div class="info-box">

                                <span>
                                    Number of Items
                                </span>

                                <strong>
                                    ${getItemCount(
                                        order.items
                                    )}
                                </strong>

                            </div>

                        </div>


                        <h4 class="items-title">
                            Order Items
                        </h4>


                        <div class="order-items">

                            ${itemsHtml}

                        </div>


                    </div>


                    <div class="order-footer">

                        <span class="order-date">
                            ${formatDate(
                                order.created_at
                            )}
                        </span>


                        <div class="order-actions">

                            <button
                                type="button"
                                class="action-button details-button"
                                data-order-id="${Number(
                                    order.id
                                )}"
                            >
                                View Details
                            </button>

                            ${getActions(
                                order
                            )}

                        </div>

                    </div>

                `;


                ordersList.appendChild(
                    card
                );

            }
        );


        attachActionButtons();

        attachDetailsButtons();

    }



    function getActions(order) {



        if (
            order.status === "Pending"
        ) {

            const queuePosition =
                order.queue_position !== null
                    ? Number(
                        order.queue_position
                    )
                    : null;



            if (queuePosition === 1) {

                return `

                    <button
                        type="button"
                        class="action-button prepare-button"
                        data-order-id="${Number(
                            order.id
                        )}"
                        data-status="Preparing"
                    >
                        Process Order
                    </button>

                    <button
                        type="button"
                        class="action-button cancel-button"
                        data-order-id="${Number(
                            order.id
                        )}"
                        data-status="Cancelled"
                    >
                        Cancel
                    </button>

                `;

            }


            return `

                <button
                    type="button"
                    class="action-button waiting-button"
                    disabled
                >
                    Waiting for Queue #1
                </button>

                <button
                    type="button"
                    class="action-button cancel-button"
                    data-order-id="${Number(
                        order.id
                    )}"
                    data-status="Cancelled"
                >
                    Cancel
                </button>

            `;

        }




        if (
            order.status === "Preparing"
        ) {

            const queuePosition =
                order.queue_position !== null
                    ? Number(
                        order.queue_position
                    )
                    : null;




            if (queuePosition === 1) {

                return `

                    <button
                        type="button"
                        class="action-button ready-button"
                        data-order-id="${Number(
                            order.id
                        )}"
                        data-status="Ready"
                    >
                        Mark Ready
                    </button>

                    <button
                        type="button"
                        class="action-button cancel-button"
                        data-order-id="${Number(
                            order.id
                        )}"
                        data-status="Cancelled"
                    >
                        Cancel
                    </button>

                `;

            }


            return `

                <button
                    type="button"
                    class="action-button waiting-button"
                    disabled
                >
                    Waiting for Queue #1
                </button>

                <button
                    type="button"
                    class="action-button cancel-button"
                    data-order-id="${Number(
                        order.id
                    )}"
                    data-status="Cancelled"
                >
                    Cancel
                </button>

            `;

        }




        if (
            order.status === "Ready"
        ) {

            const queuePosition =
                order.queue_position !== null
                    ? Number(
                        order.queue_position
                    )
                    : null;



            if (queuePosition === 1) {

                return `

                    <button
                        type="button"
                        class="action-button complete-button"
                        data-order-id="${Number(
                            order.id
                        )}"
                        data-status="Completed"
                    >
                        Complete Order
                    </button>

                    <button
                        type="button"
                        class="action-button cancel-button"
                        data-order-id="${Number(
                            order.id
                        )}"
                        data-status="Cancelled"
                    >
                        Cancel
                    </button>

                `;

            }


            return `

                <button
                    type="button"
                    class="action-button waiting-button"
                    disabled
                >
                    Waiting for Queue #1
                </button>

                <button
                    type="button"
                    class="action-button cancel-button"
                    data-order-id="${Number(
                        order.id
                    )}"
                    data-status="Cancelled"
                >
                    Cancel
                </button>

            `;

        }




        if (
            order.status === "Completed" ||
            order.status === "Cancelled"
        ) {

            return `

                <button
                    type="button"
                    class="action-button delete-button"
                    data-order-id="${Number(
                        order.id
                    )}"
                >
                    Delete
                </button>

            `;

        }


        return "";

    }



    function attachDetailsButtons() {

        const buttons =
            document.querySelectorAll(
                ".details-button"
            );


        buttons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        const orderId =
                            Number(
                                button.dataset.orderId
                            );


                        if (!orderId) {

                            alert(
                                "Invalid order ID."
                            );

                            return;

                        }


                        window.location.href =
                            "order_details.php?id=" +
                            encodeURIComponent(
                                orderId
                            );

                    }
                );

            }
        );

    }




    function attachActionButtons() {

        const buttons =
            document.querySelectorAll(
                ".action-button:not(.details-button):not(:disabled)"
            );


        buttons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        if (
                            button.classList.contains(
                                "delete-button"
                            )
                        ) {

                            deleteOrder(
                                button
                            );

                            return;

                        }


                        updateStatus(
                            button
                        );

                    }
                );

            }
        );

    }




    async function updateStatus(button) {

        const orderId =
            Number(
                button.dataset.orderId
            );


        const status =
            button.dataset.status;


        if (!orderId || !status) {

            alert(
                "Invalid order information."
            );

            return;

        }


        if (
            status === "Cancelled"
        ) {

            const confirmed =
                window.confirm(
                    "Are you sure you want to cancel this order?"
                );


            if (!confirmed) {

                return;

            }

        }


        button.disabled = true;


        const originalText =
            button.textContent;


        button.textContent =
            "Updating...";


        try {

            const response =
                await fetch(
                    "../api/update_order_status.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        credentials:
                            "same-origin",

                        body:
                            JSON.stringify({

                                order_id:
                                    orderId,

                                status:
                                    status

                            })

                    }
                );


            if (!response.ok) {

                throw new Error(
                    "Server returned " +
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
                "Update status error:",
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




    async function deleteOrder(button) {

        const orderId =
            Number(
                button.dataset.orderId
            );


        if (!orderId) {

            alert(
                "Invalid order ID."
            );

            return;

        }


        const order =
            orders.find(
                function (item) {

                    return (
                        Number(item.id) ===
                        orderId
                    );

                }
            );


        if (
            !order ||
            (
                order.status !== "Completed" &&
                order.status !== "Cancelled"
            )
        ) {

            alert(
                "Only completed or cancelled orders can be deleted."
            );

            return;

        }


        const confirmed =
            window.confirm(
                "Are you sure you want to delete order " +
                order.order_number +
                "?"
            );


        if (!confirmed) {

            return;

        }


        button.disabled = true;

        button.textContent =
            "Deleting...";


        try {

            const response =
                await fetch(
                    "../api/delete_order.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        credentials:
                            "same-origin",

                        body:
                            JSON.stringify({

                                order_id:
                                    orderId

                            })

                    }
                );


            if (!response.ok) {

                throw new Error(
                    "Server returned " +
                    response.status
                );

            }


            const data =
                await response.json();


            if (!data.success) {

                alert(
                    data.message ||
                    "Unable to delete order."
                );


                button.disabled =
                    false;

                button.textContent =
                    "Delete";

                return;

            }


            await loadOrders();


        } catch (error) {

            console.error(
                "Delete order error:",
                error
            );


            alert(
                "Unable to connect to the server."
            );


            button.disabled =
                false;

            button.textContent =
                "Delete";

        }

    }



    filterButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    filterButtons.forEach(
                        function (item) {

                            item.classList.remove(
                                "active"
                            );

                        }
                    );


                    button.classList.add(
                        "active"
                    );


                    currentFilter =
                        button.dataset.filter;


                    renderOrders();

                }
            );

        }
    );




    refreshButton.addEventListener(
        "click",
        loadOrders
    );



    logoutButton.addEventListener(
        "click",
        async function () {

            logoutButton.disabled =
                true;


            try {

                const response =
                    await fetch(
                        "../api/logout.php",
                        {
                            method: "POST",
                            credentials:
                                "same-origin"
                        }
                    );


                const data =
                    await response.json();


                if (data.success) {

                    window.location.href =
                        "../login.html";

                    return;

                }


                logoutButton.disabled =
                    false;


                alert(
                    data.message ||
                    "Unable to logout."
                );


            } catch (error) {

                console.error(
                    "Logout error:",
                    error
                );


                logoutButton.disabled =
                    false;


                alert(
                    "Unable to connect to the server."
                );

            }

        }
    );




    function escapeHtml(value) {

        const div =
            document.createElement(
                "div"
            );


        div.textContent =
            value ?? "";


        return div.innerHTML;

    }


    function getItemCount(items) {

        if (
            !Array.isArray(items)
        ) {

            return 0;

        }


        return items.reduce(
            function (total, item) {

                return (
                    total +
                    Number(
                        item.quantity || 0
                    )
                );

            },
            0
        );

    }


    function formatDate(value) {

        if (!value) {

            return "—";

        }


        const date =
            new Date(
                String(value)
                    .replace(
                        " ",
                        "T"
                    )
            );


        if (
            Number.isNaN(
                date.getTime()
            )
        ) {

            return escapeHtml(
                value
            );

        }


        return date.toLocaleString(
            "en-PH",
            {
                month:
                    "short",

                day:
                    "numeric",

                year:
                    "numeric",

                hour:
                    "numeric",

                minute:
                    "2-digit"
            }
        );

    }



    loadOrders();

});