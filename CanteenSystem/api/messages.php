<?php

session_start();

header("Content-Type: application/json");

require_once "db_connect.php";




if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_type"])
) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "You must be logged in."
    ]);

    exit;
}


$user_id =
    (int) $_SESSION["user_id"];

$user_type =
    $_SESSION["user_type"];




if (
    $user_type !== "student" &&
    $user_type !== "admin"
) {
    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Invalid user type."
    ]);

    exit;
}



if ($user_type === "student") {

    $stmt = $conn->prepare("
        SELECT
            m.id,
            m.sender_type,
            m.sender_id,
            m.receiver_type,
            m.receiver_id,
            m.order_id,
            m.message,
            m.is_read,
            m.created_at,

            CASE
                WHEN m.sender_type = 'student'
                    THEN s_sender.name
                WHEN m.sender_type = 'admin'
                    THEN a_sender.name
            END AS sender_name,

            CASE
                WHEN m.receiver_type = 'student'
                    THEN s_receiver.name
                WHEN m.receiver_type = 'admin'
                    THEN a_receiver.name
            END AS receiver_name,

            o.order_number

        FROM messages m

        LEFT JOIN students s_sender
            ON m.sender_type = 'student'
            AND m.sender_id = s_sender.id

        LEFT JOIN admins a_sender
            ON m.sender_type = 'admin'
            AND m.sender_id = a_sender.id

        LEFT JOIN students s_receiver
            ON m.receiver_type = 'student'
            AND m.receiver_id = s_receiver.id

        LEFT JOIN admins a_receiver
            ON m.receiver_type = 'admin'
            AND m.receiver_id = a_receiver.id

        LEFT JOIN orders o
            ON m.order_id = o.id

        WHERE
            (
                (
                    m.sender_type = 'student'
                    AND m.sender_id = ?
                    AND m.student_deleted = 0
                )

                OR

                (
                    m.receiver_type = 'student'
                    AND m.receiver_id = ?
                    AND m.student_deleted = 0
                )
            )

        ORDER BY
            m.created_at ASC,
            m.id ASC
    ");


    $stmt->bind_param(
        "ii",
        $user_id,
        $user_id
    );

}


else {

    $stmt = $conn->prepare("
        SELECT
            m.id,
            m.sender_type,
            m.sender_id,
            m.receiver_type,
            m.receiver_id,
            m.order_id,
            m.message,
            m.is_read,
            m.created_at,

            CASE
                WHEN m.sender_type = 'student'
                    THEN s_sender.name
                WHEN m.sender_type = 'admin'
                    THEN a_sender.name
            END AS sender_name,

            CASE
                WHEN m.receiver_type = 'student'
                    THEN s_receiver.name
                WHEN m.receiver_type = 'admin'
                    THEN a_receiver.name
            END AS receiver_name,

            o.order_number

        FROM messages m

        LEFT JOIN students s_sender
            ON m.sender_type = 'student'
            AND m.sender_id = s_sender.id

        LEFT JOIN admins a_sender
            ON m.sender_type = 'admin'
            AND m.sender_id = a_sender.id

        LEFT JOIN students s_receiver
            ON m.receiver_type = 'student'
            AND m.receiver_id = s_receiver.id

        LEFT JOIN admins a_receiver
            ON m.receiver_type = 'admin'
            AND m.receiver_id = a_receiver.id

        LEFT JOIN orders o
            ON m.order_id = o.id

        WHERE
            (
                (
                    m.sender_type = 'admin'
                    AND m.sender_id = ?
                    AND m.admin_deleted = 0
                )

                OR

                (
                    m.receiver_type = 'admin'
                    AND m.receiver_id = ?
                    AND m.admin_deleted = 0
                )
            )

        ORDER BY
            m.created_at ASC,
            m.id ASC
    ");


    $stmt->bind_param(
        "ii",
        $user_id,
        $user_id
    );

}



if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to retrieve messages."
    ]);

    exit;
}


$result =
    $stmt->get_result();


$messages = [];




while ($row = $result->fetch_assoc()) {

    $messages[] = [

        "id" =>
            (int) $row["id"],

        "sender_type" =>
            $row["sender_type"],

        "sender_id" =>
            (int) $row["sender_id"],

        "receiver_type" =>
            $row["receiver_type"],

        "receiver_id" =>
            (int) $row["receiver_id"],

        "sender_name" =>
            $row["sender_name"] ?? "Unknown",

        "receiver_name" =>
            $row["receiver_name"] ?? "Unknown",

        "order_id" =>
            $row["order_id"] !== null
                ? (int) $row["order_id"]
                : null,

        "order_number" =>
            $row["order_number"] ?? null,

        "message" =>
            $row["message"],

        "is_read" =>
            (int) $row["is_read"],

        "created_at" =>
            $row["created_at"]

    ];

}




if ($user_type === "student") {

    $unread_stmt = $conn->prepare("
        SELECT COUNT(*) AS unread_count
        FROM messages
        WHERE
            receiver_type = 'student'
            AND receiver_id = ?
            AND student_deleted = 0
            AND is_read = 0
    ");

}

else {

    $unread_stmt = $conn->prepare("
        SELECT COUNT(*) AS unread_count
        FROM messages
        WHERE
            receiver_type = 'admin'
            AND receiver_id = ?
            AND admin_deleted = 0
            AND is_read = 0
    ");

}


$unread_stmt->bind_param(
    "i",
    $user_id
);


$unread_stmt->execute();


$unread_result =
    $unread_stmt->get_result();


$unread_data =
    $unread_result->fetch_assoc();


$unread_count =
    (int) (
        $unread_data["unread_count"] ?? 0
    );




echo json_encode([

    "success" => true,

    "messages" =>
        $messages,

    "unread_count" =>
        $unread_count

]);