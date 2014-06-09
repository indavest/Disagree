jQuery(document).ready(function () {
    window.loggedInUserMember = window.baseObj.getLoggedInUserMember();
    window.profileMetaData = jQuery('.userprofileInfo').metadata();
    window.currentTab = '';

    preInit();


    /*** Sync Scripts ***/
    setInterval( "syncProfileStatBoard()", DA.AJAX_TIME_INTERVAL*60000 );
    /*if(window.activeTab == 'argumentFed'){
        setInterval( "SyncArgument('.secondaryContainer')", DA.ajax_time_interval*60000 );
        setInterval( "syncArgumentData()", DA.ajax_time_interval*60000 );
    }*/
    /*** Sync Scripts ***/
    jQuery(window).scroll(function () {
        window.limit = window.limit + 1;
        if (!window.isLoading && jQuery(window).scrollTop() >= jQuery(document).height() - jQuery(window).height() - 100 && window.hasMoreRecords) {
            switch (jQuery(".activeMenu").attr('id')) {
                case 'activityFeed':
                    if(window.loggedInUserMember.memberId != profileMetaData.memberId){
                        loadActivityFeed();
                    }
                	break;
                case 'argumentFed':
                    loadUserStartedArguments();
                    break;
                case 'favoriteFeed':
                    loadFavoriteArguments();
                    break;
                case 'followingFeed':
                    loadFollowingMembers();
                    break;
                case 'followersFeed':
                    loadFollowers();
                    break;
                case 'statFeed':
                    break;
            }
        }
    });

    jQuery('.userInfoTab').live('click', function () {
        if (!window.isLoading&&!jQuery(this).hasClass('activeMenu')) {
            window.activeTab = jQuery(this).metadata().id;
            var metaData = jQuery(this).metadata();
            init(metaData.id);
            if (!jQuery("#" + metaData.id).hasClass('activeMenu')) {
                jQuery("#profileContentWrapper").html('');
            }
        }
    });

    window.addEventListener("hashchange", preInit, false);
});

function preInit(){
    /**** Fixed constants !!!Do not Change ****/
    var FEED_LIMIT = 0;
    /**** Fixed constants !!!Do not Change ****/
    /*set page title with argument title*/
    document.title = window.profileMetaData.username+ ' - Disagree.me'

    window.activeTab = 'argumentFed';
    window.isLoading = false;
    var pageUrl = window.location;
    var tab = (pageUrl.hash.substring(1)) ? pageUrl.hash.substring(1) : window.activeTab;
    init(tab);
}

