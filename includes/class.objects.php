<?PHP
// Stick your DBOjbect subclasses in here (to help keep things tidy).

class User extends DBObject
{
    public function __construct($id = null)
    {
        parent::__construct('users', array(
            'nid',
            'username',
            'name',
            'level',
            'oauth_provider',
            'oauth_uid',
            'oauth_token',
            'oauth_secret',
            'image_url',
            'profile_details',
            'password',
            'salt'), $id);
    }

    /**
     * Fetch user.
     * @param $uid
     * @param $oauthProvider
     * @return bool True if exists, false if empty
     */
    public function selectOAuthUser($uid, $oauthProvider)
    {
        return $this->selectWithMultipleColumns(array('oauth_provider' => $oauthProvider, 'oauth_uid' => $uid));
    }

    public function updateOAuthTokens($oauthToken, $oauthSecret)
    {
        if (!$this->ok())
        {
            return false;
        }
        $this->oauth_token = $oauthToken;
        $this->oauth_secret = $oauthSecret;
    }

    public function buildProfileUrl()
    {
        return "http://www.twitter.com/" . $this->username;
    }
}

class Note extends DBObject
{
    const PRIVACY_DEFAULT = 'private';

    const PRIVACY_PRIVATE = 'private';
    const PRIVACY_PUBLIC = 'public';


    public function __construct($id = null)
    {
        parent::__construct('notes', array(
            'nid',
            'first_save',
            'last_save',
            'text',
            'title',
            'user_id',
            'privacy'
        ), $id);
    }

    public function prettyDate()
    {
        return date("D M d, Y", $this->last_save);
    }

    public function editUrl()
    {
        return root_url() . "#" . $this->nid;
    }

    public function shareUrl()
    {
        if (Config::getConfig()->whereAmI() == 'local')
        {
            return root_url() . "view.php?noteId=" . $this->nid;
        }
        else
        {
            return root_url() . 'v/' . $this->nid;
        }
    }

}

class LoginHistory extends DBObject
{
    const STATUS_FAILURE = 'FAILURE';
    const STATUS_SUCCESS = 'SUCCESS';

    public function __construct($id = null)
    {
        parent::__construct('login_history', array(
            'username',
            'time',
            'ip',
            'status'
        ), $id);
    }

    public static function fetchForUser($username, $status = LoginHistory::STATUS_FAILURE, $timePeriod = 900)
    {
        $db = Database::getDatabase();

        if ($status != LoginHistory::STATUS_FAILURE && $status != LoginHistory::STATUS_SUCCESS)
        {
            return array();
        }


        $loginHistory = new LoginHistory();


        $periodStart = time() - $timePeriod;
        $usernameClean = $db->quote($username);

        $results = $loginHistory->fetch("SELECT * FROM `{$loginHistory->tableName}` WHERE username = " . $usernameClean . " and time > " . $periodStart . " AND status = " . $db->quote($status));
        return $results;
    }

    public static function saveLoginHistory($username, $status)
    {
        if ($status != LoginHistory::STATUS_FAILURE && $status != LoginHistory::STATUS_SUCCESS)
        {
            return;
        }

        $loginHistory = new LoginHistory();
        $loginHistory->time = time();
        $loginHistory->username = $username;
        $loginHistory->ip = $_SERVER['REMOTE_ADDR'];
        $loginHistory->status = $status;

        $loginHistory->save();
    }


}