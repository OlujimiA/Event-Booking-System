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

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check for duplicate email or reg
        $check_sql = "SELECT * FROM USERS WHERE email = '$email'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            echo "Error: Duplicate email";
        } else {
            // Insert new user
            $sql = "INSERT INTO USERS (firstname, lastname, email, phone_number, user_type, password)
                    VALUES ('$firstname', '$lastname', '$email', '$pnumber', '$UserType', '$hashed_password')";

            if (mysqli_query($conn, $sql)) {
                echo "Registration successful!";
                header('Location: login.php');
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

        $sql3 = "SELECT * FROM USERS WHERE email = '$email'";
        $result = mysqli_query($conn, $sql3);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Store user information in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
        
                echo "Login successful!<br>";
                header('Location: event.php');
                
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
                            VALUES ('" . $user['firstname'] . "', '" . $user['lastname'] . "', '" . $email . "')";
            
                    if (mysqli_query($conn, $sql2)){
                        echo "LOGS table has been updated!<br>";
                    }else {
                        echo "Error: ". mysqli_error($conn);
                    }

            } else {
                echo "Invalid password!";
            }

        } else {
            echo "Account associated with that email does not exist.";
        }
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
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                event_id CHAR(36) NOT NULL DEFAULT (UUID()),
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
                $id = $row['event_id'];
                $sql4 = "SELECT * FROM RSVP WHERE event_id ='$id'";
                $result2 = mysqli_query($conn, $sql4);
                $f_result = mysqli_num_rows($result2);
                
                echo "Event Name: ".$row['name']."<br>";
                echo "Description: ".$row['description']."<br>";
                echo "Date: ".$row['date']."<br>";
                echo "Time: ".$row['time']."<br>";
                echo "Location: ".$row['location']."<br>";
                echo "Visibility: ".$row['visibility']."<br>";
                echo "Bookings: $f_result/".$row['booking_cap']."<br>";

                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['event_id']) . "'>";
                echo "<input type='hidden' name='name' value='" . htmlspecialchars($row['name']) . "'>";
                echo "<input type='hidden' name='description' value='" . htmlspecialchars($row['description']) . "'>";
                echo "<input type='hidden' name='date' value='" . htmlspecialchars($row['date']) . "'>";
                echo "<input type='hidden' name='time' value='" . htmlspecialchars($row['time']) . "'>";
                echo "<input type='hidden' name='location' value='" . htmlspecialchars($row['location']) . "'>";
                echo "<input type='hidden' name='visibility' value='" . htmlspecialchars($row['visibility']) . "'>";
                echo "<input type='hidden' name='booking_cap' value='" . htmlspecialchars($row['booking_cap']) . "'>";
                echo "<input type='submit' name='action' value='Edit'>";
                echo "<input type='submit' name='action' value='delete'>";
                echo "<input type='submit' name='action' value='Send an Invite'><br><br>";
                echo "</form>";
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
    $email = $_SESSION['user_email'] ?? '';
    if($_SERVER["REQUEST_METHOD"] === "POST"){

        $sql = "SELECT * FROM EVENTS WHERE visibility = 'public' AND NOT EXISTS (
                  SELECT 1 
                  FROM RSVP
                  WHERE RSVP.event_id = EVENTS.event_id 
                  AND RSVP.email = '$email')";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<br>";
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['event_id'];
                $sql4 = "SELECT * FROM RSVP WHERE event_id ='$id'";
                $result2 = mysqli_query($conn, $sql4);
                $f_result = mysqli_num_rows($result2);

                if ($f_result < $row['booking_cap']) {

                    echo "Event Name: ".$row['name']."<br>";
                    echo "Description: ".$row['description']."<br>";
                    echo "Date: ".$row['date']."<br>";
                    echo "Time: ".$row['time']."<br>";
                    echo "Location: ".$row['location']."<br>";
                    // echo "Visibility: ".$row['visibility']."<br><br>";
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['event_id']) . "'>";
                    echo "<input type='hidden' name='name' value='" . htmlspecialchars($row['name']) . "'>";
                    echo "<input type='hidden' name='description' value='" . htmlspecialchars($row['description']) . "'>";
                    echo "<input type='hidden' name='date' value='" . htmlspecialchars($row['date']) . "'>";
                    echo "<input type='hidden' name='time' value='" . htmlspecialchars($row['time']) . "'>";
                    echo "<input type='hidden' name='location' value='" . htmlspecialchars($row['location']) . "'>";
                    echo "<input type='hidden' name='booking_cap' value='" . htmlspecialchars($row['booking_cap']) . "'>";
                    echo "<input type='submit' name='action' value='RSVP'><br><br>";
                    echo "</form>";
                } else {
                    echo htmlspecialchars($row['name'])." has been booked to full capacity!<BR>";
                }
                
            }
            
        } else {
            echo "<p>No events found</p>";
        }
    }
}

