<?php
     // Initialize API services
     require_once("../includes/init.php");
     $pay = new Payment($connect);
    $transaction_id = $_GET["trxref"];
    $domain = "http://localhost/digifront/in/index.php";


  $curl = curl_init();
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/$transaction_id",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer sk_test_cb859b103a9e82864c7dd83893960d234c144286",
      "Cache-Control: no-cache",
    ),
  ));
  
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  
  if ($err) {
    echo "cURL Error #:" . $err;
  }

  else 
  {
        
        
        $res = json_decode($response,true);
        $paymentStatus = $res["data"]["status"];
        $paymentAmount = $res["data"]["amount"];
        $email = $res["data"]["customer"]["email"];
        $payment_method = $res["data"]["channel"];
        $bin = $res["data"]["authorization"]["bin"];
        $last4 = $res["data"]["authorization"]["last4"];
        $phone_no = $bin."".$last4;
        $meter_id = $res["data"]["metadata"]["meter_id"];
        

        if($paymentStatus == "success")
        {
        
            $amount_paid = ($paymentAmount/100) ;
            
            $paid_status = "Paid";
            
            $result = $pay->update_payment($meter_id,$amount_paid,$paid_status,$transaction_id,$phone_no,$payment_method);
            if($result == "Success")
            {
                header("location: $domain");
                exit();
            }
            echo $result;
        
        
        }
        else
        {
        echo "SOME ERROR OCCURED.....!!";
        } 
        
    }


?>