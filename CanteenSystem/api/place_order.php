<?php

session_start();

header("Content-Type: application/json");

require_once "db_connect.php";




if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_type"]) ||
    $_SESSION["user_type"] !== "student"
) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "You must be logged in as a student."
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

$cart = $data["cart"] ?? [];


if (!is_array($cart) || count($cart) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Your cart is empty."
    ]);

    exit;
}



$student_id = (int) $_SESSION["user_id"];




$conn->begin_transaction();


try {




    $total_amount = 0;

    $validated_items = [];


    foreach ($cart as $cart_item) {


        $menu_item_id =
            (int) ($cart_item["id"] ?? 0);


        $quantity =
            (int) ($cart_item["quantity"] ?? 0);


        if (
            $menu_item_id <= 0 ||
            $quantity <= 0
        ) {

            throw new Exception(
                "Invalid cart item."
            );

        }




        $stmt = $conn->prepare("
            SELECT
                id,
                name,
                price,
                is_available
            FROM menu_items
            WHERE id = ?
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->bind_param(
            "i",
            $menu_item_id
        );

        $stmt->execute();

        $result =
            $stmt->get_result();


        if ($result->num_rows !== 1) {

            throw new Exception(
                "A menu item no longer exists."
            );

        }


        $menu_item =
            $result->fetch_assoc();




        if (
            (int) $menu_item["is_available"] !== 1
        ) {

            throw new Exception(
                $menu_item["name"] .
                " is currently unavailable."
            );

        }




        $price =
            (float) $menu_item["price"];


        $subtotal =
            $price * $quantity;


        $total_amount += $subtotal;


        $validated_items[] = [

            "menu_item_id" =>
                $menu_item_id,

            "quantity" =>
                $quantity,

            "price" =>
                $price,

            "subtotal" =>
                $subtotal

        ];

    }




    $order_number =
        "ORD-" .
        date("YmdHis") .
        "-" .
        random_int(100, 999);




    $queue_stmt = $conn->prepare("
        SELECT
            COALESCE(
                MAX(queue_position),
                0
            ) + 1 AS next_position
        FROM orders
        WHERE status = 'pending'
          AND is_archived = 0
            
        
    ");

    $queue_stmt->execute();

    $queue_result =
        $queue_stmt->get_result();

    $queue_data =
        $queue_result->fetch_assoc();

    $queue_position =
        (int) $queue_data["next_position"];



    $order_stmt = $conn->prepare("
        INSERT INTO orders
        (
            order_number,
            student_id,
            total_amount,
            status,
            queue_position
        )
        VALUES (?, ?, ?, 'Pending', ?)
    ");

    $order_stmt->bind_param(
        "sidi",
        $order_number,
        $student_id,
        $total_amount,
        $queue_position
    );


    if (!$order_stmt->execute()) {

        throw new Exception(
            "Failed to create order."
        );

    }


    $order_id =
        $conn->insert_id;




    $item_stmt = $conn->prepare("
        INSERT INTO order_items
        (
            order_id,
            menu_item_id,
            quantity,
            price,
            subtotal
        )
        VALUES (?, ?, ?, ?, ?)
    ");


    foreach ($validated_items as $item) {

        $item_stmt->bind_param(
            "iiidd",
            $order_id,
            $item["menu_item_id"],
            $item["quantity"],
            $item["price"],
            $item["subtotal"]
        );


        if (!$item_stmt->execute()) {

            throw new Exception(
                "Failed to save order items."
            );

        }

    }




    $notification_message =
        "Your order " .
        $order_number .
        " has been received and is now Pending.";


    $notification_stmt =
        $conn->prepare("
            INSERT INTO notifications
            (
                user_type,
                user_id,
                order_id,
                type,
                message
            )
            VALUES (
                'student',
                ?,
                ?,
                'order_created',
                ?
            )
        ");


    $notification_stmt->bind_param(
        "iis",
        $student_id,
        $order_id,
        $notification_message
    );


    if (!$notification_stmt->execute()) {

        throw new Exception(
            "Failed to create notification."
        );

    }

    
     


    $admin_message =
        "New order " .
        $order_number .
        " has been received from a student.";


    $admin_notification =
        $conn->prepare("
            INSERT INTO notifications
            (
                user_type,
                user_id,
                order_id,
                type,
                message
            )
            SELECT
                'admin',
                id,
                ?,
                'new_order',
                ?
            FROM admins
        ");


    $admin_notification->bind_param(
        "is",
        $order_id,
        $admin_message
    );


    if (!$admin_notification->execute()) {

        throw new Exception(
            "Failed to create admin notification."
        );

    }

    

    $conn->commit();




    echo json_encode([

        "success" => true,

        "message" =>
            "Order placed successfully.",

        "order_id" =>
            $order_id,

        "order_number" =>
            $order_number,

        "queue_position" =>
            $queue_position,

        "total_amount" =>
            number_format(
                $total_amount,
                2,
                ".",
                ""
            )

    ]);

} catch (Throwable $e) {




    $conn->rollback();


    http_response_code(500);


    echo json_encode([

        "success" => false,

        "message" =>
            $e->getMessage()

    ]);

}