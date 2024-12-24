<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "uplift");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL to create table USERS if not exists
$sql = "CREATE TABLE IF NOT EXISTS USERS (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    phone_number INT(11) UNIQUE,
    email VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(40) NOT NULL,
    user_type VARCHAR(15) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    return "Table USERS created successfully <br>";
} else {
    echo "Error creating table: <br>" . mysqli_error($conn);
}
?>