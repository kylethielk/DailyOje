<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kyle
 * Date: 6/29/13
 * Time: 9:49 AM
 * To change this template use File | Settings | File Templates.
 */
require_once('LoggedInRouter.php');
class NoteRouter
{
    const AUTOSAVE = "Note_AutoSave";
    const FETCH_NOTES = "Note_FetchNotes";
    const UPDATE_PRIVACY = "Note_UpdatePrivacy";
    const LOAD_NOTE = "Note_LoadNote";
    const DELETE_NOTE = "Note_DeleteNote";

    static function autoSave()
    {
        $noteId = -1;
        if (!isset($_REQUEST['noteId']))
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_AUTOSAVE_NO_ID);
        }
        else
        {
            $noteId = $_REQUEST['noteId'];
        }

        $noteText = "";
        if (!isset($_REQUEST['noteText']))
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_AUTOSAVE_NO_TEXT);
        }
        else
        {
            $noteText = $_REQUEST['noteText'];
        }

        $noteTitle = "";
        if (!isset($_REQUEST['noteTitle']))
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_AUTOSAVE_NO_TITLE);
        }
        else
        {
            $noteTitle = $_REQUEST['noteTitle'];
        }

        //IF no id on note, ensure we are logged in
        $loggedIn = Auth::getAuth()->loggedIn();

        if ($loggedIn && empty($noteId))
        {
            //Create a new note
            $note = new Note();
            $note->nid = uniqid();
            $note->first_save = time();
            $note->last_save = time();
            $note->text = strip_tags($noteText, '<p><div>');
            $note->title = $noteTitle;
            $note->user_id = Auth::getAuth()->user->id;
            $note->privacy = Note::PRIVACY_DEFAULT;

            $note->save();

            WebRouter::writeValidResponse(NoteRouter::autoSaveResponse($note));

        }
        else if ($loggedIn && !empty($noteId))
        {
            $note = new Note();
            $note->select($noteId, "nid");

            $user_id = Auth::getAuth()->user->id;
            if ($note->nid == $noteId && $note->user_id == $user_id)
            {
                $note->last_save = time();
                $note->text = $noteText;
                $note->title = $noteTitle;

                $note->update();

                WebRouter::writeValidResponse(NoteRouter::autoSaveResponse($note));
            }
            else if ($user_id != $note->user_id)
            {
                WebRouter::writeInvalidResponse(Error::$NOTE_AUTOSAVE_INVALID_USER);
            }
            else
            {
                WebRouter::writeInvalidResponse(Error::$NOTE_AUTOSAVE_INVALID_NID);
            }
        }
        else
        {
            //Do nothing, not logged in, got here in error.
            WebRouter::writeValidResponse((object)array());
        }


    }

    static function autoSaveResponse($note)
    {
        $object = (object)array();
        $object->noteId = $note->nid;
        return $object;
    }

    static function fetchNotes()
    {
        if (!Auth::getAuth()->loggedIn())
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_FETCH_NOT_LOGGED_IN);
        }

        $pageNumber = 1;
        $resultsPerPage = 5;
        $ajaxRequest = true;
        $searchKey = "";

        if (isset($_REQUEST['pageNumber']))
        {
            $pageNumber = intval($_REQUEST['pageNumber']);
        }
        if (isset($_REQUEST['resultsPerPage']))
        {
            $resultsPerPage = intval($_REQUEST['resultsPerPage']);
        }
        if (isset($_REQUEST['ajaxRequest']))
        {
            $ajaxRequest = parse_boolean($_REQUEST['ajaxRequest']);
        }
        if (isset($_REQUEST['searchKey']))
        {
            $searchKey = $_REQUEST['searchKey'];
        }

        $start = ($pageNumber - 1) * $resultsPerPage;

        $note = new Note();

        $searchSql = '';
        if (!empty($searchKey) && strlen($searchKey) >= 2)
        {
            $searchSql = " AND title LIKE '%" . Database::getDatabase()->escape($searchKey) . "%' ";
        }


        //Clean data
        $start = intval($start);
        $resultsPerPage = intval($resultsPerPage);


        $notes = Note::fetch("SELECT * FROM `{$note->tableName}` WHERE user_id = '" . intval(Auth::getAuth()->user->id) . "' {$searchSql} ORDER BY first_save DESC LIMIT {$start},{$resultsPerPage}");
        ob_start();

        include("templates/Notes.php");

        $html = ob_get_clean();


        $totalResults = 0;
        if (!empty($searchSql))
        {
            $countSql = "SELECT COUNT(id) as cnt FROM `{$note->tableName}` WHERE user_id = '" . intval(Auth::getAuth()->user->id) . "' {$searchSql}";
            $totalResults = $note->countFromSql($countSql);
        }
        else
        {
            $totalResults = $note->count(array("user_id" => Auth::getAuth()->user->id));
        }


        WebRouter::writeValidResponse(NoteRouter::fetchNotesResponse($html, $totalResults));

    }

    static function fetchNotesResponse($html, $totalResults)
    {
        $object = (object)array();
        $object->html = $html;
        $object->totalResults = $totalResults;
        return $object;

    }

    static function updatePrivacy()
    {

        LoggedInRouter::validateLoggedIn();

        $noteId = "";
        if (!isset($_REQUEST['noteId']))
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_UPDATE_PRIVACY_NO_ID);
        }
        else
        {
            $noteId = $_REQUEST['noteId'];
        }

        $notePrivacy = "";
        if (!isset($_REQUEST['notePrivacy']))
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_UPDATE_PRIVACY_NO_VALUE);
        }
        else
        {
            $notePrivacy = $_REQUEST['notePrivacy'];
            if ($notePrivacy != Note::PRIVACY_PRIVATE && $notePrivacy != Note::PRIVACY_PUBLIC)
            {
                WebRouter::writeInvalidResponse(Error::$NOTE_UPDATE_PRIVACY_INVALID_VALUE);
            }
        }

        $note = new Note();
        $note->select($noteId, 'nid');

        if ($note->user_id == Auth::getAuth()->user->id)
        {
            $note->privacy = $notePrivacy;
            $note->update();

            WebRouter::writeValidResponse((object)array());
        }
        else
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_UPDATE_PRIVACY_INVALID_USER);
        }

    }

    static function loadNote()
    {
        LoggedInRouter::validateLoggedIn();

        $noteId = "";
        if (!isset($_REQUEST['noteId']))
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_LOAD_NOTE_NO_ID);
        }
        else
        {
            $noteId = $_REQUEST['noteId'];
        }

        $note = new Note();
        $note->select($noteId, 'nid');

        if ($note->user_id == Auth::getAuth()->user->id)
        {

            WebRouter::writeValidResponse(NoteRouter::loadNoteResponse($note));
        }
        else
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_LOAD_NOTE_INVALID_USER);
        }
    }

    static function loadNoteResponse($note)
    {
        $object = (object)array();
        $object->noteId = $note->nid;
        $object->noteText = $note->text;
        $object->noteTitle = $note->title;
        $object->notePrivacy = $note->privacy;

        return $object;

    }

    static function fetchViewNote()
    {
        $hasError = false;
        $noteId = "";
        if (!isset($_REQUEST['noteId']))
        {
            Error::getError()->add(Error::$NOTE_VIEW_NO_ID);
            return false;
        }
        else
        {
            $noteId = $_REQUEST['noteId'];
        }

        $note = new Note();
        $note->select($noteId, 'nid');

        if ($note->nid != $noteId)
        {
            Error::getError()->add(Error::$NOTE_VIEW_INVALID_ID);
            return false;
        }
        else
        {
            return $note;
        }
    }

    static function deleteNote()
    {
        LoggedInRouter::validateLoggedIn();

        $noteId = "";
        if (!isset($_REQUEST['noteId']))
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_DELETE_NOTE_NO_ID);
        }
        else
        {
            $noteId = $_REQUEST['noteId'];
        }

        $note = new Note();
        $note->select($noteId, 'nid');

        if ($note->user_id == Auth::getAuth()->user->id)
        {
            if ($note->delete())
            {
                WebRouter::writeValidResponse((object)array());
            }
            else
            {
                WebRouter::writeInvalidResponse(Error::$NOTE_DELETE_NOTE_DB);
            }
        }
        else
        {
            WebRouter::writeInvalidResponse(Error::$NOTE_DELETE_NOTE_INVALID_USER);
        }
    }

    static function exportNote()
    {
        require_once("includes/Html2Text.php");
        if (isset($_REQUEST['type']) && isset($_REQUEST['noteId']))
        {
            $noteId = $_REQUEST['noteId'];
            $type = $_REQUEST['type'];

            if ($type == "html" || $type == "text")
            {
                $note = new Note();
                $note->select($noteId, 'nid');

                if ($note->user_id == Auth::getAuth()->user->id)
                {
                    $mime = "";
                    $output = "";
                    $filename = preg_replace("/[^a-z0-9.]+/i", "", $note->title);

                    if ($type == "html")
                    {
                        $mime = "text/html";
                        $output = '<h1>' . $note->title . '</h1>' . $note->text;
                        $filename = $filename . ".html";
                    }
                    else
                    {
                        $mime = "text/plain";
                        $output = Html2Text::convert_html_to_text($note->title) . "\n" . Html2Text::convert_html_to_text($note->text);

                        $filename = $filename . ".txt";
                    }

                    header("Content-type: " . $mime);
                    header("Content-Disposition: attachment; filename=" . $filename);
                    header("Pragma: no-cache");
                    header("Expires: 0");

                    echo $output;
                }
                else
                {
                    header("HTTP/1.0 ...", true, 401);
                }

            }
            else
            {
                header("HTTP/1.0 ...", true, 400);
            }
        }
        else
        {
            header("HTTP/1.0 ...", true, 400);
        }


    }

}