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




if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}




$data =
    json_decode(
        file_get_contents("php://input"),
        true
    );


$message_id =
    (int) ($data["message_id"] ?? 0);

$mark_all =
    !empty($data["mark_all"]);




$deleted_column =
    $user_type === "student"
        ? "student_deleted"
        : "admin_deleted";




if ($message_id > 0) {



    $stmt = $conn->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE id = ?
          AND receiver_type = ?
          AND receiver_id = ?
          AND {$deleted_column} = 0
    ");


    $stmt->bind_param(
        "isi",
        $message_id,
        $user_type,
        $user_id
    );


    if (!$stmt->execute()) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Failed to mark message as read."
        ]);

        exit;
    }


 

    if ($stmt->affected_rows === 0) {

        echo json_encode([
            "success" => false,
            "message" => "Message not found or already read."
        ]);

        exit;
    }


    echo json_encode([
        "success" => true,
        "message" => "Message marked as read."
    ]);

    exit;
}




if ($mark_all) {

    $stmt = $conn->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE receiver_type = ?
          AND receiver_id = ?
          AND {$deleted_column} = 0
          AND is_read = 0
    ");


    $stmt->bind_param(
        "si",
        $user_type,
        $user_id
    );


    if (!$stmt->execute()) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Failed to mark messages as read."
        ]);

        exit;
    }


    echo json_encode([
        "success" => true,
        "message" => "All messages marked as read.",
        "updated" => $stmt->affected_rows
    ]);

    exit;
}



echo json_encode([
    "success" => false,
    "message" => "Provide a message_id or mark_all."
]);