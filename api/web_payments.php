<?php
    
    // Initialize API services
    require_once("../includes/init.php");

    $pay = new Payment($connect);
    
            if(isset($_POST["add_payment"]))
            {
                $exec = $pay->add_payment(
                    $_POST["meter_id"],
                    $_POST["customer_id"], 
                    $_POST["amount"], 
                    $_POST["reference"], 
                    $_POST["email"]
                
                );

                if($exec == "Success")
                
                    echo $exec;
                
                else
                    echo $exec;
                
                

            }

            elseif(isset($_GET["pay_data"]))
            {
                $result = $pay->get_payments($_GET["meter_id"],$_GET["customer_id"],$_GET["no"]);

                if(is_array($result))
                {
                    $count = 0;
        
                    foreach($result as $item){
                        $count ++;
                        ?>
        
                                <tr>
                                    <td> <?php echo $item['meter_alias'] ?> </td>
                                    <td> <?php echo $item['meter_id'] ?> </td>
                                    <td> <?php echo $item['customer_id'] ?> </td>
                                    <td> <?php echo $item['amount_paid'] ?> </td>
                                    <td> <?php echo $item['paid_status'] ?> </td>
                                    <td> <?php echo $item['transaction_id'] ?> </td>
                                    <td> <?php echo $item['phone_no'] ?> </td>
                                    <td> <?php echo $item['payment_method'] ?> </td>
                                    <td> <?php echo $item['entry_time'] ?> </td>
                            </tr>
                        <?php
        
        
                    }
        
                    
                }
                

            }

            elseif(isset($_GET["pay_sum"])){

                $result = $pay->get_payment_sum($_GET["meter_id"],$_GET["customer_id"]);
                echo json_encode($result) ;
            }

            

           

            