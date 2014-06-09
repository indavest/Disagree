<?php

class Base extends DA_Controller
{

    private $data = array();

    public function __construct()
    {
        parent::__construct();

        /*$this->data['cssList'] = array("static");*/
        $this->data['jsList'] = array();
        if (!$this->isLoggedIn()) {
            $this->data['loggedInUserMemberFlag'] = false;
            $this->data['jsList'] = array("logoff");
        } else {
            $this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));
            $this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn');
        }
        $this->data['topicList'] = $this->topicList;
    }

    public function index()
    {
        if (!$this->data['loggedInUserMemberFlag']) {
            $this->data['contentview'] = 'welcome';
            $this->load->view('includes/template', $this->data);
        } else {
            redirect('home');
        }

    }

    public function htmlLogin()
    {
        $this->data['loggedInUserMemberFlag'] = true;
        $lastLoggedIn = new DateTime('2012-06-15 17:40:55');
        $lastLoggedOut = new DateTime('2012-06-15 17:39:00');
        $timIntervalObj = $lastLoggedOut->diff($lastLoggedIn);
        $this->data['contentview'] = 'htmlLogin';
        $this->load->view('includes/template', $this->data);
    }

    public function htmlStartArg()
    {
        $this->data['loggedInUserMemberFlag'] = false;
        $this->data['contentview'] = 'htmlStartArg';
        $this->load->view('includes/template', $this->data);
    }

    public function userMemberAuthenticate()
    {
        $userMember = array("email" => $this->input->post('username'), "password" => $this->input->post('password'));
        $this->load->model('userMemberModel');
        if ($loggedInMember = $this->userMemberModel->authenticate($userMember)) {
            if ($loggedInMember->status == '0') { //acount inactive
                redirect(base_url() . 'base?res=6');
            } else {
                $this->setUserCookie($loggedInMember);
                redirect('home');
            }
        } else { //username or password error
            redirect(base_url() . 'base?res=5');
        }
    }

    public function about()
    {
        $this->data['contentview'] = 'about';
        $this->load->view('includes/template', $this->data);
    }

    public function privacyPolicy()
    {
        $this->data['contentview'] = 'privacy-policy';
        $this->load->view('includes/template', $this->data);
    }

    public function contactUs()
    {
        $this->data['contentview'] = 'contact-us';
        $this->load->view('includes/template', $this->data);
    }

    public function terms()
    {
        $this->data['contentview'] = 'terms';
        $this->load->view('includes/template', $this->data);
    }

    public function isLoggedIn()
    {
        $isLoggedIn = $this->input->cookie('isLoggedIn');
        if (!isset($isLoggedIn) || $isLoggedIn != true) {
            return false;
        } else {
            return true;
        }
    }

    public function logout()
    {
        $memberId = $this->data['loggedInUserMember']->id;
        $this->load->model('userMemberModel');
        $this->userMemberModel->setUserOffline($memberId);
        $expire = 3600;
        /*setcookie('user', '', time() - $expire, '/');
   setcookie('id', '', time() - $expire, '/');
   setcookie('oauth_provider', '', time() - $expire, '/');
   setcookie('isLoggedIn', false, time() - $expire, '/');*/
        /*to reset all cookies (to fix fb cookies issue )*/
        $past = time() - 3600;
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, $value, $past, '/');
        }
        redirect('base');
    }

    public function welcome()
    {
        $this->data['contentview'] = 'welcome';
        $this->load->view('includes/template', $this->data);
    }

    public function setUserCookie($loggedInMember)
    {
        $expire = time() + 60 * 60 * 24 * 30;
        setcookie('user', $loggedInMember->username, $expire, '/');
        setcookie('id', $loggedInMember->id, $expire, '/');
        setcookie('oauth_provider', $loggedInMember->oauth_provider, $expire, '/');
        setcookie('isLoggedIn', true, $expire, '/');
    }

    public function fbLogin()
    {
        $error_redirect_url = base_url() . 'base?res=10';
        if ($this->input->get('error_reason') == 'user_denied') { //to check user decliened permissions to app
            echo "<script type=\"text/javascript\">opener.location = '" . $error_redirect_url . "'; self.close();</script>";
            //redirect(base_url().'/base?res=10');
            exit(0);
        }
        //Enter your Application Id and Application Secret keys
        $config['appId'] = $this->config->item('FB_API_ID');
        $config['secret'] = $this->config->item('FB_SECRET');

        //Do you want cookies enabled?
        $config['cookie'] = true;

        //load Facebook php-sdk library with $config[] options
        $this->load->library('facebook', $config);

        // Get User ID
        // Login or logout url will be needed depending on current user state.
        $user = $this->facebook->getUser();

        if ($user) {
            // Proceed knowing you have a logged in user who's authenticated.
            $data = $this->facebook->api('/me/permissions');
            $data = $data['data'][0];
            //permissions check condition
            /* if($data['read_stream'] == 1 && $data['email'] == 1 && $data['publish_stream'] && $data['user_location'] == 1){ //check all permissions accepted by user , change this when you change permissions requests
         echo 'all permissions alowed';*/
            $expire = time() + 60 * 60 * 24 * 30;
            $user_profile = $this->facebook->api('/me');

            $username = $user_profile['name'];

            $userdata = $this->checkUser($user_profile);

            if (!empty($userdata)) {
                //session_start();
                // it allows to clear cookies and logging out facebook
                //$logoutUrl = $this->facebook->getLogoutUrl(array('next'=>"http://".$_SERVER['HTTP_HOST']."/member/logout.php"));
                //it allows to clear cookies insted of loggin out from facebook
                $logoutUrl = "http://" . $_SERVER['HTTP_HOST'] . "/member/logout.php";

                setcookie('user', $user_profile['name'], $expire, '/');
                setcookie('id', $userdata->id, $expire, '/');
                setcookie('oauth_id', $userdata->oauth_uid, $expire, '/');
                setcookie('oauth_provider', $userdata->oauth_provider, $expire, '/');
                setcookie('logouturl', $logoutUrl, $expire, '/');
                setcookie('isLoggedIn', true, $expire, '/');

                //setcookie('oauth_provider','facebook',$expire,'/');
                //setcookie('id',$user,$expire,'/');//this is already cookied as oauth_id
                $arguMarkData = null;
                if ($arguMarkData['popUp']) {
                    header("location:http://" . $_SERVER['HTTP_HOST'] . "/baseActivity/argMark/createArgument.php?arguMarkData=" . serialize($arguMarkData));
                } else {
                    echo '<script type="text/javascript">opener.location = \'http://' . $_SERVER['HTTP_HOST'] . '\'; self.close();</script>';
                }
            }
            exit;
        } else {
            $param = array(scope => ('read_stream,offline_access,email,publish_stream,user_location')); //change permissions check condition when permissions here changed
            $loginUrl = $this->facebook->getLoginUrl($param);
            header('Location: ' . $loginUrl);
            $FB_LOGIN_URL = '"' . $loginUrl . '"&abc=test';
            $FB_PARAM = '"Facebook","menubar=no,width=930,height=560,toolbar=no"';
            echo 'window.open(".$FB_LOGIN_URL.", ".$FB_PARAM.");';
        }

        //Load view to display results of API calls
        //redirect('home');
    }

    function fbFeedReaderLogin()
    {
        //facebook configurations
        $config['appId'] = $this->config->item('FB_API_ID');
        $config['secret'] = $this->config->item('FB_SECRET');
        $config['cookie'] = true;

        //load Facebook php-sdk library with $config[] options
        $this->load->library('facebook', $config);

        //get loggedin facebook user
        $user = $this->facebook->getUser();

        //if loggedin user is a facebook user
        if ($user) {
            //echo '<script type="text/javascript">window.getElementById("SocilaMediaContent").innerHTML="<img src="/images/da-loader.gif">";/*window.opener.fbFeedReader();*/self.close();</script>';
            /*echo '<script type="text/javascript">window.opener.fbFeedReader();self.close();</script>';*/
            echo '<script type="text/javascript">window.opener.fbFeedReader();self.close();</script>';
        } else { //if you dont have a loggedin facebook user
            $param = array(scope => ('read_stream,offline_access,email,publish_stream,user_location')); //change permissions check condition when permissions here changed
            $loginUrl = $this->facebook->getLoginUrl($param);
            header('Location: ' . $loginUrl);
            $FB_LOGIN_URL = '"' . $loginUrl . '"&abc=test';
            $FB_PARAM = '"Facebook","menubar=no,width=930,height=560,toolbar=no"';
            echo 'window.open(".$FB_LOGIN_URL.", ".$FB_PARAM.");';
        }
    }

    function fbFeedReader()
    {
        //facebook configurations
        $config['appId'] = $this->config->item('FB_API_ID');
        $config['secret'] = $this->config->item('FB_SECRET');
        $config['cookie'] = true;

        //load Facebook php-sdk library with $config[] options
        $this->load->library('facebook', $config);

        //get loggedin facebook user
        $user = $this->facebook->getUser();
        if ($user) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $newsfeeds = $this->facebook->api('/me/HOME?limit=100');
                $wallfeeds = $this->facebook->api('/me/links?limit=100');
                $newsfeedsdata = array();
                $wallfeedsdata = array();
                $newsfeedsCount = sizeof($newsfeeds['data']);
                $wallfeedsCount = sizeof($wallfeeds['data']);
                if ($newsfeedsCount == 0 && $wallfeedsCount == 0) {
                    $result = array("response" => "You don't have any activities on your facebook to retrive.");
                    echo json_encode($result);
                } else {
                    $feedsHTML = "<div id='mcs_container'><div class='customScrollBox'><div class='container'><div class='content'><ul style='float:left;padding:0;'>";
                    for ($i = 0, $x = 0; $i <= $newsfeedsCount; $i++) {
                        if ($newsfeeds['data'][$i]['message'] != NULL) {
                            $feedUser = $newsfeeds['data'][$i]['from']['name'];
                            //$feedUserObj =  $facebook->api($newsfeeds['data'][$i]['from']['id']);
                            $feedUserImg = "http://graph.facebook.com/" . $newsfeeds['data'][$i]['from']['id'] . "/picture";
                            $feedContent = $newsfeeds['data'][$i]['message'];
                            $feedTime = $newsfeeds['data'][$i]['created_time'];
                            $newsfeedsdata[$x]['feedusername'] = $feedUser;
                            $newsfeedsdata[$x]['feeduserid'] = $newsfeeds['data'][$i]['from']['id'];
                            $newsfeedsdata[$x]['feeduserimage'] = $feedUserImg;
                            $newsfeedsdata[$x]['feedcontent'] = $feedContent;
                            $newsfeedsdata[$x]['feedtime'] = $feedTime;
                            $newsfeedsdata[$x]['permaLink'] = $newsfeeds['data'][$i]['actions'][0]['link'];             //public url for each feed
                            $feedsHTML .= "<li class='feedItem' oauth_id='" . $_COOKIE['oauth_id'] . "' memberid='" . $_COOKIE['id'] . "' feedId='" . $newsfeeds['data'][$i]['id'] . "'>";
                            $feedsHTML .= '<div class="sectionTopicsHeader">
							<img src="' . $feedUserImg . '" alt="Facebook User" style="width: 50px; height: 50px; display: inline; float: left; margin: 0pt 10px 0pt 0pt;">
							<span class="feedUser" >' . $feedUser . '</span>
							<span class="feedTime" >' . $feedTime . '</span>
							<span class="feedContent" > ' . $feedContent . '</span><br/>
							<input type="hidden" name="source" class="argumentSource" value="facebook.com" />
							<span class="argueLink">Start Argument</span>
							</div></li>';
                            $x++;
                        }
                        if ($x == 30) {
                            break 1;
                        }
                    }
                    for ($i = 0, $x = 0; $i < $wallfeedsCount; $i++) {
                        if ($wallfeeds['data'][$i]['message'] != NULL) {
                            $feedUser = $wallfeeds['data'][$i]['from']['name'];
                            $feedContent = $wallfeeds['data'][$i]['description'];
                            $feedTime = $wallfeeds['data'][$i]['created_time'];
                            $wallfeedsdata['feedusername'] = $feedUser;
                            $wallfeedsdata['feeduserid'] = $wallfeedsdata['data'][$i]['from']['id'];
                            $wallfeedsdata['feeduserimage'] = $feedUserImg;
                            $wallfeedsdata['feedcontent'] = $feedContent;
                            $wallfeedsdata['feedtime'] = $feedTime;
                            $wallfeedsdata[$x]['permaLink'] = $wallfeedsdata['data'][$i]['actions'][0]['link'];         //public url for each feed
                            $feedsHTML .= "<li class='feedItem' oauth_id='" . $_COOKIE['oauth_id'] . "' memberid='" . $_COOKIE['id'] . "' feedId='" . $wallfeeds['data'][$i]['id'] . "'>";
                            $feedsHTML .= '<div class="sectionTopicsHeader">
							<img src="http://graph.facebook.com/' . $_COOKIE['oauth_id'] . '/picture" alt="Facebook User"style="width: 50px; height: 50px; display: inline; float: left; margin: 0pt 10px 0pt 0pt;">
							<span class="feedUser" >' . $feedUser . '</span>
							<span class="feedContent"> ' . $feedContent . '</span><br/>
							<span class="feedTime" >' . $feedTime . '</span>
							<input type="hidden" name="source" class="argumentSource" value="facebook.com" />
							<span class="argueLink" style="float:right;cursor:pointer;display:none;">argue</span>
							</div></li>';
                            $x++;
                        }
                        if ($x == 30) {
                            break 1;
                        }
                    }
                    $feedsHTML .= "</ul></div></div><div class='dragger_container'><div class='dragger'></div></div></div></div>";
                    //error_log('Feed HTML:' . $feedsHTML, 3, 'debug.log');
                    //$result = array("response" => $feedsHTML);
                    $data = array("newsfeed" => $newsfeedsdata, "wallfeed" => $wallfeedsdata);
                    $result = array("response" => true, "data" => $data);
                    echo json_encode($result);
                }
            } catch (FacebookApiException $e) {
                //error_log($e);
                $user = null;
            }
        } else {
            $content = '<div id="SocialMediaSignInBox">';
            /*$content.='<button id="TWSignIn" class="primaryButton gradient">SIGN IN WITH TWITTER</button>';
     $content .= '<span class="orString secondaryTextColor ">-- or --</span>';*/
            $content .= '<button id="FBSignIn" class="primaryButton"onclick="window.open(\'' . base_url() . 'base/fbFeedReaderLogin\', \'Facebook\',\'menubar=no,width=930,height=560,toolbar=no\');">';
            $content .= 'SIGN IN WITH FACEBOOK';
            $content .= '</button>';
            $content .= '</div>';
            $result = array("response" => false, "data" => $content);
            echo json_encode($result);
        }

    }

    function twFeedReaderLogin()
    {
        //initializing twitter config variables
        $consumerkey = $this->config->item('TW_API_KEY');
        $consumersecret = $this->config->item('TW_API_SECRET');
        $oauthToken = $_COOKIE['oauth_token'];
        $oauthSecret = $_COOKIE['oauth_token_secret'];
        //loading twitter library
        $this->load->libraryInstance('EpiCurl');
        $this->load->library('EpiOAuth', array($consumerkey, $consumersecret));
        $this->load->library('EpiTwitter', array($consumerkey, $consumersecret, $oauthToken, $oauthSecret));
        if (strlen($oauthToken) > 5 && strlen($oauthSecret) > 5) { //user already Logged In with twitter on disagree.me
            /*echo '<script type="text/javascript">window.opener.twFeedReader();self.close();</script>';*/
            echo '<script type="text/javascript">opener.location = \'' . base_url() . 'base/twFeedReader\'; self.close();</script>';
        } else if (isset($_GET['oauth_token'])) { //confirm twitter login processing
            $Twitter = new EpiTwitter(array($consumerkey, $consumersecret));
            if (isset($_GET['oauth_token']) || (isset($_COOKIE['oauth_token']) && isset($_COOKIE['oauth_token_secret']))) {
                // user accepted access
                if (!isset($_COOKIE['oauth_token']) || !isset($_COOKIE['oauth_token_secret'])) { //loggedin just now
                    // user comes from twitter
                    $Twitter->setToken($_GET['oauth_token']);
                    $token = $Twitter->getAccessToken();
                    $expire = time() + 60 * 60 * 24 * 30;
                    setcookie('oauth_token', $token->oauth_token, $expire, '/');
                    setcookie('oauth_token_secret', $token->oauth_token_secret, $expire, '/');
                    $Twitter->setToken($token->oauth_token, $token->oauth_token_secret);
                    echo '<script type="text/javascript">window.opener.twTweetReader();self.close();</script>';
                } else { // user switched pages and came back or got here directly, stilled logged in
                    $Twitter->setToken($_COOKIE['oauth_token'], $_COOKIE['oauth_token_secret']);
                    $user = $Twitter->get_accountVerify_credentials();
                    $oauth_token = $_COOKIE['oauth_token'];
                    $oauth_token_secret = $_COOKIE['oauth_token_secret'];
                    echo '<script type="text/javascript">window.opener.twTweetReader();self.close();</script>';
                }
            } elseif (isset($_GET['denied'])) {
                // user denied access
                echo 'You must sign in through twitter first';
                echo '<script type="text/javascript">window.opener.Showmsg("Seems you denied permission to fetch tweets for you");self.close();</script>';
            }  else {
                // user not logged in
                echo 'You are not logged in';
            }
        }elseif (isset($_GET['denied'])) { // user denied access
            echo '<script type="text/javascript">window.opener.baseObj.Showmsg("Seems you denied permission to fetch tweets for you");self.close();</script>';
        } else { //if you dont have a loggedin Twitter user show him twitter login box
            $twitterObj = new EpiTwitter(array($consumerkey, $consumersecret));
            $twitterOauthUrl = '"' . $twitterObj->getAuthorizationUrl() . '"';
            echo '<script type="text/javascript">self.location.replace(' . $twitterOauthUrl . ');</script>';
        }
    }

    function twFeedReader()
    {
        //initializing twitter config variables
        $consumerkey = $this->config->item('TW_API_KEY');
        $consumersecret = $this->config->item('TW_API_SECRET');
        $oauthToken = $_COOKIE['oauth_token'];
        $oauthSecret = $_COOKIE['oauth_token_secret'];
        //loading twitter library
        $this->load->libraryInstance('EpiCurl');
        $this->load->library('EpiOAuth', array($consumerkey, $consumersecret));
        $this->load->library('EpiTwitter', array($consumerkey, $consumersecret, $oauthToken, $oauthSecret));

        if (strlen($oauthToken) > 5 && strlen($oauthSecret) > 5) { //twitter creadentials available to fetch tweets
            $twitterObj = new EpiTwitter(array($consumerkey, $consumersecret));
            $twitterObj->setToken($oauthToken, $oauthSecret);
            $hometimeline = $twitterObj->get_statusesHome_timeline(array('count' => 30));
            $responseArr = $hometimeline->response;
            foreach($responseArr as $key=>$res){                      //preparing public url to each tweet
                $responseArr[$key]['permaLink'] = 'https://twitter.com/'.$res['user']['id'].'/status/'.$res['id_str'];
            }
            $result = array("response" => true, "data" => $responseArr);
            echo json_encode($result);
        } else { //user not loggedIn, so return hadle to login with twitter (button)
            $content = '<div id="SocialMediaSignInBox">';
            $content .= '<button id="TWSignIn" class="primaryButton gradient"onclick="window.open(\'' . base_url() . 'base/twFeedReaderLogin\', \'Twitter\',\'menubar=no,width=930,height=560,toolbar=no\');">';
            $content .= 'SIGN IN WITH TWITTER';
            $content .= '</button>';
            $content .= '</div>';
            $result = array("response" => false, "data" => $content);
            echo json_encode($result);
        }
    }

    function checkUser($user)
    {
        $this->load->model('userMemberModel');
        $userMember = $this->userMemberModel;
        $uid = $user['id'];
        $oauth_provider = 'facebook';
        $username = $user['name'];
        $gender = $user['gender'];
        $email = $user['email'];
        $location = $user['location']['name'];
        $user_avatar = "https://graph.facebook.com/" . $uid . "/picture";
        $fbUser = $userMember->getFacebookUser($uid, $oauth_provider);
        if (!empty($fbUser) && $userMember->checkUserByUsernameOrEmail($email)) {   //check if oauth_id,oauth_provider already exists and email already exists
            # User is already present
        } else {
            #user not present. Insert a new Record
            if ($gender == 'male') {
                $gender = 'M';
            } elseif ($gender == 'female') {
                $gender = 'F';
            }
            $userData = array("oauth_provider" => $oauth_provider, "oauth_uid" => $uid, "username" => $username, "gender" => $gender, "email" => $email, "userphoto" => $user_avatar, "location" => $location);
            $userMember->createFbUser($userData);
            //$query = mysql_query("INSERT INTO `usermember` (id,oauth_provider, oauth_uid, username, email) VALUES ('$uid', '$oauth_provider', '$uid', '$username', '')") or die(mysql_error());
            $fbUser = $userMember->getFacebookUser($uid, $oauth_provider);
            return $fbUser;
        }
        return $fbUser;
    }

    function checkGoogleUser($userData)
    {
        $this->load->model('userMemberModel');
        $userMember = $this->userMemberModel;
        $userMemberData = array("oauth_provider" => $userData['oauth_provider'], "oauth_uid" => $userData['oauth_uid'], "username" => $userData['username'], "email" => $userData['email'], "gender" => $userData['gender'], "profilephoto" => $userData['profilephoto']);
        $result = $userMember->createGoogleUser($userMemberData);
        if ($result) {
            $googleUser = $userMember->getGoogleUser($userMemberData['oauth_uid'], $userMemberData['oauth_provider']);
            return $googleUser;
        } else {
            return false;
        }
    }

    function userMemberCreate()
    {
        $this->load->library('form_validation');
        //$this->form_validation->set_rules('username', 'Username', trim|required);
        //$this->form_validation->set_rules('password', 'Password', required);
        //$this->form_validation->set_rules('email', 'Email', trim|required|validate_email);
        /*geo location from user ip address*/
        //$data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $_SERVER['REMOTE_ADDR']));
        //$location = $data['geoplugin_city'] . ',' . $data['geoplugin_countryName'];
        /**********/

        $this->load->model('userMemberModel');
        $data['id'] = null;
        $data['oauth_provider'] = null;
        $data['oauth_uid'] = null;
        $data['username'] = filter_var($this->input->post('username'),FILTER_SANITIZE_STRING);
        $data['password'] = md5($this->input->post('password'));
        $data['email'] = filter_var(filter_var($this->input->post('email'),FILTER_SANITIZE_EMAIL),FILTER_VALIDATE_EMAIL);
        $data['gender'] = filter_var($this->input->post('gender'),FILTER_SANITIZE_STRING);
        $data['location'] = '';
        /*$data['location'] = isset($location)?$location:null;*/
        ($data['gender'] == '') ? 'M' : $data['gender'];
        if ($data['username'] && $data['password'] && $data['email'] && $data['gender']) {
            if (!$this->userMemberModel->checkUserByUsernameAndEmail($data['username'], $data['email'])) {
                if ($registeredUserMember = $this->userMemberModel->createUser($data)) {
                    $message = ACCOUNT_ACTIVATION_EMAIL_MESSAGE;
                    $acticationLink = base_url() . "base/accountConfirm?key=" . $registeredUserMember['activationkey'] . "&id=" . $registeredUserMember['memberId'];
                    $message .= '<a href="' . $acticationLink . '">' . $acticationLink . '</a>';
                    $this->sendEmail($data['email'], "Disagree.me", ACCOUNT_ACTIVATE_MESSAGE_SUBJECT, $message, 'html');
                    if ($this->processUserProfilePic($registeredUserMember['memberId'], null, null)) {
                        //redirect to Page will Message
                        /*$this->data['message'] = "An email has been sent to your email address with the activation link. You can activate your account by clicking on the link";
                       $this->data['contentview'] = 'notify';
                       $this->load->view('includes/template', $this->data);*/
                        redirect(base_url() . 'base?res=4');
                    } else {
                        echo 'user image error';
                    }
                }
            } else {
                //redirect to DB Error page
                redirect(base_url() . 'base?res=3');
            }
        } else {    //data is invalid format
            redirect(base_url() . 'base?res=3');
        }

    }

    function accountConfirm()
    {
        $activationKey = $this->input->get('key');
        $memberId = $this->input->get('id');
        $this->load->model('userMemberModel');
        if ($this->userMemberModel->checkUserAndActive($activationKey, $memberId)) {
            $this->setUserCookie($this->getUserMemberData($memberId));
            redirect('home');
        }
    }

    function dbChange()
    {
        $this->load->model('userMemberModel');
        $this->userMemberModel->dbChange();
        $this->data['contentview'] = 'dbChange';
        $this->load->view('includes/template', $this->data);
    }

    function forgetPassword()
    {
        log_message('debug', 'forget password called');
        $this->load->model('userMemberModel');
        $email = $this->input->post('email');
        $keyp = $this->input->post('key');
        $key = ($keyp == '') ? $this->input->get('key') : $keyp;
        $upass = $this->input->post('pass');
        $data = null;
        if ($email == '' && $key == '') {
            $response = 0;
            echo json_encode(array("response" => $response, "data" => ''));
        } else {
            if ($key == '' && $email != '') { //user requested new password
                $activationKey = $this->userMemberModel->forgetPassRequest($email);
                if ($activationKey != '') {
                    $message = "Please click on the below link to rest your password or copy past the below link into browser<br/>";
                    $message .= base_url() . "base/forgetPassword?key=" . $activationKey;
                    $this->sendEmail($email, $email, "Password Reset", $message, 'html');
                    $data = array("request" => true, "mailed" => true);
                    $response = 1;
                } else {
                    $response = 0;
                }
                echo json_encode(array("response" => $response, "data" => $data));

            } else if ($email == '' && $key != '') { //user resetting password
                if ($upass == '' && $key != '') { //return password rest page
                    $this->data['keyObj'] = $this->userMemberModel->forgetPassReset($key, '');
                    if ($this->data['keyObj']) { //key is valid
                        $this->data['contentview'] = 'forgetPassword';
                        $this->load->view('includes/template', $this->data);
                    } else { //key is invalid
                        $this->data['contentview'] = 'forgetPassword';
                        $this->load->view('includes/template', $this->data);
                    }
                } elseif ($upass != '' && $key != '') { //reset password
                    if ($this->userMemberModel->forgetPassReset($key, md5($upass))) {
                        $response = 1;
                        $message = "Recently your Account password changed on Disagree.me. if it is not you , please mind to change the password immediately.";
                        $this->sendEmail($email, WEBMASTER_EMAIL, "Password Reset Notification", $message);
                        $data = array("resetStatus" => true);
                        redirect(base_url(). 'base?res=11');
                    }else{
                        redirect(base_url(). 'base?res=12');
                    }
                }
            }
        }
    }

    function checkUserByUsernameOrEmail()
    {
        $input = $this->input->post('data');
        $this->load->model('userMemberModel');
        $res = ($input == '') ? true : $this->userMemberModel->checkUserByUsernameOrEmail($input);
        echo json_encode(array("response" => 1, "data" => $res));
    }

    /**
     * processes all notifications by sending emails to users
     * used: CronJob will call this url / direct url acess
     *
     * @param null
     * @return null
     */
    function processCRON()
    {
        echo 'processCRON called';
        $this->load->model('BaseModel');
        $this->load->model('argumentCommentModel');
        $jobs = $this->BaseModel->loadOfflineNotificationQueue(); //fetch offline notifications (email not sent for notification) form database
        echo 'jobs loaded';
        print_r($jobs);
        if ($jobs) {
            $jobsDone = array();
            foreach ($jobs as $job) { //notification to process according to type
                echo 'processing job' . $job . recordId;
                if ($job->type == REPLY_TO_COMMENT_OWNER_NOTIFICATION || $job->type == REPLY_TO_ARGUMENT_OWNER_NOTICTION) {
                    $argumentAndCommentData = $this->argumentCommentModel->getArgumentAndCommentByReplyId($job->recordId);
                    $job->argumentId = $argumentAndCommentData->argumentId;
                    $job->argumentTitle = $argumentAndCommentData->argumentTitle;
                    $job->commentId = $argumentAndCommentData->commentId;
                    $job->commentText = $argumentAndCommentData->comment;
                }
                $message = $this->prepareMessage($job);
                if ($this->sendEmail($job->ownerEmail, WEBMASTER_EMAIL, NOTIFICATION_EMAIL_SUBJECT, $message, 'html')) {
                    echo ' mail sent to' . $job->ownerEmail;
                    echo 'Message' . $message;
                    array_push($jobsDone, $job->id);
                } else {
                    log_message('DEBUG', 'mail sending fail for ' . $job->ownerEmail . '. Reference: Notification Id:' . $job->id);
                }
            }
            if (sizeof($jobsDone) > 0) {
                $data = $this->BaseModel->markNotificationAsRead($jobsDone);
                log_message('info', $data . ' jobs processed and popped out of the notification queue');
            }
        } else { //no notifications to process

        }
    }

    public function invite()
    {
        $this->data['jsList'] = array("invite");
        $this->data['contentview'] = 'invite';
        $this->load->view('includes/template', $this->data);
    }

    public function searchResult()
    {
        $keyword = $this->input->get('s');
        $type = $this->input->get('t');
        $response = false;
        array_push($this->data['jsList'], 'searchview');
        $this->data['keyword'] = $keyword;
        $this->data['type'] = $type;
        $this->data['contentview'] = 'searchresult';
        $this->load->view('includes/template', $this->data);

    }

    public function searchArguments()
    {
        $keyword = $this->input->post('term');
        $this->load->model('argumentModel');
        if ($data = $this->argumentModel->getArgumentsByKeyword($keyword)) {
            $response = true;
        } else {
            $response = false;
        }
        json_encode(array("response" => $response, "data" => $data));
    }

    public function searchPeople()
    {
        $keyword = $this->input->post('term');
        $this->load->model('userMemberModel');
        if ($data = $this->userMemberModel->getUsersByKeyword($keyword)) {
            $response = true;
        } else {
            $response = false;
        }
        json_encode(array("response" => $response, "data" => $data));
    }

    /* function to get the count of yesterDay new users, active users, total users, yesterday  created arguments, active arguments, total aru */
    public function dailyReport()
    {
        $this->load->model('baseModel');

        $dailyreportvalues = $this->baseModel->getdailyReport();

        if ($dailyreportvalues) {

            foreach ($dailyreportvalues as $dailyreport) {
                $message = "<h3>DAILY REPORT</h3><div>New users yesterday : " . $dailyreport->yesterdaynewusercount . "</div><div>Total users : " . $dailyreport->totalusers . "</div><div>Active users yesterday : " . $dailyreport->activeusers . "</div><div>New Arguments created yesterday : " . $dailyreport->newargumentcount . "
                </div><div>Active arguments yesterday :  " . $dailyreport->yesterdaysactivearguments . "</div><div>Total arguments : " . $dailyreport->totalArguments . "</div><div>Unique Monthly Active Users : " . $dailyreport->Last30daysactiveusercount . "</div>";
                if ($this->sendEmail(CONTACT_EMAIL, WEBMASTER_EMAIL, "Disagree.me daily report", $message, 'html')) {
                    echo ' mail sent to ' . CONTACT_EMAIL;
                    echo 'Message ' . $message;

                } else {
                    echo 'sending failed';
                    log_message('error', 'mail sending failed');
                }
            }

        }
    }

}