<?php
    
    // Initialize API services
    require_once("../includes/init.php");

    $pay = new Payment($connect);
    for($i = 1; $i< 200; $i++){
    $meter_id = "14357098432";
    $customer_id = "15162326667357" ;
    $amount = 10 + $i;
    $amount_paid = 10.5 + $i;
    $paid_status ='Paid'; 
    $transaction_id = "14357098432".$i; 
    $user_email = "oc90699@yahoo.com"; 
    $phone_no = "0543524033";
     $payment_method = "Mobile Money";
    
    
    $result = $pay->add_payment_direct($meter_id,$customer_id, 
    $amount, $amount_paid,
    $paid_status, $transaction_id, 
    $user_email, $phone_no, $payment_method
);
    };
    

    
    echo "Success";