function init(tab) {
    if (!window.isLoading) {
        window.recordsLowerLimit = 0;
        window.recordsPerLoad = 6;
        window.hasMoreRecords = true;
        window.limit = 0;
        jQuery('#profileContentWrapper').html('');
        //CALL THE FUNCTIONS ON TAB CLICK
        switch (tab) {
            case 'activityFeed':
            	var thisObj = jQuery("#" + tab);
                window.currentTab = jQuery("#" + tab);
                if (!(jQuery(thisObj).hasClass('activeMenu'))) {
                	jQuery("#profileContentWrapper").removeClass('activityFeed favouriteFeed followingFeed followersFeed statFeed argumentFed');
                    jQuery("#profileContentWrapper").addClass('activityFeed');
                    if(window.loggedInUserMember.memberId == profileMetaData.memberId){
                        loadNotificationFeed();
                    }else{
                        loadActivityFeed();
                    }
                }
                break;

            case 'favoriteFeed':
                var thisObj = jQuery("#" + tab);
                window.currentTab = jQuery("#" + tab);
                if (!(jQuery(thisObj).hasClass('activeMenu'))) {
                    jQuery("#profileContentWrapper").removeClass('activityFeed favouriteFeed followingFeed followersFeed statFeed argumentFed');
                    jQuery("#profileContentWrapper").addClass('favouriteFeed');
                    loadFavoriteArguments();
                }
                break;

            case 'followingFeed':
                var thisObj = jQuery("#" + tab);
                window.currentTab = jQuery("#" + tab);
                if (!(jQuery(thisObj).hasClass('activeMenu'))) {
                    jQuery("#profileContentWrapper").removeClass('activityFeed favouriteFeed followingFeed followersFeed statFeed argumentFed');
                    jQuery("#profileContentWrapper").addClass('followingFeed');
                    loadFollowingMembers();
                }
                break;

            case 'followersFeed':
                var thisObj = jQuery("#" + tab);
                window.currentTab = jQuery("#" + tab);
                if (!(jQuery(thisObj).hasClass('activeMenu'))) {
                    jQuery("#profileContentWrapper").removeClass('activityFeed favouriteFeed followingFeed followersFeed statFeed argumentFed');
                    jQuery("#profileContentWrapper").addClass('followersFeed');
                    loadFollowers();
                }
                break;

            case 'statFeed':
                var thisObj = jQuery("#" + tab);
                //jQuery("#profileContentWrapper").html("<h3>Comming Soon</h3>");
                window.currentTab = jQuery("#" + tab);
                if (!(jQuery(thisObj).hasClass('activeMenu'))) {
                    jQuery("#profileContentWrapper").removeClass('activityFeed favouriteFeed followingFeed followersFeed statFeed argumentFed');
                    jQuery("#profileContentWrapper").addClass('statFeed');
                    loadStats(thisObj);
                }
                break;

            default:
                var thisObj = jQuery("#" + tab);
                window.currentTab = jQuery("#" + tab);
                if (!(jQuery(thisObj).hasClass('activeMenu'))) {
                    jQuery("#profileContentWrapper").removeClass('activityFeed favouriteFeed followingFeed followersFeed statFeed argumentFed');
                    jQuery("#profileContentWrapper").addClass('argumentFed');
                    loadUserStartedArguments();
                }
                break;
        }
    }
}

function syncProfileStatBoard(){
	var syncUrl = 'sync/userProfileStatBoard';
	jQuery.ajax({
		url:syncUrl,
        dataType:'json',
        data:{memberId:window.profileMetaData.memberId},
        type:'post',
        success:function (result) {
            if(result && result.response) {
            	var userMemberObject = result.data;
        		jQuery(".argumentCount").html(userMemberObject.argumentCreatedCount); 
        		jQuery(".follwersCount").html(userMemberObject.followerCount);
        		jQuery(".followingCount").html(userMemberObject.followedCount);
                jQuery("#activityFeed .counter").html(userMemberObject.notificationCount);
                jQuery("#argumentFed .counter").html(userMemberObject.argumentCreatedCount);
                jQuery("#followingFeed .counter").html(userMemberObject.followedCount);
                jQuery("#followersFeed .counter").html(userMemberObject.followerCount);
            }
        }
	});
	
}

function SyncArgument(){
    var syncUrl ='';
    switch(jQuery(".activeMenu").attr('id')){
        case 'activityFeed':break;
        case 'argumentFed':
            syncUrl = 'sync/profileArgumentsStartedByUser';
            break;
        case 'favoriteFeed':
            syncUrl = 'sync/profileArgumentsFollwedByUser';
            break;
        case 'followingFeed':break;
        case 'followersFeed':break;
        case 'statFeed': break;
    }

    jQuery.ajax({
        url:syncUrl,
        dataType:'json',
        data:{memberId:window.profileMetaData.memberId},
        type:'post',
        success:function (result) {
            if(result){
                syncArgumentCallBack(result);
            }
        }
    });
}

