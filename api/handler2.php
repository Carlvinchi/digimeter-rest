<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST,GET');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With'); 
    
    // Initialize API services
    require_once("../includes/init.php");

    $data = json_decode(file_get_contents("php://input"));
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

            elseif(isset($data->bill))
            {
                $exec = $bills->add_bill( 
                $data->meter_id, 
                $data->cost_amount
               
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

            elseif(isset($_GET['get_bills']))
            {
                $exec = $bills->get_bills($_GET["meter_id"]);

                    echo json_encode(array(
                        "data" => $exec
                    ));
                
                
                

            }

            elseif(isset($_GET['get_readings']))
            {
                $exec = $bills->get_readings($_GET["meter_id"]);

                    echo json_encode(array(
                        "data" => $exec
                    ));
                
                
                

            }

            