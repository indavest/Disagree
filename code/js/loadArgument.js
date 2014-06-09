function loadArgument(argument) {
    var userImg = '';
    if (argument.userMember.profilephoto != null) {
        userImg = argument.userMember.profileThumb;
    } else {
        userImg = '/images/default_avatar_1.png';
    }
    var userImageClass = (argument.userMember.fromThirdParty)?"class='thirdPartyImgSmall'":"";
    var agreedCount = argument.agreed;
    var disagreeCount = argument.disagreed;
    var totalCount = parseInt(agreedCount) + parseInt(disagreeCount);
    var commentCount = argument.commentsCount;
    var ownerFlag = argument.isLoggedInMemberOwner;
    var votedFlag = (argument.isLoggedInMemberOwner)?argument.isLoggedInMemberOwner:argument.isLoggedInUserMemberVoted;
    var lockFlag = (argument.status==1)?false:true;
    var argContent = baseObj.Ellipsis(baseObj.cleanHTMLEntityString(argument.argument), 250);
    var dataSourceText = (argument.source.equalsIgnoreCase('Facebook'))?"<span class=\"dataSource\"> via FaceBook</span>":
        (argument.source.equalsIgnoreCase('Twitter'))?"<span class=\"dataSource\"> via Twitter</span>":'';
    if(argument.isFavorite == -1){
        var favoriteStatus = (lockFlag)?"lockOnG":"lockOffG";
    }else if(argument.isFavorite){
        var favoriteStatus = (lockFlag)?"lockOnG":"favIconOnG";
    }else{
        var favoriteStatus = (lockFlag)?"lockOnG":"favIconOffG";
    }
    if(totalCount > 0){
        var agreedPercentage = baseObj.CalculatePercentage(Math.round((agreedCount / totalCount) * 100));
    } else {
        var agreedPercentage = 'd';
        totalCount = 0;
        /*commentCount = 0;*/
    }

    var profilename = (argument.userMember.fullname ==''||argument.userMember.fullname ==null)?argument.userMember.username:argument.userMember.fullname;
    var argumentContent =   '<div class=\"secondaryContainer longArgument {id:\''+argument.id+'\',locked:\''+lockFlag+'\'}\">' +
        '<div class=\"contentHead\">' +
        '<div class=\"userImgCircleSmall\">' +
        '<a href=\"'+DA.base_url+'profile?id='+argument.userMember.id+'\">' +
        '<img alt=\"'+argument.userMember.username+' on Disagree.me\" src=\"'+userImg+'\" '+userImageClass+'>' +
        '</a>' +
        '</div>' +
        '<p class=\"heading5 secondaryText\">' +
        '<a class=\"DAtip up heading5Link profileUserName\" href=\"'+DA.base_url+'profile?id='+argument.userMember.id+'\">'+baseObj.Ellipsis(profilename, 13)+'</a>' +
        '<span class=\"argueText\">argues</span><br/> ' +
        '<i class=\"timeagoText timeStringSync {timestring:\''+argument.createdtime+'\'}\">'+baseObj.time_difference(argument.createdtime)+ ''+
        dataSourceText+'</i>' +
        '</p>' +
        '<div class=\"argumentUserActions\">' +
        '<div class=\"favIcon sprite-icon '+favoriteStatus+' {argumentId:\''+argument.id+'\',locked:'+lockFlag+', ownerId:\''+argument.userMember.id+'\'}\"></div>' +
        '</div>' +
        '</div>' +
        '<div class=\"contentBody heading3 argumentTitleLongArg\">' +
        '<div id=\"title_'+argument.id+'\" class="argumentCardTitle">'+baseObj.Ellipsis(argument.title,40)+'</div>' +
        '<div class=\"secondaryText disabled argumentDescLongArg\">&quot;'+argContent+'&quot;</div>' +
        '</div>' +
        '<div class=\"postContentActions\">' +
        '<a href=\"'+DA.base_url+'detail?id='+argument.id+'\">' +
        '<span class=\"heading6 readMoreLink\">' +
        '<span class=\"readMoreLink linkRegular\">Read Full Argument</span>' +
        '<i class=\"sprite-icon moreSmallIconG\"></i>' +
        '</span>' +
        '<span class=\"heading6 commentCount\">' +
        '<i class=\"sprite-icon commentSmallIconG\"></i>' +
        '<span>'+commentCount+'</span>' +
        '</span>' +
        '</a>' +
        '</div>' +
        '<div class=\"graphBox\">' +
        '<div class=\"AgreeButton agreementGradient {argumentId:\''+argument.id+'\', username:\''+profilename+'\', agree:\''+agreedCount+'\', owner:'+ownerFlag+', voted:'+votedFlag+', locked:'+lockFlag+'}\" id=\"agree-'+argument.id+'\">' +
        'AGREE' +
        '<div class=\"agreeTip\"></div>' +
        '</div>' +
        '<div class=\"circleMini heading4 sector'+agreedPercentage+' \">'+totalCount+'</div>' +
        '<div class=\"DisagreeButton disagreementGradient {argumentId:\''+argument.id+'\', username:\''+profilename+'\', agree:\''+agreedCount+'\', owner:'+ownerFlag+', voted:'+votedFlag+', locked:'+lockFlag+'}\" id=\"disagree-'+argument.id+'\">' +
        'DISAGREE' +
        '<div class=\"disagreeTip\"></div>' +
        '</div>' +
        '</div>' +
        '</div>';
    return argumentContent;
}

