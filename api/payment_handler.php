<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST,GET');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With'); 
    
    // Initialize API services
    require_once("../includes/init.php");

    $data = json_decode(file_get_contents("php://input"));
    $payments = new Payment($connect);
    
             if(isset($data->pay))
            {
                $exec = $payments->add_payment(
                    $data->meter_id,
                    $data->customer_id, 
                    $data->amount, 
                    $data->reference, 
                    $data->user_email 
                    
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

            elseif(isset($data->update_payment))
            {
                $exec = $payments->update_payment(
                    $data->meter_id,
                    $data->amount_paid,
                    $data->paid_status,
                    $data->reference,
                    $data->phone_no,
                    $data->payment_method
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