<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With'); 
    
    // Initialize API services
    require_once("../includes/init.php");

    $data = json_decode(file_get_contents("php://input"));
    if(!empty($data))
    {
        $auth = new Auth($connect);

        $exec = $auth->user_login(
        $data->user_email,
        
        $data->password
        );

        if($exec == "Success")
        {

            $token = $auth->generate_jwt(SECRET_KEY);

            echo json_encode(array(
                "message" => "Success",
                "token" => $token[0],
                "data" => $token[1] 
            ));
        }
        else
        {
            echo json_encode(array(
                "message" => $exec
            ));
        }
        
    }
    

    