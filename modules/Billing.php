<?php

    class Billing extends Meter
    {   
        private $bills = "billing";
        
        private $readings = "meter_readings";
        private $conn;
        public function __construct($connect)
        {   
            parent::__construct($connect);
            $this->conn = $connect;
            $sql = "
                CREATE TABLE IF NOT EXISTS $this->bills (
                    entry_no BIGINT(20) AUTO_INCREMENT,
                    meter_id CHAR(20),
                    cost_amount DECIMAL(10,4),
                    paid_status CHAR(20),
                    entry_time DATETIME,
                    entry_date DATE,
                    PRIMARY KEY(entry_no)
                )
            ";
            $sql1 = "
                CREATE TABLE IF NOT EXISTS $this->readings (
                    entry_id BIGINT(20) AUTO_INCREMENT,
                    meter_id CHAR(20),
                    reading FLOAT(11,4),
                    volume_consumed FLOAT(11,4),
                    cost DECIMAL(10,4),
                    entry_time DATETIME,
                    entry_date DATE,
                    PRIMARY KEY(entry_id)
                )
            ";

            

            if($this->conn->query($sql) && $this->conn->query($sql1))
            {

            }
            else
            {
                echo "ERROR: Could to execute  " . $this->conn->error;
            }

        }
 
        public function add_bill($meter_id, $cost_amount)
        {    
            
            $paid_status = $this->deduct_cost_of_water($meter_id, $cost_amount);
            $entry_time = $this->create_time();
            $entry_date = $this->create_date();
            $pre_stmt = $this->conn->prepare("INSERT INTO $this->bills 
            (`meter_id`, `cost_amount`, `paid_status`, `entry_time`, `entry_date`)
            VALUES(?,?,?,?,?)");
            $pre_stmt->bind_param("sssss",$meter_id,
            $cost_amount,$paid_status,$entry_time, $entry_date);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result)
                return "Success";
            else
                return "Error";
        }
 
        public function add_reading($meter_id, $reading,$volume_consumed, $cost)
        {   
            $entry_time = $this->create_time();
            $entry_date = $this->create_date();
            $pre_stmt = $this->conn->prepare("INSERT INTO $this->readings 
            (`meter_id`, `reading`, `volume_consumed`, `cost`, `entry_time`, `entry_date`)
            VALUES(?,?,?,?,?,?)");
            $pre_stmt->bind_param("ssssss",$meter_id,$reading,$volume_consumed, $cost, $entry_time, $entry_date);
            $result = $pre_stmt->execute() or die($this->conn->error);
            if($result){
                $this->add_bill($meter_id,$cost);
                return "Success";
            }
                
            else
                return "Error";
        }


        public function get_bills($meter_id)
        {
            $pre_stmt = $this->conn->prepare("SELECT * FROM $this->bills WHERE meter_id = ?");
                $pre_stmt->bind_param("s", $meter_id);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
        }

        public function get_readings($meter_id)
        {
            $pre_stmt = $this->conn->prepare("SELECT * FROM $this->readings WHERE meter_id = ?");
                $pre_stmt->bind_param("s", $meter_id);
                $result = $this->get_data($pre_stmt);
                
                if(empty($result))
                    return NULL;
                else
                    return $result;
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

        
    }