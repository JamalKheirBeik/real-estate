<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Simple Real Estate Company Simulation">
    <meta name="keywords" content="Real Estate, Properties, Houses, Locations, Buy a House">
    <meta name="author" content="Jamal Kheir Beik">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'No title'; ?></title>
    <link rel="shortcut icon" href="./public/logo.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/195c2f0acd.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/main.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <script src="./public/js/main.js" defer></script>
    <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' || 'reviews.php' ? '<script src="./public/js/slider.js" defer></script>' : ''; ?>
    <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? '<script src="./public/js/login.js" defer></script><link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">' : ''; ?>
    <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? '<script src="./public/js/counter.js" defer></script>' : ''; ?>
</head>

<body>
    <div class="container">
        <nav id="nav">
            <div class="logo">
                <a href="index.php"><img src="./public/logo.png" alt="logo"></a>
            </div>
            <span class="hamburger" id="hamburger"><i class="fa fa-bars"></i></span>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="locations.php#locations">Locations</a></li>
                <li><a href="properties.php#properties">Properties</a></li>
                <li><a href="reviews.php#reviews">Reviews</a></li>
                <li><a href="contact.php#contact">Contact</a></li>
                <li><a href="about.php#about">About</a></li>
                <li>
                    <?php
                    if (isset($_SESSION['user'])) {
                        echo '<a class="btn btn-danger" href="logout.php"><i class="fa fa-sign-out"></i>Logout</a>';
                    } else {
                        echo '<a class="btn btn-info" href="login.php#forms"><i class="fa fa-sign-in"></i>Login</a>';
                    }
                    ?>
                </li>
            </ul>
        </nav>
        <section class="banner">
            <h1>Find the property of your dreams</h1>
            <form action="search.php" method="GET">
                <input type="text" name="query" id="query" placeholder="Enter location" onblur="this.placeholder = 'Enter location'" onfocus="this.placeholder = ''" autocomplete="off">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
            <div class="overlay"></div>
        </section>