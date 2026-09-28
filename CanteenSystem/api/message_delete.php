<?php

session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);
    exit;
}

$user_type = $_SESSION['user_type'];
$user_id = (int) $_SESSION['user_id'];

if (!in_array($user_type, ['student', 'admin'], true)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid user type."
    ]);
    exit;
}

require_once "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$message_id = isset($data['message_id'])
    ? (int) $data['message_id']
    : 0;

$delete_all = isset($data['delete_all'])
    ? (bool) $data['delete_all']
    : false;

if ($delete_all) {

    if ($user_type === 'student') {

        $sql = "
            UPDATE messages
            SET student_deleted = 1
            WHERE
                (sender_type = 'student' AND sender_id = ?)
                OR
                (receiver_type = 'student' AND receiver_id = ?)
        ";

    } else {

        $sql = "
            UPDATE messages
            SET admin_deleted = 1
            WHERE
                (sender_type = 'admin' AND sender_id = ?)
                OR
                (receiver_type = 'admin' AND receiver_id = ?)
        ";
    }

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to prepare delete request."
        ]);
        exit;
    }

    $stmt->bind_param("ii", $user_id, $user_id);

    if (!$stmt->execute()) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete messages."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "All messages deleted successfully."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

if ($message_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Message ID is required."
    ]);
    exit;
}



if ($user_type === 'student') {

    $sql = "
        UPDATE messages
        SET student_deleted = 1
        WHERE id = ?
        AND (
            (sender_type = 'student' AND sender_id = ?)
            OR
            (receiver_type = 'student' AND receiver_id = ?)
        )
    ";

} else {

    $sql = "
        UPDATE messages
        SET admin_deleted = 1
        WHERE id = ?
        AND (
            (sender_type = 'admin' AND sender_id = ?)
            OR
            (receiver_type = 'admin' AND receiver_id = ?)
        )
    ";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to prepare delete request."
    ]);
    exit;
}

$stmt->bind_param(
    "iii",
    $message_id,
    $user_id,
    $user_id
);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete message."
    ]);
    exit;
}

if ($stmt->affected_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Message not found or you do not have permission to delete it."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Message deleted successfully."
]);

$stmt->close();
$conn->close();