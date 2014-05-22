<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kyle
 * Date: 6/30/13
 * Time: 2:04 PM
 * To change this template use File | Settings | File Templates.
 */

class LoggedInRouter
{

    static function validateLoggedIn()
    {
        if (!Auth::getAuth()->loggedIn())
        {
            WebRouter::writeInvalidResponse(Error::$GENERIC_NOT_LOGGED_IN);
        }
    }
}