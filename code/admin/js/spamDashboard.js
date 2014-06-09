jQuery(document).ready(function(){
	init();
	jQuery(".spamList>li").live('click',function(){
		jQuery(".spamList>li").removeClass('active');
		var metaData = jQuery(this).children('a').metadata();
		jQuery(this).addClass('active');
		loadSpamDetails(metaData);
	});
	
	jQuery("#unSpam").live('click', function(){
		var spamElement =  jQuery(".spamList>li.active");
		var metaData = jQuery(".spamList>li.active>a").metadata();
		jQuery.ajax({
			url:'markNotSpam',
			dataType:'json',
			type:'post',
			data:{recordId:metaData.recordId,id:metaData.id,type:metaData.type},
			success: function(result){
				if(result && result.response){
					jQuery(spamElement).remove();
					init();
				}
			}
		});
	});
	
	jQuery("#deleteSpam").live('click', function(){
		var spamElement =  jQuery(".spamList>li.active");
		var metaData = jQuery(".spamList>li.active>a").metadata();
		jQuery.ajax({
			url:'deleteRecord',
			dataType:'json',
			data:{type:metaData.type,recordId:metaData.recordId,id:metaData.id},
			type:'post',
			success: function(result){
				if(result && result.response){
					jQuery(spamElement).remove();
					init();
				}
			}
		});
	});
});

function init(){
	var spamElement = null;
	if(jQuery("#spamWrapper>.spamContainer:first-child>.spamList>li:first-child").length > 0){
		jQuery("#spamWrapper>.spamContainer:first-child>.spamList>li:first-child").addClass('active');
		spamElement = jQuery("#spamWrapper>.spamContainer:first-child>.spamList>li:first-child>a");
	}else if(jQuery("#spamWrapper>.spamContainer>.spamList>li:first-child").length > 0){
		jQuery("#spamWrapper>.spamContainer>.spamList>li:first-child").addClass('active');
		spamElement = jQuery("#spamWrapper>.spamContainer>.spamList>li:first-child>a");
	}else {
		jQuery("#spamDetailWrapper").hide();
		return false;
	}
	
	loadSpamDetails(jQuery(spamElement).metadata());
}

function loadSpamDetails(metaData){
	jQuery.ajax({
		url:'spamDetail',
		dataType:'json',
		type:'post',
		data:{type:metaData.type,recordId:metaData.recordId},
		success: function(result){
			if(result && result.response){
				var spamRecordHtml = "";
				var recordData = result.data;
				if(metaData.type == 'argument'){
					spamRecordHtml += '<div class="linkStrong">Title:</div><div class="contentBody heading5">'+recordData.title+'</div><div class="linkStrong">OPINION:</div><div class="contentBody heading5">'+recordData.argument+'</div>';
				}else{
					spamRecordHtml += '<div class="linkStrong">Comment:</div><div class="contentBody heading5">'+recordData.commenttext+'</div>';
				}
				jQuery("#spamDetailContainer").html(spamRecordHtml);
			}
		}
	});
}