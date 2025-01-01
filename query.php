<?php
require 'conn.php';
session_start();

// Registration function
function register($conn) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $firstname = mysqli_real_escape_string($conn, $_POST['fname']);
        $lastname = mysqli_real_escape_string($conn, $_POST['lname']);
        $pnumber = mysqli_real_escape_string($conn, $_POST['pnumber']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $UserType = mysqli_real_escape_string($conn, $_POST['ptype']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Check for duplicate email or reg
        $check_sql = "SELECT * FROM USERS WHERE email = '$email'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            echo "Error: Duplicate email";
        } else {
            // Insert new user
            $sql = "INSERT INTO USERS (firstname, lastname, email, phone_number, user_type, password)
                    VALUES ('$firstname', '$lastname', '$email', '$pnumber', '$UserType', '$password')";

            if (mysqli_query($conn, $sql)) {
                echo "Registration successful!";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Login function
function login($conn) {
    $email = '';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql3 = "SELECT * FROM USERS WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $sql3);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];

            echo "Login successful!<br>";
            header('Location: event.php');
        } else {
            echo "Invalid registration number or password.";
        }
    }

        $sql = 'CREATE TABLE IF NOT EXISTS LOGS(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        email VARCHAR(30) NOT NULL,
        last_loggedin TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        )';

        if (mysqli_query($conn, $sql)) {
            echo "LOG table has been created<br>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        
        $sql2 = "INSERT INTO LOGS(firstname, lastname, email)
                SELECT firstname, lastname, email
                FROM USERS
                WHERE email= '$email'
                ON DUPLICATE KEY UPDATE last_loggedin = VALUES(last_loggedin)";

        if (mysqli_query($conn, $sql2)){
            echo "LOGS table has been updated!<br>";
        }else {
            echo "Error: ". mysqli_error($conn);
        }

        
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'event':
            event($conn);
            break;
        
        case 'Logout':
            logout($conn);
            break;
            
        default:
            echo "";
    }
}



function event($conn){
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $EventName = mysqli_real_escape_string($conn, $_POST['name']);
        $EventDescription = mysqli_real_escape_string($conn, $_POST['description']);
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        $Location = mysqli_real_escape_string($conn, $_POST['location']);
        $Visibility = mysqli_real_escape_string($conn, $_POST['visibility']);
        $booking_cap = mysqli_real_escape_string($conn, $_POST['capc']);
        

        $sql = 'CREATE TABLE IF NOT EXISTS EVENTS(
                id int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                description VARCHAR(500) NOT NULL,
                date DATE NOT NULL,
                time TIME NOT NULL,
                location VARCHAR(500) NOT NULL,
                visibility VARCHAR(60) NOT NULL,
                time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                booking_cap int(30),
                email VARCHAR(320) NOT NULL 
                )';
        
        if (mysqli_query($conn, $sql)){
            echo "Events table has been created successfully!";
        } else{
            echo "Error: " . mysqli_error($conn);
        }
        
        $email = $_SESSION['user_email'] ?? '';

        $sql2 = "INSERT INTO EVENTS(name, description, date, time, location, visibility, booking_cap, email)
                VALUES('$EventName', '$EventDescription', '$date', '$time', '$Location', '$Visibility', '$booking_cap', '$email')";
        
        if (mysqli_query($conn, $sql2)){
            echo "Event has been added to the Events table successfully!";
        } else{
            echo "Error: " . mysqli_error($conn);
        }


    }
}

function your_events($conn) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = $_SESSION['user_email'] ?? '';

        $sql = "SELECT * FROM EVENTS WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<br>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "Event Name: ".$row['name']."<br>";
                echo "Description: ".$row['description']."<br>";
                echo "Date: ".$row['date']."<br>";
                echo "Time: ".$row['time']."<br>";
                echo "Location: ".$row['location']."<br>";
                echo "Visibility: ".$row['visibility']."<br><br>";
            }
            
        } else {
            echo "<p>No events found</p>";
        }

    }
}

function logout($conn) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        session_start();
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session

        header('Location: login.php');
        exit();
    }
}

function public_events($conn){
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $sql = "SELECT * FROM EVENTS WHERE visibility = 'public'";
        $result = mysqli_query($conn,$sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<br>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "Event Name: ".$row['name']."<br>";
                echo "Description: ".$row['description']."<br>";
                echo "Date: ".$row['date']."<br>";
                echo "Time: ".$row['time']."<br>";
                echo "Location: ".$row['location']."<br>";
                // echo "Visibility: ".$row['visibility']."<br><br>";
            ?>
                <input type="submit" value="RSVP"><br><br>
            <?php
            }
            
        } else {
            echo "<p>No events found</p>";
        }
    }
}
?>
