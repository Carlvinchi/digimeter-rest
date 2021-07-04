<?php
    require_once("../includes/init.php");

    $auth = new Auth($connect);

   # $gen_token = $auth->generate_jwt(SECRET_KEY);
    /*
    echo json_encode(array(
        "message" => "Token generated",
        "data" => $gen_token
        
    ));*/

    #$decode = $auth->verify_jwt($gen_token,SECRET_KEY);

    #var_dump($decode);
   # $key = md5("\(0):=5(O)");
   # echo $key;
   $fet = $auth->find();
   print_r($fet);