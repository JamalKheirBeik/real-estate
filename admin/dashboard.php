<?php
session_start();
if (isset($_SESSION['Username'])) {
    $title = 'Dashboard'; // setting the title must be before requiring the header
    require('./connect.php'); // database connection
    require('./includes/functions.php');
    require('./includes/header.php');
?>
    <section>
        <h1 class="custom-title">Statistics</h1>
        <div class="statistics">
            <div class="statistic">
                <h1><i class="fa fa-users"></i> Total users</h1>
                <h2><?php echo checkItem('GroupID', 'users', 0) ?></h2>
            </div>
            <div class="statistic">
                <h1><i class="fa fa-map-marker"></i> Total locations</h1>
                <h2><?php echo countItems('*', 'locations') ?></h2>
            </div>
            <div class="statistic">
                <h1><i class="far fa-file-alt"></i> Total reviews</h1>
                <h2><?php echo countItems('*', 'reviews') ?></h2>
            </div>
            <div class="statistic">
                <h1><i class="far fa-building"></i> Remaining properties</h1>
                <h2><?php echo checkItem('Sold', 'properties', 0) ?></h2>
            </div>
            <div class="statistic">
                <h1><i class="fas fa-hand-holding-usd"></i> Sold properties</h1>
                <h2><?php echo countItems('ID', 'props_sold') ?></h2>
            </div>
        </div>
        <h1 class="custom-title">Latest users</h1>
        <?php
        $users = getLatest('*', 'users', 'UserID');
        if (count($users) > 0) { ?>
            <table>
                <tr>
                    <th>UserID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Picture</th>
                    <th>Role</th>
                </tr>
                <?php
                foreach ($users as $user) { ?>
                    <tr>
                        <td><?php echo $user['UserID'] ?></td>
                        <td><?php echo $user['FullName'] ?></td>
                        <td><?php echo $user['Email'] ?></td>
                        <td><?php echo $user['Username'] ?></td>
                        <td>
                            <a href="/real-estate/admin/uploads/avatars/<?php echo $user['Picture'] ?>" target="_blank">
                                <img src="./uploads/avatars/<?php echo $user['Picture'] ?>" alt="">
                            </a>
                        </td>
                        <td><?php echo $user['GroupID'] == 1 ? 'Admin' : 'User' ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        <?php
        } else {
            echo '<p class="alert alert-info">No records</p>';
        } ?>
        <h1 class="custom-title">Latest locations</h1>
        <?php
        $locations = getLatest('*', 'locations', 'LocationID');
        if (count($locations) > 0) { ?>
            <table>
                <tr>
                    <th>Location ID</th>
                    <th>Location Name</th>
                    <th>Location Image</th>
                    <th>Options</th>
                </tr>
                <?php
                foreach ($locations as $location) { ?>
                    <tr>
                        <td><?php echo $location['LocationID'] ?></td>
                        <td><?php echo $location['LocationName'] ?></td>
                        <td>
                            <a href="/real-estate/admin/uploads/locations/<?php echo $location['LocationPicture'] ?>" target="_blank">
                                <img src="./uploads/locations/<?php echo $location['LocationPicture'] ?>" alt="">
                            </a>
                        </td>
                        <td class="options">
                            <a class="btn btn-success" href="locations.php?do=edit&locationid=<?php echo $location['LocationID'] ?>"><i class="fa fa-edit"></i>Edit</a>
                            <a class="btn btn-danger" href="locations.php?do=delete&locationid=<?php echo $location['LocationID'] ?>"><i class="fa fa-close"></i>Delete</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        <?php
        } else {
            echo '<p class="alert alert-info">No records</p>';
        } ?>
        <h1 class="custom-title">Latest reviews</h1>
        <?php
        $stmt = $con->prepare("SELECT * FROM reviews INNER JOIN users ON users.UserID = reviews.UserID ORDER BY reviews.UserID DESC LIMIT 5");
        $stmt->execute();
        $reviews = $stmt->fetchAll();
        if (count($reviews) > 0) { ?>
            <table>
                <tr>
                    <th>ReviewID</th>
                    <th>By</th>
                    <th>Picture</th>
                    <th>Message</th>
                    <th>Is_Featured</th>
                    <th>Options</th>
                </tr>
                <?php
                foreach ($reviews as $review) { ?>
                    <tr>
                        <td><?php echo $review['ReviewID'] ?></td>
                        <td><?php echo $review['FullName'] ?></td>
                        <td>
                            <a href="/real-estate/admin/uploads/avatars/<?php echo $review['Picture'] ?>" target="_blank">
                                <img src="./uploads/avatars/<?php echo $review['Picture'] ?>" alt="">
                            </a>
                        </td>
                        <td><?php echo $review['Message'] ?></td>
                        <td><?php echo $review['Featured'] == 0 ? 'No' : 'Yes' ?></td>
                        <td class="options">
                            <?php
                            if ($review['Featured'] == 0) { ?>
                                <a class="btn btn-success" href="reviews.php?do=feature&reviewid=<?php echo $review['ReviewID'] ?>"><i class="far fa-heart"></i> Feature</a>
                            <?php
                            } else { ?>
                                <a class="btn btn-success" href="reviews.php?do=unfeature&reviewid=<?php echo $review['ReviewID'] ?>"><i class="fa fa-heart-broken"></i> Unfeature</a>
                            <?php
                            }
                            ?>
                            <a class="btn btn-danger" href="reviews.php?do=delete&reviewid=<?php echo $review['ReviewID'] ?>"><i class="fa fa-close"></i> Delete</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        <?php
        } else {
            echo '<p class="alert alert-info">No records</p>';
        } ?>
        <h1 class="custom-title">Latest properties</h1>
        <?php
        $stmt = $con->prepare("SELECT * FROM properties INNER JOIN locations ON properties.LocationID = locations.LocationID ORDER BY properties.LocationID DESC LIMIT 5");
        $stmt->execute();
        $properties = $stmt->fetchAll();
        if (count($properties) > 0) { ?>
            <table>
                <tr>
                    <th>Property ID</th>
                    <th>Location</th>
                    <th>Images</th>
                    <th>Price</th>
                    <th>Bedrooms</th>
                    <th>Bathrooms</th>
                    <th>Size</th>
                    <th>Is Sold</th>
                    <th>Purchased By</th>
                    <th>Options</th>
                </tr>
                <?php
                foreach ($properties as $prop) { ?>
                    <tr>
                        <td><?php echo $prop['PropertyID'] ?></td>
                        <td><?php echo $prop['LocationName'] ?></td>
                        <td>
                            <?php
                            $stmt = $con->prepare("SELECT * FROM properties_images AS a INNER JOIN properties AS b ON a.PropertyID = b.PropertyID HAVING a.PropertyID = " . $prop['PropertyID']);
                            $stmt->execute();
                            $rows = $stmt->fetchAll();
                            foreach ($rows as $row) {
                                echo '<a href="/real-estate/admin/uploads/properties/' . $row['ImageSrc'] . '" target="_blank"><img src="./uploads/properties/' . $row['ImageSrc'] . '" alt=""></a>';
                            }
                            ?>
                        </td>
                        <td><?php echo $prop['Price'] ?></td>
                        <td><?php echo $prop['BedroomsCount'] ?></td>
                        <td><?php echo $prop['BathroomsCount'] ?></td>
                        <td><?php echo $prop['PropertySize'] ?></td>
                        <td><?php echo $prop['Sold'] == 0 ? 'No' : 'Yes' ?></td>
                        <td>
                            <?php
                            if ($row['Sold'] == 0) {
                                echo 'No one';
                            } else {
                                $stmt = $con->prepare('SELECT * FROM props_sold AS a INNER JOIN users AS b
                                    ON a.UserID = b.UserID HAVING PropID = :zid');
                                $stmt->execute(array('zid' => $row['PropertyID']));
                                $username = $stmt->fetch();
                                echo $username['Username'];
                            }
                            ?>
                        </td>
                        <td class="options">
                            <a class="btn btn-success" href="properties.php?do=edit&propertyid=<?php echo $prop['PropertyID'] ?>"><i class="fa fa-edit"></i>Edit</a>
                            <a class="btn btn-danger" href="properties.php?do=delete&propertyid=<?php echo $prop['PropertyID'] ?>"><i class="fa fa-close"></i>Delete</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        <?php
        } else {
            echo '<p class="alert alert-info">No records</p>';
        } ?>
    </section>

<?php
    require('./includes/footer.php');
} else {
    header('Location: index.php');
    exit();
}
