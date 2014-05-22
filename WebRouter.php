<?php
/**
 * DO NOT INCLUDE OR REQUIRE THIS FILE, it should be loaded by AJAX calls.
 * Class WebRouter
 */
class WebRouter
{
    static function route($request)
    {
        $requestType = isset($_REQUEST['requestType']) ? $_REQUEST['requestType'] : "";

        WebRouter::buildRequires($requestType);

        if ($requestType == Twitter::START_OAUTH)
        {
            Twitter::startOAuth();
        }
        else if ($requestType == NoteRouter::AUTOSAVE)
        {
            NoteRouter::autoSave();
        }
        else if ($requestType == NoteRouter::FETCH_NOTES)
        {
            NoteRouter::fetchNotes();
        }
        else if ($requestType == NoteRouter::UPDATE_PRIVACY)
        {
            NoteRouter::updatePrivacy();
        }
        else if ($requestType == NoteRouter::LOAD_NOTE)
        {
            NoteRouter::loadNote();
        }
        else if ($requestType == NoteRouter::DELETE_NOTE)
        {
            NoteRouter::deleteNote();
        }
        else if ($requestType == AuthenticateRouter::REGISTER)
        {
            AuthenticateRouter::register();
        }
        else if ($requestType == AuthenticateRouter::LOGIN)
        {
            AuthenticateRouter::login();
        }
    }

    static function buildRequires($requestType)
    {
        require_once("Twitter.php");
        require_once("NoteRouter.php");
        require_once("AuthenticateRouter.php");
    }

    static function writeValidResponse($data)
    {
        $object = (object)array();
        $object->hasError = false;
        $object->errorMessage = "";
        $object->errorCode = -1;
        $object->data = $data;

        echo json_encode($object);
        exit(3);
    }

    static function writeInvalidResponse($errorObject)
    {
        $object = (object)array();
        $object->hasError = true;
        $object->errorMessage = $errorObject['message'];

        $object->errorCode = $errorObject['code'];

        echo json_encode($object);
        exit(3);
    }
}

WebRouter::route($_REQUEST);