function loadShortArgument(argument) {
    var userImg = '';
    if (argument.userMember.profilephoto != null) {
        userImg = argument.userMember.profileThumb;
    } else {
        userImg = '/images/default_avatar_1.png';
    }

    var userImageClass = (argument.userMember.fromThirdParty)?"class='thirdPartyImgSmall'":"";
    var agreedCount = argument.agreed;
    var disagreeCount = argument.disagreed;
    var totalCount = parseInt(agreedCount) + parseInt(disagreeCount);
    var commentCount = argument.commentsCount;
    var ownerFlag = argument.isLoggedInMemberOwner;
    var votedFlag = (argument.isLoggedInMemberOwner)?argument.isLoggedInMemberOwner:argument.isLoggedInUserMemberVoted;
    var lockFlag = (argument.status==1)?false:true;
    var argContent = baseObj.Ellipsis(baseObj.cleanHTMLEntityString(argument.argument), 32);
    var dataSourceText = (argument.source.equalsIgnoreCase('Facebook'))?"<span class=\"dataSource\"> via FaceBook</span>":
        (argument.source.equalsIgnoreCase('Twitter'))?"<span class=\"dataSource\"> via Twitter</span>":'';
    if(argument.isFavorite == -1){
        var favoriteStatus = (lockFlag)?"lockOnG":"lockOffG";
    }else if(argument.isFavorite){
        var favoriteStatus = (lockFlag)?"lockOnG":"favIconOnG";
    }else{
        var favoriteStatus = (lockFlag)?"lockOnG":"favIconOffG";
    }
    if(totalCount > 0){
        var agreedPercentage = baseObj.CalculatePercentage(Math.round((agreedCount / totalCount) * 100));
    } else {
        var agreedPercentage = 'd';
        totalCount = 0;
        commentCount = 0;
    }
    var profilename = (argument.userMember.fullname ==''||argument.userMember.fullname ==null)?argument.userMember.username:argument.userMember.fullname;
    var argumentContent = '<div class=\"secondaryContainer shortArgument {id:\''+argument.id+'\'}\">' +
        '<div class=\"contentHead\">' +
        '<div class=\"userImgCircleSmall\">' +
        '<a href=\"'+DA.base_url+'profile?id='+argument.userMember.id+'\"><img alt=\"'+argument.userMember.username+' on Disagree.me\" src=\"'+userImg+'\" '+userImageClass+'></a>' +
        '</div>' +
        '<p class=\"heading5 secondaryText\">' +
        '<a class=\"heading5Link profileUserName DAtip up\" href=\"'+DA.base_url+'profile?id='+argument.userMember.id+'\">'+baseObj.Ellipsis(profilename, 19)+'</a>' +
        '<span class=\"argueText\">argues</span><br/>' +
        '<i class=\"timeagoText timeStringSync{timestring:\''+argument.createdtime+'\'}\">'+baseObj.time_difference(argument.createdtime)+
        dataSourceText+'</i>' +
        '</p>' +
        '<div class=\"argumentUserActions\">' +
        '<div class=\"favIcon sprite-icon '+favoriteStatus+' {argumentId:\''+argument.id+'\',locked:'+lockFlag+', ownerId:\''+argument.userMember.id+'\'}"></div>' +
        '</div>' +
        '</div>' +
        '<div class=\"contentBody heading5\">' +
        '<div id=\"title_'+argument.id+'\" class="argumentCardTitle">'+baseObj.Ellipsis(argument.title, 75)+'</div>' +
        '<div class=\"heading6Text disabled argumentDescShortArg\">\"'+argContent+'\"' +
        '</div>' +
        '</div>' +
        '<div class=\"postContentActions\">' +
        '<a href=\"'+DA.base_url+'detail?id='+argument.id+'\">' +
        '<span class=\"heading6 readMoreLinkWrapper\">' +
        '<span class=\"readMoreLink linkRegular\">Read Full Argument</span>' +
        '<i class=\"sprite-icon moreSmallIconG\"></i>' +
        '</span>' +
        '<span class=\"heading6 commentCount\">' +
        '<i class=\"sprite-icon commentSmallIconG\"></i>' +
        '<span>'+commentCount+'</span>' +
        '</span>' +
        '</a>' +
        '</div>' +
        '<div class=\"graphBox\">' +
        '<div class=\'AgreeButton agreementGradient {argumentId:\"'+argument.id+'\", username:\"'+profilename+'\", agree:\"'+agreedCount+'\", owner:'+ownerFlag+', voted:'+votedFlag+', locked:'+lockFlag+'}\' id=\"agree-'+argument.id+'\">' +
        'AGREE' +
        '<div class=\"agreeTip\"></div>' +
        '</div>' +
        '<div class=\"circleMini sector'+agreedPercentage+' heading4\">'+totalCount+'</div>' +
        '<div class=\'DisagreeButton disagreementGradient {argumentId:\"'+argument.id+'\", username:\"'+profilename+'\", agree:\"'+agreedCount+'\", owner:'+ownerFlag+', voted:'+votedFlag+',locked:'+lockFlag+'}\' id=\"disagree-'+argument.id+'\">' +
        'DISAGREE' +
        '<div class=\"disagreeTip\"></div>' +
        '</div>' +
        '</div>';
    if(!argument.isLoggedInMemberOwner){
        argumentContent += '<div class=\"smallText actionCotainer secondaryTextColor\">' +
            '<span class=\"hideArgument\">Hide</span>' +
            '<span title=\"Report this argument as spam\" class=\"reportArgument {type:\'argument\',id:\''+argument.id+'\'}\">' +
            'Report' +
            '</span>' +
            '</div>';
    }
    argumentContent += '</div>';
    return argumentContent;
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
            commenthtml += '<a href=\"'+DA.base_url+'profile?id='+comment.memberId+'" class="DAtip up username heading6Link secondaryText ">'+((comment.fullname==''||comment.fullname==null)?comment.username:comment.fullname)+'</a><span class="disabled voteActionString">&nbsp;&nbsp;'+actionString+' </span>';
            commenthtml += '<i class="smallText disabled timeStamp timeStringSync {timestring:\''+comment.createdtime+'\'}">'+window.baseObj.time_difference(comment.createdtime)+'</i>';
            commenthtml += '</p>';
            commenthtml += '<p class="argumentContent">'+comment.commenttext.linkify()+'</p>';

            commenthtml += '<div class="replyBox disabled" id="commentReply-'+comment.id+'">';
            commenthtml += '<span class="addReplyLink off"><span>Reply</span><i class="sprite-icon moreSmallIconG"></i></span>';
            commenthtml += '<span class="showReplyLink off" id="replyViewSwitch-'+comment.id+'"><span >'+replies[comment.id]+'</span><i class="sprite-icon commentSmallIconG"></i></span>';
            commenthtml += '<div class="replyContentWrapper form">';
            commenthtml += '</div>';
            commenthtml += '</div>';
            commenthtml += '<div class="timelineTip"></div>';
            commenthtml += '</div>';
            var time_diff_in_sec = baseObj.time_difference_seconds(comment.createdtime);
            if(comment.memberId == loggedInUserMember.memberId && time_diff_in_sec < 120){
                var x = Date.now();
                commenthtml += '<div id="r-'+x+'" title=\"Edit your comment (allowed only one minute)\" class="editCommentReply sprite-icon editIconSmallG {type:\'comment\',id:\''+comment.id+'\',memberId:\''+comment.memberId+'\'}" ></div>';
                setTimeout(function(){
                    if (jQuery('#r-'+x).length > 0) {
                        jQuery('#r-'+x).removeClass('editCommentReply').addClass('reportArgument').attr('title','Report this argument as spam');
                    }
                }, (120000 - (time_diff_in_sec*1000) ));
            }else{
                commenthtml += '<div title=\"Report this argument as spam\" class="sprite-icon reportArgument {type:\'comment\',id:\''+comment.id+'\'}" ></div>';
            }
            commenthtml += '</li>';
        });
        /*if(comments.length<10){
         commenthtml += loadNoComments();
         }*/
    }else{
        commenthtml += '<li class="data-l secondaryContainer noCommets"><div class="timelineArgument">';
        commenthtml += '<span>'+DA.NO_COMMENTS_IN_ARGUMENT_DETAIL+'</span>';
        commenthtml += '<div class="timelineTip"></div></div><div style="width:18px;float:left"></div></li>';
        commenthtml += '<li class="data-r secondaryContainer noCommets"><div class="timelineArgument">';
        commenthtml += '<span>'+DA.NO_COMMENTS_IN_ARGUMENT_DETAIL+'</span>';
        commenthtml += '<div class="timelineTip"></div></div><div style="width:16px;float:left"></div></li>';
    }


    return commenthtml;
}

