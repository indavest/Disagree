<?php ob_start(); ?>
<?php echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Disagree.me</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta http-equiv="content-type" content="text/html"/>
    <meta charset="utf-8" />
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="copyright" content="">
   <script type="text/javascript">
    <!--
	    var AD = {
	      'base_url': '<?php echo base_url(); ?>',
	      'interests' :'<?php echo json_encode($interests); ?>',
	      'argument_ra_status_change' : '<?php echo ARGUMENT_RECENT_ACTION_STATUS_CHANGE; ?>',
	      'argument_ra_comment' : '<?php echo ARGUMENT_RECENT_ACTION_COMMENT; ?>',
	      'argument_ra_spam_argument' : '<?php echo ARGUMENT_RECENT_ACTION_SPAM_ARGUMENT; ?>',
	      'argument_ra_spam_argument_comment' : '<?php echo ARGUMENT_RECENT_ACTION_SPAM_ARGUMENT_COMMENT; ?>',
	      'argument_ra_foollowed_by_member' : '<?php echo ARGUMENT_RECENT_ACTION_FOLLOWED_BY_MEMBER; ?>',
	      'argument_ra_vote' : '<?php echo ARGUMENT_RECENT_ACTION_VOTE_AN_ARGUMENT; ?>',
	      'fromDate' : '<?php echo $fromDate; ?>',
	      'toDate' : '<?php echo $toDate; ?>'
	    };
	-->
    </script>
    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>images/favicon.png"> <!-- Major Browsers -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>images/favicon.ico" type="image/x-icon">
    <!--[if IE]><link rel="SHORTCUT ICON" href="<?php echo base_url(); ?>images/favicon.png"/><![endif]--><!-- Internet Explorer-->
    <!-- Google Web Fonts-->
     <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/humanity/jquery-ui.css" rel="stylesheet" type="text/css"/>


    <link href="<?php echo base_url();?>css/admin.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>css/DA-Sprite.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/admin.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.metadata.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/class/validator.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>/js/php.js"></script>
	
		<?php if(isset($jsList)):?>
		<?php foreach ($jsList as $js):?>
		    <script src="<?php echo base_url();?>js/<?php echo $js;?>.js" type="text/javascript"></script>
		<?php endforeach;?>
	<?php endif;?>
	<script type="text/javascript" src="<?php echo base_url();?>js/Scrollbar.js"></script>
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
    <div id="header">
        <a href="<?php echo base_url();?>index.php" >
            <img src="<?php echo base_url();?>images/disagree-logo.png" alt="Disagree.me Site Logo" width="149" height="32"/>
        </a>
        <div style="float:right;margin-right:25px;line-height:32px;"><img src="<?php echo base_url();?>images/admin_login.png" alt="admin user"/><span style="float:right;">Welcome <span class="lable">Admin</span> ( <a href="<?php echo base_url();?>index.php/login/logout" style="float:none;font-weight:normal;color: #46322B;" class="primarytext linkRegular">Logout</a> )</span></div>
        <!--<div style="float: right;margin:7px 0 0 10px;"><a href="<?php echo base_url();?>index.php/login/logout">logout</a></div><div style="float:right;"><h3 style="margin:0;">Welcome Admin,</h3></div>-->
    </div>
    <div id="mainContainer">
