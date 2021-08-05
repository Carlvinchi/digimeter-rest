<?php
    // Initialize API services
    require_once("../includes/init.php");

    
    $bills = new Billing($connect);

        if(isset($_GET["get_alias_meters"]))
        {
                
            $result = $bills->get_alias_meters(
                $_GET["customer_id"],
                $_GET["no"]
                );
        
                if(is_array($result))
                {
                    $count = 0;
        
                    foreach($result as $item){
                        $count ++;
                        ?>
        
                                <tr>
                                    <td> <?php echo $item['meter_alias'] ?> </td>
                                    <td> <?php echo $item['meter_id'] ?> </td>
                                    <td> <?php echo $item['meter_account'] ?> </td>
                                    <td> <?php echo $item['health_status'] ?> </td>
                                    <td> 
                                    <?php echo $item['lock_status'] ?> |
                                    <button type="button" class="btn btn-warning" onclick="lock(<?php echo $item['meter_id']?>)">Change</button> 
                                    </td>
                                    <td>
                                    <a type="button" class="btn btn-primary" href="http://localhost/digifront/in/metfo.php?meter_id=<?php echo $item['meter_id']?>&customer_id=<?php echo $item['customer_id']?>">View Details</a>
                                    </td>
                                    <td>
                                      <button type="button" class="btn btn-warning" onclick="get_meter(<?php echo $item['customer_id']?>,<?php echo $item['meter_id']?>)">Update</button>
                                    </td>  
                                    <td>
                                    <button type="button" class="btn btn-danger" onclick="confirm_delete(<?php echo $item['meter_id']?>)">Delete</button>
                                    </td>
                            </tr>
                        <?php
        
        
                    }
        
                    
                }
        }

        elseif(isset($_GET["met-info"]))
        {
                
            $result = $bills->get_alias_meters_total(
                $_GET["customer_id"],
                );
            
            if(is_array($result)) {
                $size = sizeof($result);
                $data = $result[0];

                echo json_encode(
                    array($size,$data)
                );
            }
                   
        }

        elseif(isset($_GET["met-bal"]))
        {
                
            $result = $bills->get_balance(
                $_GET["meter_id"],
                );
            
            if(is_array($result)) {

                echo json_encode(
                    $result
                );
            }
                   
        }

        elseif(isset($_GET["met_details"]))
        {
                
            $result = $bills->get_single_alias_meter(
                $_GET["customer_id"],
                $_GET["meter_id"],
                );
            
            if(is_array($result)) {
                

                echo json_encode(
                    array($result)
                );
            }
            
                
        }

        elseif(isset($_GET["single"]))
        {
            
            $result = $bills->get_single_alias_meter(
                $_GET["customer_id"],
                $_GET["meter_id"],
                );
                
                if(is_array($result))
            {
                $count = 0;
    
                foreach($result as $item){
                    $count ++;
                    ?>
    
                    <form class="forms-sample" id="edit_alias_meter" onsubmit="return false" autocomplete="off">
                      
                      
                      <div class="form-group">
                        <label for="exampleInputPassword1">Meter Name</label>
                        <input type="text" class="form-control" name="alias_name" id="alias_name" value="<?php echo $item['meter_alias']?>">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputConfirmPassword1">Meter ID</label>
                        <input type="text" class="form-control" name="mter_id" id="mter_id" value="<?php echo $item['meter_id']?>" >
                      </div>
                      <div class="form-group">
                        <label for="exampleInputConfirmPassword1">Customer ID</label>
                    
                        <input type="text" class="form-control" name="cust_id" id="cust_id" value="<?php echo $_SESSION['customer_id']?>" >
                        <input type="hidden" name="edit_alias" id="edit_alias" required value="1">
                      </div>
                      
                      <br>
                        <div>
                            <center>
                        &emsp; &emsp;   
                        <button type="button" onclick="edit()" class="btn btn-primary mr-2">Save</button>
                        &emsp;
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </center>
                        </div>
                    </form>

                    <?php
    
    
                }
    
                
            }
                
        } 

        elseif(isset($_POST["edit_alias"])){
            $exec = $bills->update_alias_meter($_POST["mter_id"],$_POST["cust_id"],$_POST["alias"]);
            
            echo $exec;
        }

        elseif(isset($_POST["delete"])){
            $exec = $bills->delete_alias($_POST["mter_id"],$_POST["cust_id"]);
            echo $exec;
        }

        elseif(isset($_POST["lock"])){
            $exec = $bills->user_lock($_POST["mter_id"]);
            echo $exec;
        }

        elseif(isset($_GET["borrow"])){
            $exec = $bills->borrow($_GET["meter_id"]);
            echo $exec;
        }

        elseif(isset($_GET['get_readings']))
    {
        $result = $bills->fetch_readings($_GET['m_id'], 'meter_readings', $_GET['no']);
        if(!empty($result))
        {
    
        
                    foreach($result as $item){
                    
                        ?>
        
                                <tr>
                                    <td> <?php echo $item['meter_id'] ?> </td>
                                    <td> <?php echo $item['volume_consumed'] ?> </td>
                                    
                                    <td> <?php echo $item['cost'] ?> </td>
                                    <td> <?php echo $item['entry_time'] ?> </td>     
                            </tr>
                        <?php
        
        
                    }
         
                    
        }
        else
            echo 0;
            

    }

    elseif(isset($_GET["pay_sum"])){
        $exec = $bills->borrow($_GET["meter_id"]);
        echo $exec;
    }

    elseif(isset($_GET['get_all_readings']))
    {
        $result = $bills->get_readings($_GET["meter_id"]);
        if(empty($result))
            echo 0;
        else{
            echo json_encode($result);
        }
        
        
    }

          
        
    
    

    