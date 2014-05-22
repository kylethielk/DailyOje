<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kyle
 * Date: 6/29/13
 * Time: 9:49 AM
 * To change this template use File | Settings | File Templates.
 */
require_once('LoggedInRouter.php');
class AuthenticateRouter
{
    const REGISTER = "Authenticate_Register";
    const LOGIN = "Authenticate_Login";

    static function register()
    {
        $email = "";
        if (!isset($_REQUEST['email']))
        {
            WebRouter::writeValidResponse(AuthenticateRouter::registerResponse(true));
        }
        else
        {
            $email = $_REQUEST['email'];
            if (!valid_email($email))
            {
                WebRouter::writeValidResponse(AuthenticateRouter::registerResponse(true));
            }
        }

        $password = "";
        if (!isset($_REQUEST['password']))
        {
            WebRouter::writeValidResponse(AuthenticateRouter::registerResponse(false, false, true));
        }
        else
        {
            $password = $_REQUEST['password'];
            if (strlen($password) < 6)
            {
                WebRouter::writeValidResponse(AuthenticateRouter::registerResponse(false, false, true));
            }
        }

        $loggedIn = Auth::getAuth()->loggedIn();

        if ($loggedIn)
        {
            WebRouter::writeValidResponse(AuthenticateRouter::registerResponse(false, false, false, true));
        }

        $user = Auth::getAuth()->createNewNonOAuthUser($email, $password);
        if ($user === false)
        {
            WebRouter::writeValidResponse(AuthenticateRouter::registerResponse(false, true, false, false));
        }
        else
        {
            Auth::getAuth()->loginNonOauth($email, $password);
            mail("kylethielk@gmail.com", "New DailyOJE User - " . $email,
                "A new user has registered on DailyOJE. Their username is: " . $email . ' and they are user #' . $user->id,
                "From: contact@dailyoje.com");
            WebRouter::writeValidResponse(AuthenticateRouter::registerResponse(false, false, false, true));
        }


    }

    static function registerResponse($invalidEmail = false, $emailExists = false, $passwordTooShort = false, $success = false)
    {
        $object = (object)array();
        $object->invalidEmail = $invalidEmail;
        $object->emailExists = $emailExists;
        $object->passwordTooShort = $passwordTooShort;
        $object->success = $success;
        return $object;
    }

    static function login()
    {
        $email = "";
        if (!isset($_REQUEST['email']))
        {
            WebRouter::writeValidResponse(AuthenticateRouter::loginResponse(false));
        }

        $email = $_REQUEST['email'];

        $password = "";
        if (!isset($_REQUEST['password']))
        {
            WebRouter::writeValidResponse(AuthenticateRouter::loginResponse(false));
        }

        //Make sure to slow down for multiple attempts
        $histories = LoginHistory::fetchForUser($email, LoginHistory::STATUS_FAILURE, 300);
        $failureAttempts = $histories ? count($histories) : 0;
        if ($failureAttempts == 3)
        {
            sleep(1);
        }
        else if ($failureAttempts == 4)
        {
            sleep(3);
        }
        else if ($failureAttempts > 4)
        {
            sleep(5);
        }

        $password = $_REQUEST['password'];

        $loggedIn = Auth::getAuth()->loggedIn();

        if ($loggedIn)
        {
            WebRouter::writeValidResponse(AuthenticateRouter::loginResponse(true));
        }

        if (!Auth::getAuth()->loginNonOauth($email, $password))
        {
            LoginHistory::saveLoginHistory($email, LoginHistory::STATUS_FAILURE);
            WebRouter::writeValidResponse(AuthenticateRouter::loginResponse(false));
        }
        else
        {
            LoginHistory::saveLoginHistory($email, LoginHistory::STATUS_SUCCESS);
            WebRouter::writeValidResponse(AuthenticateRouter::loginResponse(true));
        }

    }

    static function loginResponse($validCredentials = false)
    {
        $object = (object)array();
        $object->validCredentials = $validCredentials;
        return $object;
    }

}