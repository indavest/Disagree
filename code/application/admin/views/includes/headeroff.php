<?php ob_start(); ?>
<?php echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="keywords" content=""/>
    <meta name="description" content="/"/>
    <meta name="author" content=""/>
    <meta name="copyright" content=""/>
    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>images/favicon.png"><!-- Major Browsers -->
    <!--[if IE]><link rel="SHORTCUT ICON" href="<?php echo base_url(); ?>images/favicon.png"/><![endif]--><!-- Internet Explorer-->
    <link href="<?php echo base_url();?>css/admin.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>css/DA-Sprite.css" rel="stylesheet" type="text/css"/>
      <link href="<?php echo base_url();?>css/jquery.jgrowl.css" rel="stylesheet" type="text/css"/> 
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.jgrowl_google.js"></script>
    <?php if(isset($jsList)):?>
		<?php foreach ($jsList as $js):?>
		    <script src="<?php echo base_url();?>js/<?php echo $js;?>.js" type="text/javascript"></script>
		<?php endforeach;?>
	<?php endif;?>
	<script type="text/javascript">
    <!--
	    var AD = {
    	    'loginfailedmsg': '<?php echo LOGIN_FAILED; ?>'
    	}
	-->
    </script>
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
        <a href="/index.php">
            <img src="<?php echo base_url();?>images/disagree-logo.png" alt="Disagree.me Site Logo" width="149" height="32"/>
        </a>
        <div style="float:right;"><h3 style="margin:0">Admin Console</h3></div>
    </div>
    <div id="mainContainer">