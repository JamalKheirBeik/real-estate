<?php
session_start();
$noNavbar = ''; // init this variable if the navbar is not needed
$title = ''; // setting the title must be before requiring the header
require('./connect.php'); // database connection
require('./includes/functions.php');
require('./includes/header.php');
?>
<!-- page content goes here -->


<?php
require('./includes/footer.php');
