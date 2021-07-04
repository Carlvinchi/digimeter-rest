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
        $user = new User($connect);

        $exec = $user->create_user(
        $data->user_email,
        $data->phone_no,
        $data->first_name,
        $data->last_name,
        $data->digital_address,
        $data->address_street,
        $data->address_city,
        $data->address_region,
        $data->password
        );

        if($exec == "Success")
        {
            echo json_encode(array(
                "message" => "Success",  
            ));
        }
        else
        {
            echo json_encode(array(
                "message" => $exec
            ));
        }
        
    }
    

    