function loadNoComments(){
    var commenthtml ='';

    commenthtml += '<li class="data-l secondaryContainer noCommets"><div class="timelineArgument">';
    commenthtml += '<span>No more Agrees on this argument</span>';
    commenthtml += '</div><div class="timelineTip"></div></li>';
    commenthtml += '<li class="data-r secondaryContainer noCommets"><div class="timelineArgument">';
    commenthtml += '<span>No more Disagrees on this argument</span>';
    commenthtml += '</div><div class="timelineTip"></div></li>';

    return commenthtml;
}

function loadTimelineComment(comment){          //prepare comment dynamiclly when user commented on an argument;input: comment object;output:prepared comment html string
    var vote = (typeof comment.uservote == 'undefined' )?comment.vote:comment.uservote;
    var dirClass = (vote == window.agreeVoteID || vote == window.agreeCommentID)?"data-l":"data-r";
    var profilename = (typeof comment.username == 'undefined' || comment.username == null)?((window.loggedInUserMember.fullname == '' || window.loggedInUserMember.fullname == null)?window.loggedInUserMember.username:window.loggedInUserMember.fullname):((comment.fullname == '' || comment.fullname == null)?comment.username:comment.fullname);

    var commenthtml='';

    commenthtml += '<li class="'+dirClass+' secondaryContainer" id="c'+comment.id+'">';
    commenthtml += '<div class="timelineArgument">';
    commenthtml += '<p class="argumentHead">';
    commenthtml += '<span class="userImgCircleSmall" ><img src="';
    commenthtml +=(typeof comment.userImage == 'undefined')?window.loggedInUserMember.profileThumb:comment.userImage;
    commenthtml += '" alt="user image" class="thirdPartyImgSmall"/></span>';
    commenthtml += '<a href=\"'+DA.base_url+'profile?id=';
    commenthtml += (typeof comment.memberId == 'undefined')?window.loggedInUserMember.memberId:comment.memberId;
    commenthtml += '" class="DAtip up username heading6Link secondaryText ">';
    commenthtml += profilename;
    commenthtml += '</a><span class="disabled voteActionString">&nbsp;&nbsp;commented </span>';
    commenthtml += '<i class="smallText disabled timeStamp">';
    commenthtml += (typeof comment.createdtime == 'undefined')?'0 Sec':baseObj.time_difference(comment.createdtime);
    commenthtml += '<span class="dataSource"></span></i>';
    commenthtml += '</p>';
    commenthtml += '<p class="argumentContent">'+comment.commenttext.linkify()+'</p>';

    commenthtml += '<div class="replyBox disabled" id="commentReply-'+comment.id+'">';
    commenthtml += '<span class="addReplyLink off"><span>Reply</span><i class="sprite-icon moreSmallIconG"></i></span>';
    commenthtml += '<span class="showReplyLink off" id="replyViewSwitch-'+comment.id+'"><span >0</span><i class="sprite-icon commentSmallIconG"></i></span>';
    commenthtml += '<div class="replyContentWrapper">';
    commenthtml += '</div>';
    commenthtml += '</div>';
    commenthtml += '<div class="timelineTip"></div>';
    commenthtml += '</div>';
    if(comment.memberId == loggedInUserMember.memberId){    // just now commented
        var x = Date.now();
        commenthtml += '<div id="r-'+x+'" title=\"Edit your Comment (allowed only one minute)\" class="editCommentReply sprite-icon editIconSmallG {type:\'comment\',id:\''+comment.id+'\',memberId:\''+comment.memberId+'\'}" style="visibility: hidden;"></div>';
        setTimeout(function(){
            if (jQuery('#r-'+x).length > 0) {
                jQuery('#r-'+x).removeClass('editCommentReply').addClass('reportArgument').attr('title','Report this argument as spam');
            }
        }, (120000));
    }else{
        commenthtml += '<div title=\"Report this argument as spam\" class="sprite-icon reportArgument {type:\'comment\',id:\''+comment.id+'\',memberId:\''+comment.memberId+'\'}" style="visibility: hidden;"></div>';
    }
    commenthtml += '</li>';

    return commenthtml;
}

