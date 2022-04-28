<?php
session_start();
$title = 'Properties'; // setting the title must be before requiring the header
require('./admin/connect.php');
require('./admin/includes/functions.php');
require('./includes/header.php');
?>
<section class="properties" id="properties">
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

<?php
require('./includes/footer.php');
