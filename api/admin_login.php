<?php
    
    // Initialize API services
    require_once("../includes/init.php");

    
        $admin = new Admin($connect);

        $exec = $admin->user_login(
        $_POST["email"],
        $_POST["pass"]
        );

        if($exec == "Success")
        {
            echo $exec;
        }
        else
        {
            echo $exec;
        }
        
    
    

    