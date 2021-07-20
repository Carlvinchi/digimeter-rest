<?php
    // Initialize API services
    require_once("../includes/init.php");
    $meter = new Meter($connect);
    $result = $meter->borrow(14357098432);
    print_r($result);
   //$user = new User($connect);
  // $data = $meter->find("14357098432","meter");
   //print_r($data);

