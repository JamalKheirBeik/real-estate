<?php
session_start();
$title = 'Locations'; // setting the title must be before requiring the header
require('./admin/connect.php');
require('./admin/includes/functions.php');
require('./includes/header.php');
?>
<section class="locations" id="locations">
    <h3 class="custom-title">areas we cover</h3>
    <h1>We cover alot of locations here are some of them</h1>
    <?php
    $locations = getLatest('*', 'locations', 'LocationID');
    if (count($locations) > 0) { ?>
        <div class="cards">
            <?php
            foreach ($locations as $location) { ?>
                <div class="card" data-aos="fade-up">
                    <img src="./admin/uploads/locations/<?php echo $location['LocationPicture'] ?>" alt="">
                    <h2><?php echo $location['LocationName'] ?></h2>
                </div>
            <?php
            }
            ?>
        </div>
    <?php
    } else {
        echo '<p class="alert alert-danger">Sorry we are not covering any locations at this moment</p>';
    }
    ?>
</section>

<?php
require('./includes/footer.php');
