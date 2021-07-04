<?php
session_start();
    // including all files for initialization

    require_once("config.php");
    require_once("../database/Database.php");
    require_once("../modules/User.php");
    require_once("../modules/Auth.php");
    require_once("../modules/Meter.php");
    require_once("../modules/Billing.php");
    require_once("../modules/Payments.php");
    require_once("../modules/Admin.php");
    require_once("../api/get_auth_hearder.php");
    

    // create the database
    $db = new Database();
    $result = $db->create_db(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

    // connecting to db
    $connect = $db->con(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    
    
