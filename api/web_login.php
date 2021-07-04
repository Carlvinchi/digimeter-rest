<?php
    
    // Initialize API services
    require_once("../includes/init.php");

    
        $auth = new Auth($connect);

        $exec = $auth->user_login(
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
        
    
    

    