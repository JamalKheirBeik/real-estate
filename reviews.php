<?php
session_start();
$title = 'Reviews'; // setting the title must be before requiring the header
require('./admin/connect.php');
require('./admin/includes/functions.php');
require('./includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $message = strip_tags($_POST['message']);

    // chcek if user bought any props before
    $count = checkItem('UserID', 'props_sold', $id);
    if ($count > 0) {
        $stmt = $con->prepare('INSERT INTO reviews (UserID, Message) VALUES (:zid, :zmsg)');
        $stmt->execute(array(
            'zid' => $id,
            'zmsg' => $message
        ));
        echo '<p class="alert alert-success">Review submitted</p>';
    } else {
        echo '<p class="alert alert-danger">You must buy a property before submitting a review</p>';
    }
}

?>
<!-- page content goes here -->
<section class="reviews" id="reviews">
    <h2 class="custom-title">Submit a review</h2>
    <h1>Give us your honest opinion about your experience with us</h1>
    <?php
    if (isset($_SESSION['user'])) { ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" data-aos="fade-up">
            <input type="hidden" name="id" value="<?php echo $_SESSION['uid'] ?>">
            <label for="message">Your Message</label>
            <textarea name="message" id="message" cols="30" rows="10" required></textarea>
            <button type="submit">Submit</button>
        </form>
    <?php
    } else {
        echo '<p class="alert alert-danger">To submit a review you must be logged in and already bought a property</p>';
    }
    ?>
    <h2 class="custom-title">other people reviews</h2>
    <h1>What others said about us</h1>
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
