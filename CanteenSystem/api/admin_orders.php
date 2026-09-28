<?php

session_start();

header("Content-Type: application/json");

require_once "db_connect.php";




if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_type"]) ||
    $_SESSION["user_type"] !== "admin"
) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Admin access required."
    ]);

    exit;
}



$stmt = $conn->prepare("
    SELECT
        o.id,
        o.order_number,
        o.student_id,
        o.total_amount,
        o.status,
        o.queue_position,
        o.created_at,
        o.updated_at,
        s.student_id AS student_number,
        s.name AS student_name
    FROM orders o
    INNER JOIN students s
        ON s.id = o.student_id
    WHERE o.is_archived = 0
    ORDER BY
        CASE
            WHEN o.status = 'Pending' THEN 1
            WHEN o.status = 'Preparing' THEN 2
            WHEN o.status = 'Ready' THEN 3
            WHEN o.status = 'Completed' THEN 4
            WHEN o.status = 'Cancelled' THEN 5
            ELSE 6
        END ASC,
        CASE
            WHEN o.queue_position IS NULL THEN 999999
            ELSE o.queue_position
        END ASC,
        o.created_at ASC,
        o.id ASC
");


if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to retrieve orders."
    ]);

    exit;
}


$result = $stmt->get_result();

$orders = [];




while ($order = $result->fetch_assoc()) {

    $order_id =
        (int) $order["id"];


    $item_stmt = $conn->prepare("
        SELECT
            oi.quantity,
            oi.price,
            oi.subtotal,
            mi.name
        FROM order_items oi
        INNER JOIN menu_items mi
            ON mi.id = oi.menu_item_id
        WHERE oi.order_id = ?
        ORDER BY oi.id ASC
    ");


    $item_stmt->bind_param(
        "i",
        $order_id
    );


    if (!$item_stmt->execute()) {

        continue;

    }


    $item_result =
        $item_stmt->get_result();


    $items = [];


    while ($item = $item_result->fetch_assoc()) {

        $items[] = [

            "name" =>
                $item["name"],

            "quantity" =>
                (int) $item["quantity"],

            "price" =>
                (float) $item["price"],

            "subtotal" =>
                (float) $item["subtotal"]

        ];

    }




    $order["id"] =
        (int) $order["id"];


    $order["student_id"] =
        (int) $order["student_id"];


    $order["total_amount"] =
        (float) $order["total_amount"];


    $order["queue_position"] =
        $order["queue_position"] !== null
            ? (int) $order["queue_position"]
            : null;


    $order["items"] =
        $items;


    $orders[] =
        $order;
}




echo json_encode([

    "success" => true,

    "orders" => $orders

]);