function syncArgumentData(input){
    var argumentIdArray = Array();
    jQuery(".longArgument").each(function(){
        argumentIdArray.push(jQuery(this).metadata().id);
    });
    jQuery.ajax({
        url:'sync/argumentListData',
        dataType:'json',
        type:'post',
        data:{argumentIdArray:argumentIdArray},
        success: function(result){
            if (result.data != null) {
                var argumentList = result.data;
                for(var argumentCount = 0;argumentCount<argumentList.length;argumentCount++){
                    var agreed = parseInt(argumentList[argumentCount].agreed);
                    var disagreed = parseInt(argumentList[argumentCount].disagreed);
                    var totalCount = parseInt(agreed) + parseInt(disagreed);
                    var commentCount = argumentList[argumentCount].commentsCount;
                    var argumentId = argumentList[argumentCount].id;
                    var oldagree = parseInt(jQuery("#agree-"+argumentId).metadata().agree);
                    var oldTotalCount = parseInt(jQuery("#agree-"+argumentId).siblings('.circleMini').html());
                    var oldPercentage = (!oldTotalCount)?'d':baseObj.CalculatePercentage(Math.round((oldagree / oldTotalCount)*100));
                    var currentPercentage = (totalCount>0)?baseObj.CalculatePercentage(Math.round((agreed / totalCount)*100)):'d';
                    jQuery("#agree-"+argumentId).metadata().agree = agreed;
                    jQuery("#disagree-"+argumentId).metadata().agree = agreed;
                    jQuery("#agree-"+argumentId).siblings('.circleMini').html(totalCount);
                    jQuery("#agree-"+argumentId).siblings('.circleMini').toggleClass("sector"+oldPercentage+" sector"+currentPercentage);
                    jQuery("#agree-"+argumentId).parent().siblings(".postContentActions").find(".commentCount").children("span").html(commentCount);

                }
            }
        }
    });
}

function syncArgumentCallBack(result){
    if (result.argumentList != null) {
        var argumentList = result.argumentList;
        var argumentListHtml = "";
        var argumentIdArray = Array();
        jQuery(".longArgument").each(function(){
            argumentIdArray.push(jQuery(this).metadata().id);
        });
        for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
            if(jQuery.inArray(argumentList[argumentCount].id, argumentIdArray) == -1){
                argumentListHtml += loadArgument(argumentList[argumentCount]);
            }
        }
        jQuery("#profileContentWrapper").prepend(argumentListHtml);
    }
}

function loadNotificationFeed(){
    if(!window.isLoading){
        jQuery.ajax({
	        url:"action/memberNotifications",
	        data:{memberId:loggedInUserMember.memberId, timeInterval:0},
	        dataType:'json',
	        type:'post',
	        cache:false,
             beforeSend:function(){
                 window.isLoading = true;
                 jQuery("#activityFeed").addClass('activeMenu').siblings().removeClass('activeMenu');
                 jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
             },
	        success:function (result) {
	        	var notificationGroupOneHtml = "";
	        	var notificationList = result.data;
	            var date = new Date;
	            var currentDay = date.getDate();
	            if(result.response){
	            	notificationGroupOneHtml += '<div class="profileNotificationGroup"><span class="heading5">Recent</span><ul class="profileNotificationContainer smallText">';
	            	for(var notificationCount = 0;notificationCount<notificationList.length ;notificationCount++){
            			notificationGroupOneHtml += prepareNotificationMessage(notificationList[notificationCount]);
	            	}
	            	notificationGroupOneHtml += '</ul></div>';
	            	jQuery("#profileContentWrapper").html(notificationGroupOneHtml);
	            	jQuery("#footerLoader").html("");
	            	setNoficationRead();
	            }else{
                    window.hasMoreRecords = false;
                    jQuery("#footerLoader").html("No Notifications to show");
                }
	        },
         complete:function () {
             window.recordsLowerLimit++;
             window.isLoading = false;
         }

	    });
    }
}

