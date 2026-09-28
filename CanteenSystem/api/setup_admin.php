<?php

require_once "db_connect.php";

$admin_id = "ADMIN001";
$name = "Canteen Administrator";
$email = "admin@canteen.com";
$password = "Admin12345";

$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);



$check = $conn->prepare("
    SELECT id
    FROM admins
    WHERE admin_id = ?
    LIMIT 1
");

$check->bind_param(
    "s",
    $admin_id
);

$check->execute();

$result = $check->get_result();



if ($result->num_rows > 0) {

    echo "
        <h2>Admin account already exists.</h2>

        <p>
            Admin ID:
            <strong>ADMIN001</strong>
        </p>
    ";

    exit;
}


$stmt = $conn->prepare("
    INSERT INTO admins
    (
        admin_id,
        name,
        email,
        password
    )
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    "ssss",
    $admin_id,
    $name,
    $email,
    $hashed_password
);


if ($stmt->execute()) {

    echo "
        <h2>Admin account created successfully.</h2>

        <p>
            Admin ID:
            <strong>ADMIN001</strong>
        </p>

        <p>
            Password:
            <strong>Admin12345</strong>
        </p>

        <p>
            You can now use these credentials to login.
        </p>
    ";

} else {

    echo "
        <h2>Failed to create admin account.</h2>
    ";
}