document.addEventListener("DOMContentLoaded", function () {

    const sidebar =
        document.getElementById("sidebar");

    const hamburger =
        document.getElementById("hamburger");

    const closeMenu =
        document.getElementById("closeMenu");

    const overlay =
        document.getElementById("sidebarOverlay");


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


    const logoutButton =
        document.getElementById("logoutButton");


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


    const pendingQueue =
        document.getElementById("pendingQueue");

    const preparingQueue =
        document.getElementById("preparingQueue");

    const readyQueue =
        document.getElementById("readyQueue");


    const pendingCount =
        document.getElementById("pendingCount");

    const preparingCount =
        document.getElementById("preparingCount");

    const readyCount =
        document.getElementById("readyCount");


    const refreshButton =
        document.getElementById(
            "queueRefreshButton"
        );


    async function loadQueue() {

        try {

            const response =
                await fetch(
                    "../api/admin_orders.php",
                    {
                        cache: "no-store"
                    }
                );


            const data =
                await response.json();


            if (!data.success) {

                throw new Error(
                    data.message ||
                    "Unable to load orders."
                );

            }


            const orders =
                data.orders || [];


            const pending =
                orders.filter(
                    function (order) {

                        return (
                            order.status ===
                            "Pending"
                        );

                    }
                );


            const preparing =
                orders.filter(
                    function (order) {

                        return (
                            order.status ===
                            "Preparing"
                        );

                    }
                );


            const ready =
                orders.filter(
                    function (order) {

                        return (
                            order.status ===
                            "Ready"
                        );

                    }
                );



            pending.sort(
                sortByQueue
            );

            preparing.sort(
                sortByQueue
            );

            ready.sort(
                sortByQueue
            );



            pendingCount.textContent =
                pending.length;

            preparingCount.textContent =
                preparing.length;

            readyCount.textContent =
                ready.length;



            renderQueue(
                pendingQueue,
                pending
            );

            renderQueue(
                preparingQueue,
                preparing
            );

            renderQueue(
                readyQueue,
                ready
            );


        } catch (error) {

            console.error(
                "Queue error:",
                error
            );


            showError(
                pendingQueue
            );

            showError(
                preparingQueue
            );

            showError(
                readyQueue
            );

        }

    }



    function sortByQueue(a, b) {

        const positionA =
            Number(
                a.queue_position ?? 999999
            );


        const positionB =
            Number(
                b.queue_position ?? 999999
            );


        return positionA - positionB;

    }


  

    function renderQueue(
        container,
        orders
    ) {

        if (!container) {
            return;
        }


        if (orders.length === 0) {

            container.innerHTML = `
                <div class="queue-empty">
                    No orders in this queue.
                </div>
            `;

            return;

        }


        container.innerHTML =
            orders.map(
                function (order) {

                    const position =
                        order.queue_position !== null &&
                        order.queue_position !== undefined
                            ? Number(
                                order.queue_position
                            )
                            : "-";


                    const orderNumber =
                        escapeHtml(
                            order.order_number
                        );


                    const total =
                        Number(
                            order.total_amount || 0
                        ).toFixed(2);


                    const createdAt =
                        formatDate(
                            order.created_at
                        );


                    return `
                        <div class="queue-item">

                            <div class="queue-position">
                                Queue #${position}
                            </div>

                            <div class="queue-order-number">
                                Order #${orderNumber}
                            </div>

                            <div class="queue-total">
                                ₱${total}
                            </div>

                            <div class="queue-date">
                                ${createdAt}
                            </div>

                        </div>
                    `;

                }
            ).join("");

    }


 

    function formatDate(value) {

        if (!value) {
            return "";
        }


        const date =
            new Date(
                value.replace(" ", "T")
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


        return date.toLocaleString();

    }



    function showError(container) {

        if (!container) {
            return;
        }


        container.innerHTML = `
            <div class="queue-empty">
                Unable to load queue.
            </div>
        `;

    }



    function escapeHtml(value) {

        const div =
            document.createElement("div");


        div.textContent =
            value ?? "";


        return div.innerHTML;

    }



    if (refreshButton) {

        refreshButton.addEventListener(
            "click",
            loadQueue
        );

    }



    loadQueue();



    setInterval(
        loadQueue,
        5000
    );

});