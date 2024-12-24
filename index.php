<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Booking System</title>
</head>
<body>
    <?php
        echo "This is an Event Booking System";
    ?> 
    <form>
        <h1>Register</h1>
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
        
        <label for="ptype">What describes you best?</label><br>
        <select name="ptype" id="ptype">
            <option value="Student">Student</option>
            <option value="Teacher">Teacher</option>
            <option value="Other">Other</option>
        </select><br><br>
        
        <button id="submit">Submit</button> 
    </form>
    <p>Already have an account? Login <a href="login.php">here</a></p>
    <p>Already have an account? Login <a href="login.php">here</a></p>
</body>
</html>

