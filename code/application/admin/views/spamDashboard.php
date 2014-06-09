<div id="container" class="right-container">
	<div id="spamWrapper">
		<div class="spamContainer">
			<h3>Reported Arguments</h3>
			<?php if($argumentList): ?>
			<ul class="spamList">
				<?php foreach ($argumentList as $argument):?>
				<li><a href="javascript:void('0');" class="heading5Link contentBody {id:'<?php echo $argument->id;?>',recordId:'<?php echo $argument->recordId;?>',type:'argument'}"><?php echo $argument->recordId;?></a></li>
				<?php endforeach;?>
			</ul>
			<?php else: ?>
				<div>No Arguments</div>	
			<?php endif;?>
		</div>
		<div class="spamContainer">
			<h3>Reported Comments</h3>
			<?php if($commentList): ?>
			<ul class="spamList">
				<?php foreach ($commentList as $comment):?>
				<li><a href="javascript:void('0');" class="heading5Link contentBody {id:'<?php echo $comment->id;?>',recordId:'<?php echo $comment->recordId;?>',type:'comment'}"><?php echo $comment->recordId;?></a></li>
				<?php endforeach;?>
			</ul>
			<?php else: ?>
				<div>No Comments</div>	
			<?php endif;?>
		</div>
	</div>
	<div id="spamDetailWrapper">
		<div id="actionContainer"><button class="primaryButton gradient" id="deleteSpam">Delete Record</button><button class="primaryButton gradient" id="unSpam">Not Spam</button></div>
		<div id="spamDetailContainer"></div>
	</div>
</div>