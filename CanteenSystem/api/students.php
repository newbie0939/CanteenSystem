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


$sql = "
    SELECT
        id,
        student_id,
        name,
        email,
        profile_image
    FROM students
    ORDER BY name ASC, id ASC
";


$result = $conn->query($sql);


if (!$result) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to load students."
    ]);

    $conn->close();

    exit;
}


$students = [];


while ($row = $result->fetch_assoc()) {

    $students[] = [
        "id" => (int) $row["id"],
        "student_id" => $row["student_id"],
        "name" => $row["name"],
        "email" => $row["email"],
        "profile_image" => $row["profile_image"]
    ];

}


echo json_encode([
    "success" => true,
    "students" => $students
]);


$conn->close();