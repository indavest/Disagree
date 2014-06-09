jQuery(document).ready(function () {
    jQuery("#staticMenu>li").click(function () {
        jQuery("#staticMenu>li").removeClass('primaryButton disabled active');
        jQuery(this).addClass('primaryButton disabled active');
        init(jQuery(this).metadata().id);
    });

    jQuery("#fetchTwFrnd").live('click', function () {
        loadTwUser(jQuery("#twitterUserName").val());
    });

    jQuery('.invite').live('click', function () {
    	
        var thisObj = jQuery(this);
        var metaData = thisObj.metadata();
        if (metaData.site == 'fb') {
            FB.ui({
                to:metaData.id,
                method:'send',
                name:'People Argue Just to Win',
                link:DA.base_url
            }, function(response) {

                if (response) {
                	
                   saveInvitedUser(metaData);
                   
                  } else {
                	 
                    alert("Invitation is not send");
                  }
                }
            );
        } else if (metaData.site == 'tw') {
            window.open('http://twitter.com/share?url='+DA.base_url+'&text=Invitation to post your opinion on Disagree.me@' + metaData.screen_name, 'twitterwindow', 'height=450, width=550, top=-65.5, left=674.5, toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        } else {
            jQuery.ajax({
                url:DA.base_url + 'action/invite',
                data:{memberId:metaData.memberId, email:jQuery("#emailAddress").val(), message:jQuery("#message").val()},
                dataType:'json',
                type:'post',
                beforeSend:function () {
                    /*var errorStats = false;
                     errorStats = validateMultipleEmailFiled(jQuery("#emailAddress"));
                     errorStats = jQuery("#emailAddress").hasClass('error');
                     if( jQuery("#emailAddress").hasClass('error') || !(validateMultipleEmailFiled(jQuery("#emailAddress")))){return false};*/
                    if (!window.validationEngine.validateForm(jQuery("#emailInviteContent"))) {
                        baseObj.Showmsg(errorProcessing(), false);
                        return false;
                    } else {
                        return true;
                    }
                },
                success:function (result) {
                    if (result && result.response) {
                    	metaData.email =  jQuery("#emailAddress").val();
                    	
                    	saveInvitedUser(metaData);
                        baseObj.Showmsg(DA.INVITE_USER_FROM_SITE_SUCCESS, true);
                        jQuery("#emailAddress").val('').trigger('mousedown');
                        jQuery("#message").text('');
                    }
                }
            });
        }
    });

    /*jQuery('#selectAllFbInvite').live('mousedown', function () {
     var fbfriends = new Array();
     var twfriends = new Array();
     var siteFriends = new Array();
     jQuery("#fbInviteContainer .memberSection .invite").each(function(i,e){
     if(jQuery(e).metadata().site == 'fb'){
     fbfriends.push(jQuery(e).metadata().id);
     }else if(jQuery(e).metadata().site == 'tw'){
     twfriends.push(jQuery(e).metadata().id);
     }
     });
     if(jQuery("#staticMenu .active").metadata().id == 'fb'){
     FB.ui({
     to:'100000118528920,100000541927861',
     method:'send',
     name:'People Argue Just to Win',
     link:DA.base_url
     });
     }else if(jQuery("#staticMenu .active").metadata().id == 'tw'){
     window.open('http://twitter.com/share?url='+DA.base_url+'&text=Invitation to post your opinion on Disagree.me@'+metaData.screen_name,'twitterwindow', 'height=450, width=550, top=-65.5, left=674.5, toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
     }else{
     jQuery.ajax({
     url:DA.base_url+'action/invite',
     data:{memberId:metaData.memberId, email:jQuery("#emailAddress").val(), message:jQuery("#message").val()},
     dataType:'json',
     type:'post',
     beforeSend:function(){
     var errorStats = false;
     errorStats = window.validationEngine.validateMultipleEmailFiled(jQuery("#emailAddress"));
     errorStats = jQuery("#emailAddress").hasClass('error');
     if( jQuery("#emailAddress").hasClass('error') || !(window.validationEngine.validateMultipleEmailFiled(jQuery("#emailAddress")))){return false};
     },
     success: function(result){
     if(result && result.response){
     baseObj.Showmsg(DA.INVITE_USER_FROM_SITE_SUCCESS,true);
     jQuery("#emailAddress").val('').trigger('mousedown');
     jQuery("#message").text('');
     }
     }
     });
     }
     });*/

    /*jQuery("#emailAddress").blur(function(){
     window.validationEngine.validateMultipleEmailFiled(jQuery(this));
     });*/

    /*jQuery("#inviteUserSearch").live('keyup', function () {
     var thisObj = jQuery(this);
     var searchTerm = jQuery(thisObj).val();
     if(searchTerm.length == 0 || searchTerm == jQuery(thisObj).metadata().defaultText){
     //search box is empty
     jQuery("#fbFriendListWrapper").children().show();
     }else{
     jQuery("#fbFriendListWrapper").children().hide();
     var users = new Array();
     jQuery("#fbFriendListWrapper").find(".profileUserName").each(function(i,e){
     users.push(jQuery(e).text());
     });
     var result = baseObj.searchStringInArray(users,searchTerm);
     if(result.length == 0){
     //no matches for search term
     }else{
     jQuery.each(result,function(i,e){
     jQuery("#fbFriendListWrapper").children().eq(e).show();
     });
     }
     }
     });*/

    jQuery("#staticMenu").children(":first-child").trigger('click');
});


function init(tab) {
    switch (tab) {
        case 'tw':
            jQuery(".staticContentChild").hide();
            jQuery("#twInviteContainer").show();
            if (jQuery("#twInviteContainer").children(".memberSection").length == 0) {
                jQuery("#twInviteContainer").html('<div id="twInviteContainer"><input type="text" value="Enter Twitter User Name" class="placeholder defaultContent {defaultText : \'Enter Twitter User Name\'}" id="twitterUserName"><button class="primaryButton gradient" id="fetchTwFrnd">Connect</button>');
            }
            break;

        case 'email':
            jQuery(".staticContentChild").hide();
            jQuery("#emailInviteContainer").show();
            break;

        default :
            jQuery(".staticContentChild").hide();
            jQuery("#fbInviteContainer").show();
            if (!FB._initialized) {   //if fb.init not initialized
                window.fbAsyncInit();
                loadFbUser();
            } else {
                loadFbUser();
            }
            break;

    }
}

function loadFbUser() {
	
    jQuery("#fbInviteContainer").html('<div style="text-align:center;"><img src="/images/da-loader.gif" /></div>');
    var friendListHtml = "";
    FB.getLoginStatus(function (response) {
        if (response.status === 'connected') {
            FB.api('/me/friends', function (response) {
                var oauth_ids = new Array();

                jQuery.each(response.data, function (i, e) {
                    oauth_ids.push(e.id);
                });
               
                isFBUserExists(oauth_ids);
                friendListHtml = '<div class="userSearchBoxWrapper"><i class="sprite-icon searchIconG"></i><input type="text" id="inviteUserSearch" class="placeholder defaultContent contentSearchBox {defaultText : \'Search...\', searchWrapper:\'memberSection\', searchTextHolder:\'profileUserName\',searchArea:\'fbFriendListWrapper\'}"></div>';
                friendListHtml += "<div id='fbFriendListWrapper'>";
             
               for (var i = 0, l = response.data.length; i < l; i++) {
                
                    var friend = response.data[i];
                    if (friend.name) {
                        friendListHtml += "<div class=\"memberSection secondaryContainer\" id=\"m" + friend.id + "\"><img class=\"userImgCircleMed\" src=\"https://graph.facebook.com/" + friend.id + "/picture\"/><div class=\"heading4Link profileUserName\">" + baseObj.Ellipsis(friend.name,22) + "</div><button class=\"invite primaryButton gradient {id:'" + friend.id + "',name:'"+friend.name+"',site:'fb'}\">Invite</button></div>";
                    }
                }
                friendListHtml += "</div>";
         
                jQuery("#fbInviteContainer").html(friendListHtml);
                jQuery(".memberSection").children(".followMember,.unfollowMember,.invite").hide();
                baseObj.processPlaceHolder(jQuery("#inviteUserSearch"));
                checkFbUserLoaded = true;
            });
        } else if (response.status === 'not_authorized') {
            // the user isn't authorized to Facebook.
            jQuery("#fbInviteContainer").html('<button class="primaryButton gradient" onclick="fbLogin();"><span>SIGN IN WITH FACEBOOK</span></button>');
        } else {
            // the user isn't even logged in to Facebook.
            jQuery("#fbInviteContainer").html('<button class="primaryButton gradient" onclick="fbLogin();"><span>SIGN IN WITH FACEBOOK</span></button>');
        }
    });
}

function fbLogin() {

    FB.login(function (response) {
        if (response.authResponse) {
        	loadFbUser();
        	/*FB.api('/me/friends', function (response) {
                friendListHtml = "<div>";
                for (var i = 0, l = response.data.length; i < l; i++) {
                    var friend = response.data[i];
                    if (friend.name) {
                        friendListHtml += "<div class=\"memberSection secondaryContainer\"><img class=\"userImgCircleMed\" src=\"https://graph.facebook.com/" + friend.id + "/picture\"/><div class=\"heading4Link profileUserName\">" + friend.name + "</div><button class=\"invite primaryButton gradient {id:'" + friend.id + "',name:'"+ friend.name +"',site:'fb'}\">Invite</button></div>";
                    }
                }
                friendListHtml += "</div>";
                jQuery("#fbInviteContainer").html(friendListHtml);
                checkFbUserLoaded = true;
            });*/
        } else {
        }
    }, {scope:'email'});
}

function loadTwUser(handle) {
	
    jQuery("#twInviteContainer").html('<div style="text-align:center;"><img src="/images/da-loader.gif" /></div>');
    jQuery(function () {
        jQuery.ajax({
            url:'https://api.twitter.com/1/friends/ids.json',
            data:{screen_name:handle},
            dataType:'jsonp',
            success:function (data) {
                var twFrndHtml = '<div class="userSearchBoxWrapper"><i class="sprite-icon searchIconG"></i><input type="text" id="inviteUserSearch" class="placeholder defaultContent contentSearchBox {defaultText : \'Search...\', searchWrapper:\'memberSection\', searchTextHolder:\'profileUserName\',searchArea:\'twFriendListWrapper\'}"></div><div id="twFriendListWrapper">';
                jQuery.ajax({
                    url:'https://api.twitter.com/1/users/lookup.json',
                    data:{user_id:data.ids.toString()},
                    dataType:'jsonp',
                    success:function (data) {
                        for (var count = 0; count < data.length; count++) {
                            var metadata = "{screen_name:'" + data[count].screen_name + "',site:'tw'}";
                            twFrndHtml += "<div class='memberSection secondaryContainer'><img class=\"userImgCircleMed\" src='" + data[count].profile_image_url + "' style=\"float:left;\"/><div class=\"heading4Link profileUserName\">" + data[count].name + "</div><button class=\"invite primaryButton gradient " + metadata + "\">Invite</button></div>";
                        }
                        twFrndHtml += '</div>';
                        jQuery("#twInviteContainer").html(twFrndHtml);
                        baseObj.processPlaceHolder(jQuery("#inviteUserSearch"));
                    }
                });

            },
            statusCode:{
                404:function () {
                    baseObj.Showmsg(DA.TWITTER_LOAD_FRIENDS_FAIL, false);
                }
            }
        });
    });

}

function isFBUserExists(oauth_ids) {

    jQuery.ajax({
        url:DA.base_url + 'action/checkFbUserData',
        type:'post',
        dataType:'json',
        data:{oauth_id:oauth_ids},
        success:function (res) {
            if (res.response) {
                jQuery.each(res.data, function (i, e) {
                
                	if(e.type == 'user') {
                		var followClass = (e.status == 1) ? 'unfollowMember disagreementGradient actionSelector' :
                            'followMember agreementGradient actionSelector';
                        var followString = (e.status == 1) ? 'UnFollow' : 'Follow';
                        var buttonObj = jQuery("#m" + e.oauth_uid).children(".invite");
                        jQuery(buttonObj).removeClass('primaryButton invite').addClass(followClass).text(followString);
                        jQuery(buttonObj).metadata().followeMemberId = e.id;
                	} else if(e.type == 'invitedmember') {
                		var buttonObj = jQuery("#m" + e.oauth_uid).children(".invite");
                		jQuery(buttonObj).text("Reinvite");
                	}
                    
                });
            } else {
                //no facebook user found on site
            } 
            /*if(res.invitedUserResponse) {
            	
            	jQuery.each(res.invitedUserData, function (i, e) { 
            		var inviteString = "Reinvite";
            		var buttonObj = jQuery("#m" + e.oauth_uid).children(".invite");
            	alert(buttonObj.html());
            		jQuery(buttonObj).text(inviteString);
            	});
            } */
        },
        complete:function () {
            jQuery(".memberSection").children(".followMember,.unfollowMember,.invite").show();
        }
    });
}

function memberFollowCallBack(result, input) {
    jQuery(input.clickObj).toggleClass('followMember unfollowMember').toggleClass('agreementGradient disagreementGradient').text('Unfollow');
}

function memberUnFollowCallBack(result, input) {
    jQuery(input.clickObj).toggleClass('followMember unfollowMember').toggleClass('agreementGradient disagreementGradient').text('Follow');
}
function saveInvitedUser(invitedmember) {

	jQuery.ajax({
		type: 'post',
		url: DA.base_url + 'action/saveInvitedUser',
		dataType: 'json',
		data: {memberId:loggedInUserMember.memberId,fbId:invitedmember.id,name:invitedmember.name,invitationType:(invitedmember.site)?invitedmember.site:'email',email:invitedmember.email},
		success: function(response) {
			
		}, error: function() {
			
		}
		
	});
}
