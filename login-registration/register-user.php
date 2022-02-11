<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'connection.php';
/** @var $connection */

$data = json_decode(file_get_contents("php://input"));

if (isset($data->username)
    && isset($data->password)
    && isset($data->confirmPassword)
    && !empty(trim($data->username))
    && !empty(trim($data->password))
    && !empty(trim($data->confirmPassword))

) {
    $username = mysqli_real_escape_string($connection, trim($data->username));
    $password = mysqli_real_escape_string($connection, trim($data->password));

    if ($password === $data->confirmPassword) {
        if (strlen($password) >= 5 && strpbrk($password, "!#$.,:;()") !== false) {
            $isUsernameTaken = mysqli_query($connection, "SELECT * FROM users WHERE username='$username'");
            if (mysqli_num_rows($isUsernameTaken) === 0) {
                $insertUser = mysqli_query(
                    $connection,
                    "INSERT INTO `users`(`username`,`password`) VALUES('$username','$password')"
                );
                if ($insertUser) {
                    $last_id = mysqli_insert_id($connection);
                    echo json_encode(["success" => 1, "msg" => "User Inserted.", "id" => $last_id]);
                } else {
                    echo json_encode(["success" => 0, "msg" => "User Not Inserted!"]);
                }
            } else {
                echo json_encode(["success" => 0, "msg" => "Username is already taken."]);
            }
        } else {
            echo json_encode(["success" => 0, "msg" => "Password is to week. Use at least 5 characters and special symbol"]);
        }
    } else {
        echo json_encode(["success" => 0, "msg" => "Given passwords are different"]);
    }
} else {
    echo json_encode(["success" => 0, "msg" => "Please fill all the required fields!"]);
}