function loadTimelineCommentReply(replies){
    var comentReply ='<div class="wrapperData" id="wrapperData-'+ window.commentId +'">';
    jQuery.each(replies,function(i,reply){
        comentReply += '<div class="commentReply" id="c'+reply.id+'">';
        comentReply += '<p class="argumentHead">';
        comentReply +='<span class="userImgCircleSmall" ><img src="'+reply.userImage+'" alt="user image" class="thirdPartyImgSmall"/></span>';
        comentReply +='<a href=\"'+DA.base_url+'profile?id='+reply.memberId+'" class="username heading6Link secondaryText">'+baseObj.Ellipsis(((reply.fullname=='' || reply.fullname == null)?reply.username:reply.fullname),35)+'</a><span class="disabled">&nbsp;&nbsp;said</span>';
        comentReply +='<i class="smallText disabled timeStamp timeStringSync {timestring:\''+reply.createdtime+'\'}">'+baseObj.time_difference(reply.createdtime)+'<!--span class="dataSource">reply.source</span--></i>';
        comentReply +='</p>';
        comentReply +='<p class="argumentContent">'+reply.commenttext.linkify();
        comentReply +='</p>';
        var time_diff_in_sec = baseObj.time_difference_seconds(reply.createdtime);
        if(window.loggedInUserMember.memberId == reply.memberId &&  time_diff_in_sec < 120){
            var x = Date.now();
            comentReply += '<div class="userActions">';
            comentReply += '<div id="r-'+x+'" title="Edit your reply (allowed only one minute)" class="editCommentReply sprite-icon editIconSmallG {type:\'Reply\',id:\''+reply.id+'\',memberId:\''+reply.memberId+'\'}"></div></div>';
            setTimeout(function(){
                if (jQuery('#r-'+x).length > 0) {
                    jQuery('#r-'+x).remove();
                }
            }, (120000 - (time_diff_in_sec*1000)));
        }
        comentReply += '</div>';
    });

    comentReply += '</div>';
    return comentReply;
}

