<?php

    class Payment extends Billing
    {
        private $payments = "payments";

        private $conn;
        public function __construct($connect)
        {   
            parent::__construct($connect);
            $this->conn = $connect;
            $sql = "
                CREATE TABLE IF NOT EXISTS $this->payments (
                    payment_id INT(11) AUTO_INCREMENT,
                    meter_id CHAR(20),
                    customer_id BIGINT(20),
                    amount DECIMAL(10,4),
                    amount_paid DECIMAL(10,4),
                    paid_status CHAR(20),
                    transaction_id VARCHAR(20),
                    user_email VARCHAR(32),
                    phone_no CHAR(10),
                    payment_method CHAR(30),
                    entry_time DATETIME,
                    entry_date DATE,
                    PRIMARY KEY(payment_id)
                )
            ";
            
            if($this->conn->query($sql))
            {

            }
            else
            {
                echo "ERROR: Could to execute  " . $this->conn->error;
            }

        }


        public function add_payment($meter_id,$customer_id, $amount, $transaction_id, $user_email)
        {   
            $amount_paid = '0.0000';
            $paid_status ='Pending';
            $entry_time = $this->create_time();
            $entry_date = $this->create_date();
            $pre_stmt = $this->conn->prepare("INSERT INTO $this->payments 
            (
                   `meter_id`,
                    `customer_id`,
                    `amount`,
                    `amount_paid`,
                    `paid_status`,
                    `transaction_id`,
                    `user_email`,
                    `entry_time`,
                    `entry_date`
                    )
            VALUES(?,?,?,?,?,?,?,?,?)");
            $pre_stmt->bind_param("sssssssss",$meter_id,$customer_id, $amount, $amount_paid,
            $paid_status,$transaction_id, $user_email, $entry_time, $entry_date);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";
        }

        public function add_payment_direct($meter_id,$customer_id, $amount, $amount_paid = '0.0000',
        $paid_status ='Paid', $transaction_id, $user_email, $phone_no, $payment_method)
        {   
            $entry_time = $this->create_time();
            $entry_date = $this->create_date();
            $pre_stmt = $this->conn->prepare("INSERT INTO $this->payments 
            (
                   `meter_id`,
                    `customer_id`,
                    `amount`,
                    `amount_paid`,
                    `paid_status`,
                    `transaction_id`,
                    `user_email`,
                    `phone_no`,
                    `payment_method`,
                    `entry_time`,
                    `entry_date`
                    )
            VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $pre_stmt->bind_param("sssssssssss",$meter_id,$customer_id, $amount, $amount_paid,
            $paid_status,$transaction_id, $user_email, $phone_no, $payment_method, $entry_time, $entry_date);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result){
                $this->meter_top_up($meter_id,$amount_paid);
                return "Success";
            }
                
            else
                return "Error";
        }

        private function check_status($meter_id,$transaction_id){

            $pre_stmt = $this->conn->prepare("SELECT  paid_status FROM $this->payments WHERE meter_id = ? AND transaction_id = ?");
            $pre_stmt->bind_param("ss", $meter_id,$transaction_id);
            $pre_stmt->execute() or die($this->con->error);
            $result = $pre_stmt->get_result();
            $row = $result->fetch_assoc();
            $status = $row["paid_status"]; 

            return $status;
    
        }

        public function update_payment($meter_id,$amount_paid,
        $paid_status,$transaction_id, $phone_no, $payment_method)
        {   
            $checker = $this->check_status($meter_id,$transaction_id);
            $entry_time = $this->create_time();
            if($checker != 'Paid')
            {
                    $pre_stmt = $this->conn->prepare("UPDATE  $this->payments SET
                    `amount_paid` = ?,
                    `paid_status` = ?,
                    `phone_no` = ?,
                    `payment_method` = ?,
                    `entry_time` = ?
                    WHERE meter_id = ? AND transaction_id = ?
                ");    
                $pre_stmt->bind_param("sssssss",$amount_paid,$paid_status, $phone_no, 
                $payment_method,$entry_time, $meter_id, $transaction_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)
                {   
                    $credits =  ($amount_paid - ($amount_paid * 0.02));
                    $amount = round($credits,2); 
                    $this->meter_top_up($meter_id,$amount);
                    return "Success";
                }
                    
                else
                    return "Error";
            }

            return "NOT ALLOWED!";
           
        }

        public function get_payments($meter_id,$customer_id,$no)
        {
            $pre_stmt = $this->conn->prepare("SELECT 
            meter_alias.meter_alias, payments.meter_id,payments.customer_id,payments.amount,payments.amount_paid,
            payments.paid_status,payments.transaction_id,payments.phone_no,payments.payment_method,
            payments.entry_time FROM meter_alias LEFT JOIN payments on 
            payments.meter_id = meter_alias.meter_id 
            WHERE meter_alias.customer_id = ? AND meter_alias.meter_id = ? ORDER BY payment_id DESC LIMIT ?,30"
        
        );
                $pre_stmt->bind_param("sss", $customer_id,$meter_id,$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
        }

        public function get_payment_sum($meter_id,$customer_id)
        {
            $pre_stmt = $this->conn->prepare("SELECT SUM(amount_paid) AS total FROM `payments` WHERE customer_id = ? AND meter_id = ?"
        
        );
                $pre_stmt->bind_param("ss", $customer_id,$meter_id);
                 $pre_stmt->execute() or die($this->conn->error);
                 $result = $pre_stmt->get_result();
                 $row = $result->fetch_assoc();
                
                if($row['total'] == NULL)
                    return 0;
                else
                    return $row['total'];
        }

    } 
