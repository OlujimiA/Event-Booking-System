<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        echo "This is the log in page."
    ?>
    <form>
        <h1>Login</h1>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required placeholder="example@domain.com"><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button id="login">Login</button> 
    </form>
    <p>Don't have an account? Create one <a href="index.php">here</a><p>
</body>
</html>
