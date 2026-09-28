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




$stmt = $conn->prepare("
    SELECT
        id,
        order_id,
        type,
        message,
        is_read,
        created_at
    FROM notifications
    WHERE user_type = ?
      AND user_id = ?
      AND is_deleted = 0
    ORDER BY created_at DESC
");


$stmt->bind_param(
    "si",
    $user_type,
    $user_id
);


$stmt->execute();


$result =
    $stmt->get_result();


$notifications = [];


while ($notification = $result->fetch_assoc()) {

    $notifications[] = [

        "id" =>
            (int) $notification["id"],

        "order_id" =>
            $notification["order_id"] !== null
                ? (int) $notification["order_id"]
                : null,

        "type" =>
            $notification["type"],

        "message" =>
            $notification["message"],

        "is_read" =>
            (int) $notification["is_read"],

        "created_at" =>
            $notification["created_at"]

    ];

}



$count_stmt = $conn->prepare("
    SELECT COUNT(*) AS unread_count
    FROM notifications
    WHERE user_type = ?
      AND user_id = ?
      AND is_deleted = 0
      AND is_read = 0
");


$count_stmt->bind_param(
    "si",
    $user_type,
    $user_id
);


$count_stmt->execute();


$count_result =
    $count_stmt->get_result();


$count_data =
    $count_result->fetch_assoc();


$unread_count =
    (int) $count_data["unread_count"];


echo json_encode([

    "success" => true,

    "notifications" =>
        $notifications,

    "unread_count" =>
        $unread_count

]);