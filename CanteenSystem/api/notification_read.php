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


$notification_id =
    (int) ($data["notification_id"] ?? 0);

$mark_all =
    !empty($data["mark_all"]);



if ($mark_all) {

    $stmt = $conn->prepare("
        UPDATE notifications
        SET is_read = 1
        WHERE user_type = ?
          AND user_id = ?
          AND is_deleted = 0
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
            "message" =>
                "Unable to mark notifications as read."
        ]);

        exit;
    }


    echo json_encode([
        "success" => true,
        "message" =>
            "All notifications marked as read."
    ]);

    exit;
}




if ($notification_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Invalid notification."
    ]);

    exit;
}


$stmt = $conn->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE id = ?
      AND user_type = ?
      AND user_id = ?
      AND is_deleted = 0
");


$stmt->bind_param(
    "isi",
    $notification_id,
    $user_type,
    $user_id
);


if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
            "Unable to mark notification as read."
    ]);

    exit;
}


echo json_encode([
    "success" => true,
    "message" =>
        "Notification marked as read."
]);