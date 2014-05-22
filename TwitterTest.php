<?php
require_once('includes/master.inc.php');

$user = new User();
if ($user->selectOAuthUser('36161762', 'twitter'))
{
    print_r($user);
//Already exists, simply update credentials
    $user->updateOAuthTokens('blah', 'blah1');
    $user->save();

    Auth::getAuth()->login($twitterUser->id, 'twitter');

}