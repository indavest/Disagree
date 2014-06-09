<?php
class DA_Controller extends CI_Controller
{

    var $topicList = NULL;

    function __construct()
    {
        parent::__construct();

        //Initiate userMember and Topic Models
        $this->load->model('topicModel');

        //Construct topic List
        $this->topicList = $this->topicModel->getTopicArray();

        //loading constants file

        $const = file_get_contents(base_url().'/js/constants.json');
        $jsonconst = json_decode($const);
        foreach($jsonconst AS $name=>$constant){
            define($name,$constant);
        }
    }

    function getUserMemberData($id)
    {
        $this->load->model('userMemberModel');
        //Construct UserMember Obj with profile and Stats
        $userMember = $this->userMemberModel->getById($id);
        if(!strpos($userMember->profilephoto,"graph.facebook.com")){
            $userMember->profilephoto = $userMember->profilephoto;
        	$userMember->profileThumb = $this->getThumbPath($userMember->profilephoto);
        	$userMember->fromThirdParty = false;
        }else{
        	$userMember->profileThumb = $userMember->profilephoto;
        	$userMember->profilephoto = $userMember->profilephoto."?type=large";
        	$userMember->fromThirdParty = true;
        }
        $userMemberStats = $this->userMemberModel->getProfileStatistics($id);
        $userMember->argumentCreatedCount = $userMemberStats->argumentCreatedCount;
        $userMember->followerCount = $userMemberStats->followerCount;
        $userMember->followedCount = $userMemberStats->followedCount;
        $userMember->argumentFollowCount = $userMemberStats->argumentFollowCount;
        $userMember->topicFollowCount = $userMemberStats->topicFollowCount;
        $userMember->participatedCount = $userMemberStats->participatedCount;
        $userMember->notificationCount = $userMemberStats->notificationCount;

        return $userMember;
    }

