<?php
require 'query.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Booking System</title>
</head>
<body>
    <h1>Register</h1>
    <?php echo '<form method="POST" action="'.register($conn).'">'; ?>
        <label for="fname">First name:</label><br>
        <input type="text" id="fname" name="fname" required placeholder="Firstname"><br><br>

        <label for="lname">Last name:</label><br>
        <input type="text" id="lname" name="lname" required placeholder="Lastname"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required placeholder="example@domain.com"><br><br>

        <label for="pnumber">Phone number:</label><br>
        <input type="tel" id="pnumber" name="pnumber"><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <label for="ptype">What is your club position?</label><br>
        <select name="ptype" id="ptype">
            <option value="" disabled selected>Select your position</option>
            <option value="executive">Executive</option>
            <option value="supervisor">Supervisor</option>
            <option value="member">Member</option>
        </select><br><br>
        
        <button id="submit">Submit</button> 
    </form>  
    <p>Already have an account? Login <a href="login.php" title="Go to our login page">here</a></p>
</body>
</html>

