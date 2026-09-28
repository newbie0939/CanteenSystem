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

    const startDate =
        document.getElementById("startDate");

    const endDate =
        document.getElementById("endDate");

    const generateButton =
        document.getElementById(
            "generateReportButton"
        );




    const today =
        new Date()
            .toISOString()
            .split("T")[0];


    startDate.value = today;
    endDate.value = today;



    hamburger.addEventListener(
        "click",
        function () {

            sidebar.classList.add("open");
            overlay.classList.add("show");
            document.body.classList.add("menu-open");

        }
    );


    function closeSidebar() {

        sidebar.classList.remove("open");
        overlay.classList.remove("show");
        document.body.classList.remove("menu-open");

    }


    closeMenu.addEventListener(
        "click",
        closeSidebar
    );


    overlay.addEventListener(
        "click",
        closeSidebar
    );



    generateButton.addEventListener(
        "click",
        function () {

            generateReport();

        }
    );


    async function generateReport() {

        const start =
            startDate.value;

        const end =
            endDate.value;


        if (!start || !end) {

            alert(
                "Please select both dates."
            );

            return;

        }


        if (start > end) {

            alert(
                "Start date cannot be later than end date."
            );

            return;

        }


        generateButton.disabled =
            true;

        generateButton.textContent =
            "Loading...";


        try {

            const response =
                await fetch(
                    `../api/admin_reports.php?start_date=${encodeURIComponent(start)}&end_date=${encodeURIComponent(end)}`,
                    {
                        method: "GET",
                        credentials: "same-origin"
                    }
                );


            const data =
                await response.json();


            if (!data.success) {

                alert(
                    data.message ||
                    "Unable to generate report."
                );

                return;

            }


            updateSummary(
                data.summary
            );


            updateStatuses(
                data.statuses
            );


            updateFoodReport(
                data.food_items
            );


        } catch (error) {

            console.error(
                "Report error:",
                error
            );


            alert(
                "Unable to connect to the server."
            );


        } finally {

            generateButton.disabled =
                false;

            generateButton.textContent =
                "Generate Report";

        }

    }




    function updateSummary(
        summary
    ) {

        document.getElementById(
            "totalOrders"
        ).textContent =
            summary.total_orders ?? 0;


        document.getElementById(
            "completedOrders"
        ).textContent =
            summary.completed_orders ?? 0;


        document.getElementById(
            "cancelledOrders"
        ).textContent =
            summary.cancelled_orders ?? 0;


        document.getElementById(
            "totalSales"
        ).textContent =
            formatCurrency(
                summary.total_sales ?? 0
            );

    }




    function updateStatuses(
        statuses
    ) {

        document.getElementById(
            "pendingCount"
        ).textContent =
            statuses.Pending ?? 0;


        document.getElementById(
            "preparingCount"
        ).textContent =
            statuses.Preparing ?? 0;


        document.getElementById(
            "readyCount"
        ).textContent =
            statuses.Ready ?? 0;


        document.getElementById(
            "completedStatusCount"
        ).textContent =
            statuses.Completed ?? 0;


        document.getElementById(
            "cancelledStatusCount"
        ).textContent =
            statuses.Cancelled ?? 0;

    }


    function updateFoodReport(
        foodItems
    ) {

        const container =
            document.getElementById(
                "foodReport"
            );


        container.innerHTML = "";


        if (
            !foodItems ||
            foodItems.length === 0
        ) {

            container.innerHTML = `
                <div class="empty-report">
                    <div>🍽️</div>
                    <h4>No completed orders</h4>
                    <p>
                        There are no completed food orders
                        for the selected date range.
                    </p>
                </div>
            `;

            return;

        }


        foodItems.forEach(
            function (item, index) {

                const row =
                    document.createElement(
                        "div"
                    );


                row.className =
                    "food-report-row";


                row.innerHTML = `

                    <div class="food-rank">
                        ${index + 1}
                    </div>

                    <div class="food-report-info">

                        <strong>
                            ${escapeHtml(item.name)}
                        </strong>

                        <span>
                            ${item.quantity_sold} item${item.quantity_sold == 1 ? "" : "s"} sold
                        </span>

                    </div>

                    <strong class="food-report-sales">
                        ${formatCurrency(item.sales)}
                    </strong>

                `;


                container.appendChild(
                    row
                );

            }
        );

    }



    function formatCurrency(
        amount
    ) {

        return "₱" +
            Number(amount || 0)
                .toLocaleString(
                    "en-PH",
                    {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );

    }




    function escapeHtml(
        value
    ) {

        const div =
            document.createElement(
                "div"
            );

        div.textContent =
            value ?? "";

        return div.innerHTML;

    }



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

});