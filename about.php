<?php
session_start();
$title = 'About'; // setting the title must be before requiring the header
require('./admin/connect.php');
require('./admin/includes/functions.php');
require('./includes/header.php');
?>
<!-- page content goes here -->
<section id="about">
    <h2 class="custom-title">About us</h2>
    <div class="about-us" data-aos="fade-up">
        <img src="./public/deal_seal.jpg" alt="">
        <div class="caption">
            <h1>We put people first</h1>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquam reprehenderit dolore ullam natus culpa, ut aperiam explicabo inventore impedit eveniet aliquid voluptatibus voluptatem voluptate veritatis aspernatur modi rem? Nulla deserunt quo sunt fugit adipisci tenetur aperiam omnis perspiciatis odit similique voluptatum dolorum iste ducimus excepturi magnam earum neque, atque illo.</p>
        </div>
    </div>
    <div class="stats" data-aos="fade-up">
        <div>
            <strong class="counter" data-target="<?php echo countItems('*', 'locations') ?>">0</strong>
            <span>locatios <br> covered</span>
        </div>
        <div>
            <strong class="counter" data-target="<?php echo countItems('*', 'properties') ?>">0</strong>
            <span>total <br> properties</span>
        </div>
        <div>
            <strong class="counter" data-target="<?php echo checkItem('GroupID', 'users', '0') ?>">0</strong>
            <span>total <br> customers</span>
        </div>
        <div>
            <strong class="counter" data-target="<?php echo countItems('*', 'reviews') ?>">0</strong>
            <span>total <br> reviews</span>
        </div>
    </div>
</section>
<?php
require('./includes/footer.php');
