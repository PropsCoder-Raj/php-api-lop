<?php
include('../../services/JWT.php');

$jwt = new JWT();

class CommonFunction{

    public function __construct(){

    }

    public function authToken(){
        $headers = apache_request_headers();
        
        $authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        // Check if the Authorization header is present and starts with "Bearer "
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(array("status" => false, "error" => "Missing or invalid Authorization header format"));
            return;
        }

        // Extract the token from the Authorization header
        $token = trim(substr($authorizationHeader, strlen('Bearer ')));
        if(!$token){
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(array("status" => false, "error" => "Invalid token"));
            return;
        }

        return $token;
    }

}

?>