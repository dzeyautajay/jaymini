<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'project');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO users (username, email, password) 
  			  VALUES('$username', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	header('location: edit.php');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: profile.php');
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}


// UPDATE PROFILE WITH PROFILE PICTURE
if (isset($_POST['update_profile'])) {
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $username = mysqli_real_escape_string($db, $_SESSION['username']);

  // Check if a file is uploaded
  if ($_FILES['profile_picture']['size'] > 0) {
      // Get the temporary file path
      $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];

      // Check file type
      $allowed_types = ['image/jpeg', 'image/png']; // Allowed file types
      $file_info = finfo_open(FILEINFO_MIME_TYPE);
      $file_mime_type = finfo_file($file_info, $profile_picture_tmp);
      finfo_close($file_info);

      // Check if the uploaded file type is allowed
      if (!in_array($file_mime_type, $allowed_types)) {
          $_SESSION['error'] = "Only JPG and PNG files are allowed for profile pictures.";
          header('location: profile.php');
          exit();
      }

      // Read the file content
      $profile_picture_data = addslashes(file_get_contents($profile_picture_tmp));

      // Update the profile information including the profile picture
      $query = "UPDATE users SET name='$name', profile_picture='$profile_picture_data' WHERE username='$username'";
  } else {
      // Update the profile information without changing the profile picture
      $query = "UPDATE users SET name='$name' WHERE username='$username'";
  }

  // Execute the query
  mysqli_query($db, $query);
  $_SESSION['success'] = "Profile updated successfully";
  header('location: profile.php');
  exit();
}



// If the change password and username button is clicked
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

  // If old password is provided, verify if it matches the one in the database
  if (!empty($old_password) && md5($old_password) !== $user['password']) {
      // If old password does not match, display error
      $_SESSION['error'] = "Old password is incorrect.";
      header('location: password.php');
      exit();
  }

  // Check if the new username already exists
  if ($new_username !== $username) {
      $check_username_query = "SELECT * FROM users WHERE username='$new_username' LIMIT 1";
      $check_username_result = mysqli_query($db, $check_username_query);
      if (mysqli_num_rows($check_username_result) > 0) {
          // If the new username exists, display an error message
          $_SESSION['error'] = "Username already exists.";
          header('location: password.php');
          exit();
      }
  }

  // Update the password if new password is provided
  if (!empty($new_password)) {
      // Encrypt the new password
      $new_password_hash = md5($new_password);
      $update_password_query = "UPDATE users SET password='$new_password_hash' WHERE username='$username'";
      mysqli_query($db, $update_password_query);
      $_SESSION['success'] = "Password changed successfully.";
  }
  header('location: edit.php');
  // Update the username if it's not empty
  if (!empty($new_username)) {
      $update_username_query = "UPDATE users SET username='$new_username' WHERE username='$username'";
      mysqli_query($db, $update_username_query);
      $_SESSION['username'] = $new_username; // Update username in session
      $_SESSION['success'] = "Username changed successfully.";
  }
  header('location: edit.php');
  // Redirect to profile page after password and username change
  if (!empty($new_password) || !empty($new_username)) {
    $_SESSION['success'] = "You recently changed your password or password";
  }
  header('location: edit.php');
  exit();
}

// Check if the delete account button is clicked
if (isset($_POST['delete_account'])) {
  // Display a confirmation message to the user
  // Add logic here for deleting the account
  $username = mysqli_real_escape_string($db, $_SESSION['username']);
  $delete_query = "DELETE FROM users WHERE username='$username'";
  mysqli_query($db, $delete_query);
  session_destroy(); // Destroy all sessions
  header('location: login.php'); // Redirect to login page
}



// Process form submission to update profile
if (isset($_POST['update_profile'])) {
  // Sanitize input to prevent SQL injection
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $surname = mysqli_real_escape_string($db, $_POST['surname']);
  $student_no = mysqli_real_escape_string($db, $_POST['student_no']);
  $contact = mysqli_real_escape_string($db, $_POST['contact']);
  $module_code = mysqli_real_escape_string($db, $_POST['module_code']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $username = mysqli_real_escape_string($db, $_SESSION['username']);

  // Update user's profile information in the database
  $query = "UPDATE users SET name='$name', surname='$surname', student_no='$student_no', contact='$contact', module_code='$module_code', email='$email' WHERE username='$username'";
  if (mysqli_query($db, $query)) {
      $_SESSION['success'] = "Profile updated successfully";
      header('location: profile.php');
      exit();
  } else {
      array_push($errors, "Error updating profile: " . mysqli_error($db));
  }

}

?>

