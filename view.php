<?php

header("Content-Type: text/html; charset=UTF-8");
require_once('includes/master.inc.php');
require_once('GlobalHandler.php');
require_once('NoteRouter.php');



GlobalHandler::executeGlobalRequests();

$edit = false;

$note = NoteRouter::fetchViewNote();
$author = null;

$showPrivacyNotice = false;
$showAuthor = false;
$privacyNote = '';
$hasError = false;

if ($note === false)
{
    //Note does not exist
    $note = (object)array();
    $note->title = "Unknown Error";
    $note->text = "There was an error loading this note.";
    $note->nid = "Invalid";
    $note->id = "";
    $hasError = true;
}
else if (Auth::getAuth()->loggedIn() && $note->user_id == Auth::getAuth()->user->id)
{
    //We are the author.
    $showAuthor = true;

    $showPrivacyNotice = true;

    $privacyNote = "You are the author of this <b>public</b> note.";
    if ($note->privacy != Note::PRIVACY_PUBLIC)
    {
        $privacyNote = "You are the author of this <b>private</b> note.";
    }

}
else if ($note->privacy != Note::PRIVACY_PUBLIC)
{
    //We are not the author, and it is private.
    $hasError = true;
    $note->title = "Note not Viewable.";
    $note->text = "This note is no longer publicly viewable.";
    $note->nid = "Invalid";

}
else
{
    //We are not the author and the note is public
    $showAuthor = true;
}


if ($showAuthor)
{
    $author = new User();
    if ($author->select($note->user_id) === false)
    {
        $author = null;
    }
    else if ($author->oauth_provider == "dailyoje")
    {
        $default = root_url() . '/images/user-avatar.png';
        $author->image_url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($author->username))) . "?d=" . urlencode($default) . "&s=50";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $note->title; ?> - DailyOJE</title>
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
            OJE.attachListeners();
            OJE.rootUrl = "<?php echo root_url(); ?>";

            OJE.view('viewText', 'viewTitle', '<?php echo $note->nid; ?>');
            OJE.setupMenu();

            <?php if(Auth::getAuth()->loggedIn()): ?>

            OJE.loggedIn = true;
            OJE.Note.fetchNotes(1, false);
            <?php endif; ?>

            <?php if($hasError): ?>
            OJE.Note.toggleShareButton();
            <?php endif; ?>
        });
    </script>

</head>
<body>
<?php include('templates/GoogleAnalytics.php'); ?>
<?php include('templates/Header.php'); ?>

<div id="wrapper">

    <?php if ($showAuthor && isset($author)): ?>
        <div id="authorInformation">
            <?php if ($author->image_url): ?>
                <img src="<?php echo $author->image_url; ?>" border="0"/>
            <?php endif; ?>
            <span id="authorName">
            <a href="<?php echo $author->buildProfileUrl(); ?>" target="_blank">
                <?php echo $author->name; ?>
            </a>
        </span>
            <span class="grey-subtext" id="authorDescription"><?php echo $author->profile_details; ?></span>
        </div>
    <?php endif; ?>

    <h1 id="viewTitle" class="editable-title"><?php echo $note->title; ?></h1>

    <div class="editable " id="viewText">
        <?php echo ($note->text); ?>
    </div>
    <div id="loadingNoteIndicator" class="loading-note-box" style="display: none">
        <img src="<?php echo root_url(); ?>images/loading.horizontal.gif"/>
    </div>
</div>

<?php if ($showPrivacyNotice): ?>

    <div id="privateNotice">
        <?php echo $privacyNote; ?>
        <a href="<?php echo $note->editUrl(); ?>">Click
            here to edit your note.</a>
    </div>

<?php endif; ?>

<?php include("templates/Footer.php"); ?>
<?php include("templates/Sidebar.php"); ?>
</body>
</html>