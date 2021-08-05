<?php

    class Admin
    {
        private $conn;
            private $table = "admin";
            // creating a class constructor
            public function __construct($connect)
            {
                $this->conn = $connect;
                // Attempt create table
                $sql = "CREATE TABLE IF NOT EXISTS $this->table (
                    admin_id INT(11) AUTO_INCREMENT,
                    user_email VARCHAR(64),
                    phone_no CHAR(10),
                    first_name CHAR(20),
                    last_name CHAR(20),
                    password VARCHAR(255),
                    reg_date DATETIME,
                    last_login DATETIME,
                    PRIMARY KEY(admin_id) 
                    )";

 
                if($this->conn->query($sql))
                {

                    //echo "Table created successfully.";
                } 
                else
                {
                    echo "ERROR: Could not execute  " . $this->conn->error;
                }
            }


            /* @params $user_name, $user_email, $password, $name */
            public function create_user($user_email,$phone_no,$first_name,$last_name,$password)
            {
                
                if($this->userExists($user_email) == 'Yes')
                {
                    return "User already registered";
                    exit;
                }
                
                $date   = new DateTime(); //this returns the current date time
                $reg_date = $date->format('Y-m-d H:i:s');
                $hashed_password = password_hash($password,PASSWORD_BCRYPT, ["cost"=>15]);
                
                $pre_stmt = $this->conn->prepare("INSERT INTO $this->table (
                `user_email`,`phone_no`,
                 `first_name`,`last_name`,
                 `password`, `reg_date`
                 )
                VALUES (?,?,?,?,?,?)
                ");
                $pre_stmt->bind_param("ssssss",$user_email, $phone_no,$first_name,$last_name,
                $hashed_password, $reg_date);
                
                $res = $pre_stmt->execute() or die($this->conn->error);
                if($res)
                    return "Success";
                
                else
                    return "Error";
                    

            }


            // Check if user already registered
            private function userExists($email)
            {
                $pre_stmt = $this->conn->prepare("SELECT admin_id FROM $this->table WHERE user_email = ? ");
                $pre_stmt->bind_param("s", $email);
                $pre_stmt->execute() or die($this->conn->error);
                $result = $pre_stmt->get_result();
                if($result->num_rows > 0)
                    return 'Yes';
                else
                    return 'No';
                
            }



            public function user_login($user_email, $password)
            {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE user_email = ? ");
                $pre_stmt->bind_param("s", $user_email) ;
                $pre_stmt->execute() or die($this->conn->error);
                $result = $pre_stmt->get_result();
                
                if($result->num_rows < 1)
                {
                    return "Not Registered, Please Register!";
                    exit;
                }

                $row = $result->fetch_assoc();
                if(password_verify($password, $row["password"]))
                {   
                    
                    $date   = new DateTime(); //this returns the current date time
                    $last_login = $date->format('Y-m-d H:i:s');

                    $_SESSION['user_email'] = $row['user_email'];
                    $_SESSION['phone_no'] = $row['phone_no'];
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['admin_id'] = $row["admin_id"];

                    $pre_stmt = $this->conn->prepare("UPDATE $this->table SET last_login = ? WHERE user_email = ?");
                    $pre_stmt->bind_param("ss",$last_login, $user_email);
                    $pre_stmt->execute() or die($this->con->error);

                    return "Success";
                }
                else
                    return "invalid password";
                
            }

            public function find_user($user_email="", $user_id="")
            {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE user_email = ? OR admin_id = ?");
                $pre_stmt->bind_param("ss", $user_email,$user_id) ;
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return "User does not exist";
                else
                    return $result;
                 
            }

            public function find_all_admins($no)
            {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY admin_id DESC LIMIT ?,30");
                $pre_stmt->bind_param("s", $no) ;
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return [];
                else
                    return $result;
                 
            }
 
            public function get_data($pre_stmt) 
            {
                $pre_stmt->execute() or die($this->conn->error); 
                $result = $pre_stmt->get_result();
                if(!$result)
                {
                    return $this->conn->error;
                    } 
        
                $data= array();
                
                 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
                 {
                        $data[]=$row;            
                 }
                    return $data;
            }

            public function change_password($old_password,$new_password,$user_id)
            {   
                $check_password = $this->find_user("",$user_id)[0]["password"];
                //echo $check_password;
                
                if(password_verify($old_password, $check_password) !== TRUE)
                {
                    return "invalid old password";
                    exit;
                }

                $hashed_password = password_hash($new_password,PASSWORD_BCRYPT, ["cost"=>15]);
                $pre_stmt = $this->conn->prepare("UPDATE $this->table SET  
                 `password` = ? WHERE admin_id = ? ");
                $pre_stmt->bind_param("ss",$hashed_password, $user_id);
                $result = $pre_stmt->execute() or die($this->con->error);
                if($result)
                    return "Success";
                    
                else
                    return "Error";
            
            }

            public function reset_password($token, $user_email, $new_password,$current_date)
            {

                $pre_stmt = $this->conn->prepare("SELECT * FROM auth WHERE token = ? AND user_email = ? ");
                $pre_stmt->bind_param("ss",$token, $user_email) ;
                $pre_stmt->execute() or die($this->conn->error);
                $result = $pre_stmt->get_result();
                if($result->num_rows == "")
                {
                    return "invalid code";
                    exit;
                }
                else
                {

                    $row = $result->fetch_assoc();
                    $expiry_date = $row["expiry_date"];
                    if( $expiry_date < $current_date)
                    {
                        return "code expired";
                        exit;
                    }

                    $hashed_password = password_hash($new_password,PASSWORD_BCRYPT, ["cost"=>15]);
                    $pre_stmt = $this->conn->prepare("UPDATE admin SET password = ? WHERE user_email = ? ");
                    $pre_stmt->bind_param("ss", $hashed_password,$user_email) ;
                    $result =  $pre_stmt->execute() or die($this->conn->error);

                    if($result)
                    {
                        return "Password changed";
                        exit;
                    }

                    else
                    {
                        return "Something went wrong!";
                        exit;
                    } 
                        
                }

            }


            public function edit_profile_no_password($user_email,$phone_no,$first_name,$last_name,$admin_id){
        
                $pre_stmt = $this->conn->prepare("UPDATE $this->table SET  
                `user_email` = ?,`phone_no` = ?,
                 `first_name` = ?,`last_name` = ?
                 WHERE admin_id = ?
                ");
                $pre_stmt->bind_param("sssss", $user_email,$phone_no,$first_name,$last_name,$admin_id);
                $result = $pre_stmt->execute() or die($this->con->error);
                if($result)
                    return "Success";

                else
                    return "Error"; 
                
            }
    }
 