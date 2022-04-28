<?php
session_start();
$title = 'Contact us'; // setting the title must be before requiring the header
require('./includes/header.php');
?>
<!-- page content goes here -->
<section class="contact-us" id="contact">
    <h2 class="custom-title">contact methods</h2>
    <h1>You can reach for us using the following methods</h1>
    <div class="cards">
        <div class="card">
            <i class="fa fa-envelope"></i>
            <h2>Via Email</h2>
            <a href="mailto:realestate@org.com">realestate@org.com</a>
        </div>
        <div class="card">
            <i class="fa fa-map-marker"></i>
            <h2>Via Branch</h2>
            <ul>
                <li>
                    <p>Branch 1 location</p>
                </li>
                <li>
                    <p>Branch 2 location</p>
                </li>
                <li>
                    <p>Branch 3 location</p>
                </li>
                <li>
                    <p>Branch 4 location</p>
                </li>
            </ul>
        </div>
        <div class="card">
            <i class="fa fa-phone"></i>
            <h2>Via Phone</h2>
            <p>+999 111 222 333</p>
        </div>
        <div class="card">
            <i class="fa fa-share-alt"></i>
            <h2>Via Socials</h2>
            <ul class="socials">
                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
            </ul>
        </div>
    </div>
</section>

<?php
require('./includes/footer.php');
