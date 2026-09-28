<?php

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


$student_id = trim(
    $data["student_id"] ?? ""
);

$name = trim(
    $data["name"] ?? ""
);

$email = trim(
    $data["email"] ?? ""
);

$password = $data["password"] ?? "";




if (
    $student_id === "" ||
    $name === "" ||
    $email === "" ||
    $password === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Please complete all fields."
    ]);

    exit;
}




if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}




if (strlen($password) < 6) {

    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 6 characters."
    ]);

    exit;
}




$check = $conn->prepare("
    SELECT id
    FROM students
    WHERE student_id = ?
       OR email = ?
    LIMIT 1
");

$check->bind_param(
    "ss",
    $student_id,
    $email
);

$check->execute();

$result = $check->get_result();


if ($result->num_rows > 0) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Student ID or email is already registered."
    ]);

    exit;
}




$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);




$stmt = $conn->prepare("
    INSERT INTO students
    (
        student_id,
        name,
        email,
        password
    )
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    "ssss",
    $student_id,
    $name,
    $email,
    $hashed_password
);




if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" =>
            "Student account created successfully!"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" =>
            "Failed to create student account."
    ]);
}