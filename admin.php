<?php

require_once('includes/master.inc.php');
require_once('AdminRouter.php');

Auth::getAuth()->requireAdmin();


?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - DailyOje</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Config::get('googleFontUrl'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo root_url(); ?>css/style.css">
    <link rel="icon" type="image/png" href="<?php echo root_url(); ?>images/icon.png">
    <script src="<?php echo Config::get('jQueryUrl'); ?>"></script>


</head>
<body>


<div id="wrapper">

    <p>There are a total of <strong><?php echo AdminRouter::countAllUsers(); ?></strong> users in the system.</p>

    <p>Users listed by registration date in descending order:</p>

    <?php
    $users = AdminRouter::fetchMostRecentUsers();
    foreach ($users as $user) :

        if(empty($user->image_url))
        {
            $default = root_url() . '/images/user-avatar.png';
            $user->image_url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($user->username))) . "?d=" . urlencode($default) . "&s=50";
        }

        $username = $user->name;
        if($user->oauth_provider == "dailyoje")
        {
            $username = $user->username;
        }
        ?>

        <div class="author-table">
            <img src="<?php echo $user->image_url; ?>" border="0"/>
            <span class="author-name">
            <a href="<?php echo $user->buildProfileUrl(); ?>" target="_blank">
                <?php echo $username; ?>
            </a>
        </span>
            <span class="grey-subtext author-description"><?php echo $user->profile_details; ?> -> Total Notes: <strong><?php echo AdminRouter::noteCountForUser($user); ?></strong></span>
        </div>

    <?php endforeach; ?>

</div>

</body>
</html>
