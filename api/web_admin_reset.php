<?php
    
    // Initialize API services
    require_once("../includes/init.php");

     // returns array or user not exist
     $auth= new Auth($connect);

     $admin = new Admin($connect);

    if(isset($_POST["forgot"]))
    {
        

        $result = $admin->find_user($_POST["email"],"");
        
        if($result == "User does not exist")
            echo $result;

        else
        {
            $user_email = $result[0]["user_email"];
            $name = $result[0]["first_name"];
                
            date_default_timezone_set("GMT");
            $expFormat = mktime(
                date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y")
                );
            $expiry_date = date("Y-m-d H:i:s",$expFormat);
            $key = rand();
            $token = md5($key);
            $result = $auth->insert_data($user_email, $token, $expiry_date);
            echo "Success";
                   
                /*
                if($result == 1)
                {

                        // Recipient
                    $from = "support@digimeter.com"; //from mail, sender email addrress
                                
                    $to_email = $user_email; //recipient email addrress 
                    
                    //Load POST data from HTML form 
                    $from_name = "Digimeter - Support"; //sender name 

                    //sender email, it will be used in "reply-to" header 
                    $email_subject	 = "Reset Password Code"; //subject for the email
                

                    // Message 
                    $html_content = '
                        <p>Dear '.$name.',</p>
                        <p>Your reset password code is below.</p>
                        <p>-------------------------------------------------------------</p>
                        <p><b>Authorization Code: '.$token.' </b></p>
                        <p>-------------------------------------------------------------</p>
                        <p>.The code will expire after 1 day for security reasons.</p>
                        <p>If you did not request this forgotten password email, no action 
                        is needed, your password will not be reset. However, you may want to log into 
                        your account and change your security password as someone may have guessed it.
                        </p>
                        <p>Thanks,</p>
                        <br>

                        <p><a href="#">Digi-meter Team</a></p>
                        
                        '
                        
                        ;
                    
                    // Header for sender info
                    $headers = "From: $from_name"." <".$from.">";
                    // $headers .= "Reply-To: $fromName"." <".$from.">";
                    // $headers .= "Reply-To: ".$reply_to_email."\r\n";
                    
                    
                        // Set content-type header for sending HTML email
                        $headers .= "\r\n". "MIME-Version: 1.0";
                        $headers .= "\r\n". "Content-type:text/html;charset=UTF-8";
                        
                        // Send email
                    if(mail($to_email, $email_subject, $html_content, $headers))
                    {
                        echo json_encode(
                            array(
                                "message" => "Success",
                                "data" => array(
                                    "user_email" => $user_email,
                                    "token" => $token,
                                    "expiry" => $expiry_date
                                )
                            )
                        );
                    }
                   
                }
            */
                
        }
            
        
        
    }


    elseif(isset($_POST["reset"]))
    {
        
        date_default_timezone_set("GMT");
        $date   = new DateTime(); //this returns the current date time
        $current_date = $date->format('Y-m-d H:i:s');
        
        $result = $admin->reset_password($_POST["token"],$_POST["email"],$_POST["pass2"],$current_date); 

        if($result == "Password changed")
        {
            echo "Success";
        }   
        else
        {
            echo $result;
        } 
            
        
    }