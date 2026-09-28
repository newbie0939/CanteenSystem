<?php

session_start();

header("Content-Type: application/json");



if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'admin' ||
    !isset($_SESSION['user_id'])
) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);

    exit;
}


require_once "db_connect.php";




$studentId = isset($_GET['student_id'])
    ? (int) $_GET['student_id']
    : 0;


if ($studentId <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid student ID."
    ]);

    exit;
}





$sql = "
    SELECT
        id,
        order_number,
        total_amount,
        status,
        created_at,
        updated_at
    FROM orders
    WHERE student_id = ?
      AND is_archived = 0
    ORDER BY created_at DESC, id DESC
";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare order query."
    ]);

    $conn->close();

    exit;
}


$stmt->bind_param(
    "i",
    $studentId
);


if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to load student orders."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}


$result = $stmt->get_result();


$orders = [];


while ($row = $result->fetch_assoc()) {

    $orders[] = [

        "id" => (int) $row["id"],

        "order_number" =>
            $row["order_number"],

        "total_amount" =>
            (float) $row["total_amount"],

        "status" =>
            $row["status"],

        "created_at" =>
            $row["created_at"],

        "updated_at" =>
            $row["updated_at"]

    ];

}


$stmt->close();
$conn->close();




echo json_encode([

    "success" => true,

    "orders" => $orders

]);

?>