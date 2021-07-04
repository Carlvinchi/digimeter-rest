<?php
require_once("C:/xampp/htdocs/digi_rest/vendor/autoload.php");
use \Firebase\JWT\JWT;

$secret_key = "secret1234";
                $issuer_claim = "THE_ISSUER"; // this can be the servername
                $audience_claim = "THE_AUDIENCE";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 10; //not before in seconds
                $expire_claim = $issuedat_claim + 60; // expire time in seconds
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => "12",
                        "firstname" => "Kwame",
                        "lastname" => "Abbey",
                        "email" => "oc90@mail.com"
                ));

                $jwt = JWT::encode($token, $secret_key);
                var_dump($jwt)