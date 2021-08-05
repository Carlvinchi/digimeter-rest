<?php
    // Initialize API services
    require_once("../includes/init.php");

    $admin = new Admin($connect);
    $user = new User($connect);
    $bills = new Billing($connect);
    $pay = new Payment($connect);
    if(isset($_GET['all_users']))
    {
        $result = $user->get_all_users($_GET['no']);
        if(!empty($result))
        {
    
        
                    foreach($result as $item){
                    
                        ?>
        
                                <tr>
                                    <td> <?php echo $item['first_name'] ?> </td>
                                    <td> <?php echo $item['last_name'] ?> </td>
                                    <td> <?php echo $item['customer_id'] ?> </td>
                                    <td> <?php echo $item['user_email'] ?> </td>
                                    <td> <?php echo $item['phone_no'] ?> </td>
                                    <td> <?php echo $item['digital_address'] ?> </td>
                                    <td> <?php echo $item['address_street'] ?> </td>
                                    <td> <?php echo $item['address_city'] ?> </td>
                                    <td> <?php echo $item['address_region'] ?> </td>
                                    <td> <?php echo $item['last_login'] ?> </td>
                                    
                            </tr>
                        <?php
        
        
                    }
         
                    
        }
        else
            echo 0;
            

    }

    elseif(isset($_GET["re-meters"]))
        {
                
            $result = $bills->get_meters('meter',$_GET["no"]);
            
            if(!empty($result))
            {

                foreach($result as $item){
                
                    ?>
    
                            <tr>
                                <td> <?php echo $item['meter_id'] ?> </td>
                                <td> <?php echo $item['meter_account'] ?> </td>
                                <td> <?php echo $item['meter_owner'] ?> </td>
                                <td> 
                                <?php echo $item['lock_status'] ?> |
                                <button type="button" class="btn btn-warning" onclick="lock(<?php echo $item['meter_id']?>)">Change</button> 
                                 </td>
                                <td> <?php echo $item['health_status'] ?> </td>
                                <td> <?php echo $item['entry_date'] ?> </td>
                                <td>
                                    <div>
                                    
                                    
                                
                                        <li class="nav-item dropdown d-none d-lg-block">
                                            <button class="nav-link btn btn-success" id="createbuttonDropdown" data-toggle="dropdown" aria-expanded="false">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="createbuttonDropdown">
            
                                            <h6 class="p-3 mb-0">
                                            <a type="button" class="btn btn-warning" href="http://localhost/digifront/in/readings.php?meter_id=<?php echo $item['meter_id']?>">Readings</a>
                                            </h6>
                                            <h6 class="p-3 mb-0">
                                            <a type="button" class="btn btn-warning" href="http://localhost/digifront/in/billings.php?meter_id=<?php echo $item['meter_id']?>">Billing</a>
                                            </h6>
                                            <h6 class="p-3 mb-0">
                                            <a type="button" class="btn btn-warning" href="http://localhost/digifront/in/meter-activity.php?meter_id=<?php echo $item['meter_id']?>">Activity</a>
                                            </h6>
                                            <h6 class="p-3 mb-0">
                                            <a type="button" class="btn btn-warning" href="http://localhost/digifront/in/meter-payments.php?meter_id=<?php echo $item['meter_id']?>">Payments</a>
                                            </h6>
                                            <h6 class="p-3 mb-0">
                                            <button type="button" class="btn btn-warning" onclick="get_details(<?php echo $item['meter_id']?>)">Update</button>
                                            </h6>
                                            <h6 class="p-3 mb-0">
                                            <button type="button" class="btn btn-danger" onclick="confirm_delete(<?php echo $item['meter_id']?>)">Delete</button>
                                            </h6>
                                            
                                            </div>
                                        </li>
                                    
                                    
                                     </div>
                                    </td>
                                
                        </tr>
                    <?php
    
    
                }
            }
            else
                echo 0;
            
                
        }
 
        elseif(isset($_POST["add_meter"]))
        {
                
            $exec = $bills->add_meter(
                $_POST["meter_id"], 
                $_POST["m_bal"], 
                $_POST["meter_owner"], 
                $_POST["meter_addr"]
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

    elseif(isset($_GET["total_users"]))
        {
                
            $result = $user->find();
            
            if(!empty($result)) {

                echo sizeof($result);
            }
            else
                echo 0;
            
                
        }

        elseif(isset($_GET["total_meters"]))
        {
                
            $result = $bills->get_all('meter');
            
            if(!empty($result)) {

                echo sizeof($result);
            }
            else
                echo 0;
            
                
        }

        elseif(isset($_GET["single_detail"]))
        {
            
            $result = $bills->find($_GET["meter_id"],'meter');
                
            if(is_array($result))
            {
    
                foreach($result as $item){
                    ?>
    
                    <form class="forms-sample" id="edit_meter" onsubmit="return false" autocomplete="off">
                      
                      
                      <div class="form-group">
                        <label for="exampleInputPassword1">Meter Owner</label>
                        <input type="text" class="form-control" name="m_owner" id="m_owner" value="<?php echo $item['meter_owner']?>" required>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputConfirmPassword1">Meter ID</label>
                        <input type="text" class="form-control" name="m_id" id="m_id" value="<?php echo $item['meter_id']?>" required>
                      </div>
                      
                      <div class="form-group">
                        <label for="exampleInputConfirmPassword1">Balance</label>
                    
                        <input type="text" class="form-control" name="mt_bal" id="mt_bal" value="<?php echo $item['meter_account']?>" required>
                        
                        <input type="hidden" name="item_no" id="item_no"  value="<?php echo $item['item_no']?>">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputConfirmPassword1">Meter Address</label>
                        <input type="text" class="form-control" name="m_addr" id="m_addr" required value="<?php echo $item['meter_address']?>" >
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
        elseif(isset($_POST["edit"])){
            $exec = $bills->update_meter(
                $_POST["m_id"], 
                $_POST["m_bal"], 
                $_POST["m_owner"], 
                $_POST["m_addr"], 
                $_POST["item_no"]
            );
            
            echo $exec;
        }

        elseif(isset($_POST["delete"])){
            $exec = $bills->delete($_POST["m_id"],'meter');
            echo $exec;
        }

        elseif(isset($_POST["lock"])){
            $exec = $bills->admin_lock($_POST["m_id"]);
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

    elseif(isset($_GET['get_bills']))
    {
        $result = $bills->fetch_bills($_GET['m_id'], 'billing', $_GET['no']);
        if(!empty($result))
        {
    
        
                    foreach($result as $item){
                    
                        ?>
        
                                <tr>
                                    <td> <?php echo $item['meter_id'] ?> </td>
                                    <td> <?php echo $item['cost_amount'] ?> </td>
                                    
                                    <td> <?php echo $item['paid_status'] ?> </td>
                                    <td> <?php echo $item['entry_time'] ?> </td>     
                            </tr>
                        <?php
        
        
                    }
         
                    
        }
        else
            echo 0;
            

    }

    elseif(isset($_GET['get_activity']))
    {
        $result = $bills->fetch_history($_GET['m_id'], 'usage_history', $_GET['no']);
        if(!empty($result))
        {
    
        
                    foreach($result as $item){
                    
                        ?>
        
                                <tr>
                                    <td> <?php echo $item['meter_id'] ?> </td>
                                    <td> <?php echo $item['balance_before'] ?> </td>
                                    
                                    <td> <?php echo $item['amount'] ?> </td>
                                    <td> <?php echo $item['balance_after'] ?> </td>
                                    <td> <?php echo $item['action'] ?> </td>
                                    <td> <?php echo $item['entry_time'] ?> </td>     
                            </tr>
                        <?php
        
        
                    }
         
                    
        }
        else
            echo 0;
            

    }

    elseif(isset($_GET['get_payment']))
    {
        $result = $bills->fetch_payments($_GET['m_id'], 'payments', $_GET['no']);
        if(!empty($result))
        {
    
        
                    foreach($result as $item){
                    
                        ?>
        
        <tr>
                                    
                                    <td> <?php echo $item['meter_id'] ?> </td>
                                    <td> <?php echo $item['customer_id'] ?> </td>
                                    <td> <?php echo $item['amount'] ?> </td>
                                    <td> <?php echo $item['amount_paid'] ?> </td>
                                    <td> <?php echo $item['paid_status'] ?> </td>
                                    <td> <?php echo $item['transaction_id'] ?> </td>
                                    <td> <?php echo $item['phone_no'] ?> </td>
                                    <td> <?php echo $item['user_email'] ?> </td>
                                    <td> <?php echo $item['payment_method'] ?> </td>
                                    <td> <?php echo $item['entry_time'] ?> </td>
                            </tr>
                        <?php
        
        
                    }
         
                    
        }
        else
            echo 0;
            

    } 

    elseif(isset($_POST["add-money"])){
        $exec = $pay->add_payment_direct(
            $_POST["met-id"],
            $_POST["cu-id"],
            $_POST["amount"],
            $_POST["amount"],
            $_POST["paid_st"], 
            $_POST["trx_id"], 
            $_POST["em"], 
            $_POST["mob"], 
            $_POST["met"]
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

    elseif(isset($_POST["create-admin"])){
        $exec = $admin->create_user(
            $_POST["admin-email"],
            $_POST["admin-mobile"], 
            $_POST["admin-firstName"], 
            $_POST["admin-lastName"], 
            $_POST["admin-pass"]
        );
        
        echo $exec;
    }

    elseif(isset($_GET['admin-list']))
    {
        $result = $admin->find_all_admins($_GET["no"]);
        if(!empty($result))
        {
    
        
                    foreach($result as $item){
                    
                        ?>
        
                                <tr>
                                    <td> <?php echo $item['user_email'] ?> </td>
                                    <td> <?php echo $item['first_name'] ?> </td>
                                    
                                    <td> <?php echo $item['last_name'] ?> </td>
                                    <td> <?php echo $item['phone_no'] ?> </td>     
                            </tr>
                        <?php
        
        
                    }
         
                    
        }
        else
            echo 0;
            

    }
    
    elseif(isset($_GET["admin-profile"])){
        $data = $admin->find_user("",$_GET['user_id']);

        if(is_array($data)){
    
            foreach($data as $item){
                
                ?>
    
                            <div class="form-group row" >
                                <label for="exampleInputName" class="col-sm-3 col-form-label"> Email</label>
                                <div class="col-sm-9">
                                <input type="email" class="form-control" name="user_email" id="user_email" value="<?php echo $item['user_email']?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMeterID" class="col-sm-3 col-form-label">Mobile No</label>
                                <div class="col-sm-9">
                                <input type="text" class="form-control" name="phone_no" id="phone_no" value="<?php echo $item['phone_no']?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputCustomerID" class="col-sm-3 col-form-label">First Name</label>
                                <div class="col-sm-9">
                                <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $item['first_name']?>" required>
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="exampleInputCustomerID" class="col-sm-3 col-form-label">Last Name</label>
                                <div class="col-sm-9">
                                <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $item['last_name']?>" required>
                                <input type="hidden" class="form-control" id="user" name="user" value="<?php echo $_SESSION['admin_id']?>">
                                <input type="hidden" class="form-control" id="admin_change_profile" name="admin_change_profile" value="1">
                            </div>
                            </div>
    
                            <br>
                            <div>
                                <center>
                            &emsp; &emsp;   
                            <input type="submit" value="Submit" class="btn btn-primary mr-2" />
                    
                                </center>
                            </div>
                            
                <?php
    
    
            }
    
            
        }

    }

    elseif(isset($_POST["admin_change_profile"])){
        $exec = $admin->edit_profile_no_password(
            $_POST["user_email"],
            $_POST["phone_no"],
            $_POST["first_name"],
            $_POST["last_name"],
            $_POST["user"]
            );
        echo $exec; 
    }

    elseif(isset($_POST["a_change_p"])){
        $exec = $admin->change_password(
            $_POST["old_pass"],
            $_POST["new_pass1"],
            $_POST["us_id"]
            );

        echo $exec; 
    }

   