function loadAddReply(commentId){
    var commenthtml ='';
    commenthtml += '<textarea cols="38" rows="6" class="replyTextArea placeholder defaultContent {defaultText : \'Type your reply here\',label:\'Reply\'} validate required"></textarea><div class="postLinks"><input id="postArgumentFBCheck" type="checkbox"><label for="postArgumentFBCheck"></label><label>Post on Facebook</label><input id="postArgumentTWCheck" type="checkbox"><label for="postArgumentTWCheck"></label><label>Share on Twitter</label></div><div class="replyActions"><button class="primaryButton addReplyButton {parentId:\''+commentId+'\' }">ADD REPLY</button><span class="disabled cancelReply">Cancel</span></div>';
    return commenthtml;
}

function loadProfileTip(userMemberData){
    var followClass = (userMemberData.isFollowing) ? "unfollowMember disagreementGradient" : "followMember agreementGradient";
    var followSpanClass = (userMemberData.isFollowing) ? "unfollowMemberSpan" : "followMemberSpan";
    var followString = (userMemberData.isFollowing) ? "Unfollow" : "Follow";
    userMemberData.followButton = (userMemberData.id == loggedInUserMember.memberId)?'':'<button class="gradient ' + followClass + ' {followeMemberId :\'' + userMemberData.id + '\'} actionSelector"><span class="' + followSpanClass + '">'+followString+'</span></button>';
    var userImageClass = (userMemberData.fromThirdParty)?"class='thirdPartyImgSmall'":"";
    var memberId = (userMemberData.id != undefined)?userMemberData.id:userMemberData.memberId;
    var addressString = (userMemberData.location =='' || userMemberData.location == null)?"":
        '<i class=\"sprite-icon locationSmallIconG\"></i><span>'+ baseObj.Ellipsis(userMemberData.location,12)+'</span>'

    var htmlContent = '<div class=\"contentHead a'+memberId+'\">' +
        '<a href=\"'+DA.base_url+'profile?id='+memberId+'\" class=\"userImgCircleSmall\" >' +
        '<img src=\"'+userMemberData.profileThumb+'\" alt=\"'+userMemberData.username+' on Disagree.me\" '+userImageClass+'/>' +
        '</a>' +
        '<a href=\"'+DA.base_url+'profile?id=' + userMemberData.id + '\" class=\"contentBody heading3\">' +
        baseObj.Ellipsis(((userMemberData.fullname==''||userMemberData.fullname==null)?userMemberData.username:userMemberData.fullname), 10) +
        '</a>'+
        userMemberData.followButton+
        '<address class=\"addressStats\">'+
        addressString+
        '</address>'+
        '</div>' +
        '<div class=\"secondaryContainer othersStats\">' +
        '<ul class=\"horizontalMenu\">' +
        '<li class=\"statsInfoOthers\">' +
        '<span class=\"othersStatTitle heading6\">ARGUMENTS</span>' +
        '<a href=\"'+DA.base_url+'profile?id='+ userMemberData.id+'#argumentFed\"  class=\"headingLight\">'+
        userMemberData.argumentCreatedCount+
        '</a>' +
        '</li>' +
        '<li class=\"statsInfoOthers\">' +
        '<span class=\"othersStatTitle heading6\">FOLLOWERS</span>' +
        '<a href=\"'+DA.base_url+'profile?id='+ userMemberData.id+'#followersFeed\" class=\"headingLight\">'+
        userMemberData.followerCount+
        '</a>' +
        '</li>' +
        '<li class=\"statsInfoOthers\">' +
        '<span class=\"othersStatTitle heading6\">FOLLOWING</span>' +
        '<a href=\"'+DA.base_url+'profile?id='+ userMemberData.id+'#followingFeed\" class=\"headingLight\">'+
        userMemberData.followedCount+
        '</a>' +
        '</li>' +
        '</ul>' +
        '</div>';

    return htmlContent;
}