function loadActivityFeed() {
    if (window.hasMoreRecords && !window.isLoading) {
        jQuery.ajax({
            url:"action/getUserActivity",
            data:{memberId:profileMetaData.memberId, start:window.recordsLowerLimit},
            dataType:'json',
            type:'post',
            beforeSend:function () {
                window.isLoading = true;
                jQuery("#activityFeed").addClass('activeMenu').siblings().removeClass('activeMenu');
                jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
            },
            success:function (res) {
                var activityGroupHTML = '';
                if (res.response == undefined) {
                    res = eval('(' + res + ')');
                }
                if (res.response) {
                    if (res.data.length < window.recordsPerLoad) {  //if arguments loaded are lesthan 10 means no more arguments on this argumetn
                        window.hasMoreRecords = false;
                        jQuery("#footerLoader").html("<span class='startArgHandle'>No more Activity found for this user.</span>");
                    }
                    if (window.recordsLowerLimit == 0) {
                        activityGroupHTML += '<div class="profileActivityGroup"><span class="heading5">Recent</span><ul class="profileActivityContainer smallText">';
                    }
                    jQuery.each(res.data, function (i, e) {
                        e.userid = profileMetaData.memberId;
                        e.profileThumb = profileMetaData.profileThumb;
                        e.username = profileMetaData.username;
                        activityGroupHTML += prepareActivityTabMessage(e);
                    });
                    if (window.recordsLowerLimit == 0) {
                        activityGroupHTML += '</ul></div>';
                        jQuery("#profileContentWrapper").append(activityGroupHTML);
                    }else{
                        jQuery("#profileContentWrapper .profileActivityContainer ").append(activityGroupHTML);
                    }
                    jQuery("#footerLoader").html("");
                } else {
                    window.hasMoreRecords = false;
                    if (window.recordsLowerLimit == 0) {
                        jQuery("#footerLoader").html("<span >No Activity Found</span>");
                    } else {
                        jQuery("#footerLoader").html("<span >No More Activity For this user.</span>");
                    }
                }
            },
            complete:function () {
                window.recordsLowerLimit++;
                window.isLoading = false;
            }
        });
    }
}

function loadUserStartedArguments() {
    //LOAD THE ARGUMENTS CREATED BY THE MEMBER THROUGH AJAX
    if (window.hasMoreRecords && !window.isLoading) {
        jQuery.ajax({
            url:"action/profileStartedArgument",
            data:{memberId:profileMetaData.memberId, limit:window.recordsLowerLimit},
            dataType:'json',
            type:'post',
            cache:false,
            beforeSend:function () {
                window.isLoading = true;
                jQuery("#argumentFed").addClass('activeMenu').siblings().removeClass('activeMenu');
                jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
            },
            success:function (result) {
                var argumentListHtml = "";
                if (result.response && result.data) {
                    var argumentList = result.data;
                    for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                        argumentListHtml += loadArgument(argumentList[argumentCount]);
                    }
                    if (result.data.length < window.recordsPerLoad) {  //if arguments loaded are lesthan 10 means no more arguments on this argumetn
                        window.hasMoreRecords = false;
                        if(profileMetaData.memberId  == loggedInUserMember.memberId){
                            jQuery("#footerLoader").html("<span class='startArgHandle'>No more arguments to display.</span>");
                        } else {
                            jQuery("#footerLoader").html("No more Arguments.");
                        }
                    }
                    jQuery('#profileContentWrapper').append(argumentListHtml);
                } else {
                    window.hasMoreRecords = false;
                    if(window.recordsLowerLimit == 0){
                    	if(profileMetaData.memberId  == loggedInUserMember.memberId){
                    		jQuery("#footerLoader").html("<span class='startArgHandle'>"+DA.PROFILE_NO_STARTED_ARGUMENTS+"</span>");
                    	} else {
                    		jQuery("#footerLoader").html("No arguments Created by user.");
                    	}
                    	
                    }else {
                    	if(profileMetaData.memberId  == loggedInUserMember.memberId){
                            jQuery("#footerLoader").html("<span class='startArgHandle'>"+DA.PROFILE_NO_STARTED_ARGUMENTS+"</span>");
                    	}
                    	else {
                    		jQuery("#footerLoader").html("No more Arguments.");
                    	}
                    }
                }
            },
            complete:function () {
                window.recordsLowerLimit++;
                window.isLoading = false;
            }
        });
    }
}

