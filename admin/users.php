<?php
session_start();
$title = 'Users'; // setting the title must be before requiring the header
if (isset($_SESSION['Username'])) {
    require('./connect.php'); // database connection
    require('./includes/functions.php');
    require('./includes/header.php');


    $users = getAllFrom('*', 'users', NULL, NULL, 'UserID', 'ASC');
    if (!empty($users)) {
?>
        <section>
            <h1 class="custom-title">Users</h1>
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
        </section>
    <?php
    } else { ?>
        <p class="alert alert-info">There is no records</p>
<?php   }
} else {
    header("Location: index.php");
    exit();
}
require('./includes/footer.php');
