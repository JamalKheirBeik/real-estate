<?php
session_start();
$noNavbar = ''; // init this variable if the navbar is not needed
$title = 'Admin Login'; // setting the title must be before requiring the header
if (isset($_SESSION['Username'])) {
    header('Location: dashboard.php');
}
require('./connect.php'); // database connection
require('./includes/functions.php');
require('./includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashedPass = sha1($password);

    $stmt = $con->prepare("SELECT UserID, Username, Password FROM users WHERE Username = ? AND Password = ? AND GroupID = 1 LIMIT 1");
    $stmt->execute(array($username, $hashedPass));
    $row = $stmt->fetch();
    $count = $stmt->rowCount();

    if ($count > 0) {
        $_SESSION['Username'] = $username; // Register Session Name
        $_SESSION['ID'] = $row['UserID']; // Register Session ID
        header('Location: dashboard.php'); // Redirect To Dashboard Page
        exit();
    }
}
?>
<!-- page content goes here -->
<section class="login">
    <h1>Admin Login</h1>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="off">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">
        <button type="submit">Login</button>
    </form>
</section>

<?php
require('./includes/footer.php');
