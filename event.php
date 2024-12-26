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
    <h1>EBS Dashboard</h1>
    <h3>This is the dashboard, where you can manage/create events</h3>
    
    
    <form method="POST" id="content-area" action="">
        <button id='createEvent' onclick="hideButton()" name='create_event'>Create event</button>
        
    </form>
    
    <script>
        document.getElementById('createEvent').addEventListener('click', function() {
      // HTML code to insert
        const newHtml = `
            <label for="name">Event title:</label><br>
            <input type="text" id="name" name="name" required placeholder="Event name"><br><br>

            <label for="description">Event description:</label><br>
            <textarea name="description" id="description" placeholder="Kindly describe your event here!"></textarea><br><br>
            
            <label for="date">Event date:</label><br>
            <input type="date" id="date" name="date" required placeholder=""><br><br>

            <label for="time">Event time:</label><br>
            <input type="time" id="time" name="time" required placeholder=""><br><br>

            <label for="location">Event location:</label><br>
            <input type="location" id="location" name="location" required placeholder="Abuja, Nigeria"><br><br>

            <label for="visibility">Event visibility:</label><br>
            <select name="visibility" id="visibility">
                <option value="" disabled selected>Select your event visibility</option>
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select><br><br>
            
            <input type="hidden" name="action" value="event">

            <button id="submit">Submit</button><br><br>
           
      `;
      // Insert the HTML into the content area
        document.getElementById('content-area').innerHTML = newHtml;
        });
        function hideButton(){
            const button = document.getElementById('createEvent');
            button.style.display = 'none';
            
        }

    </script>
    
    <form method="post" action="">
        <input type="submit" id="your_events"name="your_events" value="See your events">
        <input type="hidden" name="action" value="your_events">
    </form>

    <div>
        <?php 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
        
            switch ($action) {
                case 'your_events':
                    your_events($conn);
                    break;
                
                default:
                    echo "";
            }
        }
        ?>
    </div>

</body>
</html>