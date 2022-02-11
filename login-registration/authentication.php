<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'connection.php';
/** @var $connection */

session_start();

$data = json_decode(file_get_contents("php://input"));

if (isset($data->username)
    && isset($data->password)
    && !empty(trim($data->username))
    && !empty(trim($data->password))
) {
    if ($stmt = $connection->prepare('SELECT id, password FROM users WHERE username = ?')) {
        $stmt->bind_param('s', $data->username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password);
            $stmt->fetch();
            //TO REMEMBER: implement hash password
            if ($data->password === $password) {
                session_regenerate_id();
                $_SESSION['loggedin'] = true;
                $_SESSION['name'] = $data->username;
                $_SESSION['id'] = $id;
                echo json_encode(["success" => 1, "msg" => 'Welcome ' . $_SESSION['name'] . '!']);
            } else {
                // wrong password
                echo json_encode(["success" => 0, "msg" => 'Incorrect password!']);
            }
        } else {
            // wrong username
            echo json_encode(["success" => 0, "msg" => 'Incorrect username']);
        }
        $stmt->close();
    }
} else {
    // not all required fields were filled
    echo json_encode(["success" => 0, "msg" => 'Please fill all required fields']);
}


