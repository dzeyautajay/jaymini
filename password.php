<?php
include('server.php');

// Check if the change password and username button is clicked
if (isset($_POST['change_password_username'])) {
    // Get form data
    $old_password = mysqli_real_escape_string($db, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($db, $_POST['new_password']);
    $new_username = mysqli_real_escape_string($db, $_POST['new_username']);

    // Fetch user's current password hash from the database
    $username = mysqli_real_escape_string($db, $_SESSION['username']);
    $query = "SELECT password FROM users WHERE username='$username'";
    $result = mysqli_query($db, $query);

    if (!$result) {
        // Error handling if query fails
        $_SESSION['error'] = "Query failed: " . mysqli_error($db);
        header('location: password.php');
        exit();
    }

    $user = mysqli_fetch_assoc($result);

    // Check if old password matches the one in the database
    if (!empty($old_password) && md5($old_password) !== $user['password']) {
        // If old password does not match, display error
        $_SESSION['error'] = "Old password is incorrect.";
        header('location: password.php');
        exit();
    }

    // Check if old and new passwords are the same
    if (!empty($old_password) && !empty($new_password) && $old_password !== $new_password) {
        // If old and new passwords match, display error
        $_SESSION['error'] = "Old and new passwords cannot be the same.";
        header('location: password.php');
        exit();
    }

    // Check if the new username already exists
    if (usernameExists($new_username, $db)) {
        // If the new username exists, display an error message
        $_SESSION['error'] = "Username already exists.";
        header('location: password.php');
        exit();
    }

    // Update the password if new password is provided
    if (!empty($new_password)) {
        // Encrypt the new password
        $new_password_hash = md5($new_password);
        $update_password_query = "UPDATE users SET password='$new_password_hash' WHERE username='$username'";
        mysqli_query($db, $update_password_query);
    }

    // Update the username if it's not empty
    if (!empty($new_username)) {
        $update_username_query = "UPDATE users SET username='$new_username' WHERE username='$username'";
        mysqli_query($db, $update_username_query);
        $_SESSION['username'] = $new_username; // Update username in session
    }

    // Redirect to profile page after password and username change
    $_SESSION['success'] = "Password and username changed successfully.";
    header('location: edit.php');
    exit();
}

// Check if the delete account button is clicked
if (isset($_POST['delete_account'])) {
    // Display a confirmation message to the user
    echo '<script>alert("Are you sure you want to delete your account? This action cannot be undone.");</script>';
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Add a CSS class to make the text field background red */
        .error-background {
            background-color: rgba(255, 0, 0, 0.1); /* Adjust the transparency as needed */
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="input-group">
    <div class="header">
        <h2>Security</h2>
    </div>
        <form method="post" action="server.php" id="changePasswordForm">
        <?php if (isset($_SESSION['error'])) : ?>
                <div class="error">
                    <?php echo $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif ?>

            <?php if (isset($_SESSION['success'])) : ?>
                <div class="success">
                    <?php echo $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif ?>
            <div class="input-group">
                <label>Old Password</label>
                <input type="password" name="old_password" id="oldPasswordInput"<?php echo !empty($_POST['new_username']) ? ' required' : ''; ?>>
            </div>
            <div class="input-group">
                <label>New Password</label>
                <input type="password" name="new_password" id="newPasswordInput">
            </div>
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="new_username" value="<?php echo isset($_POST['new_username']) ? $_POST['new_username'] : $_SESSION['username']; ?>" required>
            </div>
            <div class="input-group">
                <!-- Disable the button by default -->
                <button type="submit" class="btn" name="change_password_username" id="changePasswordButton" disabled><img src="images/padlock.png" alt="padlock.png" class="png4">&nbsp;&nbsp;&nbsp;Change Password and Username</button>
            </div>
            <br>
            <p class="setup-text" style="font-size:12px;">Account deletion is permanent!</p>
            <br>
            <div style="display: flex; justify-content: space-between;">
    <!-- Button for discarding changes -->
    <button type="button" class="btn1" onclick="window.location.href='edit.php';" style="text-decoration: none; color: black;">Discard</button>
    <!-- Button for deleting account -->
    &nbsp;
    <button type="submit" class="btn1" name="delete_account" id="deleteAccountButton" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');" style="text-decoration: none; color: black; :hover{color: #0011ff;}
  transition: 1s;"><img src="images/bin.png" alt="bin.png" class="png5">&nbsp;&nbsp;&nbsp;Delete Account</button>
</div>
                <hr>
                <br>
                <p class="setup-text" style="font-size:10px;">Copyright © 2024 Jay Autajay. All rights reserved.</p>
            </div>
        </form>
    </div>

    <script>
    // Get the password and username input fields and the button
    const oldPasswordInput = document.getElementById('oldPasswordInput');
    const newPasswordInput = document.getElementById('newPasswordInput');
    const newUsernameInput = document.getElementsByName('new_username')[0]; // Get the first element with the name 'new_username'
    const changePasswordButton = document.getElementById('changePasswordButton');
    const deleteAccountButton = document.getElementById('deleteAccountButton');
    // Store the initial values of the inputs
    let initialOldPassword = oldPasswordInput.value.trim();
    let initialNewPassword = newPasswordInput.value.trim();
    let initialUsername = newUsernameInput.value.trim();

    // Add event listeners to the password and username input fields
    oldPasswordInput.addEventListener('input', checkInput);
    newPasswordInput.addEventListener('input', checkInput);
    newUsernameInput.addEventListener('input', checkInput);

    function checkInput() {
        const oldPassword = oldPasswordInput.value.trim();
        const newPassword = newPasswordInput.value.trim();
        const newUsername = newUsernameInput.value.trim();

        // If old password is empty, discard input in new password and change color to red
        if (oldPassword === '') {
            newPasswordInput.value = '';
            newPasswordInput.style.backgroundColor = 'red';
        } else {
            newPasswordInput.style.backgroundColor = ''; // Reset color to default
        }

        // If any of the fields has input, enable the button
        if (oldPassword !== '' || newPassword !== '' || newUsername !== '') {
            changePasswordButton.disabled = false;
            // Reset button color to its default
            changePasswordButton.style.backgroundColor = '';
        } else {
            // If all fields are empty, disable the button
            changePasswordButton.disabled = true;
            // Change button color to gray
            changePasswordButton.style.backgroundColor = '#ff6969';
        }

        if (oldPassword !== '' || newPassword !== ''){
            deleteAccountButton.style.backgroundColor = '';
        } else {
            deleteAccountButton.style.backgroundColor = 'white';
        }

        // Disable the button if the old password and new password are the same
        if (oldPassword === newPassword && oldPassword !== '' && newPassword !== '') {
            changePasswordButton.disabled = true;
            // Change button color to gray
            changePasswordButton.style.backgroundColor = '#ff6969';
            deleteAccountButton.style.backgroundColor = 'white';
            // Change new password input color to red
            newPasswordInput.style.backgroundColor = '#ffd5d5';
        } else {
            // Reset color to default if the passwords are not the same
            newPasswordInput.style.backgroundColor = '';
            deleteAccountButton.style.backgroundColor = '';
        }

        // Enable the button if the username has changed
        if (initialUsername !== newUsername) {
            changePasswordButton.disabled = false;
            // Reset button color to its default
            changePasswordButton.style.backgroundColor = '';
        }
    }
</script>


</body>
</html>
