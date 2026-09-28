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


$order_id =
    (int) ($data["order_id"] ?? 0);


if ($order_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID."
    ]);

    exit;
}




$conn->begin_transaction();


try {



    $order_stmt = $conn->prepare("
        SELECT
            id,
            order_number,
            status,
            is_archived
        FROM orders
        WHERE id = ?
        LIMIT 1
        FOR UPDATE
    ");


    $order_stmt->bind_param(
        "i",
        $order_id
    );


    $order_stmt->execute();


    $order_result =
        $order_stmt->get_result();


    if ($order_result->num_rows !== 1) {

        throw new Exception(
            "Order not found."
        );

    }


    $order =
        $order_result->fetch_assoc();




    if (
        (int) $order["is_archived"] === 1
    ) {

        throw new Exception(
            "This order has already been deleted."
        );

    }


 

    if (
        $order["status"] !== "Completed" &&
        $order["status"] !== "Cancelled"
    ) {

        throw new Exception(
            "Only completed or cancelled orders can be deleted."
        );

    }




    $archive_stmt = $conn->prepare("
        UPDATE orders
        SET
            is_archived = 1,
            queue_position = NULL,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
          AND status IN ('Completed', 'Cancelled')
          AND is_archived = 0
    ");


    $archive_stmt->bind_param(
        "i",
        $order_id
    );


    if (!$archive_stmt->execute()) {

        throw new Exception(
            "Failed to delete order."
        );

    }


    if (
        $archive_stmt->affected_rows !== 1
    ) {

        throw new Exception(
            "Unable to delete this order."
        );

    }




    $conn->commit();




    echo json_encode([

        "success" => true,

        "message" =>
            "Order deleted successfully.",

        "order_id" =>
            $order_id,

        "order_number" =>
            $order["order_number"]

    ]);

}

catch (Throwable $e) {

 

    $conn->rollback();


    http_response_code(500);


    echo json_encode([

        "success" => false,

        "message" =>
            $e->getMessage()

    ]);

}