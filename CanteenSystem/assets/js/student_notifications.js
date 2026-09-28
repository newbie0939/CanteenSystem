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

    const notificationList =
        document.getElementById("notificationList");

    const markAllButton =
        document.getElementById("markAllButton");

    const clearAllButton =
        document.getElementById("clearAllButton");

    const sidebarUnreadCount =
        document.getElementById(
            "sidebarUnreadCount"
        );




    hamburger.addEventListener(
        "click",
        function () {

            sidebar.classList.add("open");

            overlay.classList.add("show");

        }
    );


    function closeSidebar() {

        sidebar.classList.remove("open");

        overlay.classList.remove("show");

    }


    closeMenu.addEventListener(
        "click",
        closeSidebar
    );


    overlay.addEventListener(
        "click",
        closeSidebar
    );


    /* LOAD */

    async function loadNotifications() {

        notificationList.innerHTML = `
            <div class="loading-state">
                Loading notifications...
            </div>
        `;


        try {

            const response =
                await fetch(
                    "../api/notifications.php",
                    {
                        cache: "no-store"
                    }
                );


            const data =
                await response.json();


            if (!data.success) {

                notificationList.innerHTML = `
                    <div class="empty-state">
                        ${escapeHtml(
                            data.message ||
                            "Unable to load notifications."
                        )}
                    </div>
                `;

                return;

            }


            updateUnreadCount(
                data.unread_count
            );


            renderNotifications(
                data.notifications
            );


        } catch (error) {

            console.error(error);

            notificationList.innerHTML = `
                <div class="empty-state">
                    Unable to connect to the server.
                </div>
            `;

        }

    }



    function updateUnreadCount(count) {

        const unread =
            Number(count) || 0;


        sidebarUnreadCount.textContent =
            unread;


        sidebarUnreadCount.style.display =
            unread > 0
                ? "inline-block"
                : "none";

    }



    function renderNotifications(
        notifications
    ) {

        if (
            !notifications ||
            notifications.length === 0
        ) {

            notificationList.innerHTML = `
                <div class="empty-state">
                    You don't have any notifications.
                </div>
            `;

            return;

        }


        notificationList.innerHTML = "";


        notifications.forEach(
            function (notification) {

                const item =
                    document.createElement(
                        "article"
                    );


                item.className =
                    "notification-item";


                if (
                    Number(
                        notification.is_read
                    ) === 0
                ) {

                    item.classList.add(
                        "unread"
                    );

                }


                const unreadDot =
                    Number(
                        notification.is_read
                    ) === 0
                        ? `<span class="unread-dot"></span>`
                        : "";


                item.innerHTML = `

                    <div class="notification-icon">
                        🔔
                    </div>


                    <div class="notification-main">

                        <p class="notification-message">
                            ${escapeHtml(
                                notification.message
                            )}
                        </p>

                        <span class="notification-time">
                            ${formatDate(
                                notification.created_at
                            )}
                        </span>

                    </div>


                    <div class="notification-controls">

                        ${unreadDot}

                        ${
                            Number(
                                notification.is_read
                            ) === 0
                            ? `
                                <button
                                    type="button"
                                    class="notification-control read-button"
                                    data-id="${notification.id}"
                                >
                                    Mark read
                                </button>
                              `
                            : ""
                        }


                        <button
                            type="button"
                            class="notification-control delete delete-button"
                            data-id="${notification.id}"
                        >
                            Delete
                        </button>

                    </div>

                `;


                notificationList.appendChild(
                    item
                );

            }
        );


        attachButtons();

    }


    function attachButtons() {

        document
            .querySelectorAll(
                ".read-button"
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        "click",
                        function () {

                            markAsRead(
                                Number(
                                    button.dataset.id
                                )
                            );

                        }
                    );

                }
            );


        document
            .querySelectorAll(
                ".delete-button"
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        "click",
                        function () {

                            deleteNotification(
                                Number(
                                    button.dataset.id
                                )
                            );

                        }
                    );

                }
            );

    }



    async function markAsRead(
        notificationId
    ) {

        try {

            const response =
                await fetch(
                    "../api/notification_read.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        body: JSON.stringify({

                            notification_id:
                                notificationId

                        })

                    }
                );


            const data =
                await response.json();


            if (data.success) {

                loadNotifications();

            } else {

                alert(
                    data.message ||
                    "Unable to mark notification as read."
                );

            }

        } catch (error) {

            console.error(error);

            alert(
                "Unable to connect to the server."
            );

        }

    }


    /* MARK ALL */

    markAllButton.addEventListener(
        "click",
        async function () {

            try {

                const response =
                    await fetch(
                        "../api/notification_read.php",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json"
                            },

                            body: JSON.stringify({
                                mark_all: true
                            })

                        }
                    );


                const data =
                    await response.json();


                if (data.success) {

                    loadNotifications();

                } else {

                    alert(
                        data.message ||
                        "Unable to mark notifications as read."
                    );

                }

            } catch (error) {

                console.error(error);

                alert(
                    "Unable to connect to the server."
                );

            }

        }
    );


    /* DELETE ONE */

    async function deleteNotification(
        notificationId
    ) {

        try {

            const response =
                await fetch(
                    "../api/notification_delete.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        body: JSON.stringify({

                            notification_id:
                                notificationId

                        })

                    }
                );


            const data =
                await response.json();


            if (data.success) {

                loadNotifications();

            } else {

                alert(
                    data.message ||
                    "Unable to delete notification."
                );

            }

        } catch (error) {

            console.error(error);

            alert(
                "Unable to connect to the server."
            );

        }

    }


    /* CLEAR ALL */

    clearAllButton.addEventListener(
        "click",
        async function () {

            if (
                !confirm(
                    "Clear all notifications?"
                )
            ) {
                return;
            }


            try {

                const response =
                    await fetch(
                        "../api/notification_delete.php",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json"
                            },

                            body: JSON.stringify({
                                delete_all: true
                            })

                        }
                    );


                const data =
                    await response.json();


                if (data.success) {

                    loadNotifications();

                } else {

                    alert(
                        data.message ||
                        "Unable to clear notifications."
                    );

                }

            } catch (error) {

                console.error(error);

                alert(
                    "Unable to connect to the server."
                );

            }

        }
    );


    /* LOGOUT */

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


    /* HELPERS */

    function escapeHtml(value) {

        const div =
            document.createElement("div");

        div.textContent =
            value ?? "";

        return div.innerHTML;

    }


    function formatDate(value) {

        const date =
            new Date(
                value.replace(" ", "T")
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


    loadNotifications();

});