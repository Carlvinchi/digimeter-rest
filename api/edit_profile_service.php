<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With'); 
    
    // Initialize API services
    require_once("../includes/init.php");

    $header = get_authorization_header();
    if($header != NULL)
    {
        $arr = explode(" ",$header);
        $jwt = $arr[1];
        $auth = new Auth($connect);
 
        $decode = $auth->verify_jwt($jwt,SECRET_KEY);

        $data = json_decode(file_get_contents("php://input"));
        
        if(isset($decode->data) && $decode->iss == "DIGIMETER")
        {   
            $auth = new Auth($connect);
            if(isset($data->no_password))
            {
                $exec = $auth->edit_profile_no_password(
                $data->user_email,
                $data->phone_no,
                $data->first_name,
                $data->last_name,
                $data->digital_address,
                $data->address_street,
                $data->address_city,
                $data->address_region,
                $decode->data->user_id
                );

                if($exec == "Success")
                {
                    echo json_encode(array(
                        "message" => $exec
                    ));
                }
                else
                {
                    echo json_encode(array(
                        "message" => $exec
                    ));
                }
                

            }
 
            elseif(isset($data->change_password))
            {
                $exec = $auth->change_password(
                $data->old_password,
                $data->new_password,
                $decode->data->user_id
                );

                if($exec == "Success")
                {
                    echo json_encode(array(
                        "message" => $exec
                    ));
                }
                else
                {
                    echo json_encode(array(
                        "message" => $exec
                    ));
                }
                

            }



            
        }
        else
        {

            echo json_encode(array(
                "message" => "Authorization Error",
                "data" => $decode
                
            ));
        }

        
    }
    else
    {
        echo json_encode(array(
            "message" => "No authorization header"
        ));
    }

    
    
        

        

        
        
    
    

    