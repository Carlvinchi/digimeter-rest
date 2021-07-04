<?php
    // Initialize API services
    require_once("../includes/init.php");

    $auth = new Auth($connect);
    if(isset($_POST["change_profile"])){
        $exec = $auth->edit_profile_no_password(
            $_POST["user_email"],
            $_POST["phone_no"],
            $_POST["first_name"],
            $_POST["last_name"],
            $_POST["digital_address"],
            $_POST["street"],
            $_POST["city"],
            $_POST["region"],
            $_POST["user"]
            );
        echo $exec; 
    }
    elseif(isset($_POST["change_p"])){
        $exec = $auth->change_password(
            $_POST["old_pass"],
            $_POST["new_pass1"],
            $_POST["us_id"]
            );

        echo $exec; 
    }
    