<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitle() ?></title>
    <link rel="shortcut icon" href="../public/logo.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/195c2f0acd.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/main.css">
</head>

<body>
    <div class="container">
        <?php if (!isset($noNavbar)) { ?>
            <nav id="nav">
                <div class="logo">
                    <a href="dashboard.php"><img src="../public/logo.png" alt="logo"></a>
                </div>
                <span class="hamburger" id="hamburger"><i class="fa fa-bars"></i></span>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="locations.php">Locations</a></li>
                    <li><a href="properties.php">Properties</a></li>
                    <li><a href="reviews.php">Reviews</a></li>
                    <li><a href="logout.php" class="btn btn-danger"><i class="fa fa-sign-out"></i>Logout</a></li>
                </ul>
            </nav>
        <?php } ?>