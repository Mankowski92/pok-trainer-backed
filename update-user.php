<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db_connection.php';
/** @var $connection */

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->username) && isset($data->password) && is_numeric($data->id) && !empty(
    trim(
        $data->username
    )
    ) && !empty(trim($data->password))) {
    $username = mysqli_real_escape_string($connection, trim($data->username));
    $password = mysqli_real_escape_string($connection, trim($data->password));
    $updateUser = mysqli_query(
        $connection,
        "UPDATE `users` SET `username`='$username', `password`='$password' WHERE `id`='$data->id'"
    );
    if ($updateUser) {
        echo json_encode(["success" => 1, "msg" => "User Updated."]);
    } else {
        echo json_encode(["success" => 0, "msg" => "User Not Updated!"]);
    }
} else {
    echo json_encode(["success" => 0, "msg" => "Please fill all the required fields!"]);
}