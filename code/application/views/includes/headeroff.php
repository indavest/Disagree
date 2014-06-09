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
    <meta http-equiv="last-modified" content="Mon, 03 Jan 2011 17:45:57 GMT" />
    <!--<meta charset="utf-8" />-->
    <?php /*header('Last-Modified: '.gmdate('D, d M Y H:i:s', strtotime("Sat, 1 Sep 2000 15:26:00")).' GMT');*/?>
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
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.metadata.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/class/validator.js"></script>
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
    <!--<button class="primaryButton gradient" id="inviteFriendsOverlay">Invite Friends</button>-->
    <div id="header">
        <a id="logoLink" href="<?php echo base_url();?>welcome" >
            <img src="<?php echo base_url();?>images/disagree-logo.png" alt="Disagree.me Site Logo" width="149" height="32"/>
            <span class="betalogotext secondaryText">beta</span>
        </a>
        <ul id="headerMenu" class="horizontalMenu">
            <li id="headerAbout">
                <a href="<?php echo base_url();?>about" class="linkRegular primarytext">About Us</a>
            </li>
            <li id="headerContact">
                <a href="<?php echo base_url();?>contactUs" class="linkRegular primarytext">Contact</a>
            </li>
            <li id="startArgumentWrapper">
                <button class="primaryButton gradient">
                    <i class="sprite-icon daIconOffW"></i>
                    <span id="startArgument">START AN ARGUMENT</span>
                </button>
            </li>
            <li id="headerSignOn">
                <div class="signIn">Sign In</div>
                <div id="createAccount">Create New Account</div>
            </li>
        </ul>

    </div>
    <div id="mainContainer">