function loadVotedUserProfile(userMemberData){
    var profilename = (typeof userMemberData.fullname == 'undefined' || userMemberData.fullname ==''||userMemberData.fullname ==null)?userMemberData.username:userMemberData.fullname;
    var commentId = (typeof userMemberData.commentId == 'undefined')?userMemberData.commentId : null;
    var actionString = (userMemberData.vote == '1')?'agreed':'disagreed';
    var commentLinkString = userMemberData.commentId==null?"":'<a href=\"#c'+userMemberData.commentId+'\" class=\"commentLink\" onClick="baseObj.CloseModel();"><i class=\"sprite-icon commentSmallIconG\"></i></a>'
    var htmlContent = '<p class=\"argumentHead primaryBorder\">' +
        '<span class=\"userImgCircleSmall\">' +
        '<img class=\"thirdPartyImgSmall\" alt=\"user image\" src=\"'+userMemberData.profileThumb+'\">' +
        '</span>' +
        '<a class=\"username heading6Link secondaryText\" href=\"'+DA.base_url+'profile?id='+userMemberData.id+'">'+
        baseObj.Ellipsis(profilename,15)+
        '</a>' +
        '<span class=\"disabled voteActionString\">&nbsp;&nbsp;voted</span>' +
        '<i class=\"smallText disabled timeStamp timeStringSync {timestring:\''+userMemberData.votedTime+'\'}\">'+
        baseObj.time_difference(userMemberData.votedTime)+
        '</i>'+
        commentLinkString+
        '</p>';
    return htmlContent;
}

function loadInviteUserProfile(userMemberData){
    var profilename = (typeof userMemberData.fullname == 'undefined' || userMemberData.fullname == null)?userMemberData.username : userMemberData.fullname ;
    var addressString = (userMemberData.location =='' || userMemberData.location == null)?"":
        '<span class="addressStats"><i class=\"sprite-icon locationSmallIconG\"></i><span>'+ baseObj.Ellipsis(userMemberData.location,17)+'</span></span>';
    var htmlContent = '<p class=\"argumentHead primaryBorder {username:\''+
        profilename+
        '\'}\">' +
        '<span class=\"userImgCircleSmall\">' +
        '<img class=\"thirdPartyImgSmall\" alt=\"user image\" src=\"'+userMemberData.profileThumb+'\">' +
        '</span>' +
        '<a class=\"username heading6Link secondaryText\" href=\"'+DA.base_url+'profile?id='+userMemberData.id+'">'+
        baseObj.Ellipsis(profilename,22)+
        '</a>' +
        '<input type="checkbox" class="suggestUsersuggestUser" name="suggest[]" value="'+
        userMemberData.id+
        '" id="s'+
        userMemberData.id+
        '"><label for="s'+
        userMemberData.id+
        '" ></label>'+
        addressString+
        '</p>';
    return htmlContent;
}

function loadFBFeedHTML(feed){
    var username = baseObj.Ellipsis(feed.feedusername,13);
    var userImage = feed.feeduserimage;
    var userId = feed.feeduserid;
    var createdTime = feed.feedtime;
    var feedContent = baseObj.Ellipsis(feed.feedcontent,240);
    var htmlContent = '';
    htmlContent += '<div class="fbFeed primaryBorder secondaryContainer ">' +
        '<p class="argumentHead">' +
        '<span class="userImgCircleSmall nofollow" ><img src="'+userImage+'" alt="user image" class="thirdPartyImgSmall"/></span>' +
        '<span class="username heading6Link secondaryText {username:\''+feed.feedusername+'\',oauth_uid:\''+userId+'\',permaLink:\''+feed.permaLink+'\'}">'+username+'</span>' +
        '<span class="disabled voteActionString">&nbsp;&nbsp;posted</span>' +
        '<i class="smallText disabled timeStamp timeStringSync {timestamp:\''+feed.feedtime+'\'}">'+baseObj.time_difference_fb(feed.feedtime)+'<span class="dataSource"> via FaceBook</span></i>' +
        '</p>' +
        '<button class="primaryButton gradient fbFeedPost"><span class="sprite-icon daIconOffW"></span></button>' +
        '<div class="argumentDesc">'+feedContent+'</div>' +
        '<div class="argumentDescFull">'+feed.feedcontent+'</div> ' +
        '</div>';
    return htmlContent;
}

function loadTWTweetHTML(tweet){
    var twTeetHtml = '<div class="twTweet primaryBorder secondaryContainer ">' +
        '<p class="argumentHead">' +
        '<span class="userImgCircleSmall nofollow">' +
        '<img class="thirdPartyImgSmall" alt="user image" src="'+
        tweet['user']['profile_image_url'] +'" />' +
        '</span>' +
        '<span class="username heading6Link secondaryText {username:\''+tweet['user']['name']+'\',feedId:\''+tweet['id_str']+'\',permaLink:\''+tweet['permaLink']+'\'}"> '+
        baseObj.Ellipsis(tweet['user']['name'],13) +
        '</span>' +
        '<span class="disabled voteActionString"> &nbsp;&nbsp;Tweeted </span>' +
        '<i class="smallText disabled timeStamp timeStringSync {timestring:\''+tweet['created_at']+'\'}">' +
        baseObj.time_difference_tw(tweet['created_at']) +
        '<span class="dataSource"> via Twitter</span>' +
        '</i>' +
        '</p>' +
        '<button class="primaryButton gradient twTweetPost">' +
        '<span class="sprite-icon daIconOffW"></span>' +
        '</button>' +
        '<div class="argumentDesc">' +
        baseObj.Ellipsis(tweet['text']) +
        '</div>' +
        '<div class="argumentDescFull">' +
        tweet['text'] +
        '</div>' +
        '</div>';
    return twTeetHtml;
}

