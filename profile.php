<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit(); // Ensure script stops execution after redirection
}

// Fetch user's profile information from the database
$db = mysqli_connect('localhost', 'root', '', 'project');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = mysqli_real_escape_string($db, $_SESSION['username']);
$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($db, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* CSS for the navbar */
        .navbar {
            position: fixed;
            top: -100px; /* Change to -100px to fully hide initially */
            width:60%;
            background-color: #5f5f5faf;
  backdrop-filter: blur(10px); /* Blur effect */
            padding: 5px 0;
            transition: top 1s;
            display: flex;
            align-items: center;
            z-index: 1000; /* Ensure navbar is on top */
            margin:10px;
            margin-left:20%;
            border-radius:25px;
        }
        .navbar-background{
            margin-left:5px;
            background-color: #818181;
            backdrop-filter: blur(10px); /* Blur effect */
            border: 1px solid #d5d5d7;
        display: flex;
            align-items: center;
            border-radius: 40px;
        }

        #navbar-profile-picture {
            overflow: hidden;
            display: block;
  width: 100%; /* Set the width to fill the entire picture box */
  height: 100%; /* Set the height to fill the entire picture box */
  object-fit: cover; /* Scale the image while preserving aspect ratio to cover the entire box */
  object-position: center; /* Center the image within the box */
            width: 40px;
            height: 40px;
            border-radius: 50%;
   padding:2px;
            margin-right: 30px;
        }
   
        #navbar-username {
            margin-right: 30px;
            font-size: 18px;
            color: white;
        }

        /* Hide navbar initially */
        .navbar-hidden {
            top: -200px;
        }

        .btnedit{
  padding: 5px;
  text-decoration:none;
  color: #1d1d1f;
  background-color: #f8f8f8;
  font-size:16px;
  border-radius: 30px;
  backdrop-filter: blur(10px);
  margin-right: 6px;
}

.btnout{
  background-color:#1d1d1f;
  padding: 5px;
  text-decoration:none;
  color:#f5f5f7;
  font-size:16px;
  border-radius: 30px;
  margin-left:auto;
  margin-right: 6px;
  padding:10px;
}
    </style>
</head>
<div class="content-background">
<body class="pbody">
    <!-- Navigation bar initially hidden -->
    <div id="navbar" class="navbar navbar-hidden">
    <div id="navbar-background" class="navbar-background">
        <img id="navbar-profile-picture" src="<?php echo (!empty($user['profile_picture']) ? 'get_profile_picture.php' : 'images/default.jpg'); ?>" alt="Profile Picture">
        <p id="navbar-username">@<?php echo $user['username'] ?></p>
        <a href="edit.php" class="btnedit">&nbsp;&nbsp;<img src="images/pencil.png" alt="pencil.png" class="png6">&nbsp;&nbsp;Edit Profile&nbsp;&nbsp;</a>
</div>
        <a href="login.php" class="btnout" onclick="return confirm('Are you sure you want to log out?');" style="background-color: #818181; border: 1px solid #d5d5d7;">&nbsp;&nbsp;Log out&nbsp;&nbsp;</a>
</div>
    </div>

    <form class="pform" method="post" action="server.php">
        <!-- Display profile picture -->
        <div class="picturebox1">
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
    </form>
    <form class="pform2" method="post" action="server.php">
        <!-- Display profile information -->
        <div class="pelements-container">
            <p3>
                <div class="pelements-name fade-up"> <!-- Initially hidden and will fade up -->
                    <label style="border:none;" type="text" name="name"><?php echo $user['name'].' '.$user['surname'];?></label>
                </div>
            </p3>

            <div class="pelements">
                <p3>
                    <label style="border:none;" type="text" name="username" id="username">@<?php echo $user['username']; ?>&nbsp;&nbsp;&nbsp;</label>
                </p3>
                <span class="dot-divider">|</span>
                <div class="pelements">
                    <label style="border:none;" type="text" name="email"><?php echo $user['email']; ?></label>
                </div>
                <br>
                <br>
                <div class="pelements">
                    <a href="edit.php" class="btnedit"  style="border: 1px solid #1d1d1f">&nbsp;&nbsp;<img src="images/pencil.png" alt="pencil.png" class="png6">&nbsp;&nbsp;Edit Profile&nbsp;&nbsp;</a>
                </div>
            </div>
        </div>
    </form>

       <!-- Container for sample text and photo -->
       <div class="container-b fade-in1">
        <div class="left">
            <h2 class="container-b fade-in1-1">Brushing Beyond Boundaries</h2>
            <p class="container-b fade-in1-2">Unleashing Creativity Through Drawing</p>
            <p4 class="container-b fade-in1-3">Dive into the realm of boundless imagination and artistic expression as we embark on a journey through the world of drawing. From intricate pencil sketches to bold strokes of digital art, we celebrate the diverse ways creativity manifests on canvas.</p4>
        </div>
        <div class="right">
        <img src="images\draw.jpg" alt="draw.jpg">
        </div>
    </div>

    <div class="container-w fade-in2">
        <div class="left">
        <div id="video-container">
        <div id="video-container">
  <video id="myVideo" autoplay loop muted>
    <source src="images/apl.mp4" type="video/mp4">
  </video>
