<?php
session_start();
$title = 'Locations'; // setting the title must be before requiring the header
if (isset($_SESSION['Username'])) {
    require('./connect.php'); // database connection
    require('./includes/ftp_config.php');
    require('./includes/functions.php');
    require('./includes/header.php');

    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';

    if ($do == 'manage') {
        $locations = getAllFrom('*', 'locations', NULL, NULL, 'LocationID', 'ASC');
?>
        <section>
            <h1 class="custom-title">Locations</h1>
            <a class="btn btn-info" href="locations.php?do=add"><i class="fa fa-plus"></i>Add a new location</a>
            <?php
            if (!empty($locations)) {
            ?>
                <table>
                    <tr>
                        <th>Location ID</th>
                        <th>Location Name</th>
                        <th>Image</th>
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
        </section>
    <?php
            } else {
                echo '<p class="alert alert-info">there is no records</p>';
            }
        } elseif ($do == 'add') { ?>
    <section>
        <h1 class="custom-title">New location</h1>
        <form action="?do=insert" method="POST" enctype="multipart/form-data">
            <label for="name">Location Name</label>
            <input type="text" name="name" id="name" autocomplete="off" required>
            <label for="image">Select Image</label>
            <input type="file" id="image" name="image" required>
            <button type="submit">Add</button>
        </form>
    </section>
    <?php
        } elseif ($do == 'insert') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name = $_POST['name'];
                $imageName = $_FILES['image']['name'];
                $imageSize = $_FILES['image']['size'];
                $imageTmp    = $_FILES['image']['tmp_name'];
                $imageType = $_FILES['image']['type'];
                $imageAllowedExtension = array("jpeg", "jpg", "png", "gif");
                $tmp = explode('.', $imageName);
                $imageExtension = strtolower(end($tmp));

                $formErrors = array();

                if (strlen($name) < 4) {
                    $formErrors[] = 'Location Name Cant Be Less Than 4 Characters';
                }

                if (strlen($name) > 80) {
                    $formErrors[] = 'Location Name Cant Be More Than 80 Characters';
                }

                if (empty($name)) {
                    $formErrors[] = 'Location Name Cant Be Empty';
                }

                if (!empty($imageName) && !in_array($imageExtension, $imageAllowedExtension)) {
                    $formErrors[] = 'This Extension Is Not Allowed';
                }

                if (empty($imageName)) {
                    $formErrors[] = 'Image Is Required';
                }

                if ($imageSize > 4194304) {
                    $formErrors[] = 'Image Cant Be Larger Than 4MB';
                }

                foreach ($formErrors as $error) {
                    echo '<p class="alert alert-danger">' . $error . '</p>';
                }

                if (empty($formErrors)) {
                    // ftp connection
                    $ftp_conn = ftp_connect($ftp_server) or die('<p class="alert alert-danger">Could not connect to ' . $ftp_server . '</p>');
                    $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass) or die('<p class="alert alert-danger">Could not login to ftp server</p>');
                    // upload file to server
                    $image = rand(0, 10000000000) . '_' . $imageName;
                    uploadFile($ftp_conn, $_FILES['image'], $image, '/htdocs/admin/uploads/locations/');
                    // end ftp connection
                    ftp_close($ftp_conn);

                    $stmt = $con->prepare("INSERT INTO 
                                                locations(LocationName, LocationPicture)
                                            VALUES(:zname, :zimage) ");
                    $stmt->execute(array(

                        'zname'     => $name,
                        'zimage'     => $image

                    ));

                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Inserted</div>';
                    redirectHome($theMsg, 'back');
                }
            } else {
                $theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';
                redirectHome($theMsg);
            }
        } elseif ($do == 'edit') {
            $locationid = isset($_GET['locationid']) && is_numeric($_GET['locationid']) ? intval($_GET['locationid']) : 0;
            $stmt = $con->prepare("SELECT * FROM locations WHERE LocationID = " . $locationid);
            $stmt->execute();
            $row = $stmt->fetch();
            if (count($row) > 0) { ?>
        <section>
            <h1 class="custom-title">Edit location</h1>
            <form action="?do=update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="locationid" value="<?php echo $locationid ?>" />
                <label for="name">Location Name</label>
                <input type="text" name="name" id="name" autocomplete="off" value="<?php echo $row['LocationName'] ?>" required>
                <label for="image">Select Image</label>
                <p>Upload an image if only you want to change the old one</p>
                <input type="file" id="image" name="image">
                <button type="submit">Add</button>
            </form>
        </section>
<?php
            }
        } elseif ($do == 'update') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $id = $_POST['locationid'];
                $name = $_POST['name'];
                $imageName = $_FILES['image']['name'];
                $imageSize = $_FILES['image']['size'];
                $imageTmp    = $_FILES['image']['tmp_name'];
                $imageType = $_FILES['image']['type'];
                $imageAllowedExtension = array("jpeg", "jpg", "png", "gif");
                $tmp = explode('.', $imageName);
                $imageExtension = strtolower(end($tmp));

                $formErrors = array();

                if (strlen($name) < 4) {
                    $formErrors[] = 'Location Name Cant Be Less Than 4 Characters';
                }

                if (strlen($name) > 80) {
                    $formErrors[] = 'Location Name Cant Be More Than 80 Characters';
                }

                if (empty($name)) {
                    $formErrors[] = 'Location Name Cant Be Empty';
                }

                if (!empty($imageName) && !in_array($imageExtension, $imageAllowedExtension)) {
                    $formErrors[] = 'This Extension Is Not Allowed';
                }

                if ($imageSize > 4194304) {
                    $formErrors[] = 'Image Cant Be Larger Than 4MB';
                }

                foreach ($formErrors as $error) {
                    echo '<p class="alert alert-danger">' . $error . '</p>';
                }

                if (empty($formErrors)) {
                    if (!empty($imageName)) {
                        // delete the old image
                        $getOldImage = $con->prepare("SELECT LocationPicture FROM locations WHERE LocationID = :zid");
                        $getOldImage->execute(array('zid' => $id));
                        $oldImage = $getOldImage->fetch();
                        if(!unlink('./uploads/locations/' . $oldImage['LocationPicture'])) die('<p class="alert alert-danger">Couldnt delete the old image(s).</p>');
                        // add the new image
                        $ftp_conn = ftp_connect($ftp_server) or die('<p class="alert alert-danger">Could not connect to ' . $ftp_server . '</p>');
                        $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass) or die('<p class="alert alert-danger">Could not login to ftp server</p>');
                        $image = rand(0, 10000000000) . '_' . $imageName;
                        uploadFile($ftp_conn, $_FILES['image'], $image, '/htdocs/admin/uploads/locations/');
                        ftp_close($ftp_conn);

                        $stmt = $con->prepare("UPDATE locations SET LocationName = :zname, LocationPicture = :zpic WHERE LocationID = :zid");
                        $stmt->execute(array(
                            'zname' => $name,
                            'zid' => $id,
                            'zpic' => $image
                        ));
                    } else {
                        $stmt = $con->prepare("UPDATE locations SET LocationName = :zname WHERE LocationID = :zid");
                        $stmt->execute(array(
                            'zname' => $name,
                            'zid' => $id
                        ));
                    }

                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
                    redirectHome($theMsg, 'back');
                }
            } else {
                $theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';
                redirectHome($theMsg);
            }
        } elseif ($do == 'delete') {
            $locationid = isset($_GET['locationid']) && is_numeric($_GET['locationid']) ? intval($_GET['locationid']) : 0;
            $stmt = $con->prepare("SELECT * FROM locations WHERE LocationID = " . $locationid);
            $stmt->execute();
            $row = $stmt->fetch();
            if (count($row) > 0) {
                // delete old image from the 'uploads/locations/' folder
                if(!unlink('./uploads/locations/' . $row['LocationPicture'])) die('<p class="alert alert-danger">Couldnt delete the old image(s).</p>');

                $stmt = $con->prepare("DELETE FROM locations WHERE LocationID = " . $locationid);
                $stmt->execute();

                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Deleted</div>';
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