/**
 * prepareNotificationMessage
 * prepare notification message
 *
 * data structure - id, type, recordId, ownerId, ownerEmail, followinguserprofilephoto, followinguserid, followingusername, argumentId, argumentTile, createdtime, argumentId ,argumentTitle ,commentId ,commentText
 *
 * @param mixed data
 * @return string msg
 */
function prepareNotificationMessage(data){
    var html ='';
    var imagePath = (data.userprofilephoto.startsWith("https://graph.facebook.com")||data.userprofilephoto.startsWith("http://graph.facebook.com"))?data.userprofilephoto:DA.base_url+data.userprofilephoto;
    switch (parseInt(data.type)) {
        case DA.COMMENT_NOTIFICATION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id=' + data.userid + '" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ imagePath +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText"> commented on </span>'+
                '<a href="'+ DA.base_url +'detail?id='+ data.argumentId  +'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.argumentTitle, 20)+'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.AGREE_NOTIFICATION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ imagePath +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+ data.username +'</a>'+
                '<span class="notificationMsgText secondaryText"> agreed with </span>'+
                '<a href="'+ DA.base_url +'detail?id='+ data.argumentId +'" class="linkStrong notificationArgumentLink heading4Link">'+ baseObj.Ellipsis(data.argumentTitle, 45) +'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.DISAGREE_NOTIFICATION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ imagePath +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+ data.username +'</a>'+
                '<span class="notificationMsgText secondaryText"> disagreed with </span>'+
                '<a href="'+ DA.base_url +'detail?id='+ data.argumentId +'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.argumentTitle, 45)+'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.FOLLOW_ARGUMENT_NOTIFICATION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ imagePath +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+ data.username +'</a>'+
                '<span class="notificationMsgText secondaryText"> added </span>'+
                '<a href="'+ DA.base_url +'detail?id='+ data.argumentId +'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.argumentTitle, 35)+'</a> ' +
                '<span class="notificationMsgText secondaryText">to his favorites</span>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.FOLLOW_MEMBER_NOTIFICATION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+data.userid+'" class="userImgCircleSmall" >'+
                '<img alt="'+data.username+' on Disagree.me" src="'+imagePath+'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+data.userid+'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText">is following you</span>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.REPLY_TO_ARGUMENT_OWNER_NOTICTION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id=' + data.userid + '" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ imagePath +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText">Replied to comment</span>'+
                '<a class="heading4Link" href="'+ DA.base_url +'detail?id='+data.argumentId+'#c'+ data.commentId +'">'+ data.commentText +'</a>'+
                '<span class="notificationMsgText secondaryText">on your argument</span> ' +
                '<a class="heading4Link" href="' + DA.base_url + 'detail?id=' + data.argumentId + '">' + data.argumentTitle + '</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.REPLY_TO_COMMENT_OWNER_NOTIFICATION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id=' + data.userid + '" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ imagePath +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText">replied on your  comment </span>'+
                '<a class="heading4Link" href="' + DA.base_url + 'detail?id=' + data.argumentId + '#c'+ data.commentId +'">'+ data.commentText +'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.INVITE_TO_ARGUMENT:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ imagePath +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+ data.username +'</a>'+
                '<span class="notificationMsgText secondaryText"> invited you to argue on his argument </span>'+
                '<a href="'+ DA.base_url +'detail?id='+ data.argumentId +'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.argumentTitle, 45)+'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
    }
    return html;
}

/** prepare activity tab message
 *--------------------------------------------------------------------------------------------------------------------------
 * Object Structure
 * recordId
 * action (1-argument started, 2,3-vote(Agree/ Disagree), 4- commented on argument, 5- replied on an argument, 6-Follow User, 7- Follow Argument)
 * ExtraField1-Extrafield-5
 * createdTime
 * --------------------------------------------------------------------------------------------------------------------------
 * status = 1 => Started Argument: returns argumentid as recordid, argumentTitle as extraField1, createdtime
 * status = 2,3 => Vote (+ comment) : returns argumentid as recordid,uservote as action,argumentTitle,commentid,commenttext as extraFields, createdtime
 * status = 4 => Comment: returns argumentid as recordid, argumentTitle,commentid,commenttext,uservote as extraFields, createdtime
 * status = 5 => Reply: returns argumentid as recordid, argumentTitle,commentid,commenttext,replyId,replyText as extraFields, createdtime
 * status = 6 => Follow User: returns followedmemberid as recordid, followedmemberusername as extraFields, createdtime
 * status = 7 => Follow Argument: returns argumentid as recordid, argumentTitle as extraFields, createdtime
 * --------------------------------------------------------------------------------------------------------------------------
 *
 * @param data
 * @return {String}
 */
