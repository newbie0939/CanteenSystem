<?php

session_start();

header("Content-Type: application/json");

require_once "db_connect.php";




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


$login = trim(
    $data["login"] ?? ""
);

$password = $data["password"] ?? "";




if ($login === "" || $password === "") {

    echo json_encode([
        "success" => false,
        "message" => "Please enter your ID and password."
    ]);

    exit;
}



$stmt = $conn->prepare("
    SELECT
        id,
        student_id,
        name,
        email,
        password
    FROM students
    WHERE student_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "s",
    $login
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 1) {

    $student = $result->fetch_assoc();


    if (password_verify(
        $password,
        $student["password"]
    )) {

        $_SESSION["user_id"] =
            $student["id"];

        $_SESSION["user_type"] =
            "student";

        $_SESSION["student_id"] =
            $student["student_id"];

        $_SESSION["name"] =
            $student["name"];

        $_SESSION["email"] =
            $student["email"];


        echo json_encode([
            "success" => true,
            "role" => "student",
            "message" => "Login successful."
        ]);

        exit;
    }
}




$stmt = $conn->prepare("
    SELECT
        id,
        admin_id,
        name,
        email,
        password
    FROM admins
    WHERE admin_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "s",
    $login
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 1) {

    $admin = $result->fetch_assoc();


    if (password_verify(
        $password,
        $admin["password"]
    )) {

        $_SESSION["user_id"] =
            $admin["id"];

        $_SESSION["user_type"] =
            "admin";

        $_SESSION["admin_id"] =
            $admin["admin_id"];

        $_SESSION["name"] =
            $admin["name"];

        $_SESSION["email"] =
            $admin["email"];


        echo json_encode([
            "success" => true,
            "role" => "admin",
            "message" => "Login successful."
        ]);

        exit;
    }
}




echo json_encode([
    "success" => false,
    "message" => "Invalid ID or password."
]);