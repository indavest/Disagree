<?php ob_start(); ?>
<?php echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:fb="http://ogp.me/ns/fb#">
<head>
    <title>Disagree.me</title>
    <meta name="description" content="Argue from anywhere : Argue with anyone and everyone. Start arguments from your Facebook or Twitter feed or from anywhere on the web.
Invite your friends: Don\'t let your opinions get lonely.
Insight with statistics : Fun stats to see if you know how to stir some controversy." />
    <meta name="keywords" content="" />
    <meta name="robot" content="index,follow" />
    <meta name="copyright" content="© 2012 Disagree.me" />
    <meta name="author" content="Indavest Technology Ventures" />
    <meta name="language" content="EN-US" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <!--<meta charset="utf-8" />-->
    <?php /*header('Last-Modified: '.gmdate('D, d M Y H:i:s', strtotime("Sat, 1 Sep 2012 15:26:00")).' GMT');*/?>
    <!-- favicon -->
    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>images/favicon.png"/><!-- Major Browsers -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>images/favicon.ico" type="image/x-icon"/>
    <!--[if IE]><link rel="SHORTCUT ICON" href="<?php echo base_url(); ?>images/favicon.png"/><![endif]--><!-- Internet Explorer-->
    <!-- css files-->
    <link href="<?php echo base_url();?>css/base.pack.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css"/>
    <?php if(isset($cssList)):?>
    <?php foreach ($cssList as $css):?>
        <link rel="stylesheet" href="<?php echo base_url();?>css/<?php echo $css?>.css" type="text/css"/>
        <?php endforeach;?>
    <?php endif;?>
    <!-- script files-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.jgrowl_google.js"></script>
    <script type="text/javascript">
        var DA = null;
        jQuery.ajax({
            url: '/js/constants.json',
            dataType: 'json',
            async:false,
            success: function(res){
                DA = res;
                DA.base_url = '<?php echo base_url();?>';
            }
        });
        jQuery.ajax({
            url: '/bootstrap/getTopicArrayData',
            dataType: 'json',
            async:false,
            success: function(res){
                window.topicArray = res.data;
            }
        });
    </script>
    <script type="text/javascript" src="<?php echo base_url();?>js/php.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/class/validator.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.metadata.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/engage.itoggle-min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/Scrollbar.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/loadArgument.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/class/base.js"></script>
    <?php if(isset($jsList)):?>
    <?php foreach ($jsList as $js):?>
        <script src="<?php echo base_url();?>js/<?php echo $js;?>.js" type="text/javascript"></script>
        <?php endforeach;?>
    <?php endif;?>

    <script src="http://platform.twitter.com/anywhere.js?id=<?php echo $this->config->item('TW_API_KEY');?>&amp;v=1" type="text/javascript"></script>
    <script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
    <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
</head>
<body>
<!-- IE9 gradient adjustment -->
<!--[if gte IE 9]>
<style type="text/css">
    .overlay,.secondaryContainer,.primaryButton,.primaryButton.disabled,.AgreeButton.disabled,.Disagreebutton.disabled,.agreementGradient,.disagreementGradient,.obtuseGradient{filter:none!important;}

    li#startArgumentWrapper > .primaryButton {
        width: 184px;
    }