function loadFavoriteArguments() {
    if (!window.isLoading) {
        //LOAD THE MEMBER'S FAVOURITE ARGUMENT THROUGH AJAX
        if (window.hasMoreRecords) {
            jQuery.ajax({
                url:"action/profileFollowingArgument",
                data:{memberId:window.profileMetaData.memberId, limit:window.recordsLowerLimit, load:window.recordsPerLoad},
                dataType:'json',
                type:'post',
                cache:false,
                beforeSend:function () {
                    window.isLoading = true;
                    jQuery("#favoriteFeed").addClass('activeMenu').siblings().removeClass('activeMenu');
                    jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
                },
                success:function (result) {
                    var argumentListHtml = "";
                    if (result.response && result.data) {
                        var argumentList = result.data;
                        for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {

                            argumentListHtml += loadArgument(argumentList[argumentCount]);
                        }
                        if (result.data.length < window.recordsPerLoad) {  //if arguments loaded are lesthan 10 means no more arguments on this argumetn
                            window.hasMoreRecords = false;
                            jQuery("#footerLoader").html("No more Favorites");
                        }
                        jQuery('#profileContentWrapper').append(argumentListHtml);
                    } else {
                    	if(window.recordsLowerLimit == 0)
                    		jQuery("#footerLoader").html("No Favorites");
                    	else
                    		jQuery("#footerLoader").html("No more Favorites");
                        window.hasMoreRecords = false;
                    }
                },
                complete:function () {
                	window.recordsLowerLimit++;
                    window.isLoading = false;
                }
            });
        }
    }
}

function loadFollowingMembers() {
    if (!window.isLoading) {
//LOAD THE USERS MEMBER FOLLOWS THROUGH AJAX
        if (window.hasMoreRecords) {
            jQuery.ajax({
                url:"action/profileMemberFollowing",
                data:{memberId:profileMetaData.memberId, limit:window.recordsLowerLimit},
                dataType:'json',
                type:'post',
                cache:false,
                beforeSend:function () {
                    jQuery("#followingFeed").addClass('activeMenu').siblings().removeClass('activeMenu');
                    jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
                    window.isLoading = true;
                },
                success:function (result) {
                    if (result.response) {
                        for (var userMemberData = null, i = 0; i < result.data.length; i++) {
                            userMemberData = result.data[i];
                            jQuery('#profileContentWrapper').append('<div class="secondaryContainer UserFollowInfo">' + loadProfileTip(userMemberData) + '</div>');
                        }
                        if (result.data.length < 9) {
                            if(profileMetaData.memberId  == loggedInUserMember.memberId){
                                jQuery("#footerLoader").html("You are not following any more users.");
                            } else {
                                jQuery("#footerLoader").html("no more following users.");
                            }
                            window.hasMoreRecords = false;
                        }
                    } else {
                        window.hasMoreRecords = false;
                        if(profileMetaData.memberId  == loggedInUserMember.memberId ){
                            if(window.recordsLowerLimit == 0){
                                jQuery("#footerLoader").html("You are not following anyone.");
                            } else {
                                jQuery("#footerLoader").html("You are not following any more users.");
                            }
                        } else {
                            if(window.recordsLowerLimit == 0){
                                jQuery("#footerLoader").html("Not following anyone.");
                            } else {
                                jQuery("#footerLoader").html("Not following any more users.");
                            }
                        }
                    }
                },
                complete:function () {
                    window.recordsLowerLimit++;
                    window.isLoading = false;
                }
            });
        }
    }
}

