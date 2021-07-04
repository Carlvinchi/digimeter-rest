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
            $bills = new Billing($connect);
            if(isset($data->readings))
            {
                $exec = $bills->add_reading(
                $data->meter_id,
                $data->reading,
                $data->volume_consumed,
                $data->cost
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

            elseif(isset($data->check_balance))
            {
                $exec = $bills->get_balance($data->meter_id);

                    echo json_encode(array(
                        "balance" => $exec
                    ));
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

    
    
        

        

        
        
    
    

    