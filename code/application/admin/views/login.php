<script type="text/javascript">
 var loginfailed = '<?php echo $login_failed;?>';;
</script>
<div class="popupContent primarytext loginContainer">
	<h2>Login</h2>
	
	<form action="<?php echo base_url();?>index.php/login/authenticate" method="post">
        <fieldset>
		<label class="lable" for="admin_email">Email</label>
		<input type="text" class="placeholder defaultContent {defaultText : 'Type your Argument Title here'} validate required" id="admin_email" name="admin_email"/>
        <i class="sprite-icon  atIconG"></i>
        <label for="admin_password" class="lable">Password</label>
		<input type="password" class="placeholder defaultContent {defaultText : 'Type your Argument Title here'} validate required" id="admin_password" name="admin_password"/>
        <i class="sprite-icon lockOnG"></i>
		<button class="primaryButton gradient" id="postNewArgButton"><span>LOGIN</span></button>
        </fieldset>
	</form>
</div>
