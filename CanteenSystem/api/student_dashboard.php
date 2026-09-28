<?php

session_start();

header("Content-Type: application/json");



if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_type"]) ||
    $_SESSION["user_type"] !== "student"
) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);

    exit;

}


require_once "db_connect.php";




$student_id =
    (int) $_SESSION["user_id"];




$activeStmt =
    $conn->prepare("
        SELECT COUNT(*) AS total
        FROM orders
        WHERE student_id = ?
          AND is_archived = 0
          AND status IN (
              'Pending',
              'Preparing',
              'Ready'
          )
    ");


$activeStmt->bind_param(
    "i",
    $student_id
);


$activeStmt->execute();


$activeResult =
    $activeStmt->get_result();


$activeRow =
    $activeResult->fetch_assoc();


$activeOrders =
    (int) ($activeRow["total"] ?? 0);




$completedStmt =
    $conn->prepare("
        SELECT COUNT(*) AS total
        FROM orders
        WHERE student_id = ?
          AND is_archived = 0
          AND status = 'Completed'
    ");


$completedStmt->bind_param(
    "i",
    $student_id
);


$completedStmt->execute();


$completedResult =
    $completedStmt->get_result();


$completedRow =
    $completedResult->fetch_assoc();


$completedOrders =
    (int) ($completedRow["total"] ?? 0);



$recentStmt =
    $conn->prepare("
        SELECT
            order_number,
            total_amount,
            status,
            queue_position,
            created_at
        FROM orders
        WHERE student_id = ?
          AND is_archived = 0
        ORDER BY created_at DESC
        LIMIT 5
    ");


$recentStmt->bind_param(
    "i",
    $student_id
);


$recentStmt->execute();


$recentResult =
    $recentStmt->get_result();


$recentOrders = [];


while (
    $row =
    $recentResult->fetch_assoc()
) {

    $recentOrders[] = [

        "order_number" =>
            $row["order_number"],

        "total_amount" =>
            (float) $row["total_amount"],

        "status" =>
            $row["status"],

        "queue_position" =>
            $row["queue_position"] !== null
                ? (int) $row["queue_position"]
                : null,

        "created_at" =>
            $row["created_at"]

    ];

}



echo json_encode([

    "success" => true,

    "active_orders" =>
        $activeOrders,

    "completed_orders" =>
        $completedOrders,

    "recent_orders" =>
        $recentOrders

]);


$activeStmt->close();

$completedStmt->close();

$recentStmt->close();

$conn->close();

?>