function loadFollowers() {
//LOAD THE USERS FOLLOWING THE MEMBER THROUGH AJAX
    if (window.hasMoreRecords && !window.isLoading) {
        jQuery.ajax({
            url:"action/profileMemberFollowed",
            data:{memberId:profileMetaData.memberId, limit:window.recordsLowerLimit},
            dataType:'json',
            type:'post',
            cache:false,
            beforeSend:function () {
                window.isLoading = true;
                jQuery("#followersFeed").addClass('activeMenu').siblings().removeClass('activeMenu');
                jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
            },
            success:function (result) {
                if (result.response) {
                    for (var userMemberData = null, i = 0; i < result.data.length; i++) {
                        userMemberData = result.data[i];
                        jQuery('#profileContentWrapper').append('<div class="secondaryContainer UserFollowInfo">' + loadProfileTip(userMemberData) + '</div>');
                    }
                    if (result.data.length < 9) {
                        if(profileMetaData.memberId  == loggedInUserMember.memberId){
                            jQuery("#footerLoader").html("You don't have any more followers.");
                        } else {
                            jQuery("#footerLoader").html("No more followers.");
                        }
                        window.hasMoreRecords = false;
                    }
                } else {
                    window.hasMoreRecords = false;
                    if(profileMetaData.memberId  == loggedInUserMember.memberId){
                        if(window.recordsLowerLimit == 0){
                            jQuery("#footerLoader").html("You don't have any followers Yet.");
                        } else {
                            jQuery("#footerLoader").html("You don't have any more followers");
                        }
                    } else {
                        if(window.recordsLowerLimit == 0){
                            jQuery("#footerLoader").html("No followers Yet.");
                        } else {
                            jQuery("#footerLoader").html("No more followers");
                        }
                    }
                }
            },
            complete:function () {
                window.recordsLowerLimit++;
                window.isLoading = false;
            }
        });
    }
}

function loadStats(thisObj) {
	 jQuery('.horizontalMenu li').removeClass('activeMenu');
	 jQuery(thisObj).addClass('activeMenu');
	 jQuery('#profileContentWrapper').html("<div class='message'><h3>Coming Soon</h3></div>");
    jQuery('#footerLoader').html('');
}

function memberFollowCallBack(result, input){
	jQuery(input.clickObj).toggleClass('followMember unfollowMember').children(".sprite-icon").toggleClass('tickIconW unfollowIconW');
    if(jQuery(input.clickObj).hasClass('agreementGradient') || jQuery(input.clickObj).hasClass('disagreementGradient')){
        jQuery(input.clickObj).toggleClass('disagreementGradient agreementGradient');
    }
    jQuery(input.clickObj).children("span").html('Unfollow');
	if(profileMetaData.memberId == profileMetaData.loggedInMemberId){               //if profile is loggedin user profile update following stats on stat board and count
		var count = jQuery("#followingFeed").children('.counter').text();
        count = parseInt(count) + 1;
		jQuery("#followingFeed").children('.counter').text(count);
        jQuery("#profileStatBoard .followingCount").text(count);
        if(window.activeTab == 'followingFeed'  &&  count == 0 ){
            if(profileMetaData.memberId  == loggedInUserMember.memberId){
                jQuery("#profileContentWrapper").html("<div class='message'><h3>You don't have any more followers.</h3></div>");
            } else {
                jQuery("#profileContentWrapper").html("<div class='message'><h3>No more followers.</h3></div>");
            }
            jQuery("#footerLoader").text('');
        }
	}
    if(profileMetaData.memberId != profileMetaData.loggedInMemberId){           //if profile is not loggedin user profile update followers stats on stat board and count
        var count = jQuery("#followersFeed").children('.counter').text();
        count = parseInt(count) + 1;
        jQuery("#followersFeed").children('.counter').text(count);
        jQuery("#profileStatBoard .follwersCount ").text(count);
        if(window.activeTab == 'followersFeed'  &&  count == 0 ){
            if(profileMetaData.memberId  == loggedInUserMember.memberId){
                jQuery("#profileContentWrapper").html("<div class='message'><h3>No one has started following you</h3></div>");
            } else {
                jQuery("#profileContentWrapper").html("<div class='message'><h3>No more followers.</h3></div>");
            }
            jQuery("#footerLoader").text('');
        }else if(window.activeTab == 'followersFeed'  &&  count > 0 ){
            jQuery("#profileContentWrapper").prepend("<div class='secondaryContainer UserFollowInfo'>"+loadProfileTip(window.loggedInUserMember)+'</div>');
            jQuery("#profileContentWrapper").children('.message').text('');
            jQuery("#footerLoader").text('');
        }
    }
}

