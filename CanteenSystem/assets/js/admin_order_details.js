document.addEventListener("DOMContentLoaded", () => {

    const sidebar = document.getElementById("sidebar");
    const hamburger = document.getElementById("hamburger");
    const closeMenu = document.getElementById("closeMenu");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    const logoutButton = document.getElementById("logoutButton");

    function openSidebar() {
        if (sidebar) {
            sidebar.classList.add("open");
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.add("show");
        }
    }

    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove("open");
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.remove("show");
        }
    }

    if (hamburger) {
        hamburger.addEventListener("click", openSidebar);
    }

    if (closeMenu) {
        closeMenu.addEventListener("click", closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", closeSidebar);
    }

    document.querySelectorAll(".nav-item").forEach((link) => {
        link.addEventListener("click", () => {
            closeSidebar();
        });
    });

    async function updateOrderStatus(status) {

        const orderId = window.orderDetailsId;

        if (!orderId) {
            alert("Order ID is missing.");
            return;
        }

        try {

            const response = await fetch(
                "../api/update_order_status.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: status
                    })
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || "Failed to update order status."
                );
            }

            alert(
                data.message ||
                "Order status updated successfully."
            );

            window.location.reload();

        } catch (error) {

            console.error(error);

            alert(
                error.message ||
                "Unable to update the order."
            );

        }

    }

    const startPreparingButton =
        document.getElementById(
            "startPreparingButton"
        );

    const markReadyButton =
        document.getElementById(
            "markReadyButton"
        );

    const completeOrderButton =
        document.getElementById(
            "completeOrderButton"
        );

    const cancelOrderButton =
        document.getElementById(
            "cancelOrderButton"
        );

    if (startPreparingButton) {

        startPreparingButton.addEventListener(
            "click",
            () => {

                const confirmed = confirm(
                    "Start preparing this order?"
                );

                if (!confirmed) {
                    return;
                }

                updateOrderStatus("Preparing");

            }
        );

    }

    if (markReadyButton) {

        markReadyButton.addEventListener(
            "click",
            () => {

                const confirmed = confirm(
                    "Mark this order as ready?"
                );

                if (!confirmed) {
                    return;
                }

                updateOrderStatus("Ready");

            }
        );

    }

    if (completeOrderButton) {

        completeOrderButton.addEventListener(
            "click",
            () => {

                const confirmed = confirm(
                    "Mark this order as completed?"
                );

                if (!confirmed) {
                    return;
                }

                updateOrderStatus("Completed");

            }
        );

    }

    if (cancelOrderButton) {

        cancelOrderButton.addEventListener(
            "click",
            () => {

                const confirmed = confirm(
                    "Cancel this order?"
                );

                if (!confirmed) {
                    return;
                }

                updateOrderStatus("Cancelled");

            }
        );

    }

    if (logoutButton) {

        logoutButton.addEventListener(
            "click",
            async () => {

                const confirmed = confirm(
                    "Are you sure you want to logout?"
                );

                if (!confirmed) {
                    return;
                }

                try {

                    const response = await fetch(
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

                        return;

                    }

                    alert(
                        data.message ||
                        "Logout failed."
                    );

                } catch (error) {

                    console.error(error);

                    window.location.href =
                        "../login.html";

                }

            }
        );

    }

});