function up_events($conn){
    $email = $_SESSION['user_email'] ?? '';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $booking_cap = mysqli_real_escape_string($conn, $_POST['booking_cap']);

    
        // Insert event details into the RSVP table
        $sql = "CREATE TABLE IF NOT EXISTS RSVP(
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                event_id CHAR(36) NOT NULL,
                name VARCHAR(30) NOT NULL,
                description VARCHAR(500) NOT NULL,
                date DATE NOT NULL,
                time TIME NOT NULL,
                location VARCHAR(500) NOT NULL,
                time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                booking_cap int(30),
                email VARCHAR(320) NOT NULL)";
        
        if (mysqli_query($conn, $sql)){
            echo "Created RSVP table!";
        } else{
            echo "Error: " . mysqli_error($conn);
        }

        $sql2 = "INSERT INTO RSVP (event_id, name, description, date, time, location, booking_cap, email) 
                 VALUES ('$id', '$name', '$description', '$date', '$time', '$location', '$booking_cap', '$email')";
        
        if (mysqli_query($conn, $sql2)){
            echo "Event has been added to the RSVP table successfully!";
        } else{
            echo "Error: " . mysqli_error($conn);
        }

        $sql3 = "SELECT * FROM RSVP WHERE email = '$email'";
        $result = mysqli_query($conn,$sql3);


        if (mysqli_num_rows($result) > 0) {
            echo "<br><br>";
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['event_id'];
                $sql4 = "SELECT * FROM RSVP WHERE event_id ='$id'";
                $result2 = mysqli_query($conn, $sql4);
                $f_result = mysqli_num_rows($result2);

                echo "Event Name: ".$row['name']."<br>";
                echo "Description: ".$row['description']."<br>";
                echo "Date: ".$row['date']."<br>";
                echo "Time: ".$row['time']."<br>";
                echo "Location: ".$row['location']."<br>";
                echo "Bookings: $f_result/".$row['booking_cap']."<br>";

                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='id' value='" . htmlspecialchars($id) . "'>";
                echo "<input type='submit' name='action' value='cancel'><br><br>";
                echo "</form>";
            }
        } else {
            echo "No events found";
        }
    }
    
}

function upc_events($conn){
    $email = $_SESSION['user_email'] ?? '';

    $sql = "CREATE TABLE IF NOT EXISTS RSVP(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        event_id CHAR(36) NOT NULL,
        name VARCHAR(30) NOT NULL,
        description VARCHAR(500) NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        location VARCHAR(500) NOT NULL,
        time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        booking_cap int(30),
        email VARCHAR(320) NOT NULL)";

    if (mysqli_query($conn, $sql)){
        echo "Created RSVP table!";
    } else{
        echo "Error: " . mysqli_error($conn);
    }

    $sql3 = "SELECT * FROM RSVP WHERE email = '$email'";
    $result = mysqli_query($conn, $sql3);

    if (mysqli_num_rows($result) > 0) {
        echo "<br><br>";
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['event_id'];
            $sql4 = "SELECT * FROM RSVP WHERE event_id ='$id'";
            $result2 = mysqli_query($conn, $sql4);
            $f_result = mysqli_num_rows($result2);

            echo "Event Name: ".$row['name']."<br>";
            echo "Description: ".$row['description']."<br>";
            echo "Date: ".$row['date']."<br>";
            echo "Time: ".$row['time']."<br>";
            echo "Location: ".$row['location']."<br>";
            echo "Bookings: $f_result/".$row['booking_cap']."<br>";

            echo "<form method='POST' action=''>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['event_id']) . "'>";
            echo "<input type='submit' name='action' value='cancel'><br><br>";
            echo "</form>";
        }
    } else {
        echo "Your RSVP'd events will appear here!";
    }
}

function delete_rsvp($conn){
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        $sql = "DELETE FROM RSVP WHERE event_id = '$id'";

        if (mysqli_query($conn, $sql)){
            echo "Canceled your booking :(";
        } else{
            echo "Error: " . mysqli_error($conn);
        }
    }
}

function delete_your_event($conn){
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        $sql = "DELETE FROM EVENTS WHERE event_id = '$id'";

        if (mysqli_query($conn, $sql)){
            echo "Deleted your event :(";
        } else{
            echo "Error: " . mysqli_error($conn);
        }
    }
}

