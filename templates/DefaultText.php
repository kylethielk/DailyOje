<?php if (Auth::getAuth()->loggedIn()): ?>
    <script type="text/dailyoje" id="emptyTitleText">Type Your Title<span id="placeholderEmptyTitleTextId"></span>
    </script>
    <script type="text/dailyoje" id="emptyText">
        <p spellcheck="false">Your thoughts...<span id="placeholderEmptyTextId"></span></p>
    </script>
<?php endif; ?>
<?php if (!Auth::getAuth()->loggedIn()): ?>
    <script type="text/dailyoje" id="emptyTitleText">DailyOJE - Distraction-Free Writing<span
            id="placeholderEmptyTitleTextId"></span></script>
    <script type="text/dailyoje" id="emptyText"><div style="color: #4E4E4E">
        <p spellcheck="false">I have never been a strong writer and never really
            enjoyed it. Writing was at best a chore, a necessary but annoying part of everyday life. Then I decided to
            try writing for myself. Free from the pressure of an audience writing became enjoyable, something I looked
            forward to everyday.</p>

        <p spellcheck="false">Writing daily has brought me better focus and a clearer mind. It reminds me of the things
            I've done well and those that I need to work on. It helps me concentrate on the things that really matter
            and reminds me to avoid the things that don't.</p>

        <p spellcheck="false">I created DailyOJE because I wanted a distraction free way to write daily. I wanted a tool
            that stayed out of my way. Just the screen and my thoughts.</p>

        <p spellcheck="false">If you just want a quick way to jot down your thoughts, simply start typing. Nothing is
            recorded, once you leave the page everything is deleted. Login and we will save all of your
            notes for you. Notes are private by default but can be made public.</p>

        <p spellcheck="false">I hope that DailyOJE can be of some benefit to you. Happy Writing!</p>

        <p spellcheck="false">Kyle</p><span id="placeholderEmptyTextId"></span></div></script>
<?php endif; ?>