<?php

session_start();

header("Content-Type: application/json");



if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user_type"])
) {

    echo json_encode([
        "success" => false,
        "logged_in" => false,
        "message" => "Not logged in."
    ]);

    exit;
}




echo json_encode([
    "success" => true,
    "logged_in" => true,

    "user_id" =>
        $_SESSION["user_id"],

    "user_type" =>
        $_SESSION["user_type"],

    "name" =>
        $_SESSION["name"] ?? "",

    "email" =>
        $_SESSION["email"] ?? ""
]);