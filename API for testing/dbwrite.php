<?php

// Change port accordingly
$servername = "localhost";

// REPLACE with your Database name
$dbname = "mobileappdb_test2";
// REPLACE with Database user
$username = "nodemcu_user";
// REPLACE with Database user password
$password = "";

// Keep this API Key value to be compatible with the ESP32 code provided in the project page.
// If you change this value, the ESP32 sketch needs to match
$api_key_value = 'tPmAT5Ab3j7F9';

// keep the respnse payload
$response = ['success' => false, 'massage' => 'Internal server error' ];
// set the http response code
$responseCode = 500; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {    
    
    // get input json payload
    $json = file_get_contents('php://input');
    $json = json_decode($json, true);

    // get api key
    $apiKey = $json['X-API-KEY'] ?? null; // shorthand operator IF X-API-KEY exist in header assing X-API-KEY value ELSE assign null


    if($apiKey == $api_key_value) {
        

        // var_dump($json); return;

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            $response['massage'] = 'Connection failed'.$conn->connect_error;
            $responseCode = 500; // internal server error
        } else {
            // prepare sql connection
            // ref -> https://www.w3schools.com/php/php_mysql_prepared_statements.asp

            $stmt1 = $conn->prepare("INSERT INTO data (dateTimeStamp,gasLeakageDetected,flameDetected,temperatureValue,window1Status) VALUES (?,?,?,?,?);");

            // get values form pay load
            
            $serialPayload=$json['serialPayload'];

            // // Remove the starting "*" character from the payload
            //     $payload = substr($httpPayload, 1);

            // Split the payload into an array of values using "&" and "$" as delimiters
            //$values = explode('&', substr($payload, 0, -1));
            $values = explode('&',$serialPayload);

            // Access each value in the array
            $dateTime = $values[0];
            $gasLeakageDetected = $values[1] == "1";
            $flameDetected = $values[2] == "1";
            $temperatureValue = floatval($values[3]);
            $window1Status = $values[4];
            $gasWeight = floatval($values[5]);


            // bind paramiters to sql statement
            $stmt1->bind_param(
                'siidi',
                $dateTime, 
                $gasLeakageDetected, 
                $flameDetected, 
                $temperatureValue,
                $window1Status,
                //$window2Status,
            );

            if ($stmt1->execute() === true) {
                $response['success'] = true;
                $response['massage'] = 'Data inserted to Main Table successfully';
                $responseCode = 200; // response success
            } else {
                $response['massage'] = 'Error: ' . $sql .' '. $conn->error;
                $responseCode = 500; // internal server error
            }

            $stmt1->close();

            $stmt2 = $conn->prepare("INSERT INTO weight_data (gasWeight,dateTimeStamp) VALUES (?,?);");

            $stmt2->bind_param(
                'ds',
                $gasWeight,
                $dateTime,
            );

            if ($stmt2->execute() === true) {
                $response['success'] = true;
                $response['massage'] = 'Data inserted to Weight Table successfully';
                $responseCode = 200; // response success
            } else {
                $response['massage'] = 'Error: ' . $sql .' '. $conn->error;
                $responseCode = 500; // internal server error
            }

            $stmt2->close();
        }

        // Close the connection
        $conn->close();

    } else {
        $response['massage'] = 'Wrong API Key provided!';
        $responseCode = 401; // unauthorized request
    } 


} else {
    $response['massage'] = 'Method Not Allowed';
    $responseCode = 405; // Method Not Allowed
}

// send response to client
http_response_code($responseCode);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>
