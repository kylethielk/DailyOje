<div id="errors" <?php if (Error::getError()->ok())
{
    echo 'style="display: none"';
} ?>>
    <?php if (!Error::getError()->ok()): ?>
        <?php echo Error::getError()->errorTimed(45000); ?>
    <?php endif; ?>
</div>

<div class="messages" id="messages" <?php if (!GlobalHandler::getGlobalHandler()->getMessage())
{
    echo 'style="display:none"';
}?>>

    <?php if(GlobalHandler::getGlobalHandler()->getMessage()): ?>
    <script
        type="text/javascript">
        OJE.showMessage('<?php echo GlobalHandler::getGlobalHandler()->getMessage(); ?>', 20000, true);
    </script>
    <?php endif; ?>

</div>

<?php if (!Auth::getAuth()->loggedIn()): ?>

    <div id="signinButtons">
        <a href="javascript:void(0);" onclick="OJE.Authenticate.toggleLoginDialog(true);">Login/Register</a>
    </div>

<?php endif; ?>