<div id="container" class="right-container">
<?php if(count($userList) && $userList[0] != ""): ?>
	<ul id="userDashboard-list">
		<?php foreach ($userList as $user):?>
		<li><a href="javascript:void('0');" class="heading5 contentBody {id:'<?php echo $user->id?>',createdTime:'<?php echo $user->createdTime?>',email:'<?php echo $user->email?>',lastLoginTime:'<?php echo $user->lastloggedin;?>'}"><?php echo $user->username;?></a></li>
		<?php endforeach;?>
	</ul>
	<div id="userInfo"></div>
	<div id="lastActionInfo">
		
	</div>
	<?php else: ?>
	<div style='text-align:center'>No Users</div>
	<?php endif; ?>
</div>
