<?php
include('server.php');

// Check if the change password and username button is clicked
if (isset($_POST['change_password_username'])) {
    // Get form data
    $old_password = mysqli_real_escape_string($db, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($db, $_POST['new_password']);
    $new_username = mysqli_real_escape_string($db, $_POST['new_username']);

    // Check if the new username already exists
    if (usernameExists($new_username, $db)) {
        // If the new username exists, display an error message
        $_SESSION['error'] = "Username already exists.";
        header('location: password.php');
        exit();
    }

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

    // If old password is provided, verify if it matches the one in the database
    if (!empty($old_password) && md5($old_password) !== $user['password']) {
        // If old password does not match, display error
        $_SESSION['error'] = "Old password is incorrect.";
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
    if (!empty($new_password) || !empty($new_username)) {
        $_SESSION['success'] = "Password and username changed successfully.";
    }
    header('location: edit.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Add transition effect for button */
        .changeButton {
            transition: opacity 0.5s, visibility 0.5s;
            opacity: 0;
            visibility: hidden;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .changeButton.show {
            opacity: 1;
            visibility: visible;
        }

        .input-group {
            position: relative;
        }

        .btn2 {
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Change Password and Username</h2>
    </div>
    <div class="input-group">
        <form method="post" action="password.php" id="changePasswordForm">
            <div class="input-group">
                <label>Old Password</label>
                <input type="password" name="old_password" id="oldPasswordInput">
            </div>
            <div class="input-group">
                <label>New Password</label>
                <input type="password" name="new_password" id="newPasswordInput">
            </div>
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
                <label>Username</label>
                <input type="text" name="new_username" id="newUsernameInput" value="<?php echo isset($_POST['new_username']) ? $_POST['new_username'] : $_SESSION['username']; ?>" required>
            </div>
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

            <div class="buttons-container">
                <button type="submit" class="btn changeButton" name="change_password" id="changePasswordButton" disabled>Change Password</button>
                <button type="submit" class="btn changeButton" name="change_username" id="changeUsernameButton" disabled>Change Username</button>
                <button type="submit" class="btn changeButton" name="change_password_username" id="changePasswordUsernameButton" disabled><img src="images/padlock.png" alt="padlock.png" class="png4">&nbsp;&nbsp;&nbsp;Change Password and Username</button>
            </div>
<br>br
            <button type="button" class="btn2" onclick="window.location.href='edit.php';" style="text-decoration: none; color: black;">Discard</button>
            <div class="input-group">
                <hr style="border:none;border-top: 1px solid #ddd;">
                <br>
                <p class="setup-text" style="font-size:10px;">Copyright © 2024 Jay Autajay. All rights reserved.</p>
            </div>
        </form>
    </div>

    <script>
        // Get the input fields and buttons
        const oldPasswordInput = document.getElementById('oldPasswordInput');
        const newPasswordInput = document.getElementById('newPasswordInput');
        const newUsernameInput = document.getElementById('newUsernameInput');
        const changePasswordButton = document.getElementById('changePasswordButton');
        const changeUsernameButton = document.getElementById('changeUsernameButton');
        const changePasswordUsernameButton = document.getElementById('changePasswordUsernameButton');
        const buttons = [changePasswordButton, changeUsernameButton, changePasswordUsernameButton];

        // Store the initial values of the input fields
        let initialOldPassword = oldPasswordInput.value.trim();
        let initialNewPassword = newPasswordInput.value.trim();
        let initialNewUsername = newUsernameInput.value.trim();

        // Function to check if there's any change in input fields
        function checkForChanges() {
            const oldPasswordValue = oldPasswordInput.value.trim();
            const newPasswordValue = newPasswordInput.value.trim();
            const newUsernameValue = newUsernameInput.value.trim();

            const oldPasswordChanged = oldPasswordValue !== initialOldPassword;
            const newPasswordChanged = newPasswordValue !== initialNewPassword;
            const newUsernameChanged = newUsernameValue !== initialNewUsername;

            return [oldPasswordChanged, newPasswordChanged, newUsernameChanged];
        }

        // Function to toggle visibility of the buttons and enable/disable them
        function toggleButtons() {
            const [oldPasswordChanged, newPasswordChanged, newUsernameChanged] = checkForChanges();

            if (oldPasswordChanged && newPasswordChanged && newUsernameChanged) {
                toggleButtonVisibility(changePasswordUsernameButton, true);
                toggleButtonVisibility(changePasswordButton, false);
                toggleButtonVisibility(changeUsernameButton, false);
            } else if (oldPasswordChanged && newPasswordChanged) {
                toggleButtonVisibility(changePasswordButton, true);
                toggleButtonVisibility(changeUsernameButton, false);
                toggleButtonVisibility(changePasswordUsernameButton, false);
            } else if (newUsernameChanged) {
                toggleButtonVisibility(changeUsernameButton, true);
                toggleButtonVisibility(changePasswordButton, false);
                toggleButtonVisibility(changePasswordUsernameButton, false);
            } else {
                toggleButtonVisibility(changePasswordButton, false);
                toggleButtonVisibility(changeUsernameButton, false);
                toggleButtonVisibility(changePasswordUsernameButton, false);
            }
        }

        // Function to toggle button visibility and enable/disable them
        function toggleButtonVisibility(button, show) {
            if (show) {
                button.classList.add('show');
                button.disabled = false;
            } else {
                button.classList.remove('show');
                button.disabled = true;
            }
        }

        // Call the toggleButtons function initially
        toggleButtons();

        // Add event listeners to input fields to monitor changes
        oldPasswordInput.addEventListener('input', toggleButtons);
        newPasswordInput.addEventListener('input', toggleButtons);
        newUsernameInput.addEventListener('input', toggleButtons);
    </script>

</body>
</html>
