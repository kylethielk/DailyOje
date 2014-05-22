<?php

class AdminRouter
{

    static function fetchMostRecentUsers()
    {
        if (!Auth::getAuth()->isAdmin())
        {
            return array();
        }

        $user = new User();
        $users = User::fetch("SELECT * FROM `{$user->tableName}` ORDER BY id DESC LIMIT 50");
        return $users;

    }

    static function countAllUsers()
    {
        if (!Auth::getAuth()->isAdmin())
        {
            return array();
        }

        $user = new User();
        return $user->count();
    }

    static function noteCountForUser($user)
    {
        if (!Auth::getAuth()->isAdmin() || !isset($user))
        {
            return 0;
        }

        $note = new Note();
        $note->user_id = $user->id;
        return $note->count(array('user_id' => $user->id));
    }


}