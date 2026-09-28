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




if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}



$start_date = $_GET["start_date"] ?? "";
$end_date = $_GET["end_date"] ?? "";



if ($start_date === "") {
    $start_date = date("Y-m-d");
}

if ($end_date === "") {
    $end_date = date("Y-m-d");
}



$start_object = DateTime::createFromFormat(
    "Y-m-d",
    $start_date
);

$end_object = DateTime::createFromFormat(
    "Y-m-d",
    $end_date
);


if (
    !$start_object ||
    $start_object->format("Y-m-d") !== $start_date ||
    !$end_object ||
    $end_object->format("Y-m-d") !== $end_date
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid date format."
    ]);

    exit;
}


if ($start_date > $end_date) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" =>
            "Start date cannot be later than end date."
    ]);

    exit;
}




$start_datetime =
    $start_date . " 00:00:00";

$end_datetime =
    $end_date . " 23:59:59";




$stmt = $conn->prepare("
    SELECT
        COUNT(*) AS total_orders,

        SUM(
            CASE
                WHEN status = 'Completed'
                THEN 1
                ELSE 0
            END
        ) AS completed_orders,

        SUM(
            CASE
                WHEN status = 'Cancelled'
                THEN 1
                ELSE 0
            END
        ) AS cancelled_orders,

        COALESCE(
            SUM(
                CASE
                    WHEN status = 'Completed'
                    THEN total_amount
                    ELSE 0
                END
            ),
            0
        ) AS total_sales

    FROM orders

    WHERE created_at >= ?
    AND created_at <= ?
");


$stmt->bind_param(
    "ss",
    $start_datetime,
    $end_datetime
);


if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
            "Failed to retrieve order summary."
    ]);

    exit;
}


$summary_result =
    $stmt->get_result();

$summary =
    $summary_result->fetch_assoc();


$total_orders =
    (int) ($summary["total_orders"] ?? 0);

$completed_orders =
    (int) ($summary["completed_orders"] ?? 0);

$cancelled_orders =
    (int) ($summary["cancelled_orders"] ?? 0);

$total_sales =
    (float) ($summary["total_sales"] ?? 0);




$status_stmt = $conn->prepare("
    SELECT
        status,
        COUNT(*) AS total

    FROM orders

    WHERE created_at >= ?
    AND created_at <= ?

    GROUP BY status
");


$status_stmt->bind_param(
    "ss",
    $start_datetime,
    $end_datetime
);


if (!$status_stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
            "Failed to retrieve order statuses."
    ]);

    exit;
}


$status_result =
    $status_stmt->get_result();


$status_counts = [

    "Pending" => 0,

    "Preparing" => 0,

    "Ready" => 0,

    "Completed" => 0,

    "Cancelled" => 0

];


while (
    $row =
        $status_result->fetch_assoc()
) {

    $status =
        $row["status"];

    if (
        array_key_exists(
            $status,
            $status_counts
        )
    ) {

        $status_counts[$status] =
            (int) $row["total"];

    }

}




$food_stmt = $conn->prepare("
    SELECT
        mi.name,
        SUM(oi.quantity) AS quantity_sold,
        SUM(oi.subtotal) AS sales

    FROM order_items oi

    INNER JOIN orders o
        ON o.id = oi.order_id

    INNER JOIN menu_items mi
        ON mi.id = oi.menu_item_id

    WHERE o.created_at >= ?
    AND o.created_at <= ?
    AND o.status = 'Completed'

    GROUP BY
        oi.menu_item_id,
        mi.name

    ORDER BY
        quantity_sold DESC,
        sales DESC,
        mi.name ASC

    LIMIT 10
");


$food_stmt->bind_param(
    "ss",
    $start_datetime,
    $end_datetime
);


if (!$food_stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
            "Failed to retrieve food statistics."
    ]);

    exit;
}


$food_result =
    $food_stmt->get_result();


$food_items = [];


while (
    $food =
        $food_result->fetch_assoc()
) {

    $food_items[] = [

        "name" =>
            $food["name"],

        "quantity_sold" =>
            (int) $food["quantity_sold"],

        "sales" =>
            (float) $food["sales"]

    ];

}




echo json_encode([

    "success" => true,

    "date_range" => [

        "start" =>
            $start_date,

        "end" =>
            $end_date

    ],

    "summary" => [

        "total_orders" =>
            $total_orders,

        "completed_orders" =>
            $completed_orders,

        "cancelled_orders" =>
            $cancelled_orders,

        "total_sales" =>
            $total_sales

    ],

    "statuses" => $status_counts,

    "food_items" => $food_items

]);