<div id="container" class="right-container">	
	<div class="newDashboardStats primaryText">
		<div class="box box-margin"> 
			
			<div class="box-title obtuseGradient"><span class="box-text"><span class="box-icon sprite-icon profileIconG"></span><span>Total Users</span></span></div>
			<div class="box-content" id="user-count"><a href="<?php echo base_url();?>index.php/dashboard/users?fromDate=<?php echo $fromDate;?>&toDate=<?php echo $toDate;?>" class="linkStrong box-content" style="width:100%"><?php echo $dashboardData->userCount;?></a></div>
		</div>
		<div class="box box-margin">
			<div class="box-title obtuseGradient"><span class="box-text"><span class="box-icon sprite-icon daIconOnG"></span>Total Arguments</span></div>
			<div class="box-content" id="argument-count"><a href="<?php echo base_url();?>index.php/dashboard/arguments?fromDate=<?php echo $fromDate;?>&toDate=<?php echo $toDate;?>" class="linkStrong box-content" style="width:100%"><?php echo $dashboardData->argumentCount;?></a></div>
		</div>
		<div class="box">
			<div class="box-title obtuseGradient"><span class="box-text"><span class="box-icon sprite-icon daIconOffG"></span>Total Comments</span></div>
			<div class="box-content" id="comment-count"><?php echo $dashboardData->commentCount;?></div>
		</div>
		<div style="width:100%; height:20px;clear:both;"></div>
		<div class="box box-margin"> 
			<div class="box-title obtuseGradient"><span class="box-text"><span class="box-icon sprite-icon profileIconG"></span>Total Agrees</span></div>
			<div class="box-content" id="agreed-count"><?php echo ($dashboardData->agreedCount != null) ? $dashboardData->agreedCount : 0; ?></div>
		</div>
		<div class="box box-margin">
			<div class="box-title obtuseGradient"><span class="box-text"><span class="box-icon sprite-icon daIconOnG"></span>Total Disagrees</span></div>
			<div class="box-content" id="disagreed-count"><?php echo ($dashboardData->disagreedCount != null) ? $dashboardData->disagreedCount : 0; ?></div>
		</div>
		<div class="box">
			<div class="box-title obtuseGradient"><span class="box-text"><span class="box-icon sprite-icon daIconOffG"></span>Total Users Online</span></div>
			<div class="box-content" id="online-count"><?php echo $dashboardData->onlineCount;?></div>
		</div>
	</div>

</div>
