jQuery(document).ready(function(){
	/*** On Load ***/
	
	argmentVariblesInit();
	argumentBashboardInit();
	
	/*** On Load ***/

	jQuery("#argumentDashboard-list>li>a").live('click', function() {
		argmentVariblesInit();

		jQuery(".argument-info-secondtable > ul").html("");
		jQuery("#argumentDashboard-list>li>a.active").siblings().removeClass("argument-circle-active");
		jQuery("#argumentDashboard-list>li>a").removeClass('active');
		jQuery("#argumentDashboard-list>li>a").removeAttr('style');
		var metadata = jQuery(this).metadata();
		var input = {};
		input.id = metadata.id;
		input.status = metadata.status;
		input.createdtime = metadata.createdtime;
		window.currentArgument = input;
		jQuery(this).addClass('active');
		jQuery(this).siblings().addClass("argument-circle-active");
		loadArgumentData(input);
		jQuery("#comment-coloumn").addClass("argument-info-active");
		loadComments(input,true);
		if(jQuery(window).scrollTop() >= 100) {
			jQuery(window).scrollTop(100);
		}
	});

	jQuery("#comment-coloumn").live('click', function() {
		argmentVariblesInit();
		jQuery(".argument-Info-firsttable-secondcolumn").removeClass("argument-info-active");
		jQuery(".argument-Info-firsttable-firstcolumn").removeClass("argument-info-active");
		jQuery(this).addClass("argument-info-active");
		var metadata = jQuery(this).children(':first-child').metadata();
		var input = {};
		input.id = metadata.argumentId;
		loadComments(input,true);
	});
	
	
	jQuery("#argument-lastaction").live('click', function() {
		jQuery(".argument-Info-firsttable-secondcolumn").removeClass("argument-info-active");
		jQuery(".argument-Info-firsttable-firstcolumn").removeClass("argument-info-active");
		jQuery(this).addClass("argument-info-active");
		var metadata = jQuery(this).children(':first-child').metadata();
		var input = {};
		input.id = metadata.id;
		input.activitytype = metadata.type;
		input.argumentId = metadata.argumentId;
		input.lastmodified = metadata.lastmodified;
		jQuery.ajax({
			url:AD.base_url+'index.php/argumentdashboard/argumentLatestAction',
			data: input,
			dataType: 'json',
			type: 'post',
			beforeSend: function() {
				
				jQuery(".argument-info-secondtable > ul").html('<div style="text-align:center"><img alt="loading..." src="/images/da-loader.gif" style="text-align: center;"/></div>');
			},
			success: function(result){ 
				if(result.response){
					var lastAction = result.data.lastAction[0];
					var lastActionHtml = "";
					var argumenthtml = "";
					var profilelink = "<a class = 'heading5Link' href='/profile?id="+lastAction.id+"' target = '_blank' style='float:none;'>"+lastAction.username+"</a>"; 
				
					switch (input.activitytype)
					{
						case AD.argument_ra_status_change: 
							if(lastAction.argumentstatus == 0) {
								lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Locked argument by "+profilelink+"</span></div>";
							} else lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Unlocked argument by "+profilelink+"</span></div>";
							
							break;
						case AD.argument_ra_comment: 
							if(lastAction.parentId == null && lastAction.parentcomment == null) { 
								 if(lastAction.uservote == 1 || lastAction.uservote == 0) { 
									 if(lastAction.commenttext == null) { lastAction.commenttext = "No comment"; }
										lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Voted by "+profilelink+"</span></div>";
										if(lastAction.vote == 1) {
											lastActionHtml = lastActionHtml + "<div class='heading6'><span>Vote : </span><span>Agreed</span></div>";
										} else {
											lastActionHtml = lastActionHtml + "<div class='heading6'><span>Vote : </span><span>Agreed</span></div>";
										}
										lastActionHtml  = lastActionHtml + "<div class='heading6'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div>";
										break;
									 
								 } else {
									 lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Commented by <a class = 'heading5Link' href='/profile?id="+lastAction.userid+"' target = '_blank' style='float:none;'>"+lastAction.username+"</a></span></div><div class='heading6'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div>";
								 }
								
							} else {
						
								lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Reply for an argument comment by <a class = 'heading5Link' href='/profile?id="+lastAction.userid+"' target = '_blank' style='float:none;'>"+lastAction.username+"</a></span></div><div class='heading6'><span>Comment : </span><span>"+lastAction.parentcomment+"</span></div><div class='heading6'><span>Reply : </span><span>"+lastAction.commenttext+"</span></div>";
								
							}
							break;
						case AD.argument_ra_spam_argument: 
							lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Argument reported as spam by "+profilelink+"</span></div>";
							break;

						case AD.argument_ra_spam_argument_comment: 
							lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Argument comment reported as spam by <a class = 'heading5Link' href='/profile?id="+lastAction.userid+"' target = '_blank' style='float:none;'>"+lastAction.username+"</a></span></div><div class='heading6'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div>";
							break;
						case AD.argument_ra_foollowed_by_member: 
							lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Followed by "+profilelink+"</span></div>";
							
							break;
						case AD.argument_ra_vote: 
							
							if(lastAction.commenttext == null) { 
								lastAction.commenttext = "No comment";
							}
							lastActionHtml = "<div class='heading6'><span>Last Activity : </span><span>Voted by "+profilelink+"</span></div>";
							if(lastAction.vote == 1) {
								lastActionHtml = lastActionHtml + "<div class='heading6'><span>Vote : </span><span>Agreed</span></div>";
							} else {
								lastActionHtml = lastActionHtml + "<div class='heading6'><span>Vote : </span><span>Disagreed</span></div>";
							}
							lastActionHtml  = lastActionHtml + "<div class='heading6'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div>";
							break;
						default : 
							lastActionHtml = "No Action";
							
					}
					//jQuery("#argument-lastaction-info").html(lastActionHtml);
					jQuery(".argument-info-secondtable > ul").html('<li style="padding:0 20px;">'+lastActionHtml+'</li>');
					
				
				}
			} 
		});
	});


	jQuery(".showReplyLink").live('click', function(event){
		var target =jQuery(event.target).closest(".showReplyLink");
	    jQuery(target).toggleClass("on off");
	    jQuery(target).children("i").toggleClass("commentSmallIconG commentSmallIconS");
	    
	    var commentId = jQuery(target).parent().attr("id");
	    commentId = commentId.split('-')[1];
	    if (jQuery(this).hasClass("on")) {
	        jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html(fetchReplies(commentId));
	        jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").attr('id',"replyContentWrapper-" + commentId);
	        jQuery("#replyContentWrapper-"+commentId).children(".wrapperData").attr('id',"wrapperData-" + commentId);
	        jQuery(this).siblings(".replyContentWrapper").show();
	    } else if (jQuery(this).hasClass("off")) {
	        jQuery(this).siblings(".replyContentWrapper").hide();
	    }
	    
	});  
	
	jQuery(window).scroll(function(){
		/*jQuery(window).scrollTop() == (jQuery(document).height() - jQuery(window).height()) => if this is true then scroll bar reach the end of the page, before reaching */
		if (!argumentCommentsIsLoading && jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height()-50)){
			commentLowerLimit = commentLowerLimit + commentsPerLoad;   
			if(hasMoreComments && commentLowerLimit >= 0) {
				loadComments(window.currentArgument,false);
			}   
		   }  
	});
	
});//EOF document ready

