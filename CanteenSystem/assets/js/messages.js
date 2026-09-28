document.addEventListener("DOMContentLoaded", () => {
    loadMessages();

    const sendForm = document.getElementById("sendMessageForm");

    if (sendForm) {
        sendForm.addEventListener("submit", handleSendMessage);
    }

    const deleteAllBtn = document.getElementById("deleteAllMessagesBtn");

    if (deleteAllBtn) {
        deleteAllBtn.addEventListener("click", deleteAllMessages);
    }

    const markAllBtn = document.getElementById("markAllMessagesBtn");

    if (markAllBtn) {
        markAllBtn.addEventListener("click", markAllMessages);
    }


    if (typeof window.loadStudents === "function") {
        window.loadStudents();
    }

    if (typeof window.loadAdmins === "function") {
        window.loadAdmins();
    }


    setInterval(loadMessages, 10000);
});


async function loadMessages() {

    const container = document.getElementById("messagesContainer");

    if (!container) {
        return;
    }

    try {

        const response = await fetch("../api/messages.php", {
            method: "GET",
            credentials: "same-origin",
            cache: "no-store"
        });

        if (!response.ok) {
            throw new Error("HTTP " + response.status);
        }

        const data = await response.json();

        if (!data.success) {

            container.innerHTML = `
                <div class="empty-messages">
                    <h3>Unable to load messages</h3>
                    <p>${escapeHtml(
                        data.message || "Please try again."
                    )}</p>
                </div>
            `;

            updateUnreadCount(0);
            return;
        }

        renderMessages(data.messages || []);

        updateUnreadCount(
            Number(data.unread_count || 0)
        );

    } catch (error) {

        console.error("Load messages error:", error);

        container.innerHTML = `
            <div class="empty-messages">
                <h3>Unable to connect</h3>
                <p>Unable to connect to the server.</p>
            </div>
        `;
    }
}



function renderMessages(messages) {

    const container =
        document.getElementById("messagesContainer");

    if (!container) {
        return;
    }

    if (!messages.length) {

        container.innerHTML = `
            <div class="empty-messages">
                <h3>No messages yet</h3>
                <p>Your conversations will appear here.</p>
            </div>
        `;

        return;
    }


    container.innerHTML = messages.map(message => {

        const currentUserType =
            window.currentUserType || "";

        const currentUserId =
            Number(window.currentUserId || 0);


        const senderId =
            Number(message.sender_id || 0);

        const receiverId =
            Number(message.receiver_id || 0);


        const isReceived =
            message.receiver_type === currentUserType &&
            receiverId === currentUserId;


        const isSent =
            message.sender_type === currentUserType &&
            senderId === currentUserId;


        const unread =
            isReceived &&
            !Number(message.is_read);


        const alignmentClass =
            isSent ? "sent" : "received";


        const senderName =
            message.sender_name ||
            (
                message.sender_type === "admin"
                    ? "Admin"
                    : "Student"
            );


        const date =
            formatMessageDate(message.created_at);


        const orderHtml =
            message.order_number
                ? `
                    <div class="message-order">
                        Order #${escapeHtml(
                            message.order_number
                        )}
                    </div>
                `
                : "";


        const readButton =
            unread
                ? `
                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="markMessageRead(${Number(message.id)})"
                    >
                        Mark as Read
                    </button>
                `
                : "";


        return `
            <div
                class="message-item ${alignmentClass}"
                data-message-id="${Number(message.id)}"
            >

                <div class="message-bubble">

                    <div class="message-sender">
                        ${escapeHtml(senderName)}
                    </div>

                    <div class="message-text">
                        ${escapeHtml(message.message)}
                    </div>

                    ${orderHtml}

                    <span class="message-time">
                        ${escapeHtml(date)}
                    </span>

                    <div
                        class="message-actions"
                        style="
                            margin-top: 10px;
                            display: flex;
                            gap: 7px;
                            justify-content: flex-end;
                            flex-wrap: wrap;
                        "
                    >

                        ${readButton}

                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick="deleteMessage(${Number(message.id)})"
                        >
                            Delete
                        </button>

                    </div>

                </div>

            </div>
        `;

    }).join("");
}



async function handleSendMessage(event) {

    event.preventDefault();

    const form = event.currentTarget;

    const receiverIdInput =
        form.querySelector('[name="receiver_id"]');

    const receiverTypeInput =
        form.querySelector('[name="receiver_type"]');

    const orderIdInput =
        form.querySelector('[name="order_id"]');

    const messageInput =
        form.querySelector('[name="message"]');


    if (
        !receiverIdInput ||
        !receiverTypeInput ||
        !messageInput
    ) {

        showMessageAlert(
            "The message form is missing required fields.",
            "error"
        );

        return;
    }


    const receiverId =
        Number(receiverIdInput.value);

    const receiverType =
        receiverTypeInput.value.trim();

    const message =
        messageInput.value.trim();


    const orderId =
        orderIdInput &&
        orderIdInput.value
            ? Number(orderIdInput.value)
            : null;


    if (!receiverId) {

        showMessageAlert(
            "Please select a recipient.",
            "error"
        );

        return;
    }


    if (!message) {

        showMessageAlert(
            "Please enter a message.",
            "error"
        );

        return;
    }


    const submitButton =
        form.querySelector(
            'button[type="submit"]'
        );


    if (submitButton) {

        submitButton.disabled = true;

        submitButton.dataset.originalText =
            submitButton.textContent;

        submitButton.textContent =
            "Sending...";
    }


    try {

        const response =
            await fetch(
                "../api/send_message.php",
                {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        receiver_id: receiverId,
                        receiver_type: receiverType,
                        order_id: orderId,
                        message: message
                    })
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

            showMessageAlert(
                data.message ||
                "Failed to send message.",
                "error"
            );

            return;
        }


        messageInput.value = "";


        if (orderIdInput) {
            orderIdInput.value = "";
        }


        showMessageAlert(
            "Message sent successfully.",
            "success"
        );


        await loadMessages();

    } catch (error) {

        console.error(
            "Send message error:",
            error
        );

        showMessageAlert(
            "Unable to connect to the server.",
            "error"
        );

    } finally {

        if (submitButton) {

            submitButton.disabled = false;

            submitButton.textContent =
                submitButton.dataset.originalText ||
                "Send Message";
        }
    }
}



