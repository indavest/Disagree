jQuery(document).ready(function() {
	jQuery.noConflict();
	jQuery("#leftContainer").css('margin-top','30px');
	jQuery("#leftContainer").next().css('margin-top','30px');
	var editInterestclick = false;
	var interests = new Array();
	interests = jQuery.parseJSON(AD.interests);

	var interestnames = new Array();
	jQuery.each(interests, function(index,interest) {
		interestnames.push(interests[index].topic.toLowerCase());
	});
	/*for (var index in interests) { 	
		
		interestnames.push(interests[index].topic.toLowerCase());
	}*/
	var editinterestnames = interestnames;
	var editInterestclick = false;
	var checkedNewInterestname = false;

	jQuery("#postNewInterest").click(function(e) {
		e.stopPropagation(); 
		if(jQuery.inArray(jQuery("#new-interest-input").val().toLowerCase(), interestnames) != -1) {
			 jQuery("#new-interest-avalability-check").html('unavailable');
			 jQuery("#new-interest-input").addClass('na');
			 return false;
		}
		 jQuery("#new-interest-avalability-check").html('');
		 jQuery("#new-interest-input").removeClass('na');
		return true;
	});
	jQuery('#new-interest-input').focus(function() {
		 jQuery("#new-interest-avalability-check").html(''); 
	});
	jQuery(".edit-interest").live("click",function(){
		editInterestclick = closeotherEditForms(editInterestclick);
		var input = jQuery(this).parent().metadata();
	
		var index = editinterestnames.indexOf(input.topic.toLowerCase());
		editinterestnames.splice(index, 1);
		
		
		jQuery(this).parent().replaceWith('<form class="interest-edit-form" action="'+AD.base_url+'index.php/dashboard/editTopic" method="post"><input type="text" name="topic" id="input-field" value="'+input.topic+'" class="placeholder defaultContent {defaultText : \'Enter Interest\'} validate required"/><input type="hidden" name="id" value="'+input.id+'"><span class="sprite-icon interest-save-icon" id="interest-save-button"></span><span class="sprite-icon interest-cancel-icon interest{id:\''+input.id+'\',topic:\''+input.topic+'\'}" id="interest-cancel-button"></span></form>');
	});
	
	jQuery(".delete-interest").live("click", function() {
		editInterestclick = closeotherEditForms(editInterestclick);
		var input = jQuery(this).parent().metadata();
		var r = confirm("Are sure you want to remove Interest "+input.topic);
		if(r == true) {
			var topicId = input.id;
			jQuery.ajax({
				url: AD.base_url+'index.php/dashboard/deleteTopic',
				data: {id:topicId},
				dataType: 'json',
				type: 'post',
				success: function(result) {
					var url = AD.base_url+"index.php/dashboard/interests";
					window.location = url;
				},
				error: function() {
					var url = AD.base_url+"index.php/dashboard/interests";
					window.location = url;
				}
				
			});
		}
	});
	
	
	
	jQuery("#interest-cancel-button").live('click',function() {
		var input = jQuery(this).metadata();
		editinterestnames.push(input.topic.toLowerCase());
		jQuery(this).parent().replaceWith('<div class="interestname interest {id: \''+input.id+'\', topic: \''+input.topic+'\'}" style="cursor:pointer;"><span>'+input.topic+'</span><span class="interest-edit-icon sprite-icon edit-interest"></span><span class="interest-delete-icon sprite-icon delete-interest"></span></div>');
	});
	jQuery("#interest-save-button").live('click',function() {
		jQuery("#input-field").removeClass('error');
		var editinterestnames = interestnames;
		var interestval = jQuery(".interest-edit-form #input-field").val();
		var status = window.validationEngine.validateForm(jQuery(this).parent());
		
		if(jQuery.inArray(jQuery("#input-field").val().toLowerCase(), interestnames) != -1) {
			jQuery("#input-field").addClass('error');
			return false;
		}
		
		if(status) {
			jQuery(this).parent().submit();
		}
	});
	
});

function closeotherEditForms(editInterestclick) {
	if(!editInterestclick) {
		editInterestclick = true;
		
	}
	else {
		jQuery('.interest-edit-form').each(function() {
			jQuery("#interest-cancel-button").trigger('click');
		});
	}
	return editInterestclick;
}


/*function interesHtml(elements) {
	if(!elements) {
		jQuery(".interest-stats").html("<h3 class='interest-heading'>Interests</h3><div class='interest-list'>No Interests</div>");
		return;
	} 
	var divofInterests = '';
	for (var index in elements) {
		var data = elements[index];
		divofInterests = divofInterests+'<div class="interestname interest {id: \''+data.id+'\', topic: \''+data.topic+'\'}" style="cursor:pointer;"><span>'+data.topic+'</span><span class="interest-edit-icon sprite-icon edit-interest"></span><span class="interest-delete-icon sprite-icon delete-interest"></span></div>';
	}
	var interesthtml ='<div class="interest-stats" class="right-container"><h3 class="interest-heading">Interests</h3><div class="interest-list">'+divofInterests+'</div></div>';
	jQuery(".interest-stats").html(interesthtml);
}*/

/*function loadTopics() {
	jQuery.ajax({
		url:'ajaxinterests',
		dataType: 'json',
		type: 'get',
		success: function(result){ 
			if(result.response){
				var data = result.data;
				var html = "";
				for each (var item in data) {
					html += '<li class="interestname interest {id: \''+item.id+'\', topic: \''+item.topic+'\'}">'+ item.topic +'</li>';			
				}
			}
			jQuery(".interest-list").html(html);
		}
	});
}*/
/*jQuery(".interestname").live('click', function(){
	jQuery("#interest-edit-form").show();
	jQuery("#editInterestTitle").val(jQuery(this).metadata().topic);
	jQuery("#editInterestId").val(jQuery(this).metadata().id);
	
	
});
*/