function fetchReplies(commentId) {
	jQuery.ajax({
		url:AD.base_url+'index.php/argumentdashboard/getReplies',
		type:"POST",
	    data:{commentId:commentId},
	    dataType:"json",
	        beforeSend:function () {
	            jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html('<div style="display: block;text-align:center; width: 100%;"><img  src="/images/da-loader.gif" alt="loading..."></div>');
	        },
	        success:function (res) {
	            if (res.data) {
	                jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html(adminObj.decodeEntities(loadTimelineCommentReply(res.data)));
	            } else {
	                jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html("<div style='display: block;text-align:center; width: 100%;'>No Replies Yet.</div>");
	            }
	           
	            if(jQuery("#replyContentWrapper-"+commentId).outerHeight(true) > 369){
	            	/*
	                 add for scroll func
	                 */
	                var comentReply = '<div class="scrollbar_track" id="scrollbar_track'+commentId+'"><div class="scrollbar_handle" id="scrollbar_handle'+commentId+'" ></div></div>';
	                jQuery('#wrapperData-'+ commentId).before((comentReply));
	                adminObj.initScroll('wrapperData-'+ commentId,'scrollbar_track'+commentId);
	            }
	        }
	    });
	
	
}
function loadTimelineCommentReply(replies){
    var comentReply ='<div class="wrapperData" id="wrapperData-'+ replies[0].parentId +'">';
    jQuery.each(replies,function(i,reply){
        comentReply += '<div class="commentReply">';
            comentReply += '<p class="argumentHead">';
                comentReply +='<span class="userImgCircleSmall" ><img src="'+reply.userImage+'" alt="user image" class="thirdPartyImgSmall"/></span>';
                comentReply +='<a href="/profile?id='+reply.memberId+'" class="username heading6Link secondaryText">'+adminObj.Ellipsis(((reply.fullname=='' || reply.fullname == null)?reply.username:reply.fullname),35)+'</a><span class="disabled">&nbsp;&nbsp;said</span>';
                comentReply +='<i class="smallText disabled timeStamp">'+adminObj.time_difference(reply.createdtime)+'<!--span class="dataSource">reply.source</span--></i>';
            comentReply +='</p>';
            comentReply +='<p class="argumentContent">'+reply.commenttext;
            comentReply +='</p>';
        comentReply += '</div>';
    });

    comentReply += '</div>';
    return comentReply;
}
function loadArgumentData(input){

	var argumentId = input.id;
	jQuery.ajax({
		url:AD.base_url+'index.php/argumentdashboard/argument',
		data: {id:argumentId},
		dataType: 'json',
		type: 'post',
		beforeSend: function() {
			jQuery(".argument-info-firsttable").html('<div style="text-align:center;line-height:216px;"><img alt="loading..." src="/images/da-loader.gif" style="text-align: center;vertical-align:middle;"/></div>');
			
		},
		success: function(result){
			if(result.response){
				var argument = result.data.argument;
				var lastAction = {};
				var maleAgreed = argument.maleAgreedCount != null ? argument.maleAgreedCount : 0;
				var femaleAgreed = argument.femaleAgreedCount != null ? argument.femaleAgreedCount : 0 ;
				var maleDisagreed = argument.maleDisagreedCount !=null ? argument.maleDisagreedCount:0;
				var femaleDisagreed = argument.femaleDisagreedCount !=null ? argument.femaleDisagreedCount:0;
				var totalAgreed = parseInt(maleAgreed) + parseInt(femaleAgreed);
				var totalDisagreed = parseInt(maleDisagreed) + parseInt(femaleDisagreed);
				var status = input.status ==1 ? "UnLocked" : "Locked";
				var cretaedTime = input.createdtime;
				var lastActionHtml = "";
				var reportCount = argument.reportCount;
				
				if(result.lastActionTimeresponse) {
					lastAction = result.data.lastAction;
					lastActionHtml = "<div class='argument-Info-firsttable-firstcolumn linkStrong' id='argument-lastaction'><span class='firstcolumn-left cell-left-padding data {lastmodified:\""+lastAction.lastactiontime+"\", id:\""+lastAction.id+"\",type:\""+lastAction.activitytype+"\",argumentId:\""+argumentId+"\"}'>Last Action</span><span class='firstcolumn-right cell-left-padding'>"+adminObj.time_difference(lastAction.lastactiontime)+"</span></div>"
				} else {
					lastActionHtml = "<div class='argument-Info-firsttable-firstcolumn'><span class='firstcolumn-left cell-left-padding'>Last Action</span><span class='firstcolumn-right cell-left-padding'>No Action</span></div>"
				}
				var argumentHtml = "<div class='argument-row arument-row-bottomborder'><div class='argument-Info-firsttable-firstcolumn'><span class='firstcolumn-left cell-left-padding'>Owner</span><span class='firstcolumn-right cell-left-padding'><a href='/profile?id="+argument.userId+"' target='_blank' class='linkRegular' style='line-height:30px;'>"+adminObj.Ellipsis(argument.userName,8)+"</a></span></div><div class='argument-Info-firsttable-secondcolumn'><span class='secondcolumn-left cell-left-padding'>Total views</span><span class='secondcolumn-right cell-left-padding'>Not yet Done</span></div></div>" +
						"<div class='argument-row arument-row-bottomborder'><div class='argument-Info-firsttable-firstcolumn'><span class='firstcolumn-left cell-left-padding'>Created Time</span><span class='firstcolumn-right cell-left-padding'>"+adminObj.time_difference(cretaedTime)+"</span></div><div class='argument-Info-firsttable-secondcolumn'><span class='secondcolumn-left cell-left-padding'>Status</span><span class='secondcolumn-right cell-left-padding'>"+status+"</span></div></div><div class='argument-row arument-row-bottomborder'>"+lastActionHtml+
								"<div class='argument-Info-firsttable-secondcolumn linkStrong' id='comment-coloumn'><span class='secondcolumn-left cell-left-padding data {argumentId:\""+argumentId+"\"}'>Total Comments</span><span class='secondcolumn-right cell-left-padding'>"+argument.commentCount+"</span></div></div>"+
						"<div class='argument-row arument-row-bottomborder'><div class='argument-Info-firsttable-firstcolumn'><span class='firstcolumn-left cell-left-padding'>Total Agreed</span><span class='firstcolumn-right cell-left-padding'>"+totalAgreed+"</span></div><div class='argument-Info-firsttable-secondcolumn'><span class='secondcolumn-left cell-left-padding'>Male Agreed</span><span class='secondcolumn-right cell-left-padding'>"+maleAgreed+"</span></div></div>"+
				"<div class='argument-row arument-row-bottomborder'><div class='argument-Info-firsttable-firstcolumn'><span class='firstcolumn-left cell-left-padding'>Female Agreed</span><span class='firstcolumn-right cell-left-padding'>"+femaleAgreed+"</span></div><div class='argument-Info-firsttable-secondcolumn'><span class='secondcolumn-left cell-left-padding'>Total Disagreed</span><span class='secondcolumn-right cell-left-padding'>"+totalDisagreed+"</span></div></div>"+
				"<div class='argument-row arument-row-bottomborder'><div class='argument-Info-firsttable-firstcolumn'><span class='firstcolumn-left cell-left-padding'>Male Disagreed</span><span class='firstcolumn-right cell-left-padding'>"+maleDisagreed+"</span></div><div class='argument-Info-firsttable-secondcolumn'><span class='secondcolumn-left cell-left-padding'>Female Disagreed</span><span class='secondcolumn-right cell-left-padding'>"+femaleDisagreed+"</span></div></div>"+
				"<div class='argument-row'><div class='argument-Info-firsttable-firstcolumn'><span class='firstcolumn-left cell-left-padding'>Favorites</span><span class='firstcolumn-right cell-left-padding'>"+argument.favoriteCount+"</span></div><div class='argument-Info-firsttable-secondcolumn'><span class='secondcolumn-left cell-left-padding'>Reports</span><span class='secondcolumn-right cell-left-padding'>"+reportCount+"</span></div></div>";
				jQuery(".argument-info-firsttable").html(argumentHtml);
				jQuery("#comment-coloumn").addClass("argument-info-active");
			}
			
		}
	});
}
function loadComments(input, isFirstLoad) {
	if(hasMoreComments && commentLowerLimit >= 0) {
	jQuery.ajax({
		url: AD.base_url+'index.php/argumentdashboard/getComments',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			if(isFirstLoad) {
				jQuery(".argument-info-secondtable > ul").html('<div style="text-align:center"><img alt="loading..." src="/images/da-loader.gif" style="text-align: center;"/></div>');
			} else {
				jQuery(".argument-info-secondtable > ul").append('<div style="text-align:center"><img alt="loading..." src="/images/da-loader.gif" style="text-align: center;"/></div>');
			}
			
			argumentCommentsIsLoading = true;
		},
		data: {argumentId:input.id,lowerLimit:commentLowerLimit,noofrecords:commentsPerLoad},
		success: function(res) {
			
			var commenthtml = adminObj.decodeEntities(loadTimelineBatchComment(res.data.comments, res.data.replyCount));
			
			jQuery(".argument-info-secondtable > ul > div >img").remove();
			if(isFirstLoad) {
				jQuery(".argument-info-secondtable > ul").html(commenthtml);
			} else {
				if(res.data.comments.length != undefined)
				jQuery(".argument-info-secondtable > ul").append(commenthtml);
			}
			
		
			if(res.data.comments.length < commentsPerLoad || res.data.comments.length == undefined) {
				commentLowerLimit = -1;
				hasMoreComments = false;
			} 
			
		},
		complete: function() {
			argumentCommentsIsLoading = false;
		}
		
	});
	}
}

