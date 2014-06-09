<?php if($dateRangeFlag):?>
<div id='date-info-container' style='margin:20px 25px 0 0;text-align:right;' class='lable'><?php echo date_format(DateTime::createFromFormat('Y-m-d', $fromDate),'M d, Y'); ?> - <?php echo date_format(DateTime::createFromFormat('Y-m-d', $toDate),'M d, Y'); ?></div>
<?php endif;?>
<div id="leftContainer">
	<ul>
		<li id="stats"><a href="<?php echo base_url();?>index.php/dashboard?fromDate=<?php echo $fromDate;?>&toDate=<?php echo $toDate;?>" class="linkstrong heading6">Dashboard</a></li>
		<li id="args"><a href="<?php echo base_url();?>index.php/argumentdashboard?fromDate=<?php echo $fromDate;?>&toDate=<?php echo $toDate;?>" class="linkstrong heading6">Arguments</a></li>
		<li id="users"><a href="<?php echo base_url();?>index.php/dashboard/users?fromDate=<?php echo $fromDate;?>&toDate=<?php echo $toDate;?>" class="linkstrong heading6">Users</a></li>
		<li id="spams"><a href="<?php echo base_url();?>index.php/dashboard/spam?fromDate=<?php echo $fromDate;?>&toDate=<?php echo $toDate;?>" class="linkstrong heading6">Spam</a></li>
		<li id="ints"><a href="<?php echo base_url();?>index.php/dashboard/interests?fromDate=<?php echo $fromDate;?>&toDate=<?php echo $toDate;?>" class="linkstrong heading6">Interests</a></li>
		<!--  <li><a href="<?php echo base_url();?>index.php/argumentdashboard" class="linkstrong heading6">Test</a></li> -->
	</ul>
	
	<div class="lable" style="margin-top:10px;border-top:1px solid #F0E9E1;clear:both;padding-top:10px;padding-bottom:10px;">Date Range</div>
	<div style="clear:both;padding-left:10px;" id="">
		<form id="datesform" action="" method="post">
			<div class="linkStrong heading6" style="padding-left:15px;margin:0">From</div>
			<span><input id="fromdatepicker" type="text" name="fromdate" value=""></span>
			<div class="linkStrong heading6" style="padding-left:15px;margin:0">To</div>
			<span><input id="todatepicker" type="text" name="toDate" value=""></span>
			<input type="hidden"  name="backendFromDate" id="alt-fromdate">
			<input type="hidden" name="backendToDate" id="alt-todate">
			<input type="button" value="Apply" class="primaryButton gradient linkstrong heading6 disabled" id="date-submit">
		</form>
	</div>
	
</div>
