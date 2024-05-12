<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit();
}

// Connect to the database (replace with your actual database credentials)
$db = mysqli_connect('localhost', 'root', '', 'project');

// Check for database connection errors
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch profile picture for the logged-in user
$username = mysqli_real_escape_string($db, $_SESSION['username']);
$query = "SELECT profile_picture FROM users WHERE username='$username'";
$result = mysqli_query($db, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Set the appropriate header for an image file
    header('Content-Type: image/jpeg'); // Assuming the profile pictures are JPEGs

    // Output the profile picture
    echo mysqli_fetch_assoc($result)['profile_picture'];
} else {
    // Output default profile picture or a placeholder image
    $default_picture_path = "C:\Users\PERSONAL\Pictures\jay\htdocs\jaymini+\user.png"; // Provide the path to your default image
    header('Content-Type: image/png'); // Set appropriate content type
    readfile($default_picture_path);
}
?>