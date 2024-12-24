<?php
require 'conn.php';

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
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql3 = "SELECT * FROM USERS WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $sql3);

        if (mysqli_num_rows($result) > 0) {
            echo "Login successful!<br>";
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
?>