</style>
<![endif]-->
<div id="contentContainer">
    <button class="primaryButton gradient" id="ScrollTopButton"><img alt="Go Top" src="<?php echo base_url();?>images/arrow-up.png"></button>
    <button class="primaryButton gradient" id="inviteFriendsOverlay">Invite Friends</button>
    <div id="header">
        <a id="logoLink" href="<?php echo base_url();?>welcome">
            <img src="<?php echo base_url();?>images/disagree-logo.png" alt="Disagree.me Site Logo" width="149" height="32"/>
            <span class="betalogotext secondaryText">beta</span>
        </a>
        <ul id="headerMenu" class="horizontalMenu">
        	<li id="argumentsLink">
                <a href="<?php echo base_url();?>" class="linkRegular primarytext">Arguments</a>
            </li>
            <li id="searchBoxWrapper">
                <i class="sprite-icon searchIconG"></i>
                <input type="text" class="placeholder defaultContent {defaultText : 'Search...'}" id="searchBox" />
            </li>
            <li id="userInfoDisplayWrapper">
                <div id="userNameDisplayHodler">
                    <a href="profile?id=<?php echo $loggedInUserMember->id;?>" class="userImgCircleSmall">
                        <img src="<?php echo $loggedInUserMember->profileThumb;?>" alt="<?php echo $loggedInUserMember->username;?>" <?php echo ($loggedInUserMember->fromThirdParty)?"class='thirdPartyImgSmall'":"";?>/>
                    </a>
                    <div class="userNameWrapper">
                        <span class="float">Welcome, </span>
                        <span class="username"><a href="<?php echo base_url();?>profile?id=<?php echo $loggedInUserMember->id;?>" class="linkStrong"><?php echo ellipsis((($loggedInUserMember->fullname=='')?$loggedInUserMember->username:$loggedInUserMember->fullname), 14);?></a></span>
                        <span id="notificationCountContainer" class="disabled"><a href="profile?id=<?php echo $loggedInUserMember->id;?>#activityFeed" class="counter"></a></span>
                        <button class="gradient obtuseGradient navigationLink" id="settingButton" >
                            <span class="sprite-icon darrowSmallIconG">&nbsp;</span>
                        </button>
                    </div>
                    <div class="quickMenu secondaryContainer popupActive">
                        <span class="sprite-icon larrowIconW quickMenuTip"></span>
                        <ul>
                            <li><a href="<?php echo base_url();?>profile?id=<?php echo $loggedInUserMember->id;?>#activityFeed" class="linkRegular"><span class="sprite-icon notificationIconG"></span><span class="primarytext">Notifications</span></a></li>
                            <li><a href="<?php echo base_url();?>profile?id=<?php echo $loggedInUserMember->id;?>#argumentFed" class="linkRegular"><span class="sprite-icon daIconOnG"></span><span class="primarytext">Arguments</span></a></li>
                            <li><a href="<?php echo base_url();?>profile?id=<?php echo $loggedInUserMember->id;?>#favoriteFeed" class="linkRegular"><span class="sprite-icon favIconOnG"></span><span class="primarytext">Favorites</span></a></li>
                            <li><a href="<?php echo base_url();?>profile?id=<?php echo $loggedInUserMember->id;?>#followingFeed" class="linkRegular"><span class="sprite-icon tickIconG"></span><span class="primarytext">Following</span></a></li>
                            <li><a href="<?php echo base_url();?>profile?id=<?php echo $loggedInUserMember->id;?>#followersFeed" class="linkRegular"><span class="sprite-icon maleIconG"></span><span class="primarytext">Followers</span></a></li>
                            <li><a href="<?php echo base_url();?>profile?id=<?php echo $loggedInUserMember->id;?>#statFeed" class="linkRegular"><span class="sprite-icon statsIconG"></span><span class="primarytext">Stats</span></a></li>
                            <li><a href="<?php echo base_url();?>invite" class="linkRegular"><span class="sprite-icon profileIconG"></span><span class="primarytext">Invite</span></a></li>
                            <li><a href="<?php echo base_url();?>logout" class="linkRegular"><span class="sprite-icon settingIconG"></span><span class="primarytext">Logout</span></a></li>
                        </ul>
                    </div>
                </div>
            </li>
            <li id="startArgumentWrapper">
                <button class="primaryButton gradient" >
                    <i class="sprite-icon daIconOffW"></i>
                    <span id="startArgument">START AN ARGUMENT</span>
                </button>
            </li>


        </ul>

    </div>
    <?php if($loggedInUserMemberFlag):?>
 	<div id='loggedInMemberObj' class="loggedInMember {status: '<?php echo $loggedInUserMemberFlag;?>',memberId:'<?php echo $loggedInUserMember->id; ?>',profilephoto:'<?php echo $loggedInUserMember->profilephoto;?>',username:'<?php echo $loggedInUserMember->username;?>',gender:'<?php echo $loggedInUserMember->gender;?>',userEmail:'<?php echo $loggedInUserMember->email;?>',oauth_provider:'<?php echo $loggedInUserMember->oauth_provider;?>',oauth_uid:'<?php echo $loggedInUserMember->oauth_uid;?>',birthdate:'<?php echo $loggedInUserMember->birthdate;?>',location:'<?php echo $loggedInUserMember->location;?>',fullname:'<?php echo $loggedInUserMember->fullname;?>',notifyFlag:'<?php echo $loggedInUserMember->notifyFlag;?>',argumentCreatedCount:'<?php echo $loggedInUserMember->argumentCreatedCount;?>',followerCount:'<?php echo $loggedInUserMember->followerCount;?>',followedCount:'<?php echo $loggedInUserMember->followedCount;?>',argumentFollowCount:'<?php echo $loggedInUserMember->argumentFollowCount;?>',topicFollowCount:'<?php echo $loggedInUserMember->topicFollowCount;?>',participatedCount:'<?php echo $loggedInUserMember->participatedCount;?>',notificationCount:'<?php echo $loggedInUserMember->notificationCount;?>',profileThumb:'<?php echo $loggedInUserMember->profileThumb;?>',fromThirdParty:'<?php echo $loggedInUserMember->fromThirdParty;?>'}"></div>
 	<?php endif;?>
 	<div id='apiData' class="loggedInMember {fb_api: '<?php echo $this->config->item('FB_API_ID');?>',tw_api: '<?php echo $this->config->item('TW_API_KEY');?>'}"></div>
    <div id="mainContainer">
        <div style="margin:0 auto;width:600px">
            <div id="fb-root"></div>
        </div>

