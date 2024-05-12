<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Registration system PHP and MySQL</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
  	<h2>Create an account</h2>
  </div>
	
  <form method="post" action="register.php">
  	<?php include('errors.php'); ?>
  	<div class="input-group">
  	  <label class="form-label">Username</label>
  	  <input class="form-control" type="text" name="username" value="<?php echo $username; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Email</label>
  	  <input type="email" name="email" value="<?php echo $email; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Password</label>
  	  <input type="password" name="password_1">
  	</div>
  	<div class="input-group">
		<div class="password_2">
  	  <label>Confirm password</label>
  	  <input type="password" name="password_2">
  	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Register</button>
  	</div>
	  <div class="input-group">
<a href="login.php" class="btn2" style="text-decoration:none;">Already have an Account?</a>
  </div>
  <div class="input-group">
  <hr style="border:none;border-top: 1px solid #ddd;">
		<br>
		<p class="setup-text" style="font-size:13px;"><img src="images/padlock.png" alt="padlock.png" class="png2">&nbsp;&nbsp;&nbsp;Your password is incrypted for enhanced security.</p>
  <br>
  <p class="setup-text" style="font-size:10px;">Copyright © 2024 Jay Autajay. All rights reserved.</p>

</div>
  </form>
</body>
</html>