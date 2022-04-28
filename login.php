<?php
session_start();
$title = 'Login'; // setting the title must be before requiring the header
if (isset($_SESSION['user'])) {
    header('Location: index.php');
}
require('./admin/connect.php');
require('./admin/includes/ftp_config.php');
require('./admin/includes/functions.php');
require('./includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // handling login
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $hashedPass = sha1($pass);

        $stmt = $con->prepare("SELECT 
                                    UserID, Username, Password 
                                FROM 
                                    users 
                                WHERE 
                                    Username = ? 
                                AND 
                                    Password = ?
                                AND GroupID = 0");

        $stmt->execute(array($user, $hashedPass));
        $get = $stmt->fetch();
        $count = $stmt->rowCount();

        if ($count > 0) {
            $_SESSION['user'] = $user; // Register Session Name
            $_SESSION['uid'] = $get['UserID']; // Register User ID in Session
            header('Location: index.php'); // Redirect To Dashboard Page
            exit();
        }
    } else {
        // handling signup
        $formErrors = array();

        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $email = $_POST['email'];

        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageTmp    = $_FILES['image']['tmp_name'];
        $imageType = $_FILES['image']['type'];
        $imageAllowedExtension = array("jpeg", "jpg", "png", "gif");
        $tmp = explode('.', $imageName);
        $imageExtension = strtolower(end($tmp));

        if (isset($username)) {
            $filterdUser = filter_var($username, FILTER_SANITIZE_SPECIAL_CHARS);
            if (strlen($filterdUser) < 3) {
                $formErrors[] = 'Username Must Be Larger Than 3 Characters';
            }
        }

        if (isset($password) && isset($password2)) {
            if (empty($password)) {
                $formErrors[] = 'Sorry Password Cant Be Empty';
            }
            if (sha1($password) !== sha1($password2)) {
                $formErrors[] = 'Sorry Password Is Not Match';
            }
        }

        if (isset($email)) {
            $filterdEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (filter_var($filterdEmail, FILTER_VALIDATE_EMAIL) != true) {
                $formErrors[] = 'This Email Is Not Valid';
            }
        }

        if (!empty($imageName) && !in_array($imageExtension, $imageAllowedExtension)) {
            $formErrors[] = 'This Extension Is Not Allowed';
        }

        if (empty($imageName)) {
            $formErrors[] = 'Image Is Required';
        }

        if ($imageSize > 4194304) {
            $formErrors[] = 'Image Cant Be Larger Than 4MB';
        }

        foreach ($formErrors as $error) {
            echo '<p class="alert alert-danger">' . $error . '</p>';
        }

        if (empty($formErrors)) {
            $check = checkItem("Username", "users", $username);
            if ($check == 1) {
                echo '<p class="alert alert-danger">Sorry This Username Is Taken</p>';
            } else {
                $ftp_conn = ftp_connect($ftp_server) or die('<p class="alert alert-danger">Could not connect to ' . $ftp_server . '</p>');
                ftp_login($ftp_conn, $ftp_username, $ftp_userpass) or die('<p class="alert alert-danger">Could not login to ftp server</p>');

                $image = rand(0, 10000000000) . '_' . $imageName;
                uploadFile($ftp_conn, $_FILES['image'], $image, '/htdocs/admin/uploads/avatars/');

                ftp_close($ftp_conn);

                $stmt = $con->prepare("INSERT INTO 
                                        users(Username, Password, Email, FullName, Picture)
                                    VALUES(:zuser, :zpass, :zmail, :zname, :zimage)");
                $stmt->execute(array(
                    'zuser' => $username,
                    'zpass' => sha1($password),
                    'zmail' => $email,
                    'zname' => $fullname,
                    'zimage' => $image
                ));
                echo '<p class="alert alert-success">Congrats You Are Now Registerd User</p>';
            }
        }
    }
}
?>
<!-- page content goes here -->
<section>
    <div class="forms-container">

        <div class="forms" id="forms">
            <div class="form login">
                <span class="title">Login</span>

                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="input-field">
                        <input type="text" name="username" placeholder="Enter your username" autocomplete="off" required>
                        <i class="uil uil-user"></i>
                    </div>

                    <div class="input-field">
                        <input type="password" name="password" class="password" placeholder="Enter your password" required>
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>

                    <div class="input-field button">
                        <input type="submit" name="login" value="Login Now">
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">Not a member?
                        <a href="#" class="text signup-link">Signup now</a>
                    </span>
                </div>
            </div>


            <div class="form signup">
                <span class="title">Registration</span>

                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="input-field">
                        <input type="text" name="fullname" placeholder="Enter your full name" autocomplete="off" required>
                        <i class="uil uil-edit-alt"></i>
                    </div>

                    <div class="input-field">
                        <input type="text" name="username" placeholder="Enter your user name" autocomplete="off" required>
                        <i class="uil uil-user"></i>
                    </div>

                    <div class="input-field">
                        <input type="email" name="email" placeholder="Enter your email" autocomplete="off" required>
                        <i class="uil uil-envelope"></i>
                    </div>

                    <div class="input-field">
                        <input type="password" name="password" placeholder="Create a password" required>
                        <i class="uil uil-lock icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password2" class="password" placeholder="Confirm a password" required>
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>

                    <div class="input-field">
                        <input type="file" name="image" required>
                        <i class="uil uil-image"></i>
                    </div>

                    <div class="input-field button">
                        <input type="submit" name="signup" value="Register Now">
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">Already have an account?
                        <a href="#" class="text login-link">Login now</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
require('./includes/footer.php');
