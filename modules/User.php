<?php
    date_default_timezone_set("GMT");
    class User
    {
            private $conn;
            private $table = "users";
            // creating a class constructor
            public function __construct($connect)
            {
                $this->conn = $connect;
                // Attempt create table
                $sql = "CREATE TABLE IF NOT EXISTS $this->table (
                    user_id BIGINT(20) AUTO_INCREMENT,
                    customer_id BIGINT(20) UNIQUE,
                    user_email VARCHAR(64),
                    phone_no CHAR(10),
                    first_name CHAR(20),
                    last_name CHAR(20),
                    digital_address CHAR(32),
                    address_street CHAR(120),
                    address_city CHAR(60),
                    address_region CHAR(60),
                    password VARCHAR(255),
                    reg_date DATETIME,
                    last_login DATETIME,
                    PRIMARY KEY(user_id) 
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
            public function create_user($user_email,$phone_no,$first_name,$last_name,
            $digital_address,$address_street,$address_city,$address_region,$password)
            {
                
                if($this->userExists($user_email) == 'Yes')
                {
                    return "User already registered";
                    exit;
                }
                
                $customer_id = rand(1,50).time().''.rand(51,99);
                $date   = new DateTime(); //this returns the current date time
                $reg_date = $date->format('Y-m-d H:i:s');
                $hashed_password = password_hash($password,PASSWORD_BCRYPT, ["cost"=>15]);
                
                $pre_stmt = $this->conn->prepare("INSERT INTO $this->table (
                `customer_id`,`user_email`,`phone_no`,
                 `first_name`,`last_name`,`digital_address`,
                 `address_street`, `address_city`, `address_region`,
                 `password`, `reg_date`
                 )
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
                ");
                $pre_stmt->bind_param("sssssssssss",$customer_id,$user_email, $phone_no,$first_name,$last_name,
                $digital_address,$address_street,$address_city,$address_region,$hashed_password, $reg_date);
                
                $res = $pre_stmt->execute() or die($this->conn->error);
                if($res)
                    return "Success";
                
                else
                    return "Error";
                    

            }


            // Check if user already registered
            private function userExists($email)
            {
                $pre_stmt = $this->conn->prepare("SELECT user_id FROM $this->table WHERE user_email = ? ");
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
                    $_SESSION['customer_id'] = $row['customer_id'];
                    $_SESSION['phone_no'] = $row['phone_no'];
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['user_id'] = $row["user_id"];

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
                $pre_stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE user_email = ? OR user_id = ?");
                $pre_stmt->bind_param("ss", $user_email,$user_id) ;
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return "User does not exist";
                else
                    return $result;
                 
            }

            public function find()
            {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $this->table");
                
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return [];
                else
                    return $result;
                 
            }

            public function get_all_users($no)
            {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY user_id DESC LIMIT ?,30");
                
                $pre_stmt->bind_param("s",$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return [];
                else
                    return $result;
                 
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
                 `password` = ? WHERE user_id = ? ");
                $pre_stmt->bind_param("ss",$hashed_password, $user_id);
                $result = $pre_stmt->execute() or die($this->con->error);
                if($result)
                    return "Success";
                    
                else
                    return "Error";
            
            }
        
            public function edit_profile_no_password($user_email,$phone_no,$first_name,$last_name,
            $digital_address,$address_street,$address_city,$address_region,$user_id){
        
                $pre_stmt = $this->conn->prepare("UPDATE $this->table SET  
                `user_email` = ?,`phone_no` = ?,
                 `first_name` = ?,`last_name` = ?,`digital_address` = ?,
                 `address_street` = ?, `address_city` = ?, `address_region` = ?
                 WHERE user_id = ?
                ");
                $pre_stmt->bind_param("sssssssss", $user_email,$phone_no,$first_name,$last_name,
                $digital_address,$address_street,$address_city,$address_region,$user_id);
                $result = $pre_stmt->execute() or die($this->con->error);
                if($result)
                    return "Success";

                else
                    return "Error"; 
                
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
    }