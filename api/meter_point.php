<?php
     
    
    // Initialize API services
    require_once("../includes/init.php");

        $bills = new Billing($connect);
            if(isset($_POST['readings']))
            {
                $exec = $bills->add_reading(
                $_POST["meter_id"],
                $_POST["reading"],
                $_POST["volume_consumed"],
                $_POST["cost"]
                );

                     echo $exec;
                
                
            }

            elseif(isset($_GET['check_balance']))
            {
                $exec = $bills->get_balance($_GET['meter_id']);

                    echo $exec[0]['meter_account'];
            }

            elseif(isset($_GET['check_borrow']))
            {
                $exec = $bills->check_borrowed($_GET['meter_id']);

                    echo $exec;
            }

            elseif(isset($_GET['check_lock']))
            {
                $exec = $bills->get_lock_status($_GET['meter_id']);

                    echo $exec;
            }
            