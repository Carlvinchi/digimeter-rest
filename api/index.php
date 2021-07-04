<?php

    //add headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With'); 

    require_once("get_auth_hearder.php");

    $header = get_authorization_header();
    if($header != NULL){
        $arr = explode(" ",$header);
        $jwt = $arr[1];
    }
    echo json_encode(array(
        "message" => "Header Message",
        "data" => $jwt
        
    ));