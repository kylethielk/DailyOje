<?PHP
class Auth
{
    const SALT = '3jfi29sdkmwoiwef';

    private static $me;

    public $id;
    public $username;
    public $user;
    public $expiryDate;
    public $loginUrl = ''; // Where to direct users to login

    private $nid;
    private $loggedIn;

    public function __construct()
    {
        $this->id = null;
        $this->nid = null;
        $this->username = null;
        $this->user = null;
        $this->loggedIn = false;
        $this->expiryDate = mktime(0, 0, 0, 6, 2, 2037);
        $this->user = new User();
    }

    public static function getAuth()
    {
        if (is_null(self::$me))
        {
            self::$me = new Auth();
            self::$me->init();
        }
        return self::$me;
    }

    public function init()
    {
        $this->setACookie();
        $this->loggedIn = $this->attemptCookieLogin();
    }

    public function loginNonOauth($username, $nonHashedPassword)
    {
        $this->loggedIn = false;

        if (empty($nonHashedPassword) || strlen($nonHashedPassword) < 6)
        {
            return false;
        }


        $user = new User();
        if ($user->select($username, "username"))
        {
            $hashedPassword = Auth::getAuth()->hashedPassword($nonHashedPassword, $user->salt);

            if ($user->password == $hashedPassword)
            {
                $this->id = $user->id;
                $this->nid = $user->nid;
                $this->username = $user->username;

                $this->generateBCCookies();

                $this->loggedIn = true;

            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }


        return true;
    }

    public function login($uid, $oauth_provider)
    {
        $this->loggedIn = false;

        $db = Database::getDatabase();
        $row = $db->getRow("SELECT * FROM users WHERE oauth_uid = " . $db->quote($uid) . " AND oauth_provider = " . $db->quote($oauth_provider));

        if ($row === false)
        {
            return false;
        }

        $this->id = $row['id'];
        $this->nid = $row['nid'];
        $this->username = $row['username'];
        $this->user = new User();
        $this->user->id = $this->id;
        $this->user->load($row);

        $this->generateBCCookies();

        $this->loggedIn = true;

        return true;
    }

    public function logout()
    {
        $this->loggedIn = false;
        $this->clearCookies();
        $this->sendToLoginPage();
    }

    public function loggedIn()
    {
        return $this->loggedIn;
    }

    public function requireUser()
    {
        if (!$this->loggedIn())
        {
            $this->sendToLoginPage();
        }
    }

    public function requireAdmin()
    {
        if (!$this->loggedIn() || !$this->isAdmin())
        {
            $this->sendToLoginPage();
        }
    }

    public function isAdmin()
    {
        return ($this->user->username === 'kylethielk');
    }

//        public function changeCurrentUsername($new_username)
//        {
//            $db = Database::getDatabase();
//            srand(time());
//            $this->user->nid = Auth::newNid();
//            $this->nid = $this->user->nid;
//            $this->user->username = $new_username;
//            $this->username = $this->user->username;
//            $this->user->update();
//            $this->generateBCCookies();
//        }

//        public function changeCurrentPassword($new_password)
//        {
//            $db = Database::getDatabase();
//            srand(time());
//            $this->user->nid = self::newNid();
//            $this->user->password = self::hashedPassword($new_password);
//            $this->user->update();
//            $this->nid = $this->user->nid;
//            $this->generateBCCookies();
//        }

//        public static function changeUsername($id_or_username, $new_username)
//        {
//            if(ctype_digit($id_or_username))
//                $u = new User($id_or_username);
//            else
//            {
//                $u = new User();
//                $u->select($id_or_username, 'username');
//            }
//
//            if($u->ok())
//            {
//                $u->username = $new_username;
//                $u->update();
//            }
//        }

//        public static function changePassword($id_or_username, $new_password)
//        {
//            if(ctype_digit($id_or_username))
//                $u = new User($id_or_username);
//            else
//            {
//                $u = new User();
//                $u->select($id_or_username, 'username');
//            }
//
//            if($u->ok())
//            {
//                $u->nid = self::newNid();
//                $u->password = self::hashedPassword($new_password);
//                $u->update();
//            }
//        }

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public static function createNewNonOAuthUser($email, $password)
    {
        $db = Database::getDatabase();

        //Make sure requireds are set.
        if (!isset($email) || !isset($password))
        {
            return false;
        }

        $user_exists = $db->getValue("SELECT COUNT(*) FROM users WHERE username = " . $db->quote($email) . " AND oauth_provider = " . $db->quote("dailyoje"));
        if ($user_exists > 0)
        {
            return false;
        }

        $user = new User();
        $user->username = $email;
        $user->name = "";
        $user->nid = Auth::newNid();
        $user->oauth_provider = "dailyoje";
        $user->image_url = "";
        $user->profile_details = "";
        $user->salt = Auth::getAuth()->generateStrongPassword(12);
        $user->password = Auth::getAuth()->hashedPassword($password, $user->salt);

        $user->insert();

        return $user;

    }

    public static function createNewUser($uid, $username, $oauth_provider, $oauth_token, $oauth_secret, $image_url = "", $name = "", $profile_details = "")
    {

        $db = Database::getDatabase();

        //Make sure required are set.
        if (!isset($uid) || !isset($username) || !isset($oauth_provider) || !isset($oauth_token) || !isset($oauth_secret))
        {
            return false;
        }

        $user_exists = $db->getValue("SELECT COUNT(*) FROM users WHERE oauth_uid = " . $db->quote($uid) . " AND oauth_provider = " . $db->quote($oauth_provider));
        if ($user_exists > 0)
        {
            return false;
        }


        $user = new User();
        $user->username = $username;
        $user->name = $name;
        $user->nid = Auth::newNid();
        $user->oauth_uid = $uid;
        $user->oauth_provider = $oauth_provider;
        $user->oauth_token = $oauth_token;
        $user->oauth_secret = $oauth_secret;
        $user->image_url = $image_url;
        $user->profile_details = $profile_details;

        $user->insert();

        return $user;
    }

    public static function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if (strpos($available_sets, 'l') !== false)
        {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }
        if (strpos($available_sets, 'u') !== false)
        {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }
        if (strpos($available_sets, 'd') !== false)
        {
            $sets[] = '23456789';
        }
        if (strpos($available_sets, 's') !== false)
        {
            $sets[] = '!@#$%&*?';
        }

        $all = '';
        $password = '';
        foreach ($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);

        if (!$add_dashes)
        {
            return $password;
        }

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    public function impersonateUser($id_or_username)
    {
        if (ctype_digit($id_or_username))
        {
            $u = new User($id_or_username);
        }
        else
        {
            $u = new User();
            $u->select($id_or_username, 'username');
        }

        if (!$u->ok())
        {
            return false;
        }

        $this->id = $u->id;
        $this->nid = $u->nid;
        $this->username = $u->username;
        $this->user = $u;
        $this->generateBCCookies();

        return true;
    }

    private function attemptCookieLogin()
    {
        if (!isset($_COOKIE['A']) || !isset($_COOKIE['B']) || !isset($_COOKIE['C']))
        {
            return false;
        }

        $ccookie = base64_decode(str_rot13($_COOKIE['C']));
        if ($ccookie === false)
        {
            return false;
        }

        $c = array();
        parse_str($ccookie, $c);
        if (!isset($c['n']) || !isset($c['l']))
        {
            return false;
        }

        $bcookie = base64_decode(str_rot13($_COOKIE['B']));
        if ($bcookie === false)
        {
            return false;
        }

        $b = array();
        parse_str($bcookie, $b);
        if (!isset($b['s']) || !isset($b['x']))
        {
            return false;
        }

        if ($b['x'] < time())
        {
            return false;
        }

        $computed_sig = md5(str_rot13(base64_encode($ccookie)) . $b['x'] . self::SALT);
        if ($computed_sig != $b['s'])
        {
            return false;
        }

        $nid = base64_decode($c['n']);
        if ($nid === false)
        {
            return false;
        }

        $db = Database::getDatabase();

        // We SELECT * so we can load the full user record into the user DBObject later
        $row = $db->getRow('SELECT * FROM users WHERE nid = ' . $db->quote($nid));
        if ($row === false)
        {
            return false;
        }

        $this->id = $row['id'];
        $this->nid = $row['nid'];
        $this->username = $row['username'];
        $this->user = new User();
        $this->user->id = $this->id;
        $this->user->load($row);

        return true;
    }

    private function setACookie()
    {
        if (!isset($_COOKIE['A']))
        {
            srand(time());
            $a = md5(rand() . microtime());
            setcookie('A', $a, $this->expiryDate, '/', Config::get('authDomain'));
        }
    }

    private function generateBCCookies()
    {
        $c = '';
        $c .= 'n=' . base64_encode($this->nid) . '&';
        $c .= 'l=' . str_rot13($this->username) . '&';
        $c = base64_encode($c);
        $c = str_rot13($c);

        $sig = md5($c . $this->expiryDate . self::SALT);
        $b = "x={$this->expiryDate}&s=$sig";
        $b = base64_encode($b);
        $b = str_rot13($b);

        setcookie('B', $b, $this->expiryDate, '/', Config::get('authDomain'));
        setcookie('C', $c, $this->expiryDate, '/', Config::get('authDomain'));
    }

    private function clearCookies()
    {
        setcookie('B', '', time() - 3600, '/', Config::get('authDomain'));
        setcookie('C', '', time() - 3600, '/', Config::get('authDomain'));
    }

    private function sendToLoginPage()
    {
        $url = WEB_ROOT;

        $full_url = full_url();
        if (strpos($full_url, 'logout') === false)
        {
            $url .= '?r=' . $full_url;
        }

        redirect($url);
    }

    private static function hashedPassword($password, $userSalt)
    {
        return md5($userSalt . $password . self::SALT);
    }

//
    private static function newNid()
    {
        srand(time());
        return md5(rand() . microtime());
    }
}
