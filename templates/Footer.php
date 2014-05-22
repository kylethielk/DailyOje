<div id="footer">
    <span class="footer-padding">

         <?php if (Auth::getAuth()->loggedIn() && $edit): ?>
            <a href="javascript:void(0);" onclick="OJE.Note.toggleDeleteWarning(true);" id="deleteLink"
               class="delete-link right-aligned" style="display: none">(Delete)</a>
            <a href="javascript:void(0);" onclick="OJE.Note.toggleExport(true);" id="exportLink"
               class="export-link right-aligned" style="display: none">(Export)</a>
        <?php endif; ?>

        <span id="shareButton" class="right-aligned" <?php if ($edit)
        {
            echo 'style="display: none"';
        } ?>>
            <a href="javascript:void(0);" onclick="OJE.Note.showShare();" class="share-button">+ Share</a>
        </span>

        <?php if (Auth::getAuth()->loggedIn() && $edit): ?>
            <span id="privacySettings" class="right-aligned">Visibility: <select id="privacySettingsSelect"
                                                                                 name="privacySettingsSelect"
                                                                                 onchange="OJE.Note.updatePrivacy();">
                    <option value="private" selected>Private</option>
                    <option value="public">Public</option>
                </select>
                <img src="<?php echo root_url(); ?>images/loading.small.gif" style="display: none" id="privacyLoader"/>
                <img src="<?php echo root_url(); ?>images/checkmark.png" style="display: none" id="privacySuccess"/>
            </span>
        <?php endif; ?>

        <span class="right-aligned" id="fontSizeContainer">
            Font Size:
            <select name="fontSize" id="fontSize" onchange="OJE.refreshSettings();">
                <option value="16px">16px</option>
                <option value="18px">18px</option>
                <option value="20px" selected>20px</option>
                <option value="22px">22px</option>
                <option value="24px">24px</option>
            </select>
        </span>



        <?php if ($edit): ?>
            <span class="save-status save-status-unsaved left-aligned"
                  id="saveStatus" <?php if (!Auth::getAuth()->loggedIn())
            {
                echo 'style="display:none"';
            } ?>>UnSaved</span>


            <span id="counts" class="right-aligned">Words: <span id="wordCount">0</span>,
            Characters: <span id="characterCount">0</span>
        </span>

        <?php endif; ?>



        <div id="shareBox" class="share-box" style="display: none">
            <p>Copy/Paste URL to share.
                <?php if ($edit): ?>
                    If you make this note private, this link will no longer be valid.
                <?php endif; ?>
            </p>
            <input type="text" id="shareUrl" name="shareUrl"> <input type="button" value="Ok"
                                                                     onclick="OJE.Note.hideShare();"/>
        </div>

         <div id="deleteBox" class="delete-box" style="display: none">
             <p>Are you sure you want to delete this note? It cannot be undone.
             </p>
             <input type="button" value="Yes, Delete Note" onclick="OJE.Note.deleteCurrentNote();"
                    class="left-aligned"/>
             <input type="button" value="No, Don't Delete" onclick="OJE.Note.toggleDeleteWarning(false);"
                    class="right-aligned"/>
         </div>





        </span>
</div>
<div id="exportDialog" style="display:none">
    <div id="exportWindow">
        <a href="javascript:void(0);" id="exportCloseLink" onclick="OJE.Note.toggleExport(false);">Close</a>

        <p>You can export as plaintext or html.</p>
        <a href="javascript:void(0);" class="left-aligned grey-button" onclick="OJE.Note.exportText();">Export as
            Text</a>
        <a href="javascript:void(0);" class="right-aligned grey-button" onclick="OJE.Note.exportHtml();">Export As
            HTML</a>
    </div>
</div>
<div id="loginDialog" style="display: none">
    <div id="loginWindow">
        <a href="javascript:void(0);" id="loginCloseLink" onclick="OJE.Authenticate.toggleLoginDialog(false);">Close</a>

        <div id="loginContent">
            <div id="loginRibbon">Login</div>
            <div id="loginFormContainer">
                <p id="loginError"></p>

                <form id="loginForm">
                    <input type="text" id="loginEmail" name="loginEmail" placeholder="Email"/>
                    <input type="password" id="loginPassword" name="loginPassword" placeholder="Password"/>
                    <input type="submit" id="loginProcessBtn" value="Login" />
                    <a href="javascript:void(0);" onclick="OJE.Twitter.startAuthenticate();" id="twitterProcessBtn">
                        <img src="<?php echo root_url(); ?>images/twitter-icon.png"/> <span>Signin With Twitter</span>
                    </a>
                </form>
                <span id="loginOrLabel">-or-</span>
            </div>
            <div id="registerFormContainer" style="display:none">
                <p id="registerError"></p>

                <form id="registerForm">
                    <input type="text" id="registerEmail" name="registerEmail" placeholder="Email"/>
                    <input type="password" id="registerPassword" name="registerPassword"
                           placeholder="Password (min 6 characters)"/>
                    <input type="submit" id="registerProcessBtn" value="Register" />
                </form>
                <span id="backToLoginText"><a href="javascript:void(0);"
                                              onclick="OJE.Authenticate.toggleRegisterForm(false);">Back to
                        Login</a></span>
            </div>
            <p id="registerText">...or <a href="javascript:void(0);"
                                          onclick="OJE.Authenticate.toggleRegisterForm(true);">Register
                    A New Account.</a></p>
        </div>

        <img src="<?php echo root_url(); ?>images/loading.horizontal.gif" style="display:none"
             id="loginFormLoadingIndicator"/>

    </div>
</div>