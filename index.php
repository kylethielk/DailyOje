<?php
require_once('includes/master.inc.php');
require_once('GlobalHandler.php');

GlobalHandler::executeGlobalRequests();

$edit = true;
?>
<!DOCTYPE html>
<html>
<head>
    <title>DailyOje - Distraction Free Writing</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Config::get('googleFontUrl'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo root_url(); ?>css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo root_url(); ?>css/simplePagination.css">
    <link rel="icon" type="image/png" href="<?php echo root_url(); ?>images/icon.png">
    <script src="<?php echo Config::get('jQueryUrl'); ?>"></script>
    <script src="<?php echo root_url(); ?>thirdjs/jquery.simplePagination.js"></script>
    <script src="<?php echo Config::get('dailyOjeJsUrl'); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function()
        {

            OJE.checkForMobile();
            OJE.attachListeners();


            $("#initialEditable").attr("contentEditable", "true");

            OJE.rootUrl = "<?php echo root_url(); ?>";
            OJE.edit = true;

            <?php if(!Auth::getAuth()->loggedIn()): ?>

            $("#initialEditable").focus();
            OJE.loggedIn = false;

            <?php endif; ?>

            <?php if(Auth::getAuth()->loggedIn()): ?>

            $("#initialTitle").focus();
            $("#initialTitle").addClass("editable-title-initial");


            OJE.loggedIn = true;
            OJE.Note.fetchNotes(1, true);

            <?php endif; ?>

            OJE.initialize("initialEditable", "initialTitle", $("#emptyText").html(), $("#emptyTitleText").html());
            OJE.setupMenu();

        });
    </script>
</head>
<body>
<?php include('templates/GoogleAnalytics.php'); ?>
<div class="force-viewport-margin">

    <?php include('templates/Header.php'); ?>

    <div id="wrapper">
        <h1 contenteditable="true" id="initialTitle" class="editable-title"></h1>

        <div class="editable editable-initial" id="initialEditable">
        </div>
        <div id="loadingNoteIndicator" class="loading-note-box" style="display: none">
            <img src="<?php echo root_url(); ?>images/loading.horizontal.gif"/>
        </div>
    </div>
</div>
<?php include ("templates/Footer.php"); ?>
<?php include("templates/Sidebar.php"); ?>
<?php include("templates/DefaultText.php"); ?>
</body>
</html>