function memberUnFollowCallBack(result, input) {
    jQuery(input.clickObj).toggleClass('followMember unfollowMember').children(".sprite-icon").toggleClass('tickIconW unfollowIconW');
    if (jQuery(input.clickObj).hasClass('agreementGradient') || jQuery(input.clickObj).hasClass('disagreementGradient')) {
        jQuery(input.clickObj).toggleClass('disagreementGradient agreementGradient');
    }
    jQuery(input.clickObj).children("span").html('Follow');
    if (profileMetaData.memberId == profileMetaData.loggedInMemberId) {                           //if profile is loggedin user profile update following stats on stat board and count
        if (window.activeTab == 'followingFeed') {
            jQuery('.a' + input.followMemberId).parent().remove();
            jQuery("#footerLoader").text('');
        }
        var followingCount = jQuery("#followingFeed").children('.counter').text();
        followingCount = parseInt(followingCount) - 1;
        jQuery("#followingFeed").children('.counter').text(followingCount);
        jQuery("#profileStatBoard .followingCount").text(followingCount);
        if (window.activeTab == 'followingFeed' && followingCount == 0) {
            if (profileMetaData.memberId == loggedInUserMember.memberId) {
                jQuery("#profileContentWrapper").html("<div class='message'><h3>No one has started following you</h3></div>");
            } else {
                jQuery("#profileContentWrapper").html("<div class='message'><h3>No more followers.</h3></div>");
            }
            jQuery("#footerLoader").text('');
        }
    }
    if (profileMetaData.memberId != profileMetaData.loggedInMemberId) {                           //if profile is not loggedin user profile update followers stats on stat board and count
        if (window.activeTab == 'followersFeed') {
            jQuery('.a' + input.memberId).parent().remove();
            jQuery("#footerLoader").text('');
        }
        var count = jQuery("#followersFeed").children('.counter').text();
        count = parseInt(count) - 1;
        jQuery("#followersFeed").children('.counter').text(count);
        jQuery("#profileStatBoard .follwersCount").text(count);
        if (window.activeTab == 'followersFeed' && count == 0) {
            if (profileMetaData.memberId == loggedInUserMember.memberId) {
                jQuery("#profileContentWrapper").html("<div class='message'><h3>No one has started following you</h3></div>");
            } else {
                jQuery("#profileContentWrapper").html("<div class='message'><h3>No more followers.</h3></div>");
            }
            jQuery("#footerLoader").text('');
        } else if (window.activeTab == 'followersFeed' && count > 0) {
            jQuery("#footerLoader").text('');
        }
    }
}

function favoriteCallBack(result, input){
	var clickObj = input.clickObj;
	jQuery(clickObj).toggleClass('favIconOnG favIconOffG');
	if(profileMetaData.memberId == profileMetaData.loggedInMemberId){
		jQuery(input.clickObj).parent('.argumentUserActions').parent('.contentHead').parent('.secondaryContainer').remove();
		var favCount = jQuery(window.currentTab).children('.counter').text();
		
		jQuery(window.currentTab).children('.counter').text(parseInt(favCount) - 1);
        if(window.activeTab = 'favoriteFeed' && favCount-1 == "0" ){
            jQuery("#profileContentWrapper").html("<div class='message'><h3>No Favorites</h3></div>");
            jQuery("#footerLoader").text('');
        }
	}
}

