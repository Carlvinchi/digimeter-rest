<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST,GET');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With'); 
    
    // Initialize API services
    require_once("../includes/init.php");



    $header = get_authorization_header();
    if($header != NULL){

        $arr = explode(" ",$header);
        $jwt = $arr[1];
        $auth = new Auth($connect);
 
        $decode = $auth->verify_jwt($jwt,SECRET_KEY);

        
        
        if(isset($decode->data) && $decode->iss == "DIGIMETER"){
                
    $data = json_decode(file_get_contents("php://input"));
    $bills = new Meter($connect);
    
            if(isset($data->add_meter))
            {
                $exec = $bills->add_meter(
                $data->meter_id,
                $data->meter_account,
                $data->meter_owner, 
                $data->meter_address
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

            elseif(isset($data->add_meter_alias))
            {
                $exec = $bills->add_alias_meter(
                $data->meter_id,
                $data->customer_id,
                $data->meter_alias
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

            elseif(isset($data->update_meter))
            {
                $exec = $bills->update_meter(
               $data->meter_id,
               $data->meter_account, 
               $data->meter_owner, 
               $data->meter_address, 
               $data->item_no
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

            elseif(isset($data->update_meter_alias))
            {
                $exec = $bills->update_alias_meter(
               $data->meter_id,
                $data->customer_id,
                $data->meter_alias
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

            elseif(isset($data->deduct))
            {
                $exec = $bills->deduct_cost_of_water($data->meter_id, $data->amount_due);

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

            elseif(isset($data->top_up))
            {
                $exec = $bills->meter_top_up($data->meter_id, $data->amount);

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

            elseif(isset($_GET['meter_data']))
            {
                $exec = $bills->find($_GET['meter_id'],"meter");

                    echo json_encode(array(
                        "data" => $exec
                    ));
                
                
                

            }

            elseif(isset($_GET['all_meters']))
            {
                $exec = $bills->get_all("meter");

                    echo json_encode(array(
                        "data" => $exec
                    ));
                
                
                

            }

            elseif(isset($_GET['history']))
            {
                $exec = $bills->get_history($_GET["meter_id"]);

                    echo json_encode(array(
                        "data" => $exec
                    ));
                
                
                

            }

            elseif(isset($_GET['alias']))
            {
                $exec = $bills->get_alias_meters($_GET["customer_id"],$_GET["no"]);

                    echo json_encode(array(
                        "data" => $exec
                    ));
                
                
                

            }

            elseif(isset($_GET['delete']))
            {
                $exec = $bills->delete($_GET["meter_id"],"meter");

                    echo json_encode(array(
                        "message" => $exec
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