async function markMessageRead(messageId) {

    if (!messageId) {
        return;
    }


    try {

        const response =
            await fetch(
                "../api/message_read.php",
                {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        message_id: Number(messageId)
                    })
                }
            );


        const data =
            await response.json();


        if (!data.success) {

            showMessageAlert(
                data.message ||
                "Failed to mark message as read.",
                "error"
            );

            return;
        }


        await loadMessages();

    } catch (error) {

        console.error(
            "Mark message read error:",
            error
        );
    }
}



async function markAllMessages() {

    try {

        const response =
            await fetch(
                "../api/message_read.php",
                {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        mark_all: true
                    })
                }
            );


        const data =
            await response.json();


        if (!data.success) {

            showMessageAlert(
                data.message ||
                "Failed to mark messages as read.",
                "error"
            );

            return;
        }


        await loadMessages();


        showMessageAlert(
            "All messages marked as read.",
            "success"
        );

    } catch (error) {

        console.error(
            "Mark all messages error:",
            error
        );

        showMessageAlert(
            "Unable to connect to the server.",
            "error"
        );
    }
}



async function deleteMessage(messageId) {

    if (!messageId) {
        return;
    }


    const confirmed =
        confirm(
            "Are you sure you want to delete this message?"
        );


    if (!confirmed) {
        return;
    }


    try {

        const response =
            await fetch(
                "../api/message_delete.php",
                {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        message_id: Number(messageId)
                    })
                }
            );


        const data =
            await response.json();


        if (!data.success) {

            showMessageAlert(
                data.message ||
                "Failed to delete message.",
                "error"
            );

            return;
        }


        await loadMessages();


        showMessageAlert(
            "Message deleted.",
            "success"
        );

    } catch (error) {

        console.error(
            "Delete message error:",
            error
        );

        showMessageAlert(
            "Unable to connect to the server.",
            "error"
        );
    }
}


async function deleteAllMessages() {

    const confirmed =
        confirm(
            "Are you sure you want to delete all messages?"
        );


    if (!confirmed) {
        return;
    }


    try {

        const response =
            await fetch(
                "../api/message_delete.php",
                {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        delete_all: true
                    })
                }
            );


        const data =
            await response.json();


        if (!data.success) {

            showMessageAlert(
                data.message ||
                "Failed to delete messages.",
                "error"
            );

            return;
        }


        await loadMessages();


        showMessageAlert(
            "All messages deleted.",
            "success"
        );

    } catch (error) {

        console.error(
            "Delete all messages error:",
            error
        );

        showMessageAlert(
            "Unable to connect to the server.",
            "error"
        );
    }
}




function updateUnreadCount(count) {

    const number =
        Number(count || 0);


    const elements =
        document.querySelectorAll(
            ".message-unread-count"
        );


    elements.forEach(element => {

        element.textContent =
            number;


        if (number > 0) {
            element.style.display = "inline-flex";
        } else {
            element.style.display = "none";
        }

    });
}



function showMessageAlert(
    message,
    type = "success"
) {

    let alertBox =
        document.getElementById(
            "messageAlert"
        );


    if (!alertBox) {

        alertBox =
            document.createElement("div");

        alertBox.id =
            "messageAlert";

        alertBox.className =
            "message-alert";

        const container =
            document.querySelector(
                ".messages-card"
            );

        if (container) {
            container.prepend(alertBox);
        } else {
            document.body.prepend(alertBox);
        }
    }


    alertBox.className =
        `message-alert ${type}`;


    alertBox.textContent =
        message;


    alertBox.style.display =
        "block";


    clearTimeout(
        window.messageAlertTimer
    );


    window.messageAlertTimer =
        setTimeout(() => {

            alertBox.style.display =
                "none";

        }, 3000);
}




function formatMessageDate(dateString) {

    if (!dateString) {
        return "";
    }


    const date =
        new Date(
            String(dateString)
                .replace(" ", "T")
        );


    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return dateString;
    }


    return date.toLocaleString(
        [],
        {
            month: "short",
            day: "numeric",
            year: "numeric",
            hour: "numeric",
            minute: "2-digit"
        }
    );
}




function escapeHtml(value) {

    const div =
        document.createElement("div");


    div.textContent =
        value === null ||
        value === undefined
            ? ""
            : String(value);


    return div.innerHTML;
}