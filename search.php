<?php
session_start();
$title = 'Search'; // setting the title must be before requiring the header
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    require('./admin/connect.php');
    require('./admin/includes/functions.php');
    require('./includes/header.php');

    $query = strip_tags($_GET['query']);

    $stmt = $con->prepare("SELECT * FROM properties INNER JOIN locations 
    ON properties.LocationID = locations.LocationID
    HAVING properties.Description LIKE :zquery AND properties.Sold = 0
    ORDER BY properties.LocationID DESC");

    $stmt->execute(array('zquery' => '%' . $query . '%'));
    $rows = $stmt->fetchAll();
?>
    <section class="properties">
        <h1 class="custom-title">Search result</h1>
        <?php
        if (count($rows) > 0) { ?>
            <div class="cards">
                <?php
                foreach ($rows as $row) { ?>
                    <a href="property.php?propertyid=<?php echo $row['PropertyID'] ?>">
                        <div class="card" data-aos="fade-up">
                            <?php
                            $stmt = $con->prepare("SELECT * FROM properties_images as a INNER JOIN properties as b on a.PropertyID = b.PropertyID HAVING a.PropertyID = :zid LIMIT 1");
                            $stmt->execute(array('zid' => $row['PropertyID']));
                            $image = $stmt->fetch();
                            ?>
                            <img src="./admin/uploads/properties/<?php echo $image['ImageSrc'] ?>" alt="">
                            <div class="details">
                                <h2 class="price"><?php echo number_format($row['Price']) ?> SYP</h2>
                                <ul>
                                    <li><i class="fas fa-bed"></i><span><?php echo $row['BedroomsCount'] ?></span></li>
                                    <li><i class="fas fa-bath"></i><span><?php echo $row['BathroomsCount'] ?></span></li>
                                    <li><i class="fas fa-hotel"></i> <span><?php echo number_format($row['PropertySize']) ?> m<sup>2</sup> </span></li>
                                </ul>
                                <h3><?php echo $row['LocationName'] ?></h3>
                            </div>
                        </div>
                    </a>
                <?php
                }
                ?>
            </div>
        <?php
        } else {
            echo '<p class="alert alert-danger">Sorry couldnt find a match</p>';
        } ?>
    </section>
<?php
} else {
    header('Location: index.php');
}
?>
<!-- page content goes here -->

<?php
require('./includes/footer.php');
