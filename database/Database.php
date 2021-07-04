<?php
    class Database
    {

        /*Establish database connection
        @params
        server_name, $user_name, $password, db_name
        http://localhost/todo/backend/database/db.php
        */

        function con($server_name,$user_name,$password,$db_name='')
        {
            $link = new mysqli($server_name,$user_name,$password,$db_name);
            // Check connection
            if ($link->connect_error) {
                die("Connection failed: " . $link->connect_error);
            }
            return $link;
        
        }

        /* Create database if it does not exist 
            @params $db_name
        */
        function create_db($server_name,$user_name,$password,$db_name)
        {
            $sql = "CREATE DATABASE IF NOT EXISTS ".$db_name;
            $result =$this->con($server_name,$user_name,$password);

            if($result->query($sql) === TRUE){
                //echo "Database Created";
            } else{
                echo "ERROR: Could not able to execute $sql. " . $result->error;
            }
            
            // Close connection
            $result->close();


        }

    }

    //$db = new Database();
    //$result = $db->create_db("localhost","root","","mav");
