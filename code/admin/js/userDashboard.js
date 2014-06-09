jQuery(document).ready(function(){
	/*** On Load ***/
	/*jQuery("#userDashboard-list>li:first-child").addClass('active');
	var input = {};
	var metadata = jQuery("#userDashboard-list>li:first-child>a").metadata();
	input.id = metadata.id;
	input.email = metadata.email;
	input.createdTime = metadata.createdTime;
	input.lastLoginTime = metadata.lastLoginTime;
	loadUserData(input);*/
	userDashboardInit();
	/*** On Load ***/
	
	jQuery("#userDashboard-list>li").live('click', function(){
		jQuery("#lastActionInfo").html("");
		jQuery("#userDashboard-list>li").removeClass('active');
		var input = {};
		var metadata = jQuery(this).children('a').metadata();
		input.id = metadata.id;
		input.email = metadata.email;
		input.createdTime = metadata.createdTime;
		input.lastLoginTime = metadata.lastLoginTime;
		
		var userId = jQuery(this).children('a').metadata().id;
		jQuery(this).addClass('active');
		loadUserData(input);
		
		if(jQuery(window).scrollTop() >= 100) {
			jQuery(window).scrollTop(100);
		}
	});
	
	
	
	jQuery("#user-lastaction").live('click', function(){
		
		var metadata = jQuery(this).metadata();
		
		var input = {};
		input.id = metadata.id;
		input.type = metadata.type;
		input.userId = metadata.userId;
		input.lastmodified = metadata.lastmodified;
		jQuery.ajax({
			url:AD.base_url+'index.php/dashboard/userLatestAction',
			data: input,
			dataType: 'json',
			type: 'post',
			success: function(result){
				if(result.response){
					var lastAction = result.data.lastAction[0];
					var lastActionHtml = "";
					var argumenthtml = "";
					var viewindetailargument = "";
					
					viewindetailargument = "<div class='heading4'><span><a href='/detail?id="+lastAction.argumentid+"' target = '_blank' class='heading5Link' style='float:none;margin:0'>View in detail</a></span></div>";
					//argumenthtml = "<div class='heading4'><span>Title : </span><span>"+lastAction.title+"</span></div><div class='heading4'><span>Description : </span><span>"+lastAction.argument+"</span></div><div class='heading4'><span>Topic : </span><span>"+lastAction.topic+"</span></div><div class='heading4'><span>Status : </span><span>"+lastAction.status+"</span></div>";
					argumenthtml = "<div class='heading4'><span>Title : </span><span>"+lastAction.title+"</span></div>";
					switch (input.type)
					{
						case '1':
						  if(lastAction.lastmodified == lastAction.createdtime){
								lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Creating argument</span></div>"+argumenthtml+"<div class='heading4'><span><a href='/detail?id="+lastAction.id+"' target = '_blank' class='heading5Link' style='flaot:none;margin:0'>View in detail</a></span></div>";
							}
							else {
								if(lastAction.status == 0) {
									lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Locking argument</span></div>"+argumenthtml+"<div class='heading4'><span><a href='/detail?id="+lastAction.id+"' target = '_blank' class='heading5Link' style='flaot:none;margin:0'>View in detail</a></span></div>";
								} else lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Unlocking argument</span></div>"+argumenthtml+"<div class='heading4'><span><a href='/detail?id="+lastAction.id+"' target = '_blank' class='heading5Link' style='flaot:none;margin:0'>View in detail</a></span></div>";
								
							}
						  		break;
						case '2':
							var vote = "";
							if(lastAction.parentId == null && lastAction.parentcomment == null){
								
								 if(lastAction.uservote == 1 || lastAction.uservote == 0) {
									
									 lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Voting an argument</span></div><div class='heading4'><span>Title : </span><span>"+lastAction.title+"</span></div>";
										if(lastAction.vote == 1 ) {
											lastActionHtml = lastActionHtml + "<div class='heading4'><span>Vote : </span><span>Agreed</span></div>";
										} else {
											lastActionHtml = lastActionHtml + "<div class='heading4'><span>Vote : </span><span>Disgreed</span></div>";
										}
										lastActionHtml  = lastActionHtml + "<div class='heading4'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div>"+viewindetailargument;
									 
								 } else {
									 lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Commenting argument</span></div>"+argumenthtml+"<div class='heading4'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div>"+viewindetailargument;
								 }
									
								}
								else {
									lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Replying for argument comment</span></div>"+argumenthtml+"<div class='heading4'><span>Comment : </span><span>"+lastAction.parentcomment+"</span></div><div class='heading4'><span>Reply : </span><span>"+lastAction.commenttext+"</span></div>"+viewindetailargument;
									
								}
							  		break;
						 
						case '3': 
							if(lastAction.type == "argument") {
								lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Argument reported as spam</span></div>"+argumenthtml+""+viewindetailargument;
							}
							else {
								lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Argument comment reported as spam</span></div>"+argumenthtml+"<div class='heading4'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div>"+viewindetailargument;
							}
							
						  break;
						case '4': 
							lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Followed an Argument</span></div>"+argumenthtml+""+viewindetailargument;
						  break;
						case '5':
							
							lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Started following <a class = 'heading5Link' href='/profile?id="+lastAction.id+"' target = '_blank' style='float:none;'>"+lastAction.username+"</a></span></div>";

						  break;
						case '6':
							break;
						case '7':
							
							if(lastAction.commenttext == null) { lastAction.commenttext = "No comment"; }
			
							lastActionHtml = "<div class='heading4' style='margin-top:0;'><span>Last Activity : </span><span>Voting an argument</span></div><div class='heading4'><span>Title : </span><span>"+lastAction.title+"</span></div>";
							if(lastAction.vote == 1 ) {
								lastActionHtml = lastActionHtml + "<div class='heading4'><span>Vote : </span><span>Agreed</span></div>";
							} else {
								lastActionHtml = lastActionHtml + "<div class='heading4'><span>Vote : </span><span>Disgreed</span></div>";
							}
							lastActionHtml  = lastActionHtml + "<div class='heading4'><span>Comment : </span><span>"+lastAction.commenttext+"</span></div><div class='heading4'><span><a href='/detail?id="+lastAction.id+"' target = '_blank'  class='heading5Link' style='flaot:none;margin:0;'>View in detail</a></span></div>";
							break;
					  default:  
						  lastActionHtml = "<div>No details</div>";
					}
					
					
					jQuery("#lastActionInfo").html(lastActionHtml);
				}
			}
		});
	});
	//clearDateForm();
});

