<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kyle
 * Date: 6/28/13
 * Time: 6:41 PM
 * To change this template use File | Settings | File Templates.
 */
require_once("Twitter.php");
require_once("NoteRouter.php");
class GlobalHandler
{
    const MESSAGE_DELETE_NOTE = "Note_DeleteSuccess";
    const LOGOUT = "logout";

    // Singleton object. Leave $me alone.
    private static $me;
    private $message = '';

    static function getGlobalHandler()
    {
        if (is_null(self::$me))
        {
            self::$me = new GlobalHandler();
        }
        return self::$me;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    static function executeGlobalRequests()
    {
        $requestType = isset($_REQUEST['requestType']) ? $_REQUEST['requestType'] : "";

        if ($requestType == Twitter::FINALIZE_OAUTH)
        {
            Twitter::finalizeOAuth();
        }
        else if ($requestType == GlobalHandler::LOGOUT)
        {
            Auth::getAuth()->logout();
        }

        $message = isset($_REQUEST['message']) ? $_REQUEST['message'] : "";
        if ($message == GlobalHandler::MESSAGE_DELETE_NOTE)
        {
            GlobalHandler::getGlobalHandler()->setMessage("Successfully Deleted Your Note.");
        }

    }

}