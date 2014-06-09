
<div id="container">
	<div class="interest-stats right-container">
		<h3 class="interest-heading">Interests</h3>
		<div class="interest-list">
			<?php foreach ($interestList as $interest): ?>
				<div class="interestname interest {id: '<?php echo $interest->id;?>', topic: '<?php echo $interest->topic;?>'}" style="cursor:pointer;"><span><?php echo $interest->topic;?></span><span class="interest-edit-icon sprite-icon edit-interest"></span><span class="interest-delete-icon sprite-icon delete-interest"></span></div>
			<?php endforeach;?>
		</div>

	</div>
	<div class="interest-form-stats"> 
		<h3 class="interest-heading">Create Interest </h3>
		<form method="post" action="<?php echo base_url(); ?>index.php/dashboard/uploadTopic" id="interest-form">
            <fieldset>
			<label class="lable" for="new-interest-input">Interest</label>
			<input id="new-interest-input" class="placeholder defaultContent {defaultText : 'Enter Interest'} validate required" type="text" name="topic">
			<input type="submit" id="postNewInterest" class="primaryButton gradient button" style="float:none;" value="Save">
			<span id="new-interest-avalability-check" class="checkAvailability available secondaryText"></span>
            </fieldset>
		</form>
		
		
		<!--  <form action="<?php echo base_url(); ?>index.php/dashboard/editTopic" id="interest-edit-form" style="display:none; margin-top:20px;border-top:1px solid #8C9AA1" method="post">
			<h3 class="interest-heading">Edit/delete Interest </h3>
			<label class="lable">Interest</label>
			<input id="editInterestTitle" class="" type="text" name="topic">
			<input type="hidden" name="id" value="" id="editInterestId">
			<input type ="submit" id="postEditInterest" class="primaryButton gradient interest {id :''}" style="float:none;" value="Save"/>
			<input type="button" class="primaryButton gradient" style="float:none;" value="Delete"/>
			<input type="button" value="cancel" id="cancelEditform"/>
		</form> -->
	
	</div>
</div>