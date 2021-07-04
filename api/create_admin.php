<?php
    
    // Initialize API services
    require_once("../includes/init.php");

    
        $admin = new Admin($connect);
        $user_email = 'admin@digimter.com';
        $first_name = 'Boasiako';
        $last_name = 'Antwi';
        $phone_no = '0278601016';
        $password = 'cybot95';

        $result = $admin->create_user($user_email,$phone_no,$first_name,$last_name,$password);
        echo $result;

        
    
    

    