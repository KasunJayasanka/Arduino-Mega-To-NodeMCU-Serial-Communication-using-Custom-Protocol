<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1"> <!-- Refreshes the browser every 1 second -->
    <!-- All the CSS styling for our Web Page, is inside the style tag below -->
    <title>NodeMCU MySQL Datalog</title>
   
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap');
       * {
            margin: 0;
            padding: 0;
        }
        body {
            background: url('img/bg.jpg') no-repeat center center;
            background-attachment: fixed;
            background-size: cover;
            display: grid;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family:Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        }
        .container {
            box-shadow: 0 0 1rem 0 rgba(0, 0, 0, .2);
            border-radius: 1rem;
            position: relative;
            z-index: 1;
            background: inherit;
            overflow: hidden;
                text-align:center;
                padding: 5rem;
            font-size:40px;
                color: black;
        }
        .container:before {
            content: "";
            position: absolute;
            background: inherit;
            z-index: -1;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            box-shadow: inset 0 0 2000px rgba(255, 255, 255, .5);
            filter: blur(10px);
            margin: -20px;
        }

        .column {
          flex: 50%;
        }
        
        .columns {
          display: flex;
          flex-direction: row;
          flex-wrap: wrap;
          width: 100%;
        }
        
        h1{
               font-size: 40px;
        }
        
        p{
            margin: 60px 0 0 0;
            
        }
        
  </style>
</head>
<title>System API Test</title>    
<body>
    <?php
        $host = "localhost";  // host = localhost because database hosted on the same server where PHP files are hosted
        $dbname = "mobileappdb_test2";  // Database name
        $username = "nodemcu_user";  // Database username
        $password = ""; // Database password
        // Establish connection to MySQL database
        $conn = new mysqli($host, $username, $password, $dbname);
        // Check if connection established successfully
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // Query the single latest entry from the database. -> SELECT * FROM table_name ORDER BY col_name DESC LIMIT 1
        $sql = "SELECT * FROM data ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            //Will come handy if we later want to fetch multiple rows from the table
            while($row = $result->fetch_assoc()) { //fetch_assoc fetches a row from the result of our query iteratively for every row in our result.
                //Returning HTML from the server to show on the webpage.
                                                  
                echo '<p style="font-size:100px;"><center>System API Test</center></p>';
                                                  
                echo '<div class="columns">';
                   echo'<div class="column">';

                        echo '<div class="container">';
                
                                                  
                            echo '<p>';
                            echo '   <span class="dht-labels">dateTime = </span>';
                            echo '   <span id="#">'.$row["dateTimeStamp"].'</span>';
                            echo ' </p>';
                                                              
                            echo '<p>';                               
                            echo '   <span class="dht-labels">Gas Leakage = </span>';
                            echo '   <span id="#">'.$row["gasLeakageDetected"].'</span>';
                            echo ' </p>';
            
                            echo '<p>';                               
                            echo '   <span class="dht-labels">Flame Detected = </span>';
                            echo '   <span id="#">'.$row["flameDetected"].'</span>';
                            echo ' </p>';
            
                            echo '<p>';                               
                            echo '   <span class="dht-labels">Temperature Value = </span>';
                            echo '   <span id="#">'.$row["temperatureValue"].'</span>';
                            echo ' </p>';
            
                            echo '<p>';                               
                            echo '   <span class="dht-labels">Window1 Status = </span>';
                            echo '   <span id="#">'.$row["window1Status"].'</span>';
                            echo ' </p>';
            
                                                              
                            echo '</div>';                           
                   echo'</div>';
                                                
                echo '</div>'; 
                                                 
            }
        } else {
            echo "0 results";
        }


        // Query the single latest entry from the database. -> SELECT * FROM table_name ORDER BY col_name DESC LIMIT 1
        $sql2 = "SELECT * FROM weight_data ORDER BY id DESC LIMIT 1";
        $result2 = $conn->query($sql2);

        if ($result2->num_rows > 0) {
            //Will come handy if we later want to fetch multiple rows from the table
            while($row2 = $result2->fetch_assoc()) { //fetch_assoc fetches a row from the result of our query iteratively for every row in our result.
                //Returning HTML from the server to show on the webpage.
                                                    
                echo'<div class="columns">';
                    echo'<div class="column">';
                        echo'<div class="container">';
                        
                            echo '<p>';
                            echo '   <span class="dht-labels">Gas Weight = </span>';
                            echo '   <span id="#">'.$row2["gasWeight"].'</span>';
                            echo ' </p>';     
                        echo'</div>';  
                    echo'</div>';  
                echo'</div>';  
                                                    
            }
        } else {
            echo "0 results";
        }
    ?>
</body>
</html>

<html>
<head>
    
</head>
<body>
    
</body>
</html>