</div>
    </div>
        </div>
        <div class="right">
        <h2 class="container-b fade-in2-1">Crafting Digital Love</h2>
            <p class="container-b fade-in2-2">Bringing Passion and Purpose to Pixels</p>
            <p4 class="container-b fade-in2-3">From the meticulous selection of color palettes to the seamless integration of interactive elements.</p4>
        </div>
        </div>


    <div class="container-w fade-in3">
    <div class="left">
            <img src="images\draw.png" alt="draw.png">
        </div>
        <div class="right">
        </div>
    </div>

    <div class="container-b fade-in4">
        <div class="left">
            <h2 class="container-b fade-in4-1">Magic Keys</h2>
            <p class="container-b fade-in4-2">Redesigning Typing Experience</p>
            <p4 class="container-b fade-in4-3">From its responsive keys to its intuitive trackpad,  redefines typing experience, offering users a blend of style, functionality, and convenience.</p4>
        </div>
        <div class="right">
            <img src="images\magickeyboard.jpg" alt="magickeyboard.jpg">
        </div>
    </div>
    <!---->
 
    <!-- Footer -->
    <div class="footer">
        <div class="row">
            <div class="column">
                <p4>Developed by Jay Autajay</p4>
            </div>
            <div class="column">
                <p4>Copyright © 2024</p4>
            </div>
        </div>
    </div>
    </div>
    <script>
// Function to handle scroll behavior
window.addEventListener('wheel', function(event) {
    // Change this value to control scrolling speed
    var scrollSpeed = 0.1; // Adjust as needed

    // Determine the direction of scrolling
    var delta = Math.sign(event.deltaY);

    // Calculate the amount of scroll
    var amount = delta * scrollSpeed;

    // Scroll the page by the calculated amount
    window.scrollBy(0, amount);

    // Prevent default scroll behavior
    event.preventDefault();
});


// Function to handle scroll behavior
window.onscroll = function() {
    // Scroll to top of the page when navbar is clicked
    document.getElementById("navbar").addEventListener("click", function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Handle navbar visibility
    var scrolled = window.scrollY;
    var navbar = document.getElementById("navbar");
    if (scrolled > 590 || document.documentElement.scrollTop > 700) {
        navbar.style.top = "0";
    } else {
        navbar.style.top = "-100px";
    }

    // Handle fade effects
    var fadeElements = [
        { element: document.querySelector(".fade-in1"), offset: 200 }, // Example element 1
        { element: document.querySelector(".fade-in1-1"), offset: 500 }, // Example element 1
        { element: document.querySelector(".fade-in1-2"), offset: 650 }, // Example element 1
        { element: document.querySelector(".fade-in1-3"), offset: 800 }, // Example element 1
        { element: document.querySelector(".fade-in2"), offset: 1000 }, // Example element 2
        { element: document.querySelector(".fade-in2-1"), offset: 1300 }, // Example element 2
        { element: document.querySelector(".fade-in2-2"), offset: 1500 }, // Example element 2
        { element: document.querySelector(".fade-in2-3"), offset: 1600 }, // Example element 2
        { element: document.querySelector(".fade-in3"), offset: 1800 }, // Example element 3
        { element: document.querySelector(".fade-in4"), offset: 2300 }, // Example element 4
        { element: document.querySelector(".fade-in4-1"), offset: 2700 }, // Example element 4
        { element: document.querySelector(".fade-in4-2"), offset: 2800 }, // Example element 4
        { element: document.querySelector(".fade-in4-3"), offset: 2900 }, // Example element 4
        // Add more elements as needed
    ];

    var scrollPosition = window.scrollY;

    fadeElements.forEach(function(fadeElement) {
        var element = fadeElement.element;
        var offset = fadeElement.offset;

        if (scrollPosition >= offset) {
            element.classList.add("active");
        } else {
            element.classList.remove("active");
        }
    });
};


// Get the video elements
var videos = document.querySelectorAll("video");

// Add event listeners to detect when the videos end
videos.forEach(function(video) {
    video.addEventListener('ended', function() {
        // When the video ends, replay it
        this.currentTime = 0; // Set the current time of the video to the beginning
        this.play(); // Play the video again
    }, false);
});
</script>




</body>
</html>