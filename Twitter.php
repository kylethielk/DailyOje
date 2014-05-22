<?php
require_once('twitteroauth/twitteroauth.php');
require_once('includes/master.inc.php');

class Twitter
{
    const START_OAUTH = "Twitter_StartOAuth";
    const FINALIZE_OAUTH = "Twitter_FinalizeOAuth";

    static function startOAuth()
    {
        if (!isset($_REQUEST['currentUrl']))
        {
            WebRouter::writeInvalidResponse(Error::$TWITTER_CURRENT_URL_NOT_SET);
        }

        $twitterOAuth = new TwitterOAuth(Config::get('twitterConsumerKey'), Config::get('twitterConsumerSecret'));

        $redirect_url = Twitter::buildOAuthRedirectUrl($_REQUEST['currentUrl']);
        if (!Twitter::validateOAuthRedirectUrl($redirect_url))
        {
            WebRouter::writeInvalidResponse(Error::$TWITTER_INVALID_REDIRECT_URL);
        }

        // Requesting authentication tokens, the parameter is the URL we will be redirected to
        $request_token = $twitterOAuth->getRequestToken($redirect_url);

        // Saving them into the session
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];


        // If everything goes well..
        if ($twitterOAuth->http_code == 200)
        {
            // Let's generate the URL and redirect
            $url = $twitterOAuth->getAuthorizeURL($request_token['oauth_token']);
            WebRouter::writeValidResponse(Twitter::buildStartOAuthResponse($url));
        }
        else
        {
            WebRouter::writeInvalidResponse("There was an error authorizing you via Twitter, please try again.");
        }
    }

    /**
     * We have to add on a parameter so we are can login the user.
     */
    static function buildOAuthRedirectUrl($currentUrl)
    {
        //Remove anchor
        if (($anchorPos = strpos($currentUrl, '#')) !== false)
        {
            $currentUrl = substr($currentUrl, 0, $anchorPos);
        }

        $query = parse_url($currentUrl, PHP_URL_QUERY);

        // Returns a string if the URL has parameters or NULL if not
        if ($query)
        {
            $currentUrl = $currentUrl . '&requestType=' . Twitter::FINALIZE_OAUTH;
        }
        else
        {
            $currentUrl = $currentUrl . '?requestType=' . Twitter::FINALIZE_OAUTH;
        }

        return $currentUrl;
    }

    static function validateOAuthRedirectUrl($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        return strtolower($host) == strtolower($_SERVER['HTTP_HOST']);
    }

    static function buildStartOAuthResponse($authorizeUrl)
    {
        $object = (object)array();
        $object->authorizeUrl = $authorizeUrl;
        $object->currentUrl = Twitter::buildOAuthRedirectUrl($_REQUEST['currentUrl']);
        return $object;
    }

    static function finalizeOAuth()
    {

        if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret']))
        {
            // TwitterOAuth instance, with two new parameters we got in startOAuth
            $twitteroOAuth = new TwitterOAuth(Config::get('twitterConsumerKey'), Config::get('twitterConsumerSecret'), $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

            // Let's request the access token
            $accessToken = $twitteroOAuth->getAccessToken($_GET['oauth_verifier']);

            $errorMessage = Twitter::checkForTwitterErrors($accessToken);
            if ($errorMessage === false)
            {
                // Save it in a session var
                $_SESSION['access_token'] = $accessToken;

                // Let's get the user's info
                $twitterUser = $twitteroOAuth->get('account/verify_credentials');

                $errorMessage = Twitter::checkForTwitterErrors($twitterUser);
                if ($errorMessage === false)
                {
                    $user = new User();
                    if ($user->selectOAuthUser($twitterUser->id, 'twitter'))
                    {
                        //Already exists, simply update credentials and twitter details
                        $user->image_url = $twitterUser->profile_image_url;
                        $user->profile_details = $twitterUser->description;
                        $user->updateOAuthTokens($accessToken['oauth_token'], $accessToken['oauth_token_secret']);
                        $user->save();

                        Auth::getAuth()->login($twitterUser->id, 'twitter');

                    }
                    else
                    {
                        //Register User
                        $user = Auth::createNewUser(
                            $twitterUser->id,
                            $twitterUser->screen_name,
                            'twitter',
                            $accessToken['oauth_token'],
                            $accessToken['oauth_token_secret'],
                            $twitterUser->profile_image_url,
                            $twitterUser->name,
                            $twitterUser->description);

                        Auth::getAuth()->login($twitterUser->id, 'twitter');

                        mail("kylethielk@gmail.com", "New DailyOJE User - " . $twitterUser->screen_name,
                            "A new user has registered on DailyOJE. Their username is: " . $twitterUser->screen_name . ' and they are user #' . $user->id,
                            "From: contact@dailyoje.com");
                    }

                    Twitter::redirectAfterOAuthSuccess();

                }
                else
                {
                    Error::getError()->add(array("code" => "UNKNOWN", "message" => "Unknown error received from Twitter: " . $errorMessage));
                }

            }
            else
            {
                Error::getError()->add(array("code" => "UNKNOWN", "message" => "Unknown error received from Twitter: " . $errorMessage));
            }

        }
        else
        {
            // Something's missing, go back to square 1
            Error::getError()->add(Error::$TWITTER_FINALIZE_ERROR);
        }
    }

    /**
     * Checks response for errors and returns string if has error. false otherwise.
     * @param $response Object The response object received from Twitter that contains errors.
     * @return String the error messages.
     */
    static function checkForTwitterErrors($response)
    {
        if ($response && isset($response->errors))
        {
            $errors = $response->errors;
            $message = '';
            foreach ($errors as $error)
            {
                $message = $message . $error->message . '<br />';
            }
            return $message;
        }

        return false;

    }

    static function redirectAfterOAuthSuccess()
    {
        $url = full_url();
        $url = strip_url_parameter($url, "requestType");
        $url = strip_url_parameter($url, "oauth_verifier");
        $url = strip_url_parameter($url, "oauth_token");

        redirect($url);


    }
}