function loadTimelineBatchComment(comments,replies){        //prepares coplete timeline graphcontent html element;input : commentsList, repliesList, is this first call to this function (true/false);output: complete graphContent
    var commenthtml = '';
    if(comments!=false && comments != null && comments !=undefined && comments.length>0 ){
    jQuery.each(comments,function(i,comment){
        var actionString = (comment.uservote == window.agreeVoteID || comment.uservote == window.disagreeVoteID)?"voted":"commented";
        var dirClass = (comment.uservote == window.agreeVoteID || comment.uservote == window.agreeCommentID)?"data-l":"data-r";
    commenthtml += '<li class="'+dirClass+' secondaryContainer" id="c'+comment.id+'">';
        commenthtml += '<div class="timelineArgument">';
            commenthtml += '<p class="argumentHead">';
                commenthtml += '<span class="userImgCircleSmall" ><img src="'+comment.userImage+'" alt="user image" class="thirdPartyImgSmall"/></span>';
                commenthtml += '<a href="/profile?id='+comment.memberId+'" class="DAtip up username heading6Link secondaryText ">'+((comment.fullname==''||comment.fullname==null)?comment.username:comment.fullname)+'</a><span class="disabled voteActionString">&nbsp;&nbsp;'+actionString+' </span>';
                commenthtml += '<i class="smallText disabled timeStamp">'+adminObj.time_difference(comment.createdtime)+'</i>';
            commenthtml += '</p>';
            commenthtml += '<p class="argumentContent">'+comment.commenttext+'</p>';

            commenthtml += '<div class="replyBox disabled" id="commentReply-'+comment.id+'">';
                commenthtml += '<span class="showReplyLink off" id="replyViewSwitch-'+comment.id+'"><span >'+replies[comment.id]+'</span><i class="sprite-icon commentSmallIconG"></i></span>';
                commenthtml += '<div class="replyContentWrapper form">';
                commenthtml += '</div>';
            commenthtml += '</div>';
            commenthtml += '<div class="timelineTip"></div>';
        commenthtml += '</div>';
       
    commenthtml += '</li>';
    });
 
    }else{
        commenthtml += '<li class="data-l secondaryContainer noCommets"><div class="timelineArgument">';
        commenthtml += '<div style="padding: 15px 0;">There are no comments yet for this argument.</div>';
        commenthtml += '<div class="timelineTip"></div></div></li>';
      
    }
    return commenthtml;
}