function edit_your_event($conn){
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $EventName = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        $Location = mysqli_real_escape_string($conn, $_POST['location']);
        $booking_cap = mysqli_real_escape_string($conn, $_POST['booking_cap']);
        $visibility = mysqli_real_escape_string($conn, $_POST['visibility']);

        echo "<form method='post' action=''>";
        echo "<br><label for='name'>Event title:</label><br>";
        echo "<input type='text' id='name' name='name' value='$EventName'><br><br>";
        echo "<label for='description'>Event description:</label><br>";
        echo "<textarea id='description' name='description' value='$description'>$description</textarea><br><br>";
        echo "<label for='date'>Event date:</label><br>";
        echo "<input type='date' id='date' name='date' value='$date'><br><br>";
        echo "<label for='time'>Event time:</label><br>";
        echo "<input type='time' id='time' name='time' value='$time'><br><br>";
        echo "<label for='location'>Event location:</label><br>";
        echo "<input type='location' id='location' name='location' value='$Location'><br><br>";
        echo "<label for='visibility'>Event visibility:</label><br>";
        if ($visibility == 'public') {
            echo "<select name='visibility' id='visibility'>";
            echo "<option value='public' selected>Public</option>";
            echo "<option value='private'>Private</option>";
            echo "</select><br><br>";
        } else {
            echo "<select name='visibility' id='visibility'>";
            echo "<option value='public'>Public</option>";
            echo "<option value='private' selected>Private</option>";
            echo "</select><br><br>";
        }
        echo "<label for='booking_cap'>Booking capacity:</label><br>";
        echo "<input type='number' id='booking_cap' name='booking_cap' value='$booking_cap'><br><br>";
        echo "<input type='hidden' id='event_id' name='event_id' value='$id'>";
        echo "<input type='submit' name='action' value='Submit'>";
        echo "<input type='submit' name='action' value='Cancel'>";
        echo "</form>";
    }
}

function update_event($conn) {
    $id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $EventName = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $Location = mysqli_real_escape_string($conn, $_POST['location']);
    $booking_cap = mysqli_real_escape_string($conn, $_POST['booking_cap']);
    $visibility = mysqli_real_escape_string($conn, $_POST['visibility']);

    $sql = "UPDATE EVENTS
            SET name = '$EventName', description = '$description', date = '$date', time = '$time', location = '$Location', visibility = '$visibility', booking_cap = '$booking_cap'
            WHERE event_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "Edited the event successfully!";
    } else {
        echo "Error: ". mysqli_error($cconn);
    }
}

function Invite($conn){
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $fullname = $_SESSION['user_name'];
        $s_email = $_SESSION['user_email'];

        echo "Copy this event id and send it to your invitee. ID = $id<br><br>";
        echo "<b>OR</b><br><br>";
        echo "<div>";
            echo "<form method='post' action=''>";
                echo "<b>Submit this form:<br></b>";
                echo "<label for='email'>Invitee's email:</label><br>";
                echo "<input type='email' placeholder='example@domain.com' required id='email' name='i_email'><br><br>";
                echo "<input type='hidden' name='id' value='$id'>";
                echo "<input type='hidden' name='name' value='$fullname'>";
                echo "<input type='hidden' name='s_email' value='$s_email'>";
                echo "<input type='submit' name ='action' value='Send'><br><br>";
            echo "</form>";
        echo "</div>";
    }
}

function received_invite($conn){
   if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $s_fullname = mysqli_real_escape_string($conn, $_POST['name']);
        $s_email = mysqli_real_escape_string($conn, $_POST['s_email']);
        $i_email = mysqli_real_escape_string($conn, $_POST['i_email']);

        $sql = "CREATE TABLE IF NOT EXISTS INVITE(
                id int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                event_id VARCHAR(350) NOT NULL,
                inviter_name VARCHAR(100) NOT NULL,
                inviter_email VARCHAR(250) NOT NULL,
                invitee_name VARCHAR(100) NOT NULL, 
                invitee_email VARCHAR(250) NOT NULL,
                time_invite_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        
        if (mysqli_query($conn, $sql)) {
            echo "Invite table has been created!";
        } else {
            echo "Error: ". mysqli_error($conn);
        }

        $sql2 = "SELECT * FROM USERS WHERE email = '$i_email'";
        $result = mysqli_query($conn, $sql2);

        if (mysqli_num_rows($result)>0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $i_fullname = $row['firstname'] . ' ' . $row['lastname'];
            }
            $sql3 = "INSERT INTO INVITE(event_id, inviter_name, inviter_email, invitee_name, invitee_email) 
                    VALUES ('$id','$s_fullname','$s_email','$i_fullname','$i_email')";

            if (mysqli_query($conn, $sql3)){
                echo "Invite has been sent!";
            } else{
                echo "Error: ". mysqli_error($conn);
            }
        } else {
            "Account associated with that email doesn't exist.";
        }
        
   } 
}
?>