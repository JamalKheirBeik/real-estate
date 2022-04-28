<?php
session_start();
$title = 'Property Details'; // setting the title must be before requiring the header
require('./admin/connect.php');
require('./admin/includes/functions.php');
require('./includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pid = $_POST['propertyid'];
    $uid = $_POST['userid'];

    $updateProp = $con->prepare('UPDATE properties SET Sold = 1 WHERE PropertyID = :zpid');
    $updateProp->execute(array('zpid' => $pid));
    // insert to sales table
    $stmt = $con->prepare('INSERT INTO props_sold (UserID, PropID) VALUES (:zuid, :zpid)');
    $stmt->execute(array('zuid' => $uid, 'zpid' => $pid));

    header('Location: properties.php');
    exit();
}

if (!isset($_GET['propertyid']) || !is_numeric($_GET['propertyid'])) {
    header('Location: index.php');
    exit();
}


$stmt = $con->prepare('SELECT * FROM properties WHERE PropertyID = :zid');
$stmt->execute(array('zid' => $_GET['propertyid']));
$prop = $stmt->fetch();
$count = $stmt->rowCount();
if ($count > 0) { ?>
    <section class="property">
        <h1>Property Details</h1>
        <h2 class="custom-title">Property images</h2>
        <div class="slides" data-aos="fade-up">
            <?php
            $isActive = false;
            $stmt = $con->prepare("SELECT * FROM properties_images AS a INNER JOIN properties AS b ON a.PropertyID = b.PropertyID HAVING a.PropertyID = " . $prop['PropertyID']);
            $stmt->execute();
            $images = $stmt->fetchAll();
            foreach ($images as $image) { ?>
                <div class="slide <?php echo $isActive ? '' : 'active' ?>">
                    <img src="./admin/uploads/properties/<?php echo $image['ImageSrc'] ?>" alt="" />
                </div>
            <?php
                $isActive = true;
            }
            ?>
        </div>
        <div id="indexes"></div>
        <h2 class="custom-title">About this property</h2>
        <div class="details">
            <h3>Price: <span><?php echo number_format($prop['Price']) ?> SYP</span></h3>
            <h3>Location: <span>
                    <?php
                    $stmt = $con->prepare("SELECT * FROM properties INNER JOIN locations ON properties.LocationID = locations.LocationID HAVING properties.LocationID = :zid");
                    $stmt->execute(array('zid' => $prop['LocationID']));
                    $row = $stmt->fetch();
                    echo $row['LocationName'];
                    ?>
                </span></h3>
            <h3>Size: <span><?php echo $prop['PropertySize'] ?> m<sup>2</sup></span></h3>
            <h3>Number of Bedrooms: <span><?php echo $prop['BedroomsCount'] ?></span></h3>
            <h3>Number of Bathrooms: <span><?php echo $prop['BathroomsCount'] ?></span></h3>
            <h3>Description:</h3>
            <p><?php echo $prop['Description'] ?></p>
        </div>
        <h2 class="custom-title">You can buy this property right now</h2>
        <?php
        if (isset($_SESSION['user'])) { ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="hidden" name="propertyid" value="<?php echo $prop['PropertyID'] ?>">
                <input type="hidden" name="userid" value="<?php echo $_SESSION['uid'] ?>">
                <button type="submit">buy now</button>
            </form>
        <?php
        } else {
            echo '<p class="alert alert-danger">You need to be logged-in in order to buy a property</p>';
        }
        ?>
    </section>
<?php
} else {
    header('Location: index.php');
    exit();
}

require('./includes/footer.php');