function postOpinionCallBack(result, input){
	var resultData = result.data;
	var agreeCount = null;
	if(input.vote == 1){
		agreeCount = parseInt(jQuery("#agree-"+input.argumentId).metadata().agree);
		input.buttonSelector = jQuery("#agree-"+input.argumentId);
	}else {
		agreeCount = parseInt(jQuery("#disagree-"+input.argumentId).metadata().agree);
		input.buttonSelector = jQuery("#disagree-"+input.argumentId);
		var agreedPercentage = baseObj.CalculatePercentage(Math.round(((agreeCount) / currentCommentCount) * 100));
	}
	
	var commentHtmlSelector = jQuery(input.buttonSelector).parent().siblings(".postContentActions").find(".commentCount").children('span');
	var voteHtmlSelector = jQuery(input.buttonSelector).siblings(".circleMini");
	var currentCommentCount = parseInt(jQuery(commentHtmlSelector).html());
    var currentVote = parseInt(jQuery(voteHtmlSelector));
	var totalVoteCount = (resultData.voted)?parseInt(jQuery(voteHtmlSelector).html())+1:parseInt(jQuery(voteHtmlSelector).html());
	var currentVoteCount = parseInt(jQuery(voteHtmlSelector).html());
	var currentAgreedPercentage = baseObj.CalculatePercentage(Math.round((agreeCount / currentVoteCount) * 100));
	if(input.vote == 1){
		var agreedPercentage = baseObj.CalculatePercentage(Math.round(((agreeCount+1) / totalVoteCount) * 100));
	}else{
		var agreedPercentage = baseObj.CalculatePercentage(Math.round(((agreeCount) / totalVoteCount) * 100));
	}
	if(!resultData.voted){
		currentCommentCount += 1;
		jQuery(commentHtmlSelector).html(currentCommentCount);
	}else if(resultData.voted && !resultData.commented){
		currentVoteCount += 1;
		jQuery(voteHtmlSelector).html(currentVoteCount);
        currentAgreedPercentage=(currentAgreedPercentage==0)?'d':currentAgreedPercentage;
		jQuery(voteHtmlSelector).toggleClass("sector"+currentAgreedPercentage+" sector"+agreedPercentage);
		jQuery(input.buttonSelector).metadata().voted = true;
	}else{
		currentVoteCount += 1;
		jQuery(voteHtmlSelector).html(currentVoteCount);
        currentAgreedPercentage=(currentAgreedPercentage==0)?'d':currentAgreedPercentage;
		jQuery(voteHtmlSelector).toggleClass("sector"+currentAgreedPercentage+" sector"+agreedPercentage);
		currentCommentCount += 1;
		jQuery(commentHtmlSelector).html(currentCommentCount);
		jQuery(input.buttonSelector).metadata().voted = true;
	}
    var options = new Object;
    if(input.fbFlag){
        options.link = DA.base_url+"detail?id="+input.argumentId;
        options.title = (input.vote == 1)?"Agreed with "+input.argumentTitle:"Disagreed with "+input.argumentTitle;
        options.description = input.commenttext;
        options.img = window.currArgumentObj.profilephoto;
        if(input.twFlag){
            options.twFlag = input.twFlag;
            options.twurl = DA.base_url+"detail?id="+input.argumentId;
            options.twdescription = (input.vote == 1)?"Agreeing with "+input.argumentTitle:"Disagreeing with "+input.argumentTitle;
        }
        setTimeout(function(){baseObj.postToFB(options);},3000);
    }else{
        if(input.twFlag){
            options.url = DA.base_url+"detail?id="+input.argumentId;
            options.description = (input.vote == 1)?"Agreeing with "+input.argumentTitle:"Disagreeing with "+input.argumentTitle;
            baseObj.postToTW(options);
        }
    }
}

function setNoficationRead(){
	jQuery.ajax({
		url: 'action/notificationRead',
		dataType: 'json',
		success: function(result){
			if(result && result.response){
				jQuery('#notificationCountContainer').hide();
				jQuery("title").html("Disagree.me");
			}
		}
	});
}