<?php

    // Initialize API services
    require_once("../includes/init.php");
    $user = new User($connect);
    $data = $user->find_user("",$_GET['user_id']);

    if(is_array($data)){
        $count = 0;

        foreach($data as $item){
            
            ?>

                        <div class="form-group row" >
                            <label for="exampleInputName" class="col-sm-3 col-form-label">User Email</label>
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
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="exampleInputCustomerID" class="col-sm-3 col-form-label">Digital Address</label>
                            <div class="col-sm-9">
                            <input type="text" class="form-control" name="digital_address" id="digital_address" value="<?php echo $item['digital_address']?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="exampleInputCustomerID" class="col-sm-3 col-form-label">Street Address</label>
                            <div class="col-sm-9">
                            <input type="text" class="form-control" name="street" id="street" value="<?php echo $item['address_street']?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="exampleInputCustomerID" class="col-sm-3 col-form-label">City</label>
                            <div class="col-sm-9">
                            <input type="text" class="form-control" name="city" id="city" value="<?php echo $item['address_city']?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="exampleInputCustomerID" class="col-sm-3 col-form-label">Region</label>
                            <div class="col-sm-9">
                            <input type="text" class="form-control" name="region" id="region" value="<?php echo $item['address_region']?>" required>
                            <input type="hidden" class="form-control" id="user" name="user" value="<?php echo $_SESSION['user_id']?>">
                            <input type="hidden" class="form-control" id="change_profile" name="change_profile" value="1">
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