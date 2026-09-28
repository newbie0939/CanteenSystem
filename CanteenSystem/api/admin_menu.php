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

$action = $data["action"] ?? "";



if ($action === "add") {

    $name =
        trim($data["name"] ?? "");

    $description =
        trim($data["description"] ?? "");

    $price =
        (float) ($data["price"] ?? 0);

    $category =
        trim($data["category"] ?? "");

    $image =
        trim($data["image"] ?? "");


    if ($name === "") {

        echo json_encode([
            "success" => false,
            "message" => "Food name is required."
        ]);

        exit;
    }


    if ($price <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Price must be greater than zero."
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        INSERT INTO menu_items
        (
            name,
            description,
            price,
            image,
            category,
            is_available
        )
        VALUES (?, ?, ?, ?, ?, 1)
    ");


    $stmt->bind_param(
        "ssdss",
        $name,
        $description,
        $price,
        $image,
        $category
    );


    if (!$stmt->execute()) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Failed to add food."
        ]);

        exit;
    }


    echo json_encode([
        "success" => true,
        "message" => "Food added successfully.",
        "id" => $conn->insert_id
    ]);

    exit;
}



if ($action === "edit") {

    $id =
        (int) ($data["id"] ?? 0);

    $name =
        trim($data["name"] ?? "");

    $description =
        trim($data["description"] ?? "");

    $price =
        (float) ($data["price"] ?? 0);

    $category =
        trim($data["category"] ?? "");

    $image =
        trim($data["image"] ?? "");


    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid food item."
        ]);

        exit;
    }


    if ($name === "") {

        echo json_encode([
            "success" => false,
            "message" => "Food name is required."
        ]);

        exit;
    }


    if ($price <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Price must be greater than zero."
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        UPDATE menu_items
        SET
            name = ?,
            description = ?,
            price = ?,
            image = ?,
            category = ?
        WHERE id = ?
    ");


    $stmt->bind_param(
        "ssdssi",
        $name,
        $description,
        $price,
        $image,
        $category,
        $id
    );


    if (!$stmt->execute()) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Failed to update food."
        ]);

        exit;
    }


    echo json_encode([
        "success" => true,
        "message" => "Food updated successfully."
    ]);

    exit;
}




if ($action === "toggle") {

    $id =
        (int) ($data["id"] ?? 0);

    $is_available =
        (int) ($data["is_available"] ?? 0);


    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid food item."
        ]);

        exit;
    }


    $is_available =
        $is_available === 1
            ? 1
            : 0;


    $stmt = $conn->prepare("
        UPDATE menu_items
        SET is_available = ?
        WHERE id = ?
    ");


    $stmt->bind_param(
        "ii",
        $is_available,
        $id
    );


    if (!$stmt->execute()) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Failed to update food availability."
        ]);

        exit;
    }


    echo json_encode([
        "success" => true,
        "message" =>
            $is_available === 1
                ? "Food is now available."
                : "Food is now unavailable."
    ]);

    exit;
}



echo json_encode([
    "success" => false,
    "message" => "Invalid menu action."
]);