<?php
session_start();
$title = 'Properties'; // setting the title must be before requiring the header
if (isset($_SESSION['Username'])) {
    require('./connect.php'); // database connection
    require('./includes/ftp_config.php');
    require('./includes/functions.php');
    require('./includes/header.php');

    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';

    if ($do == 'manage') {
        $stmt = $con->prepare("SELECT * FROM properties INNER JOIN locations ON properties.LocationID = locations.LocationID");
        $stmt->execute();
        $rows = $stmt->fetchAll();
?>
        <section>
            <h1 class="custom-title">Properties</h1>
            <a href="properties.php?do=add" class="btn btn-info"><i class="fa fa-plus"></i> Add a new property</a>
            <?php
            if (!empty($rows)) {

            ?>
                <table>
                    <tr>
                        <th>Property ID</th>
                        <th>Location</th>
                        <th>Images</th>
                        <th>Price</th>
                        <th>Bedrooms</th>
                        <th>Bathrooms</th>
                        <th>Size</th>
                        <th>Is Sold?</th>
                        <th>Purchased By</th>
                        <th>Options</th>
                    </tr>
                    <?php
                    foreach ($rows as $row) { ?>
                        <tr>
                            <td><?php echo $row['PropertyID'] ?></td>
                            <td><?php echo $row['LocationName'] ?></td>
                            <td>
                                <?php
                                $stmt = $con->prepare("SELECT * FROM properties_images AS a INNER JOIN properties AS b ON a.PropertyID = b.PropertyID HAVING a.PropertyID = " . $row['PropertyID']);
                                $stmt->execute();
                                $rows = $stmt->fetchAll();
                                foreach ($rows as $row) {
                                    echo '<a href="/htdocs/admin/uploads/properties/' . $row['ImageSrc'] . '" target="_blank"><img src="./uploads/properties' . $row['ImageSrc'] . '" alt=""></a>';
                                }
                                ?>
                            </td>
                            <td><?php echo $row['Price'] . ' SYP' ?></td>
                            <td><?php echo $row['BedroomsCount'] ?></td>
                            <td><?php echo $row['BathroomsCount'] ?></td>
                            <td><?php echo $row['PropertySize'] . 'm<sup>2</sup>' ?></td>
                            <td><?php echo $row['Sold'] == 0 ? 'No' : 'Yes' ?></td>
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
                                <a class="btn btn-success" href="properties.php?do=edit&propertyid=<?php echo $row['PropertyID'] ?>"><i class="fa fa-edit"></i> Edit</a>
                                <a class="btn btn-danger" href="properties.php?do=delete&propertyid=<?php echo $row['PropertyID'] ?>"><i class="fa fa-close"></i> Delete</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            <?php
            } else { ?>
                <p class="alert alert-info">there is no records</p>
            <?php
            } ?>
        </section>
    <?php
    } elseif ($do == 'add') { ?>
        <section>
            <h1 class="custom-title">New property</h1>
            <form action="?do=insert" method="POST" multipart="" enctype="multipart/form-data">
                <label for="description">Describe the property down below</label>
                <p>talk about the location, size, number of rooms, infrastructure, price ...</p>
                <textarea name="description" id="description" cols="30" rows="10"></textarea>
                <label for="location">Location</label>
                <select name="location" id="location" required>
                    <option value=""></option>
                    <?php
                    $locations = getAllFrom('*', 'locations', NULL, NULL, 'LocationName', 'ASC');
                    foreach ($locations as $location) { ?>
                        <option value="<?php echo $location['LocationID'] ?>"><?php echo $location['LocationName'] ?></option>
                    <?php
                    }
                    ?>
                </select>
                <label for="files">Select Image(s)</label>
                <input type="file" id="files" name="files[]" multiple required>
                <label for="price">Price</label>
                <input type="number" name="price" id="price" min="0" required>
                <label for="bedrooms">Number of Bedrooms</label>
                <input type="number" name="bedrooms" id="bedrooms" min="0" required>
                <label for="bathrooms">Number of Bathrooms</label>
                <input type="number" name="bathrooms" id="bathrooms" min="0" required>
                <label for="size">Size (in m <sup>2</sup>)</label>
                <input type="number" name="size" id="size" min="0" required>
                <button type="submit">Add</button>
            </form>
        </section>
        <?php
    } elseif ($do == 'insert') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $files = reArrayFiles($_FILES['files']);

            $location = $_POST['location'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $bedrooms = $_POST['bedrooms'];
            $bathrooms = $_POST['bathrooms'];
            $size = $_POST['size'];

            $formErrors = array();

            if (strlen($description) < 50) {
                $formErrors[] = 'The description is very short please add more info';
            }

            if (strlen($description) > 1500) {
                $formErrors[] = 'The description is very long please make it shorter than 1500 characters';
            }

            if (empty($location)) {
                $formErrors[] = 'Location cannot be empty';
            }

            if (empty($price)) {
                $formErrors[] = 'Price cannot be empty';
            }

            if (empty($bedrooms)) {
                $formErrors[] = 'Bedrooms cannot be empty';
            }

            if (empty($bathrooms)) {
                $formErrors[] = 'Bathrooms cannot be empty';
            }

            if (empty($size)) {
                $formErrors[] = 'Size cannot be empty';
            }

            foreach ($files as $file) {
                $imageName = $file['name'];
                $imageSize = $file['size'];
                $imageTmp = $file['tmp_name'];
                $imageType = $file['type'];
                $imageAllowedExtension = array("jpeg", "jpg", "png", "gif");
                $tmp = explode('.', $imageName);
                $imageExtension = strtolower(end($tmp));

                if (!empty($files) && !in_array($imageExtension, $imageAllowedExtension)) {
                    $formErrors[] = 'You can only upload ' . implode(',', $imageAllowedExtension) . ' images';
                }

                if (empty($imageName)) {
                    $formErrors[] = 'Image is required';
                }

                if ($imageSize > 4194304) {
                    $formErrors[] = 'Image cannot be larger than 4MB';
                }
            }

            foreach ($formErrors as $error) {
                echo '<p class="alert alert-danger">' . $error . '</p>';
            }

            if (empty($formErrors)) {
                // insert property
                $stmt = $con->prepare("INSERT INTO 
                                                properties(LocationID, Description, Price, BedroomsCount, BathroomsCount, PropertySize)
                                            VALUES(:zlocation, :zdesc, :zprice, :zbeds, :zbaths, :zsize)");
                $stmt->execute(array(
                    'zlocation' => $location,
                    'zdesc' => $description,
                    'zprice' => $price,
                    'zbeds' => $bedrooms,
                    'zbaths' => $bathrooms,
                    'zsize' => $size
                ));
                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Inserted</div>';

                $propertyID = $con->lastInsertId();
                // moving images to the "uploads/properties" folder
                foreach ($files as $file) {
                    $imageName = $file['name'];
                    $imageTmp = $file['tmp_name'];
                    $image = rand(0, 10000000000) . '_' . $imageName;
                    // ftp connection
                    $ftp_conn = ftp_connect($ftp_server) or die('<p class="alert alert-danger">Could not connect to ' . $ftp_server . '</p>');
                    $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass) or die('<p class="alert alert-danger">Could not login to ftp server</p>');
                    // upload file to server
                    uploadFile($ftp_conn, $file, $image, '/htdocs/admin/uploads/properties/');
                    // end ftp connection
                    ftp_close($ftp_conn);
                    // insert property images
                    $stmt2 = $con->prepare("INSERT INTO 
                                                properties_images(PropertyID, ImageSrc)
                                                                        VALUES(:zprop, :zimg)");
                    $stmt2->execute(array(
                        'zprop' => $propertyID,
                        'zimg' => $image
                    ));
                }

                redirectHome($theMsg, 'back');
            }
        } else {
            $theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';
            redirectHome($theMsg);
        }
    } elseif ($do == 'edit') {
        $propertyid = isset($_GET['propertyid']) && is_numeric($_GET['propertyid']) ? intval($_GET['propertyid']) : 0;
        $stmt = $con->prepare("SELECT * FROM properties WHERE PropertyID = ? LIMIT 1");
        $stmt->execute(array($propertyid));
        $row = $stmt->fetch();
        $count = $stmt->rowCount();
        if ($count > 0) { ?>
            <section>
                <h1 class="custom-title">Edit property</h1>
                <form action="?do=update" method="POST" multipart="" enctype="multipart/form-data">
                    <input type="hidden" name="propertyid" value="<?php echo $propertyid ?>" />
                    <label for="description">Describe the property down below</label>
                    <p>talk about the location, size, number of rooms, infrastructure, price ...</p>
                    <textarea name="description" id="description" cols="30" rows="10"><?php echo $row['Description'] ?></textarea>
                    <label for="location">Location</label>
                    <select name="location" id="location" required>
                        <option value=""></option>
                        <?php
                        $locations = getAllFrom('*', 'locations', NULL, NULL, 'LocationName', 'ASC');
                        foreach ($locations as $location) { ?>
                            <option value="<?php echo $location['LocationID'] ?>" <?php if ($location['LocationID'] == $row['LocationID']) echo 'selected' ?>><?php echo $location['LocationName'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" min="0" value="<?php echo $row['Price'] ?>" required>
                    <label for="bedrooms">Number of Bedrooms</label>
                    <input type="number" name="bedrooms" id="bedrooms" min="0" value="<?php echo $row['BedroomsCount'] ?>" required>
                    <label for="bathrooms">Number of Bathrooms</label>
                    <input type="number" name="bathrooms" id="bathrooms" min="0" value="<?php echo $row['BathroomsCount'] ?>" required>
                    <label for="size">Size (in m <sup>2</sup>)</label>
                    <input type="number" name="size" id="size" min="0" value="<?php echo $row['PropertySize'] ?>" required>
                    <button type="submit">Add</button>
                </form>
            </section>
<?php
        }
    } elseif ($do == 'update') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $propertyid = $_POST['propertyid'];
            $description = $_POST['description'];
            $location = $_POST['location'];
            $price = $_POST['price'];
            $bedrooms = $_POST['bedrooms'];
            $bathrooms = $_POST['bathrooms'];
            $size = $_POST['size'];

            $formErrors = array();

            if (strlen($description) < 50) {
                $formErrors[] = 'The description is very short please add more info';
            }

            if (strlen($description) > 1500) {
                $formErrors[] = 'The description is very long please make it shorter than 1500 characters';
            }

            if (empty($location)) {
                $formErrors[] = 'Location cannot be empty';
            }

            if (empty($price)) {
                $formErrors[] = 'Price cannot be empty';
            }

            if (empty($bedrooms)) {
                $formErrors[] = 'Bedrooms cannot be empty';
            }

            if (empty($bathrooms)) {
                $formErrors[] = 'Bathrooms cannot be empty';
            }

            if (empty($size)) {
                $formErrors[] = 'Size cannot be empty';
            }

            foreach ($formErrors as $error) {
                echo '<p class="alert alert-danger">' . $error . '</p>';
            }

            if (empty($formErrors)) {
                // update property
                $stmt = $con->prepare("UPDATE properties
                 SET LocationID = :zlocation, Description = :zdesc, Price = :zprice, BedroomsCount = :zbeds, BathroomsCount = :zbaths, PropertySize = :zsize
                 WHERE PropertyID = :zid");
                $stmt->execute(array(
                    'zid' => $propertyid,
                    'zlocation' => $location,
                    'zdesc' => $description,
                    'zprice' => $price,
                    'zbeds' => $bedrooms,
                    'zbaths' => $bathrooms,
                    'zsize' => $size
                ));
                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';

                redirectHome($theMsg, 'back');
            }
        } else {
            $theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';
            redirectHome($theMsg);
        }
    } elseif ($do == 'delete') {
        $propertyid = isset($_GET['propertyid']) && is_numeric($_GET['propertyid']) ? intval($_GET['propertyid']) : 0;
        $check = checkItem('PropertyID', 'properties', $propertyid);
        if ($check > 0) {
            // delete images from the 'uploads/properties/' folder
            $deleteImages = $con->prepare("SELECT * FROM properties_images WHERE PropertyID = :zid");
            $deleteImages->execute(array('zid' => $propertyid));
            $rows = $deleteImages->fetchAll();
            // ftp connection
            $ftp_conn = ftp_connect($ftp_server) or die('<p class="alert alert-danger">Could not connect to ' . $ftp_server . '</p>');
            $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass) or die('<p class="alert alert-danger">Could not login to ftp server</p>');
            foreach ($rows as $row) {
                if(!unlink('./uploads/properties/' . $row['ImageSrc'])) die('<p class="alert alert-danger">Couldnt delete the old image(s).</p>');
            }
            // end ftp connection
            ftp_close($ftp_conn);

            // delete from properties and properties images tables (foreign key with cascade on delete) 
            $deleteProp = $con->prepare("DELETE FROM properties WHERE PropertyID = " . $propertyid);
            $deleteProp->execute();

            $theMsg = "<div class='alert alert-success'>" . $deleteProp->rowCount() . ' Record Deleted</div>';
            redirectHome($theMsg, 'back');
        } else {
            $theMsg = '<div class="alert alert-danger">This ID is Not Exist</div>';
            redirectHome($theMsg);
        }
    }
} else {
    header("Location: index.php");
    exit();
}
require('./includes/footer.php');
