<div id="argument-container" class="right-container">
<?php if(count($argumentList) && $argumentList[0] != ""): ?>
	<ul id="argumentDashboard-list">
		<?php foreach ($argumentList as $argument):?>
		<li><a href="javascript:void('0');" class="heading6 contentBody {id:'<?php echo $argument->id?>',createdtime:'<?php echo $argument->createdtime;?>',status:'<?php echo $argument->status?>'}"><?php echo $argument->title;?></a><div class="argument-circle"><a href="/detail?id=<?php echo $argument->id?>" target="_blank" style="margin:0; padding:0; float:none;"><span class="argument-read-icon sprite-icon watchIconG"></span></a></div></li>
		<?php endforeach;?>
	</ul>
	<div id="argumentInfo">
		<div class='argument-info-firsttable secondaryContainer heading6'>
			
			
		</div>
		<div class="argument-info-secondtable"><ul></ul></div>
	</div>

	<?php else: ?>
	<div style='text-align:center'>No Arguments</div>
	<?php endif; ?>
</div>