    public function sendEmail($to, $from, $subject, $message, $mailType = 'html')
    {
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => WEBMASTER_EMAIL,
            'smtp_pass' => 'indavest123',
            'mailtype' => $mailType
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");

        $this->email->from($from,'Disagree.me');
        $this->email->to($to);
        $this->email->subject($subject);
        $emailText = '<body style="font-size:12px;font-family:Arial Sans-serif;">
                            <table background="'.base_url().'images/body_bg.gif" width="640" align="center" style="margin:0 auto;width:640px; background: scroll url("'.base_url().'images/body_bg.gif") 0 0 repeat;font-size:12px;" cellpadding="0" cellspacing="0" border = "0">
                                <tr>
                                    <td>
                                        <div class="top" style="min-height: 20px; width: 640px;">
                                            <div style="float:left;width:320px;height:10px;background-color:#889273"></div>
                                        </div>
                                        <div class="container" style="margin: 0px 20px; width: 600px;">
                                            <div class="imgcontainer" style="width:100%;min-height:52px;display:block">
                                                <a href="'.base_url().'" title="Disagree.me" target="_blank">
                                                    <img width="149" height="32" src="'.base_url().'images/disagree-logo.png" alt="Disagree.me Site Logo"/>
                                                </a>
                                            </div>
                                            <div class="msgcontainer" style="min-height: 80px; background: none repeat scroll 0px 0px #ffffff; border-radius: 12px 12px 12px 12px; padding: 20px; display: inline-block; width: 560px;">'
                                                . $message . '
                                            </div>
                                            <div class="copyrightcontainer" style="color: #46322B; margin-top: 20px; width: 600px; display: inline-block;">
                                                <p style="width:300px;float:left;text-align:left;margin:0;padding:0">All Rights reserved</p>
                                                <span style="width:300px;float:right;text-align:right;"><a href="'.base_url().'" style="text-decoration:none;color:#46322B" target="_blank;">www.Disagree.me</a></span>
                                            </div>
                                        </div>
                                        <div class="bottom" style="min-height: 20px; clear: both; width: 640px;">
                                            <div style="float:left;width:320px;height:10px;background-color:#f89372;margin-top:10px;"></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </body>';
        //echo $emailText;
        $this->email->message($emailText);

        if ($this->email->send()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * prepareMessage
     * prepare notification message
     *
     * $data structure - id, type, recordId, ownerId, ownerEmail, followinguserprofilephoto, followinguserid, followingusername, argumentId, argumentTile, createdtime, argumentId ,argumentTitle ,commentId ,commentText
     *
     * @param mixed $data
     * @return string $msg
     */
    public function prepareMessage($data)
    {
        $msg = '';
       
        $imagePath = (!strpos($data->userprofilephoto,"graph.facebook.com"))?base_url().$data->userprofilephoto:$data->userprofilephoto;
        switch ($data->type) {
            case AGREE_NOTIFICATION:
                //process disagree vote
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" class="userImgCircleSmall" style="float: left; width: 35px;">';
                $msg .= '<img src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" width="35" height="35" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> agrees with your argument <br/>"<a href="' . base_url() . 'detail?id=' . $data->argumentId . '" style="color:#46322B;font-weight:bold;text-decoration:none;">' . $data->argumentTitle . '</a>"</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
            case DISAGREE_NOTIFICATION:
                //process agree vote
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" style="float: left; width: 35px;">';
                $msg .= '<img src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" width="35" height="35" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> disagrees with your argument <br/>"<a href="' . base_url() . 'detail?id=' . $data->argumentId . '" style="color:#46322B;font-weight:bold;text-decoration:none;">' . $data->argumentTitle . '</a>"</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
            case COMMENT_NOTIFICATION:
                //process comment
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" style="float: left; width: 35px;">';
                $msg .= '<img height="35" width="35" src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" class="userImgCircleSmall" title="' . $data->username . '" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> commented on your argument <br/>"<a href="' . base_url() . 'detail?id=' . $data->argumentId . '" style="color:#46322B;font-weight:bold;text-decoration:none;">' . $data->argumentTitle . '</a>"</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
            case FOLLOW_ARGUMENT_NOTIFICATION:
                //process favorite
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" style="float: left; width: 35px;">';
                $msg .= '<img height="35" width="35" src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" class="userImgCircleSmall" title="' . $data->username . '" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> made "<a href="' . base_url() . 'detail?id=' . $data->argumentId . '" style="color:#46322B;font-weight:bold;text-decoration:none;">' . $data->argumentTitle . '</a>" as one of their Favorites</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
            case FOLLOW_MEMBER_NOTIFICATION:
                //process user follow
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" style="float: left; width: 35px;">';
                $msg .= '<img height="35" width="35" src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" class="userImgCircleSmall" title="' . $data->username . '" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> is following you</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
            case REPLY_TO_ARGUMENT_OWNER_NOTICTION:
                //process comment
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" style="float: left; width: 35px;">';
                $msg .= '<img height="35" width="35" src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" class="userImgCircleSmall" title="' . $data->username . '" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> replied to a comment <br/>"<a href="'.base_url().'detail?id='.$data->argumentId.'#c'.$data->commentId.'" style="color:#46322B;font-weight:bold;text-decoration:none;">'.$data->commentText.'</a>" on your argument <br/>"<a href="' . base_url() . 'detail?id=' . $data->argumentId . '" style="color:#46322B;font-weight:bold;text-decoration:none;">' . $data->argumentTitle . '</a>"</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
            case REPLY_TO_COMMENT_OWNER_NOTIFICATION:
                //process comment
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" style="float: left; width: 35px;">';
                $msg .= '<img height="35" width="35" src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" class="userImgCircleSmall" title="' . $data->username . '" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> replied on your  comment <br/>"<a href="' . base_url() . 'detail?id=' . $data->argumentId . '#c'.$data->commentId.'" style="color:#46322B;font-weight:bold;text-decoration:none;">'.$this->cleanHTMLFromString($data->commentText).'</a>"</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
            case INVITE_TO_ARGUMENT:
                //process invite from argument to user
                //process agree vote
                $msg = '<div class="notificationWrapper">';
                $msg .= '<div class="notificationMsg">';
                $msg .= '<a href="' . base_url() . 'profile?id=' . $data->userid . '" style="float: left; width: 35px;">';
                $msg .= '<img src="' . $imagePath . '" alt="' . $data->username . 'on Disagree.me" width="35" height="35" style="border-radius:25px;"/>';
                $msg .= '</a>';
                $msg .= '<span style="color:#46322B;float:left;width: 510px; margin-left: 15px;"><span class="profileBasicInfo" style="color:#8C9AA1;">' . $data->username . '</span> invited you to argue on the argument <br/>"<a href="' . base_url() . 'detail?id=' . $data->argumentId . '" style="color:#46322B;font-weight:bold;text-decoration:none;">' . $data->argumentTitle . '</a>"</span>';
                $msg .= '</div>';
                //$msg .= '<div class="notificationSource">' . $this->time_difference($data->createdtime) . '</div>';
                $msg .= '</div>';
                break;
        }

        return $msg;
    }

    /**
     * remove all html content from given string
     * removes tag <br/> <br/>
     * remove &lt;, &nbsp;
     *
     * @param string $HTMLString
     * @return string
     */
    public function cleanHTMLFromString($HTMLString){
        $str = preg_replace('/&[^\s]*;/ims', '',$HTMLString);  // removes entity codes
        $str  = preg_replace('/<(.|\n)*?>/imsxA', ' ', $str);  // removes tags
        return  $str;
    }

    public function time_difference($date)
    {

        if (empty($date)) {
            return "No date provided";
        }

        $periods = array("s", "m", "h", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();
        $unix_date = strtotime($date);


        // check validity of date
        if (empty($unix_date)) {
            return "Bad date";
        }

        // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            //$tense = "ago";

        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }
        $hourDifference = $difference / (60 * 60);
        if ($hourDifference < 24) {
            for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
                $difference /= $lengths[$j];
            }
            $difference = round($difference);
            /*if ($difference != 1) {
                       $periods[$j] .= "s";
                   }*/

            $dateDifference = "$difference$periods[$j]";
        } else {
            $difference = date("j M", $unix_date);
            $dateDifference = "$difference";
        }

        return $dateDifference;
    }

    public function time_difference_DB_Call($date)
    {

        if (empty($date)) {
            return "No date provided";
        }
        //date_default_timezone_set('Asia/Kolkata');
        //date_default_timezone_set('GMT');

        $periods = array("s", "m", "h", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();
        $unix_date = strtotime($date);


        // check validity of date
        if (empty($unix_date)) {
            return "Bad date";
        }

        // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            //$tense = "ago";

        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }
        return round($difference / 60);
    }

    public function DARoundPercentage($userInput)
    {
        $map = array(0, 5, 15, 25, 35, 45, 50, 55, 65, 75, 85, 95, 100);
        $ul = 0;
        $ll = 0;
        $result = 0;
        for ($n = 0; $n < sizeof($map) - 1; $n++) {
            //echo $n;
            $ll = $map[$n];
            $ul = $map[$n + 1];
            //echo $n;
            if ($userInput > $ll && $userInput < $ul) {
                if (($userInput - $ll) > ($ul - $userInput)) {
                    $result = $ul;
                }
                else {
                    $result = $ll;
                }
                break;
            } else if ($userInput == $ll || $userInput == $ul) {
                $result = $userInput;
            }
        }

        return $result;
    }

    public function error_404()
    {
        $data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));
        $data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn');
        $data['contentview'] = 'error/error_404';
        $this->load->view('includes/template', $data);
    }

    public function processUserProfilePic($memberId, $profilePicTempPath, $profilePicPath)
    {
        $localPicPath = null;
        $localTempPath = null;
        $isProfilePicNull = false;
        $isTempProfilePicNull = false;
        $filemoved = false;

        $userFolder = USER_PROFILE_PIC_FOLDER_PATH . $memberId . '/';

        if (is_null($profilePicPath) || $profilePicPath === null || $profilePicPath == '' || substr($profilePicPath, 0, 26) == 'https://graph.facebook.com') { //check profilepic path is null (true for create site user and update facebook profile)
            $localPicPath = $userFolder . $memberId . '_profilepic' . strrchr(DEFAULT_PROFILE_PIC, '.');
            $isProfilePicNull = true;
        } else {
            $localPicPath = $profilePicPath;
        }

        if (is_null($profilePicTempPath) || $profilePicTempPath === null || $profilePicTempPath == '') { //check profilepic temp path is null (true for create user)
            $localTempPath = DEFAULT_PROFILE_PIC;
            $isTempProfilePicNull = true;
        } else {
            $localTempPath = $profilePicTempPath;
        }

        if ($isProfilePicNull) { //create folder for user (create user / update fb user)
            $userFolderRelative = $_SERVER['DOCUMENT_ROOT'] . $userFolder;
            if (file_exists($userFolderRelative)) {
                //user folder exists
                log_message('info', 'User folder creaed already exists (may be some error): ' . $userFolderRelative, false);
            } else {
                if (mkdir($userFolderRelative, 0777)) {
                    //user folder created
                    log_message('info', 'User folder creaed successfuly: ' . $userFolderRelative, false);
                } else {
                    //user folder
                    log_message('error', 'User folder creation fail: ' . $userFolderRelative, false);
                }
            }
        }

        if ($isTempProfilePicNull) { //copy default image to user fodler (create site user)
            $siteDefaultImgRelative = $_SERVER['DOCUMENT_ROOT'] . DEFAULT_PROFILE_PIC;
            $localPicPathRelative = $_SERVER['DOCUMENT_ROOT'] . $localPicPath;
            if (copy($siteDefaultImgRelative, $localPicPathRelative)) {
                //copying defaultimg to user fodler success
                $filemoved = true;
                log_message('info', 'Default image copied to user folder: ' . $localPicPathRelative, false);
            } else {
                //copying defaultimg to user fodler fail
                log_message('error', 'Default image fail copy to user folder: ' . $localPicPathRelative, false);
            }
        }

        if ($localPicPath != null || $localTempPath != null) {
            if (!$filemoved) { //true for site user creation
                $localPicPrePath = $localPicPath;
                $localPicPrePathRelative = $_SERVER['DOCUMENT_ROOT'] . $localPicPrePath;
                $localPicPath = str_replace(strchr($localPicPath, '.'), strchr($localTempPath, '.'), (substr($profilePicPath, 0, 26) == 'https://graph.facebook.com' ? $localPicPath : $profilePicPath)); //to replace profilepic extension with uploaded file extension (if facebook update use local pic else profile pic)
                $localPicPathRelative = $_SERVER['DOCUMENT_ROOT'] . $localPicPath;
                $localTempPathRelative = $_SERVER['DOCUMENT_ROOT'] . $localTempPath;
                if (file_exists($localPicPrePathRelative)) {
                    unlink($localPicPrePathRelative);
                } //delete existing image and move temp image to user folder
                if (rename($localTempPathRelative, $localPicPathRelative)) {
                    //temp image moved to user folder
                    log_message('info', 'temporary image moved to user folder successfully:' . $localTempPathRelative . ' -> ' . $localPicPathRelative, false);
                } else {
                    //temp image moved to user folder
                    log_message('error', 'temporary image fail move to user folder:' . $localTempPathRelative . ' -> ' . $localPicPathRelative, false);
                }
            } else {
                //temp image moved to user folder already
                log_message('info', 'temporary image already moved to user folder. if this is not for site user crwation may be error');
            }
        } else {
            //profile pic / temp pic paths are invalid
            log_message('error', 'profile picture path / propic pic temp path is invalid or empty: ' . $localPicPath . ' - ' . $localTempPath, false);
        }

        //resize and create thumbs
        $localPicPathRelative = $_SERVER['DOCUMENT_ROOT'] . $localPicPath;
        $image_location = $localPicPathRelative;
        $image_size = getimagesize($image_location);
        $width = $image_size[0];
        $height = $image_size[1];
        $scale = $width / $height;
        if ($scale > 1) {
            $scale = 180 / $height;
        } else {
            $scale = 180 / $width;
        }
        $this->imageResize($image_location, $width, $height, $scale);
        $file = $localPicPathRelative;
        $newfile = str_replace('profilepic', 'thumb', $localPicPathRelative); //to create thumb image file name

        if (!copy($file, $newfile)) {
            echo "failed to copy $file...\n";
        }
        $image_location = $newfile;
        $image_size = getimagesize($image_location);
        $width = $image_size[0];
        $height = $image_size[1];
        $scale = $width / $height;
        if ($scale > 1) {
            $scale = 35 / $height;
        } else {
            $scale = 35 / $width;
        }
        $this->imageResize($image_location, $width, $height, $scale);

        if (strcmp($profilePicPath, $localPicPath) != 0) {
            //database record updated
            log_message('info', 'Database record update required ' . $profilePicPath . ' - ' . $localPicPath, false);
            $this->load->model('userMemberModel');
            return $this->userMemberModel->updateUserProfilePic($memberId, $localPicPath);
        } else {
            //database record update not required
            log_message('info', 'no database record update required for profile pic' . $profilePicPath . ' - ' . $localPicPath, false);
        }

        return $localPicPath;


        /*$tempPathExt = explode(".", $profilePicTempPath);

        $updateDBFlag = false;
        if($profilePicTempPath != null || $profilePicPath != null){
            $profilePicPath = trim($profilePicPath,'/');
            $profilePicTempPath = trim($profilePicTempPath,'/');
        }
        if($profilePicPath === null || substr($profilePicPath,0,26) == 'https://graph.facebook.com'){
            $profilePicPath = USER_PROFILE_PIC_FOLDER_PATH . $memberId . "/";
            if (!mkdir($profilePicPath, 0777, true)) {
                return false;
            }
            $updateDBFlag = true;
        }

        if($profilePicTempPath === null){
            $name = DEFAULT_PROFILE_PIC;
            list($imgName, $ext) = explode(".", $name);
            $profilePicPath = $profilePicPath . basename($memberId . "_profilepic." . $ext);
            $profilePicTempPath = $imgName . $memberId . '_profilepic.' . $ext;
            if (!copy($name, $profilePicTempPath)) {
                return false;
            }
        }
        else if(!strstr($profilePicPath,'_profilepic')){   //for facebook image update
            $profilePicPath = $profilePicPath . $memberId . "_profilepic." . substr($profilePicTempPath,-4);
        }
        $profilePicPathArray = explode(".", $profilePicPath);
        $profilePicPath = $profilePicPathArray[0].".".$tempPathExt[1];
        if(file_exists($profilePicPath)){unlink($profilePicPath);}

        $url1 = $_SERVER['DOCUMENT_ROOT'].$profilePicTempPath;
        $url2 = $_SERVER['DOCUMENT_ROOT'].$profilePicPath;
        if(rename($url1,$url2)){
            $image_location = $profilePicPath;
            $image_size = getimagesize($image_location);
            $width = $image_size[0];
            $height = $image_size[1];
            $scale = $width / $height;
            if($scale > 1){
                $scale = 180 / $height;
            }else{
                $scale = 180 / $width;
            }
            $this->imageResize($image_location, $width, $height, $scale);
            $file = $profilePicPath;
            $newfile = USER_PROFILE_PIC_FOLDER_PATH.$memberId."/".$memberId."_thumb.".$tempPathExt[1];

            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n";
            }
            $image_location = $newfile;
            $image_size = getimagesize($image_location);
            $width = $image_size[0];
            $height = $image_size[1];
            $scale = $width / $height;
            if($scale > 1){
                $scale = 35 / $height;
            }else{
                $scale = 35 / $width;
            }
            $this->imageResize($image_location, $width, $height, $scale);
            if($updateDBFlag){
                $this->load->model('userMemberModel');
                return $this->userMemberModel->updateUserProfilePic($memberId,'/'.$profilePicPath);
            }
            return $profilePicPath;
        }else{
            return false;
        }*/
    }

    public function imageResize($image, $width, $height, $scale)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        $newImageHeight = ceil($height * $scale);
        $newImageWidth = ceil($width * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);

        switch ($imageType) {
            case "image/gif":
                imagegif($newImage, $image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage, $image, 90);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage, $image);
                break;
        }

        chmod($image, 0777);
        return $image;

    }

    public function getThumbPath($mainPath){
    	if(!strpos($mainPath,"graph.facebook.com")){
    		$picPath = explode("_", $mainPath);
    		$picExt = explode(".", $picPath[1]);
    		$thumbPath = $picPath[0]."_thumb.".$picExt[1];
    	}else {
    		$thumbPath = $mainPath;
    	}
    	 
    	return $thumbPath;
    }
}