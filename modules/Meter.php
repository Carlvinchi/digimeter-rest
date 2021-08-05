<?php
    date_default_timezone_set("GMT");
    

    class Meter
    {   
        private $meter = "meter";
        private $alias_meter = "meter_alias";
        private $history = "usage_history";
        private $conn;
        public function __construct($connect)
        {
            $this->conn = $connect;
            $sql = "
                CREATE TABLE IF NOT EXISTS $this->meter(
                    item_no BIGINT(20) AUTO_INCREMENT,
                    meter_id CHAR(20),
                    meter_account DECIMAL(11,4),
                    bal_b4 DECIMAL(11,4) DEFAULT '0.0000',
                    meter_owner CHAR(32),
                    meter_address VARCHAR(255),
                    lock_status CHAR(15) DEFAULT 'UNLOCKED',
                    health_status CHAR(15) DEFAULT 'GOOD',
                    entry_date DATETIME,
                    last_updated DATETIME,
                    admin_lock CHAR(10) DEFAULT '0',
                    user_lock CHAR(10) DEFAULT '0',
                    PRIMARY KEY(item_no)
                )
            ";

            $alias = "
                CREATE TABLE IF NOT EXISTS $this->alias_meter (
                    item_num BIGINT(20) AUTO_INCREMENT,
                    meter_id CHAR(20),
                    customer_id BIGINT(20),
                    meter_alias CHAR(32),
                    entry_date DATETIME,
                    last_updated DATETIME,
                    PRIMARY KEY(item_num)
                )
            ";

            $sql1 = "
                CREATE TABLE IF NOT EXISTS $this->history (
                    entry_id BIGINT(20) AUTO_INCREMENT,
                    meter_id CHAR(20),
                    balance_before DECIMAL(10,4),
                    amount DECIMAL(10,4),
                    balance_after DECIMAL(10,4),
                    action CHAR(20),
                    entry_time DATETIME,
                    entry_date DATE,
                    PRIMARY KEY(entry_id)
                )
            ";
 
            if($this->conn->query($sql) && $this->conn->query($sql1) && $this->conn->query($alias))
            {

            }
            else
            {
                echo "ERROR:  " . $this->conn->error;
            }

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

        public function create_time(){
            $time  = new DateTime(); //this returns the current date time
            return $time->format('Y-m-d H:i:s');
        }

        public function create_date(){
            $date   = date('Y-m-d'); //this returns the current date time
            return $date;
        }

        public function add_meter($meter_id, $meter_account = 0.00, $meter_owner = '', $meter_address)
        {   
            $entry_date = $this->create_time();
            $pre_stmt = $this->conn->prepare("INSERT INTO $this->meter 
            (`meter_id`, `meter_account`, `meter_owner`, `meter_address`, `entry_date`)
            VALUES(?,?,?,?,?)");
            $pre_stmt->bind_param("sssss",$meter_id, $meter_account, $meter_owner, $meter_address, $entry_date);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";
        }

        public function add_alias_meter($meter_id, $customer_id, $meter_alias)
        {   
            $check_meter_exist = $this->find($meter_id,"meter");
            if($check_meter_exist == NULL){
                return "Meter is not registered!";
                exit;
            }
            $entry_date = $this->create_time();
            $pre_stmt = $this->conn->prepare("INSERT INTO $this->alias_meter 
            (`meter_id`, `customer_id`, `meter_alias`, `entry_date`)
            VALUES(?,?,?,?)");
            $pre_stmt->bind_param("ssss",$meter_id, $customer_id, $meter_alias, $entry_date);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";
        }



        public function update_meter($meter_id, $meter_account, $meter_owner, $meter_address, $item_no)
        {   
            
            $last_updated = $this->create_time();
            $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
            `meter_id` = ?, `bal_b4` = `meter_account`,`meter_account` = ?, `meter_owner` = ?, `meter_address` = ?, `last_updated` =? 
            WHERE item_no = ?");
            $pre_stmt->bind_param("ssssss",$meter_id, $meter_account, $meter_owner, $meter_address, $last_updated, $item_no);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";
        }

        public function update_alias_meter($meter_id, $customer_id, $meter_alias)
        {   
            $last_updated = $this->create_time();
            $pre_stmt = $this->conn->prepare("UPDATE $this->alias_meter SET
            `meter_alias` = ?, `last_updated` = ?
            WHERE meter_id = ? AND customer_id = ?");
            $pre_stmt->bind_param("ssss",$meter_alias, $last_updated, $meter_id, $customer_id);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";
        }
 
        public function deduct_cost_of_water($meter_id, $amount_due)
        {   $last_updated = $this->create_time();
            $check_borrow = $this->check_borrowed($meter_id);

            if($check_borrow == "YES"){

                $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
                `bal_b4` = `meter_account`,  `meter_account` = `meter_account` - ?, `used_amount` = `used_amount` + ?, `last_updated` =? 
                WHERE meter_id = ?");
                $pre_stmt->bind_param("ssss",$amount_due,$amount_due, $last_updated, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)
                {   
                    $data = $this->find($meter_id,$this->meter);
                    if($data != NULL)
                    {   $action = "Deduct";
                        $bal_b4 = $data[0]["bal_b4"];
                        $bal_aft = $data[0]["meter_account"];
                        $this->add_history($meter_id,$amount_due,$bal_b4,$bal_aft,$action);
                    }

                    return "Success";
                }
                

            }

            else{

                $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
                `bal_b4` = `meter_account`,  `meter_account` = `meter_account` - ?, `last_updated` =? 
                WHERE meter_id = ?");
                $pre_stmt->bind_param("sss",$amount_due, $last_updated, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)
                {   
                    $data = $this->find($meter_id,$this->meter);
                    if($data != NULL)
                    {   $action = "Deduct";
                        $bal_b4 = $data[0]["bal_b4"];
                        $bal_aft = $data[0]["meter_account"];
                        $this->add_history($meter_id,$amount_due,$bal_b4,$bal_aft,$action);
                    }
    
                    return "Success";
                }
                    
                
            }
            
            
        }
 
        public function meter_top_up($meter_id, $amount)
        {   
            $last_updated = $this->create_time();
            $check_borrow = $this->check_borrowed($meter_id);
            if($check_borrow == "YES"){
               // $used_amount = $this->find($meter_id,$this->meter)[0]["used_amount"];
               $borrow_bal = $this->find($meter_id,$this->meter)[0]["borrowed_bal"];
                 if($amount + $borrow_bal > 0){
                        $amount = $amount + $borrow_bal;
                    $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
                    `bal_b4` = `meter_account`, `meter_account` = `meter_account` + ?, `last_updated` = ? 
                    WHERE meter_id = ?");
                    $pre_stmt->bind_param("sss",$amount, $last_updated, $meter_id);
                    $result = $pre_stmt->execute() or die($this->conn->error);
                    if($result)
                    {   

                        $data = $this->find($meter_id,$this->meter);
                        if($data != NULL)
                        {   $action = "Add";
                            $bal = $data[0]["bal_b4"];
                            $bal_aft = $data[0]["meter_account"];
                            $this->add_history($meter_id,$amount,$bal,$bal_aft,$action);


                            if($bal_aft >= 0.0000)
                                $this->off_borrowed_mode($meter_id);
                        }
                    
                        return "Success";
                    }
                    


                 }

                 else{
                      //$borrow_bal = $borrow_bal + $amount;
                    $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
                    `bal_b4` = `meter_account`, `borrowed_bal` = `borrowed_bal` + ?, `last_updated` = ? 
                    WHERE meter_id = ?");
                    $pre_stmt->bind_param("sss",$amount, $last_updated, $meter_id);
                    $result = $pre_stmt->execute() or die($this->conn->error);
                    if($result)
                    {   
    
                        $data = $this->find($meter_id,$this->meter);
                        if($data != NULL)
                        {   $action = "Add";
                            $bal = $data[0]["bal_b4"];
                            $bal_aft = $data[0]["meter_account"];
                            $this->add_history($meter_id,$amount,$bal,$bal_aft,$action);
    
    
                            
                        }
                    
                        return "Success";
                    }
                 }
               

                
                
            }

            else
            {

                $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
                `bal_b4` = `meter_account`, `meter_account` = `meter_account` + ?, `last_updated` =? 
                WHERE meter_id = ?");
                $pre_stmt->bind_param("sss",$amount, $last_updated, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)
                {   
                    $data = $this->find($meter_id,$this->meter);
                    if($data != NULL)
                    {   $action = "Add";
                        $bal = $data[0]["bal_b4"];
                        $bal_aft = $data[0]["meter_account"];
                        $this->add_history($meter_id,$amount,$bal,$bal_aft,$action);
                    }
                
                    return "Success";
                }
            

            }
        }

        public function user_lock($meter_id)
        {   
            $status = $this->find($meter_id,"meter");
            if($status[0]['lock_status'] == "UNLOCKED")
            {
                $lock = "LOCKED";
                $user_lock = 1;
                $last_updated = $this->create_time();
                $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
               `lock_status` = ?, `last_updated`  = ?, `user_lock` = ?
                WHERE meter_id = ?");
                $pre_stmt->bind_param("ssss",$lock, $last_updated, $user_lock, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)  
                    return "Success";
                    
                else
                    return "Error";
            }
            elseif($status[0]['lock_status'] == "LOCKED"){
                if($status[0]['admin_lock'] == 1){
                    return "You can't unlock, contact support!";
                    exit;
                }

                $lock = "UNLOCKED";
                $user_lock = 0;
                $last_updated = $this->create_time();
                $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
               `lock_status` = ?, `last_updated`  = ?, `user_lock` = ?
                WHERE meter_id = ? ");
                $pre_stmt->bind_param("ssss",$lock, $last_updated, $user_lock, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)  
                    return "Success";
                    
                else
                    return "Error";
            }
           
        }

        public function admin_lock($meter_id)
        {   
            $status = $this->find($meter_id,"meter");
            if($status[0]['lock_status'] == "UNLOCKED")
            {
                $lock = "LOCKED";
                $admin_lock = 1;
                $last_updated = $this->create_time();
                $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
               `lock_status` = ?, `last_updated`  = ?, `admin_lock` = ?
                WHERE meter_id = ?");
                $pre_stmt->bind_param("ssss",$lock, $last_updated, $admin_lock, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)  
                    return "Success";
                    
                else
                    return "Error";
            }
            elseif($status[0]['lock_status'] == "LOCKED"){
                
                $lock = "UNLOCKED";
                $admin_lock = 0;
                $last_updated = $this->create_time();
                $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
               `lock_status` = ?, `last_updated`  = ?, `admin_lock` = ?
                WHERE meter_id = ? ");
                $pre_stmt->bind_param("ssss",$lock, $last_updated, $admin_lock, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                if($result)  
                    return "Success";
                    
                else
                    return "Error";
            }
           
        }

        
        public function add_history($meter_id, $amount, $bal_b4, $bal_aft,$action)
        {   
            $entry_time = $this->create_time();
            $entry_date = $this->create_date();
            $pre_stmt = $this->conn->prepare("INSERT INTO $this->history 
            (`meter_id`, `balance_before`, `amount`, `balance_after`, `action`, `entry_time`, `entry_date`)
            VALUES(?,?,?,?,?,?,?)");
            $pre_stmt->bind_param("sssssss",$meter_id, $bal_b4, $amount, 
            $bal_aft, $action, $entry_time,$entry_date);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";
        }


        public function find($meter_id,$table)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $table WHERE meter_id = ?");
                $pre_stmt->bind_param("s", $meter_id);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }
 
        public function fetch_readings($meter_id,$table,$no)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $table WHERE meter_id = ? ORDER BY entry_id DESC LIMIT ?,30");
                $pre_stmt->bind_param("ss", $meter_id,$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }

        public function fetch_bills($meter_id,$table,$no)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $table WHERE meter_id = ? ORDER BY entry_no DESC LIMIT ?,30");
                $pre_stmt->bind_param("ss", $meter_id,$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }

        public function fetch_history($meter_id,$table,$no)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $table WHERE meter_id = ? ORDER BY entry_id DESC LIMIT ?,30");
                $pre_stmt->bind_param("ss", $meter_id,$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }

        public function fetch_payments($meter_id,$table,$no)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $table WHERE meter_id = ? ORDER BY payment_id DESC LIMIT ?,30");
                $pre_stmt->bind_param("ss", $meter_id,$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }
 
        public function get_all($table)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $table");
                
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return [];
                else
                    return $result;
                 
        }

        public function get_meters($table,$no)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM $table ORDER BY item_no DESC LIMIT ?,30");
                
                $pre_stmt->bind_param("s",$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return [];
                else
                    return $result;
                 
        }

        public function get_alias_meters($customer_id, $no)
        {
                $pre_stmt = $this->conn->prepare("SELECT meter.meter_account,meter.lock_status,meter.health_status,
                meter_alias.meter_id,meter_alias.customer_id,meter_alias.meter_alias
                FROM meter 
                INNER JOIN meter_alias on meter.meter_id = meter_alias.meter_id
                WHERE meter_alias.customer_id = ? LIMIT ?,30");
                $pre_stmt->bind_param("ss",$customer_id,$no);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }

        public function get_alias_meters_total($customer_id)
        {
                $pre_stmt = $this->conn->prepare("SELECT meter.meter_account,meter.lock_status,meter.health_status,
                meter_alias.meter_id,meter_alias.customer_id,meter_alias.meter_alias
                FROM meter 
                INNER JOIN meter_alias on meter.meter_id = meter_alias.meter_id
                WHERE meter_alias.customer_id = ? ");
                $pre_stmt->bind_param("s",$customer_id);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }

        public function get_single_alias_meter($customer_id,$meter_id)
        {
                $pre_stmt = $this->conn->prepare("SELECT * FROM meter_alias WHERE meter_id = ? AND customer_id = ?");
                $pre_stmt->bind_param("ss",$meter_id,$customer_id);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
                 
        }

        public function get_history($meter_id){
            $result = $this->find($meter_id,$this->history);
            return $result;
        }

        

        public function get_balance($meter_id){
            $result = $this->find($meter_id,$this->meter);
            return $result;
        }

        
        public function get_lock_status($meter_id){
            $result = $this->find($meter_id,$this->meter);
            
            $status = $result[0]['lock_status'] ;
            return $status;
        }
        
        


        public function delete($meter_id,$table){
            $pre_stmt = $this->conn->prepare("DELETE FROM $table WHERE meter_id = ? ");
            $pre_stmt->bind_param("s",$meter_id);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";

        }

        public function delete_alias($meter_id,$customer_id){
            $pre_stmt = $this->conn->prepare("DELETE FROM meter_alias WHERE customer_id = ? AND meter_id = ?");
            $pre_stmt->bind_param("ss",$customer_id,$meter_id);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";

        } 
 
        public function check_borrowed($meter_id)
        {
            $borrowed = $this->find($meter_id,"meter")[0]['borrowed'];
            return $borrowed;
        }

        public function off_borrowed_mode($meter_id)
        {   
            $last_updated = $this->create_time();
            $borrowed = "NO";
            $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
                `borrowed` = ?, `borrowed_bal` = 0, `used_amount` = 0, `last_updated` =? 
                WHERE meter_id = ?");
                $pre_stmt->bind_param("sss",$borrowed, $last_updated, $meter_id);
                $result = $pre_stmt->execute() or die($this->conn->error);
                return "Success";
        }
 
        public function borrow($meter_id)
        {   
           $check_mode = $this->check_borrowed($meter_id);
          $check_bal =  $this->find($meter_id,"meter")[0]['meter_account'];
          if(($check_mode == "YES") || ($check_mode == "NO" && $check_bal > 0.0000) ){
                return "You can't borrow";
          }

            $dd = $this->create_date();
            $pre_stmt = $this->conn->prepare("SELECT SUM(cost) AS cost FROM meter_readings WHERE meter_id = ? AND DATEDIFF(?,entry_date) > 1");
                $pre_stmt->bind_param("ss",$meter_id,$dd);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return "You can't borrow";
                else{
                       $amount =   -$result[0]["cost"]/4;
                       $real_amount = abs($amount);

                       $last_updated = $this->create_time();
                        $borrowed = "YES";
                        $pre_stmt = $this->conn->prepare("UPDATE $this->meter SET
                           `meter_account` = `meter_account` + ?, `borrowed` = ?, `borrowed_bal` = ?, `last_updated` = ? 
                            WHERE meter_id = ?");
                            $pre_stmt->bind_param("sssss",$real_amount, $borrowed, $amount, $last_updated, $meter_id);
                            $result = $pre_stmt->execute() or die($this->conn->error);
                            return "Success";
                }
                    

        }

        

    }

    $qu = "SELECT meter.meter_account,meter_alias.meter_id,meter_alias.customer_id,meter_alias.meter_alias,meter_alias.lock_status,
    meter_alias.health_status
    FROM meter 
    INNER JOIN meter_alias on meter.meter_id = meter_alias.meter_id
    WHERE meter.meter_id = 14357098432";

    $q1 = "SELECT meter.meter_account,meter_alias.meter_id,meter_alias.customer_id,meter_alias.meter_alias,meter_alias.lock_status,
    meter_alias.health_status
    FROM meter 
    INNER JOIN meter_alias on meter.meter_id = meter_alias.meter_id
    WHERE meter_alias.customer_id = 15162326667357";
    $k = "SELECT meter.meter_account,meter_alias.meter_id,meter_alias.customer_id,meter_alias.meter_alias,meter_alias.lock_status,
    meter_alias.health_status
    FROM meter 
    INNER JOIN meter_alias on meter.meter_id = meter_alias.meter_id
    WHERE meter.meter_id = 14357098432 AND meter_alias.customer_id = 15162326667357";
    $kk = "SELECT * FROM `meter_readings` WHERE DATEDIFF('2021-07-09',entry_date) > 1";
    