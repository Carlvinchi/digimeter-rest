<?php
    // Initialize API services
    require_once("../includes/init.php");

    
    $bills = new Billing($connect);

        if(isset($_POST["add"])){
            
        $exec = $bills->add_alias_meter(
            $_POST["meter_id"],
            $_POST["customer_id"],
            $_POST["meter_alias"]
            );
    
            if($exec == "Success")
            {
                echo $exec;
            }
            else
            {
                echo $exec;
            }
        }

        elseif(isset($_POST["update"])){
            
            $exec = $bills->add_alias_meter(
                $_POST["meter_id"],
                $_POST["customer_id"],
                $_POST["meter_alias"]
                );
        
                if($exec == "Success")
                {
                    echo $exec;
                }
                else
                {
                    echo $exec;
                }
            }

            elseif(isset($_POST["delete"])){
            
                $exec = $bills->add_alias_meter(
                    $_POST["meter_id"],
                    $_POST["customer_id"],
                    $_POST["meter_alias"]
                    );
            
                    if($exec == "Success")
                    {
                        echo $exec;
                    }
                    else
                    {
                        echo $exec;
                    }
                }
        
    
    

    