<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET');
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
        $meter_id = $_GET['meter_id'];
        $customer_id = $decode->data->customer_id;
        //$data = json_decode(file_get_contents("php://input"));
        
        if(isset($decode->data) && $decode->iss == "DIGIMETER")
        {   
            $payments = new Payment($connect);
            if(isset($_GET['get_data']))
            {
                $exec = $payments->get_payments($meter_id,$customer_id);
            
                    echo json_encode(array(
                        "data" => $exec  
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