function loadUserData(input){
	var userId = input.id;
	input.alreadyLoggedIn = false;
	if(input.lastLoginTime != "0000-00-00 00:00:00") 
	input.alreadyLoggedIn = true;
	var notYetLogged = "Not Yet LoggedIn";
	jQuery.ajax({
		url:AD.base_url+'index.php/dashboard/user',
		data: {id:userId},
		dataType: 'json',
		type: 'post',
		success: function(result){
			if(result.response){
				var user = result.data.user;
				var lastAction = {};
				var argumentHtml = "";
				argumentHtml="<div class='heading4' style='margin-top:0;'><span>Created Time : </span><span>"+adminObj.time_difference(input.createdTime)+"</span></div>";
				
				if(result.lastActionTimeresponse) {
					lastAction = result.data.lastAction;
					argumentHtml = argumentHtml + "<div class='heading4 data {lastmodified:\""+lastAction.lastactiontime+"\", id:\""+lastAction.id+"\",type:\""+lastAction.activitytype+"\",userId:\""+userId+"\"}' id='user-lastaction' style='cursor:pointer'><span>Last activity : </span><span>"+adminObj.time_difference(lastAction.lastactiontime)+"</span></div>"
				} else {
					argumentHtml = argumentHtml + "<div class='heading4 id='no-lastaction'><span>Last activity : </span><span>No Action</span></div>"
				}
				
				if(input.alreadyLoggedIn) {
					argumentHtml=argumentHtml + "<div class='heading4'><span>Last login : </span><span>"+adminObj.time_difference(input.lastLoginTime)+"</span></div><div class='heading4'><span>Email Address : </span><span>"+input.email+"</span></div><div class='heading4'><span>Arguments Created:</span><span>"+user.argument+"</span></div><div class='heading4'><span>Agreed with : </span><span>"+user.agreed+"</span></div><div class='heading4'><span>Disagreed with : </span><span>"+user.disagreed+"</span></div><div class='heading4'><span>Comments :</span><span>"+user.comment+"</span></div><div class='heading4'><span>Following : </span><span>"+user.following+"</span></div><div class='heading4'><span>Followed by : </span><span>"+user.followed+"</span></div><div class='heading4'><span>Favorite : </span><span>"+user.favorite+"</span></div><div class='heading4'><span>Invited members count : </span><span>"+user.invitedmembercount+"</span></div>";
				}
				else {
					argumentHtml=argumentHtml + "<div class='heading4'><span>Last login : </span><span>Not Yet Loggedin</span></div><div class='heading4'><span>Email Address : </span><span>"+input.email+"</span></div><div class='heading4'><span>Arguments Created:</span><span>"+user.argument+"</span></div><div class='heading4'><span>Agreed with : </span><span>"+user.agreed+"</span></div><div class='heading4'><span>Disagreed with : </span><span>"+user.disagreed+"</span></div><div class='heading4'><span>Comments :</span><span>"+user.comment+"</span></div><div class='heading4'><span>Following : </span><span>"+user.following+"</span></div><div class='heading4'><span>Followed by : </span><span>"+user.followed+"</span></div><div class='heading4'><span>Favorite : </span><span>"+user.favorite+"</span></div><div class='heading4'><span>Invited members count : </span><span>"+user.invitedmembercount+"</span></div>";
				}
				
				jQuery("#userInfo").html(argumentHtml);
			}
		}
	});
}

function userDashboardHtml(users) {
	if(!users) {
		jQuery("#container").html("<div style='text-align:center'>No Users</div>");
		return;
	} 
	var listofUsers = '';
	for (var index in users) {
		var data = users[index];
		
		listofUsers = listofUsers+'<li><a href="javascript:void(\'0\');" class="heading5 contentBody {id:\''+data.id+'\',createdTime:\''+data.createdTime+'\',email:\''+data.email+'\',lastLoginTime:\''+data.lastloggedin+'\'}">'+data.username+'</a></li>';
	}
	var userDashboardHtml = '<ul id="userDashboard-list">'+listofUsers+'</ul><div id="userInfo"></div><div id="lastActionInfo"></div>';		
	jQuery("#container").html(userDashboardHtml);
	userDashboardInit();
	
}
function userDashboardInit() {
	jQuery("#userDashboard-list>li:first-child").addClass('active');
	var input = {};
	var metadata = jQuery("#userDashboard-list>li:first-child>a").metadata();
	input.id = metadata.id;
	input.email = metadata.email;
	input.createdTime = metadata.createdTime;
	input.lastLoginTime = metadata.lastLoginTime;
	loadUserData(input);
}
