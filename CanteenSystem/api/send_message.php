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


$sender_id = (int) $_SESSION["user_id"];
$sender_type = $_SESSION["user_type"];




if (
    $sender_type !== "student" &&
    $sender_type !== "admin"
) {
    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Invalid user type."
    ]);

    exit;
}




if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}



$data = json_decode(
    file_get_contents("php://input"),
    true
);


if (!is_array($data)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request data."
    ]);

    exit;
}


$receiver_id =
    (int) ($data["receiver_id"] ?? 0);

$receiver_type =
    trim($data["receiver_type"] ?? "");

$message =
    trim($data["message"] ?? "");

$order_id =
    isset($data["order_id"]) &&
    $data["order_id"] !== null &&
    $data["order_id"] !== ""
        ? (int) $data["order_id"]
        : null;



if (
    $receiver_id <= 0 ||
    (
        $receiver_type !== "student" &&
        $receiver_type !== "admin"
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid receiver."
    ]);

    exit;
}




if (
    $sender_id === $receiver_id &&
    $sender_type === $receiver_type
) {

    echo json_encode([
        "success" => false,
        "message" => "You cannot send a message to yourself."
    ]);

    exit;
}




if ($message === "") {

    echo json_encode([
        "success" => false,
        "message" => "Message cannot be empty."
    ]);

    exit;
}


if (mb_strlen($message) > 2000) {

    echo json_encode([
        "success" => false,
        "message" => "Message is too long. Maximum is 2000 characters."
    ]);

    exit;
}




if ($sender_type === "student") {

    if ($receiver_type !== "admin") {

        echo json_encode([
            "success" => false,
            "message" => "Students can only message an administrator."
        ]);

        exit;
    }

} else {

    if ($receiver_type !== "student") {

        echo json_encode([
            "success" => false,
            "message" => "Administrators can only message students."
        ]);

        exit;
    }
}



if ($receiver_type === "student") {

    $receiver_stmt = $conn->prepare("
        SELECT id
        FROM students
        WHERE id = ?
        LIMIT 1
    ");

} else {

    $receiver_stmt = $conn->prepare("
        SELECT id
        FROM admins
        WHERE id = ?
        LIMIT 1
    ");
}


if (!$receiver_stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify recipient."
    ]);

    exit;
}


$receiver_stmt->bind_param(
    "i",
    $receiver_id
);

$receiver_stmt->execute();

$receiver_result =
    $receiver_stmt->get_result();


if ($receiver_result->num_rows !== 1) {

    $receiver_stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "The selected recipient does not exist."
    ]);

    exit;
}


$receiver_stmt->close();




if ($order_id !== null) {

    if ($sender_type === "student") {


        $order_stmt = $conn->prepare("
            SELECT id
            FROM orders
            WHERE id = ?
              AND student_id = ?
            LIMIT 1
        ");

        if (!$order_stmt) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Unable to verify the selected order."
            ]);

            exit;
        }

        $order_stmt->bind_param(
            "ii",
            $order_id,
            $sender_id
        );

    } else {

        /*
        | Admin can only attach an order belonging
        | to the student receiving the message.
        */

        $order_stmt = $conn->prepare("
            SELECT id
            FROM orders
            WHERE id = ?
              AND student_id = ?
            LIMIT 1
        ");

        if (!$order_stmt) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Unable to verify the selected order."
            ]);

            exit;
        }

        $order_stmt->bind_param(
            "ii",
            $order_id,
            $receiver_id
        );
    }


    $order_stmt->execute();

    $order_result =
        $order_stmt->get_result();


    if ($order_result->num_rows !== 1) {

        $order_stmt->close();

        echo json_encode([
            "success" => false,
            "message" => "The selected order is not valid for this conversation."
        ]);

        exit;
    }


    $order_stmt->close();
}




$stmt = $conn->prepare("
    INSERT INTO messages
    (
        sender_type,
        sender_id,
        receiver_type,
        receiver_id,
        order_id,
        message
    )
    VALUES (?, ?, ?, ?, ?, ?)
");


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare message."
    ]);

    exit;
}


$stmt->bind_param(
    "sisiss",
    $sender_type,
    $sender_id,
    $receiver_type,
    $receiver_id,
    $order_id,
    $message
);


if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to send message."
    ]);

    exit;
}


$message_id = $conn->insert_id;

$stmt->close();




if ($sender_type === "student") {

    $notification_message =
        "You received a new message from a student.";

} else {

    $notification_message =
        "You received a new message from the administrator.";
}


$notification_stmt = $conn->prepare("
    INSERT INTO notifications
    (
        user_type,
        user_id,
        order_id,
        type,
        message
    )
    VALUES (?, ?, ?, 'new_message', ?)
");


if (!$notification_stmt) {

    echo json_encode([
        "success" => true,
        "message" =>
            "Message sent, but notification could not be created.",
        "message_id" => $message_id,
        "order_id" => $order_id
    ]);

    exit;
}


$notification_stmt->bind_param(
    "siis",
    $receiver_type,
    $receiver_id,
    $order_id,
    $notification_message
);


if (!$notification_stmt->execute()) {

    $notification_stmt->close();

    echo json_encode([
        "success" => true,
        "message" =>
            "Message sent, but notification could not be created.",
        "message_id" => $message_id,
        "order_id" => $order_id
    ]);

    exit;
}


$notification_stmt->close();




echo json_encode([

    "success" => true,

    "message" =>
        "Message sent successfully.",

    "message_id" =>
        $message_id,

    "order_id" =>
        $order_id

]);


$conn->close();