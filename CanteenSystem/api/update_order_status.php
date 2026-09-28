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

$new_status =
    trim($data["status"] ?? "");


if ($order_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid order ID."
    ]);

    exit;
}



$allowed_statuses = [
    "Pending",
    "Preparing",
    "Ready",
    "Completed",
    "Cancelled"
];


if (!in_array($new_status, $allowed_statuses, true)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid order status."
    ]);

    exit;
}




$conn->begin_transaction();


try {



    $order_stmt = $conn->prepare("
        SELECT
            id,
            order_number,
            student_id,
            status,
            queue_position,
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



    if ((int) $order["is_archived"] === 1) {

        throw new Exception(
            "Archived orders cannot be updated."
        );

    }


    $old_status =
        $order["status"];

    $order_number =
        $order["order_number"];

    $student_id =
        (int) $order["student_id"];

    $queue_position =
        $order["queue_position"] !== null
            ? (int) $order["queue_position"]
            : null;




    $valid_transition = false;


    if (
        $old_status === "Pending" &&
        $new_status === "Preparing"
    ) {

        $valid_transition = true;

    }

    elseif (
        $old_status === "Preparing" &&
        $new_status === "Ready"
    ) {

        $valid_transition = true;

    }

    elseif (
        $old_status === "Ready" &&
        $new_status === "Completed"
    ) {

        $valid_transition = true;

    }

    elseif (
        $new_status === "Cancelled" &&
        $old_status !== "Completed" &&
        $old_status !== "Cancelled"
    ) {

        $valid_transition = true;

    }


    if (!$valid_transition) {

        throw new Exception(
            "Invalid order status transition."
        );

    }



    if (
        $new_status !== "Cancelled" &&
        $old_status !== "Completed"
    ) {

        if ($queue_position === null) {

            throw new Exception(
                "This order does not have a valid queue position."
            );

        }




        $fifo_stmt = $conn->prepare("
            SELECT
                id,
                order_number,
                queue_position
            FROM orders
            WHERE status = ?
              AND is_archived = 0
              AND queue_position IS NOT NULL
            ORDER BY
                queue_position ASC,
                created_at ASC,
                id ASC
            LIMIT 1
            FOR UPDATE
        ");


        $fifo_stmt->bind_param(
            "s",
            $old_status
        );


        $fifo_stmt->execute();


        $fifo_result =
            $fifo_stmt->get_result();


        if ($fifo_result->num_rows !== 1) {

            throw new Exception(
                "Unable to determine the first order in the FIFO queue."
            );

        }


        $first_order =
            $fifo_result->fetch_assoc();


        $first_order_id =
            (int) $first_order["id"];




        if ($first_order_id !== $order_id) {

            throw new Exception(
                "FIFO queue: Please process order " .
                $first_order["order_number"] .
                " first."
            );

        }

    }



    $remove_queue_stmt = $conn->prepare("
        UPDATE orders
        SET
            queue_position = NULL,
            status = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");


    $remove_queue_stmt->bind_param(
        "si",
        $new_status,
        $order_id
    );


    if (!$remove_queue_stmt->execute()) {

        throw new Exception(
            "Failed to update order status."
        );

    }




    if ($new_status !== "Cancelled") {



        $next_queue_stmt = $conn->prepare("
            SELECT
                COALESCE(
                    MAX(queue_position),
                    0
                ) + 1 AS next_position
            FROM orders
            WHERE status = ?
              AND is_archived = 0
        ");


        $next_queue_stmt->bind_param(
            "s",
            $new_status
        );


        $next_queue_stmt->execute();


        $next_queue_result =
            $next_queue_stmt->get_result();


        $next_queue_data =
            $next_queue_result->fetch_assoc();


        $new_queue_position =
            (int) $next_queue_data["next_position"];



        $assign_queue_stmt = $conn->prepare("
            UPDATE orders
            SET
                queue_position = ?
            WHERE id = ?
        ");


        $assign_queue_stmt->bind_param(
            "ii",
            $new_queue_position,
            $order_id
        );


        if (!$assign_queue_stmt->execute()) {

            throw new Exception(
                "Failed to assign new queue position."
            );

        }

    }



    if (
        $old_status !== "Completed" &&
        $old_status !== "Cancelled"
    ) {

        $remaining_stmt = $conn->prepare("
            SELECT
                id
            FROM orders
            WHERE status = ?
              AND is_archived = 0
              AND queue_position IS NOT NULL
            ORDER BY
                queue_position ASC,
                created_at ASC,
                id ASC
            FOR UPDATE
        ");


        $remaining_stmt->bind_param(
            "s",
            $old_status
        );


        $remaining_stmt->execute();


        $remaining_result =
            $remaining_stmt->get_result();


        $new_position = 1;


        while (
            $remaining_order =
                $remaining_result->fetch_assoc()
        ) {

            $remaining_order_id =
                (int) $remaining_order["id"];


            $position_stmt = $conn->prepare("
                UPDATE orders
                SET queue_position = ?
                WHERE id = ?
            ");


            $position_stmt->bind_param(
                "ii",
                $new_position,
                $remaining_order_id
            );


            if (!$position_stmt->execute()) {

                throw new Exception(
                    "Failed to rebuild queue position."
                );

            }


            $new_position++;

        }

    }




    if ($new_status === "Cancelled") {

        $notification_message =
            "Your order " .
            $order_number .
            " has been Cancelled.";

        $notification_type =
            "order_cancelled";

    }

    else {

        $notification_message =
            "Your order " .
            $order_number .
            " is now " .
            $new_status .
            ".";

        $notification_type =
            "order_status";

    }


    $notification_stmt = $conn->prepare("
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
            ?,
            ?
        )
    ");


    $notification_stmt->bind_param(
        "iiss",
        $student_id,
        $order_id,
        $notification_type,
        $notification_message
    );


    if (!$notification_stmt->execute()) {

        throw new Exception(
            "Failed to create student notification."
        );

    }



    $admin_message =
        "Order " .
        $order_number .
        " has been updated to " .
        $new_status .
        ".";


    $admin_id =
        (int) $_SESSION["user_id"];


    $admin_notification = $conn->prepare("
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
            'order_status',
            ?
        FROM admins
        WHERE id <> ?
    ");


    $admin_notification->bind_param(
        "isi",
        $order_id,
        $admin_message,
        $admin_id
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
            "Order status updated successfully.",

        "order_id" =>
            $order_id,

        "order_number" =>
            $order_number,

        "old_status" =>
            $old_status,

        "new_status" =>
            $new_status

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