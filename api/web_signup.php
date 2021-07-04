<?php
    // Initialize API services
    require_once("../includes/init.php");

    
        $user = new User($connect);

        $exec = $user->create_user(
        $_POST["email"],
        $_POST["phone_no"],
        $_POST["first_name"],
        $_POST["last_name"],
        $_POST["digital_addr"],
        $_POST["street_name"],
        $_POST["city"],
        $_POST["region"],
        $_POST["pass2"]
        );

        if($exec == "Success")
        {
            echo $exec;
        }
        else
        {
            echo $exec;
        }
        
    
    

    