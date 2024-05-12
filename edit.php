<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit(); // Ensure script stops execution after redirection
}

// Establish database connection
$db = mysqli_connect('localhost', 'root', '', 'project');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input to prevent SQL injection
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $surname = mysqli_real_escape_string($db, $_POST['surname']);
    $student_no = mysqli_real_escape_string($db, $_POST['student_no']);
    $contact = mysqli_real_escape_string($db, $_POST['contact']);
    $module_code = mysqli_real_escape_string($db, $_POST['module_code']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $username = mysqli_real_escape_string($db, $_SESSION['username']);

    // Check if a file is uploaded
    if ($_FILES['profile_picture']['size'] > 0) {
        // Check file size (in bytes)
        $maxFileSize = 5242880; // 5MB (adjust as needed)
        if ($_FILES['profile_picture']['size'] > $maxFileSize) {
            // File size exceeds the limit, display error message
            $errorMessage = "Error: The uploaded file exceeds the maximum file size limit.";
        } else {
            // File size is within the limit, proceed with updating profile information
            // Update user's profile information in the database
            $query = "UPDATE users SET name='$name', surname='$surname', student_no='$student_no', contact='$contact', module_code='$module_code', email='$email' WHERE username='$username'";
            if (mysqli_query($db, $query)) {
                // Profile information updated successfully

                // Get the temporary file path
                $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];

                // Read the file content
                $profile_picture_data = addslashes(file_get_contents($profile_picture_tmp));

                // Update the profile picture in the database
                $query = "UPDATE users SET profile_picture='$profile_picture_data' WHERE username='$username'";
                if (!mysqli_query($db, $query)) {
                    $errorMessage = "Error updating profile picture: " . mysqli_error($db);
                } else {
                    // Redirect to profile page after successful update
                    header('location: profile.php');
                    exit();
                }
            } else {
                $errorMessage = "Error updating profile: " . mysqli_error($db);
            }
        }
    } else {
        // No file uploaded, update profile information without changing the profile picture
        $query = "UPDATE users SET name='$name', surname='$surname', student_no='$student_no', contact='$contact', module_code='$module_code', email='$email' WHERE username='$username'";
        if (mysqli_query($db, $query)) {
            // Redirect to profile page after successful update
            header('location: profile.php');
            exit();
        } else {
            $errorMessage = "Error updating profile: " . mysqli_error($db);
        }
    }
}

// Fetch user's profile information from the database
$username = mysqli_real_escape_string($db, $_SESSION['username']);
$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($db, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="input-group">
    <div class="header">
        <h2>Edit Profile</h2>
    </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <!-- Display profile picture -->
            <!-- Display profile picture -->
<div class="input_picture">
    <div class="picturebox">
        <?php
        // Check if the user has uploaded a profile picture
        if (!empty($user['profile_picture'])) {
            // If yes, display the uploaded profile picture
            echo '<img id="profile-picture-preview" src="get_profile_picture.php" alt="Profile Picture">';
        } else {
            // If not, display the default profile picture
            echo '<img id="profile-picture-preview" src="images/default.jpg" alt="Default Profile Picture">';
        }
        ?>
    </div>
    <div class="upload">
        <label for="profile-picture-input" class="custom-file-upload"><img src="images/pencil.png" alt="pencil.png" class="png5">&nbsp;Upload</label>
        <input type="file" name="profile_picture" id="profile-picture-input"  accept=".jpg, .jpeg, .png" style="display: none;" onchange="previewProfilePicture(event)">
    </div>
</div>

            <!-- Editable profile information -->
            <div class="input-group">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
</div>
<div class="input-group">
    <label for="surname">Surname:</label>
    <input type="text" id="surname" name="surname" value="<?php echo $user['surname']; ?>"required>
</div>
<div class="input-group">
    <label for="student_no">Student Number:</label>
    <input type="text" id="student_no" name="student_no" value="<?php echo $user['student_no']; ?>"required>
</div>
<div class="input-group">
    <label for="contact">Contact:</label>
    <input type="text" id="contact" name="contact" value="<?php echo $user['contact']; ?>"required>
</div>
<div class="input-group">
    <label for="module_code">Module Code:</label>
    <input type="text" id="module_code" name="module_code" value="<?php echo $user['module_code']; ?>"required>
</div>
<div class="input-group">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" value="<?php echo $user['email']; ?>"required>
</div>


            <!-- Update profile button -->
            <div class="input-group">
                <button type="submit" class="btn" name="update_profile"><img src="images/pencil.png" alt="pencil.png" class="png4">&nbsp;&nbsp;Save Changes</button>
            </div>
            <br>
            <div class="input-group" >
            <a href="password.php" class="btn1"><img src="images/admin.png" alt="admin.png" class="png5">&nbsp;&nbsp;Account ownership and security</a>
    </div>

	<hr style="border:none;border-top: 1px solid #ddd;">
		<br>
        <p class="setup-text" style="font-size:10px;">Copyright © 2024 Jay Autajay. All rights reserved.</p>
</div>
            <!-- Display error message if applicable -->
            <?php if (isset($errorMessage)): ?>
            <div class="error">
                <?php echo $errorMessage; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
</body>
<script>
    function previewProfilePicture(event) {
        var input = event.target;
        var reader = new FileReader();
        var profilePicturePreview = document.getElementById('profile-picture-preview');

        reader.onload = function(){
            profilePicturePreview.src = reader.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
</script>
</html>
