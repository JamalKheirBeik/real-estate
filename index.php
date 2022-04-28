<?php
session_start();
$title = 'Home'; // setting the title must be before requiring the header
require('./admin/connect.php');
require('./admin/includes/functions.php');
require('./includes/header.php');
// page content goes here
?>
<section class="services">
    <h3 class="custom-title">our services</h3>
    <h1>The smartest way to buy a home</h1>
    <div class="cards">
        <div class="card" data-aos="fade-up">
            <i class="fa-solid fa-piggy-bank"></i>
            <h2>No downpayment</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa vitae quas eaque veniam iusto excepturi explicabo fugiat odio repellat possimus.</p>
        </div>
        <div class="card" data-aos="fade-up">
            <i class="fa-solid fa-wallet"></i>
            <h2>All cash offer</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa vitae quas eaque veniam iusto excepturi explicabo fugiat odio repellat possimus.</p>
        </div>
        <div class="card" data-aos="fade-up">
            <i class="fa-solid fa-user-tie"></i>
            <h2>Experts in your corner</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa vitae quas eaque veniam iusto excepturi explicabo fugiat odio repellat possimus.</p>
        </div>
        <div class="card" data-aos="fade-up">
            <i class="fa-solid fa-lock"></i>
            <h2>Locked in pricing</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa vitae quas eaque veniam iusto excepturi explicabo fugiat odio repellat possimus.</p>
        </div>
    </div>
</section>
<section class="locations">
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
<section class="properties">
    <h2 class="custom-title">latest listed properties</h2>
    <h1>Be the first to check the new listed properties</h1>
    <?php
    $stmt = $con->prepare("SELECT * FROM properties INNER JOIN locations
    ON properties.LocationID = locations.LocationID
    HAVING properties.Sold = 0
    ORDER BY properties.LocationID DESC LIMIT 5");
    $stmt->execute();
    $properties = $stmt->fetchAll();
    if (count($properties) > 0) { ?>
        <div class="cards">
            <?php
            foreach ($properties as $prop) { ?>
                <a href="property.php?propertyid=<?php echo $prop['PropertyID'] ?>">
                    <div class="card" data-aos="fade-up">
                        <?php
                        $stmt = $con->prepare("SELECT * FROM properties_images as a INNER JOIN properties as b on a.PropertyID = b.PropertyID HAVING a.PropertyID = :zid LIMIT 1");
                        $stmt->execute(array('zid' => $prop['PropertyID']));
                        $row = $stmt->fetch();
                        ?>
                        <img src="./admin/uploads/properties/<?php echo $row['ImageSrc'] ?>" alt="">
                        <div class="details">
                            <h2 class="price"><?php echo number_format($prop['Price']) ?> SYP</h2>
                            <ul>
                                <li><i class="fas fa-bed"></i><span><?php echo $prop['BedroomsCount'] ?></span></li>
                                <li><i class="fas fa-bath"></i><span><?php echo $prop['BathroomsCount'] ?></span></li>
                                <li><i class="fas fa-hotel"></i> <span><?php echo number_format($prop['PropertySize']) ?> m<sup>2</sup> </span></li>
                            </ul>
                            <h3><?php echo $prop['LocationName'] ?></h3>
                        </div>
                    </div>
                </a>
            <?php
            }
            ?>
        </div>
    <?php
    } else {
        echo '<p class="alert alert-danger">Sorry we dont have any properties available at the moment</p>';
    } ?>
</section>
<section class="reviews">
    <h3 class="custom-title">Happy clients</h3>
    <h1>What people said about us</h1>
    <?php
    $stmt = $con->prepare("SELECT * FROM reviews INNER JOIN users ON users.UserID = reviews.UserID HAVING Featured = 1");
    $stmt->execute();
    $reviews = $stmt->fetchAll();
    if (count($reviews) > 0) { ?>
        <div class="slides" data-aos="fade-up">
            <?php
            $isActive = false;
            foreach ($reviews as $review) { ?>
                <div class="slide <?php echo $isActive ? '' : 'active' ?>">
                    <img src="./admin/uploads/avatars/<?php echo $review['Picture'] ?>" alt="" />
                    <h1><?php echo $review['Username'] ?></h1>
                    <p><?php echo $review['Message'] ?></p>
                </div>
            <?php
                $isActive = true;
            }
            ?>
        </div>
        <div id="indexes"></div>
    <?php
    } else {
        echo '<p class="alert alert-danger">We dont have any featured reviews at this moment</p>';
    } ?>
</section>
<?php
require('./includes/footer.php');
