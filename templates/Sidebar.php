<div id="menu">
    <div id="menuIcon" title="Click to stick/unstick menu."><span id="daily">Daily</span><span id="oje">OJE</span></div>
    <div id="menuNavigation" style="display:none">
        <a href="<?php echo WEB_ROOT; ?>">+ New Note</a>
    </div>
    <div id="menuContent" style="display: none">

        <?php if (!Auth::getAuth()->loggedIn()): ?>
            <div id="signinText">
                <p>Register/Login to start saving, editing and publishing notes.</p>
                <a href="javascript:void(0);" onclick="OJE.Authenticate.toggleLoginDialog(true);" id="sidebarLoginBtn">
                    Login/Register
                </a>
            </div>
        <?php endif; ?>
        <?php if (Auth::getAuth()->loggedIn()) : ?>

            <input type="text" id="noteSearch" name="noteSearch" placeholder="Search Notes"/>
            <div id="noteResults">

            </div>
            <img src="<?php echo root_url(); ?>images/loading.horizontal.gif" class="loading-image" id="noteFetchLoading"/>
            <div id="notePaginationWrapper">
                <div id="notePagination"></div>
            </div>

        <?php endif; ?>


        <div id="menuSubLinks">
            <div>
                <a href="http://www.dailyoje.com/v/Privacy">Privacy</a> | <a href="http://www.dailyoje.com/v/Terms">Terms</a> | <a
                    href="https://www.twitter.com/dailyoje" target="_blank">Contact</a>
                <?php if (Auth::getAuth()->loggedIn()): ?>
                    | <a href="<?php echo WEB_ROOT; ?>?requestType=logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>