<?php
session_start();
$title = 'Reviews'; // setting the title must be before requiring the header
if (isset($_SESSION['Username'])) {
    require('./connect.php'); // database connection
    require('./includes/functions.php');
    require('./includes/header.php');

    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';

    if ($do == 'manage') {
        $stmt = $con->prepare("SELECT * FROM reviews INNER JOIN users ON users.UserID = reviews.UserID ORDER BY reviews.UserID DESC LIMIT 5");
        $stmt->execute();
        $reviews = $stmt->fetchAll();
        if (!empty($reviews)) {
?>
            <section>
                <h1 class="custom-title">Reviews</h1>
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
                                    <a href="reviews.php?do=feature&reviewid=<?php echo $review['ReviewID'] ?>" class="btn btn-success"><i class="far fa-heart"></i> Feature</a>
                                <?php
                                } else { ?>
                                    <a href="reviews.php?do=unfeature&reviewid=<?php echo $review['ReviewID'] ?>" class="btn btn-success"><i class="fa fa-heart-broken"></i> Unfeature</a>
                                <?php
                                }
                                ?>
                                <a href="reviews.php?do=delete&reviewid=<?php echo $review['ReviewID'] ?>" class="btn btn-danger"><i class="fa fa-close"></i> Delete</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </section>
        <?php
        } else { ?>
            <p class="alert alert-info">there is no records</p>
<?php
        }
    } elseif ($do == 'delete') {
        $reviewid = isset($_GET['reviewid']) && is_numeric($_GET['reviewid']) ? intval($_GET['reviewid']) : 0;
        $check = checkItem('ReviewID', 'reviews', $reviewid);
        if ($check > 0) {
            $stmt = $con->prepare("DELETE FROM reviews WHERE ReviewID = " . $reviewid);
            $stmt->execute();
            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Deleted</div>';
            redirectHome($theMsg, 'back');
        } else {
            $theMsg = '<div class="alert alert-danger">This ID is Not Exist</div>';
            redirectHome($theMsg);
        }
    } elseif ($do == 'feature') {
        $reviewid = isset($_GET['reviewid']) && is_numeric($_GET['reviewid']) ? intval($_GET['reviewid']) : 0;
        $check = checkItem('ReviewID', 'reviews', $reviewid);
        if ($check > 0) {
            $stmt = $con->prepare("UPDATE reviews SET Featured = 1 WHERE ReviewID = " . $reviewid);
            $stmt->execute();
            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
            redirectHome($theMsg, 'back');
        } else {
            $theMsg = '<div class="alert alert-danger">This ID is Not Exist</div>';
            redirectHome($theMsg);
        }
    } elseif ($do == 'unfeature') {
        $reviewid = isset($_GET['reviewid']) && is_numeric($_GET['reviewid']) ? intval($_GET['reviewid']) : 0;
        $check = checkItem('ReviewID', 'reviews', $reviewid);
        if ($check > 0) {
            $stmt = $con->prepare("UPDATE reviews SET Featured = 0 WHERE ReviewID = " . $reviewid);
            $stmt->execute();
            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
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