function argumentBashboardInit() {
	argmentVariblesInit();
	jQuery("#argumentDashboard-list>li:first-child a").addClass('active');
	jQuery("#argumentDashboard-list>li:first-child a").siblings().addClass("argument-circle-active");
	var metadata = jQuery("#argumentDashboard-list>li:first-child>a").metadata();
	
	var input = {};
	input.id = metadata.id;
	input.status = metadata.status;
	input.createdtime = metadata.createdtime;
	window.currentArgument = input;
	loadArgumentData(input);
	
	loadComments(input,true);
	jQuery("#argumentDashboard-list>li").hover( 
			
			function () {
				
				if(!jQuery(this).children('a:first-child').hasClass("active")){
					jQuery(this).children('a:first-child').attr('style','background-color:#F0E9E1;border-radius:6px');
					//jQuery(this).children('a:first-child').css('border-radius','6px');
					jQuery(this).children('.argument-circle').addClass("argument-circle-active");
				}
					
			  }, function () {
				  if(!jQuery(this).children('a:first-child').hasClass("active")) {
					  jQuery(this).children('a:first-child').removeAttr('style');
					  jQuery(this).children('.argument-circle').removeClass("argument-circle-active"); 
				  }

			  }
	);
}

function argmentVariblesInit() {
	window.argumentCommentsIsLoading = false;
	window.commentLowerLimit = 0;
	window.commentsPerLoad = 10;
	window.hasMoreComments = true;
}

