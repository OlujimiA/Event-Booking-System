<?php
require 'query.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn page</title>
</head>
<body>
<h1>Login</h1>

    <?php echo '<form method="POST" action="'.login($conn).'">'; ?>
        <br><label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required placeholder="example@domain.com"><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button href="event.php" id="login">Login</button> 
    </form>
    <p>Don't have an account? Create one <a href="index.php">here</a><p>
</body>
</html>