function prepareActivityTabMessage(data){
    var html ='';
    switch (parseInt(data.action)) {
        case DA.START_ARG_ACTION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id=' + data.userid + '" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ data.profileThumb +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText"> Started an argument </span>'+
                '<a href="'+ DA.base_url +'detail?id='+ data.recordId +'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.ExtraField1, 25)+'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.DISAGREE_ACTION:
            var commentString = (data.ExtraField2 == null || data.ExtraField2 == '' || data.ExtraField2 == undefined)
                ? '<span class="notificationMsgText secondaryText"> disagreed on </span>'
                : '<span class="notificationMsgText secondaryText"> disagreed with </span>' +
                '<a href="'+ DA.base_url +'detail?id='+ data.recordId +'#c'+data.ExtraField2+'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.ExtraField3, 25)+'</a>' +
                '<span class="notificationMsgText secondaryText"> on </span>';
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ data.profileThumb +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+ data.username +'</a>'+
                commentString +
                '<a href="'+ DA.base_url +'detail?id='+ data.recordId +'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.ExtraField1, 25)+'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.AGREE_ACTION:
            var commentString = (data.ExtraField2 == null || data.ExtraField2 == '' || data.ExtraField2 == undefined)
                ? '<span class="notificationMsgText secondaryText"> agreed on </span>'
                : '<span class="notificationMsgText secondaryText"> agreed with </span>' +
                '<a href="'+ DA.base_url +'detail?id='+ data.recordId +'#c'+data.ExtraField2+'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.ExtraField3, 25)+'</a>' +
                '<span class="notificationMsgText secondaryText"> on </span>';
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ data.profileThumb +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+ data.username +'</a>'+
                commentString +
                '<a href="'+ DA.base_url +'detail?id='+ data.recordId +'" class="linkStrong notificationArgumentLink heading4Link">'+ baseObj.Ellipsis(data.ExtraField1, 45) +'</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.COMMENT_ACTION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id=' + data.userid + '" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ data.profileThumb +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText">commented</span>'+
                '<a class="heading4Link" href="'+ DA.base_url +'detail?id='+data.ExtraField2+'#c'+ data.commentId +'">'+ baseObj.Ellipsis(data.ExtraField3,25) +'</a>'+
                '<span class="notificationMsgText secondaryText">on </span> ' +
                '<a class="heading4Link" href="' + DA.base_url + 'detail?id=' + data.recordId + '">' +baseObj.Ellipsis(data.ExtraField1,25)+ '</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.REPLY_ACTION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id=' + data.userid + '" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ data.profileThumb +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText">Replied to comment</span>'+
                '<a class="heading4Link" href="'+ DA.base_url +'detail?id='+data.ExtraField2+'#c'+ data.commentId +'">'+ baseObj.Ellipsis(data.ExtraField3,25) +'</a>'+
                '<span class="notificationMsgText secondaryText">on the argument</span> ' +
                '<a class="heading4Link" href="' + DA.base_url + 'detail?id=' + data.recordId + '">' + baseObj.Ellipsis(data.ExtraField1,25)+ '</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.FOLLOW_MEMBER_ACTION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+data.userid+'" class="userImgCircleSmall" >'+
                '<img alt="'+data.username+' on Disagree.me" src="'+ data.profileThumb +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+data.userid+'" class="heading4Link profileUserName DAtip down">'+data.username+'</a>'+
                '<span class="notificationMsgText secondaryText">started following</span>'+
                '<a class="heading4Link profileUserName DAtip down" href="' + DA.base_url + 'profile?id=' + data.recordId + '" >' + baseObj.Ellipsis(data.ExtraField1,45)+ '</a>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
        case DA.FOLLOW_ARGUMENT_ACTION:
            html += '<li class="notification">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="userImgCircleSmall" >'+
                '<img alt="'+ data.username +' on Disagree.me" src="'+ data.profileThumb +'" width="35" height="35"/>'+
                '</a>'+
                '<div class="notificationMsg">'+
                '<a href="'+ DA.base_url +'profile?id='+ data.userid +'" class="heading4Link profileUserName DAtip down">'+ data.username +'</a>'+
                '<span class="notificationMsgText secondaryText"> added </span>'+
                '<a href="'+ DA.base_url +'detail?id='+ data.recordId +'" class="linkStrong notificationArgumentLink heading4Link">'+baseObj.Ellipsis(data.ExtraField1, 35)+'</a> ' +
                '<span class="notificationMsgText secondaryText">to his favorites</span>'+
                '</div>'+
                '<span class="timeStamp smallBoldText secondaryTextColor timeStringSync {timestring:\''+data.createdtime+'\'}">'+baseObj.time_difference(data.createdtime)+'</span>'+
                '</li>';
            break;
    }
    return html;
}