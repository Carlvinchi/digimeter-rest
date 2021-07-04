<?php
    require_once("C:/xampp/htdocs/digi_rest/vendor/autoload.php");
    use \Firebase\JWT\JWT;
    class Auth extends User
    {
            private $conn;
            private $table = "auth";
            // creating a class constructor
            public function __construct($connect)
            {
                parent::__construct($connect);
                $this->conn = $connect;
                

                $sql = "CREATE TABLE IF NOT EXISTS $this->table (
                    user_email CHAR(64) NOT NULL,
                    token VARCHAR(255) NOT NULL,
                    expiry_date DATETIME
                    )";

                if($this->conn->query($sql)){

                    //echo "Table created successfully.";
                } else{
                    echo "ERROR: Could to execute  " . $this->conn->error;
                }
            }

            public function generate_jwt($secret_key)
            {
                
                $issuer_claim = "DIGIMETER"; // A string containing the name or identifier of the issuer application.
                $audience_claim = "CLIENTS";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim; //Timestamp of when the token should start being considered valid. Should be equal to or greater than iat.
                $expire_claim = $issuedat_claim + 3600; // expire time in seconds
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "user_id" => $_SESSION['user_id'],
                        "customer_id" => $_SESSION['customer_id'],
                        "user_email" => $_SESSION['user_email']
                ));
                    $user_data = parent::find_user('',$_SESSION['user_id']);
                    $jwt = JWT::encode($token, $secret_key);
                return array(
                    $jwt,
                    $user_data
                );
                
            }

            public function verify_jwt($jwt,$secret_key)
            {

                    try {

                        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
                
                        // Access is granted. Add code of the operation here 
                
                        return $decoded;
                
                    }
                    catch (Exception $e)
                    {
                    
                        return $e->getMessage();
                    }
            }

            public function insert_data($user_email, $token, $expiry_date)
            {
                 
                $pre_stmt = $this->conn->prepare(" INSERT INTO $this->table(`user_email`, `token`, `expiry_date`)
                VALUES (?,?,?)
                ");
                $pre_stmt->bind_param("sss",$user_email, $token, $expiry_date);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)
                    return 1;
                else 
                    return NULL;
            }


            public function reset_password($token, $user_email, $new_password,$current_date)
            {

                $pre_stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE token = ? AND user_email = ? ");
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
                    $pre_stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE user_email = ? ");
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

    }
    