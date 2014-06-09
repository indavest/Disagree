jQuery(document).ready(function () {
    init();
    var options = new Object;
    var urlVars = baseObj.getUrlVars();
    var postToFB = (urlVars['fbshare'] == 'true')?true:false;
    var postToTw = (urlVars['twshare'] == 'true')?true:false;

    if(postToFB){
    	options.link = DA.base_url+"detail?id="+urlVars['id'];
    	options.title = window.currArgumentObj.title;
		options.description = jQuery("#currentArgumentDesc").html();
		options.img = window.currArgumentObj.profilephoto;
        if(postToTw){
            options.twFlag = postToTw;
            options.twurl =DA.base_url+"detail?id="+window.currArgumentObj.id;
            options.twdescription = window.currArgumentObj.title;
        }
		setTimeout(function(){baseObj.postToFB(options);},3000);
    }else {
        if(postToTw){
            options.url =DA.base_url+"detail?id="+window.currArgumentObj.id;
            options.description = window.currArgumentObj.title;
            baseObj.postToTW(options);
        }
    }

    jQuery(".addReplyLink").live('click', function (e) {
        if(window.currArgumentObj.status == '1'){
        var commentId = togglecommentReply(jQuery(e.target).closest(".addReplyLink"));
        if (jQuery(this).hasClass("on")) {
            jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html(loadAddReply(commentId));
            jQuery(this).siblings(".replyContentWrapper").show();
            jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").children(".replyTextArea").trigger('mouseDown mouseUp').focus();
        } else if (jQuery(this).hasClass("off")) {
            jQuery(this).siblings(".replyContentWrapper").hide();
        }
        }else{
            baseObj.Showmsg(DA.VOTE_ON_LOCKED_ARGUMENT,false);
        }
    });

    jQuery(".showReplyLink").live("click", function (e) {
        var commentId = togglecommentReply(jQuery(e.target).closest(".showReplyLink"));
        window.commentId = commentId;
        if (jQuery(this).hasClass("on")) {
            jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html(fetchReplies(commentId));
            jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").attr('id',"replyContentWrapper-" + commentId);
            /*jQuery("#replyContentWrapper-"+commentId).children(".wrapperData").attr('id',"wrapperData-" + commentId);*/
            jQuery(this).siblings(".replyContentWrapper").show();
        } else if (jQuery(this).hasClass("off")) {
            jQuery(this).siblings(".replyContentWrapper").hide();
        }
    });

    jQuery(".cancelReply").live('click', function () {
        jQuery(this).closest(".replyContentWrapper").siblings(".addReplyLink.on").trigger('click');
    });

    jQuery("#postToFB").click(function(){
        var options = new Object;
        options.link = DA.base_url+"detail?id="+window.currArgumentObj.id;
        options.title = window.currArgumentObj.title;
        options.img = window.currArgumentObj.profilephoto;
        options.description = jQuery("#currentArgumentDesc").html();
        baseObj.postToFB(options);
    });
    jQuery("#postToTW").click(function(){
        var options = new Object;
        options.url = DA.base_url+"detail?id="+window.currArgumentObj.id;
        options.description = window.currArgumentObj.title;
        baseObj.postToTW(options);
    });

    jQuery(window).scroll(function () {
        if (!window.isloading && jQuery(window).scrollTop() >= jQuery(document).height() - jQuery(window).height() - 100) {
            window.commentsLowerLimit = window.commentsLowerLimit + window.commentsPerLoad;
            fetchComments(false);
        }
    });
    
    jQuery("li.data-l,li.data-r").live('mouseenter', function(e){
        e.stopPropagation();
    	jQuery(this).find('.reportArgument,.editCommentReply').css({'visibility':'visible'});
    });
    
    jQuery("li.data-l,li.data-r").live('mouseleave', function(e){
        e.stopPropagation();
    	jQuery(this).find('.reportArgument,.editCommentReply').css({'visibility':'hidden'});
    });

    jQuery(".customFBShareButton").click(function(){
        options.link = DA.base_url+"detail?id="+window.currArgumentObj.id;
        options.title = window.currArgumentObj.title;
        options.description = jQuery("#currentArgumentDesc").html();
        options.img = window.currArgumentObj.profilephoto;
        setTimeout(function(){baseObj.postToFB(options);},30);
    });

    jQuery("#argumentOwnerImg").click(function(){
        window.location = DA.base_url+'profile?id='+window.currArgumentObj.memberId;
    });

    jQuery(".showVotes").click(function(e){
        loadVotedPeople(jQuery(this).metadata().gender,jQuery(this).metadata().vote);
    });
    jQuery(".commentLink").live('click',function(){
        baseObj.CloseModel();
        baseObj.HideTip();
    })
    jQuery("#popupContainer").live('mouseenter',function(){
       if(jQuery("#scrollbar_track_votes_popup").length >0){
           jQuery("#scrollbar_track_votes_popup").css("opacity","1");
       }
    });
    jQuery("#popupContainer").live('mouseleave',function(){
        if(jQuery("#scrollbar_track_votes_popup").length >0){
            jQuery("#scrollbar_track_votes_popup").css("opacity","0.3");
        }
    });

    jQuery(".inviteIcon").click(function(e){
        loadFollowersAndFollowingPeople();
    });

    jQuery("#inviteAll").live('change',function(){
        var status = jQuery("#inviteAll").attr('checked') == 'checked'?true:false;
        jQuery(".suggestUsersuggestUser").each(function(i,e){
            jQuery(e).attr('checked', status);
        });
    });
    jQuery("button#inviteUsersToArgument").on('click',function(e){
        sendArgumentInviteToUsers();
    });
    jQuery(".editArgIcon").click(function(e){
        e.stopPropagation();
        if (loggedInUserMember) {
            jQuery("#newArgId").val(jQuery("#argumentDetailHead").metadata().id);
            jQuery("#updateArgButton").show().siblings("#postNewArgButton").hide();
            jQuery("#newArgTitle").val(window.currArgumentObj.title);
            jQuery("#newArgDesc").val(jQuery("#currentArgumentDesc").text());
            jQuery("#TopicSelector").customSelectBox().setSelection(window.currArgumentObj.topic);
            baseObj.OpenModel(jQuery(".startArgumentContainer"));
        } else {
            baseObj.OpenModel(jQuery(".loginContainer"));
        }
    });

    jQuery(".editCommentReply").live('click',function(){
        var metaData = jQuery(this).metadata();
        var prevcontent = (metaData.type == 'comment')?
            jQuery(this).siblings(".timelineArgument").children(".argumentContent").text():
            jQuery(this).parent().siblings(".argumentContent").text();
        if (loggedInUserMember) {                                                                   //check whether user logged in or not to vote
            if (loggedInUserMember.memberId == metaData.memberId) {
                jQuery(".updateComment .popupHeading,.updateComment .popupLable").text("Edit Your "+metaData.type);
                jQuery("#updateCommentReplyText").val(prevcontent);
                jQuery("#updateCommentReply").metadata().commentId = metaData.id;
                jQuery("#updateCommentReply").metadata().type = metaData.type;
                baseObj.OpenModel(jQuery(".updateComment"));
            }
        }
    });
});

function loadFollowersAndFollowingPeople(){
    jQuery.ajax({
        url:DA.base_url+'action/loadFollowersAndFollowingPeople',
        type:'post',
        datatype:'json',
        beforeSend:function(){
            return (window.loggedInUserMember && (window.currArgumentObj.memberId == window.loggedInUserMember.memberId));
        },
        success:function(res){
            if(res.response == undefined){
                res = eval('('+res+')');
            }
            if (res.response) {
                var r = new Object;
                var content = '<div class=\"headText PrimaryTextColor secondaryBorderColor\"><h3>Users Following / Followed</h3>'+
                                  '<div class=\"userSearchBoxWrapper\">'+
                                    '<i class=\"sprite-icon searchIconG\"></i>'+
                                    '<input type=\"text\" id="inviteUserSearch" class=\"placeholder defaultContent contentSearchBox {defaultText : \'Search...\',searchWrapper:\'argumentHead\', searchTextHolder:\'username\',searchArea:\'suggestArgumentContent\'}\">'+
                                  '</div>'+
                              '</div>'+
                              '<div id="invitePopupContentWrapper"><form name="suggestArgument" class="suggestArgument" id="suggestArgumentContent">';
                jQuery.each(res.data,function(i,e){
                    content += loadInviteUserProfile(e);
                });
                content += '<input type=\"hidden\" name=\"argumentId\" class=\"currargumentId\" value=\"'+window.currArgumentObj.id+'\" /></div></form>' +
                           '<button class=\"primaryButton\" id=\"inviteUsersToArgument\" onclick=\"sendArgumentInviteToUsers();\">Invite</button>' +
                '<p class=\"selectAllBox\"><span>Select All</span><input type=\"checkbox\" id=\"inviteAll\"><label for=\"inviteAll\"></label></p>';

                jQuery(".inviteUserDisplayBox").html(content);
                baseObj.OpenModel(jQuery("#invitePopUp"));
                baseObj.processPlaceHolder(jQuery("#inviteUserSearch"));

                if(jQuery("#invitePopupContentWrapper").outerHeight(true) >= 318){
                    /*
                     * add for scroll func
                     */
                    jQuery("#invitePopUp .argumentHead").css({'width':'210px'});
                    var scrollHTML = '<div class="scrollbar_track" id="scrollbar_track_invite_popup"><div class="scrollbar_handle" id="scrollbar_handle_invite_popup" ></div></div>';
                    jQuery('#invitePopupContentWrapper').prepend(scrollHTML);
                    baseObj.initScroll('suggestArgumentContent','scrollbar_track_invite_popup');
                }
            } else {
                baseObj.Showmsg(DA.NO_FOLLOWERS_AND_FOLLOWING)
            }
        }
    });
}

function sendArgumentInviteToUsers(){
    jQuery.ajax({
        url:DA.base_url+'action/sendArgumentInviteToUsers',
        type:'post',
        datatype:'json',
        data:jQuery(".suggestArgument").serializeArray(),
        beforeSend:function(){
            baseObj.CloseModel();
            return (window.loggedInUserMember && (window.currArgumentObj.memberId == window.loggedInUserMember.memberId));
        },
        success:function(res){
            if(res.response == undefined){
                res = eval('('+res+')');
            }
            if(res.response){
                baseObj.Showmsg(DA.INVITE_FROM_ARGUMENT_TO_USERS_SUCCESS,true);
            }else{
                baseObj.Showmsg(DA.INVITE_FROM_ARGUMENT_TO_USERS_FAIL,false);
            }
        }
    });
}

function init() {
    window.currArgumentObj = jQuery(".argumentDataObj").metadata();
    window.initReplyFiller = null;
    window.commentsLowerLimit = 0;
    window.commentsPerLoad = 10;
    window.hasMoreComments = true;

    window.selectors = new Object;
    window.selectors.agreeTotalPercentage = jQuery("#agreeTotalPercentage");
    window.selectors.maleAgreeVoteCount = jQuery("#maleAgreeVoteCount");
    window.selectors.femaleAgreeVoteCount = jQuery("#femaleAgreeVoteCount");
    window.selectors.totalAgreeVoteCount = jQuery("#totalAgreeVoteCount");
    window.selectors.currentTotalVoteCount = jQuery("#argumentTotalVoteCount");
    window.selectors.totalDisagreeVoteCount = jQuery("#totalDisagreeVoteCount");
    window.selectors.maleDisagreeVoteCount = jQuery("#maleDisagreeVoteCount");
    window.selectors.femalDisagreeVoteCount = jQuery("#femalDisagreeVoteCount");
    window.selectors.disagreeTotalPercentage = jQuery("#disagreeTotalPercentage");
    window.selectors.agreeButtonSelector = jQuery("#agree-"+window.currArgumentObj.id);
    window.selectors.disAgreeButtonSelector = jQuery("#disagree-"+window.currArgumentObj.id);

    /*set page title with argument title*/
    document.title = baseObj.cleanHTMLEntityString(window.currArgumentObj.title) + ' - Disagree.me'
    /*scroll down to argument by default*/
    baseObj.scrollPageTo(false,130);

    /*load first 10 comments by default*/
    fetchComments(true);
    /*create a sync thread that can sync all data on the current page*/
    setInterval(Sync, DA.AJAX_TIME_INTERVAL*60000);

    /*convert created time utc string to timeago string*/
    var timeString = baseObj.time_difference(window.currArgumentObj.createdTime);
    jQuery("#createdTimePlaceHolder").text(timeString).addClass('timeStringSync').metadata().timestring = window.currArgumentObj.createdTime;

    /*linkify argument title and description*/
    linkifyHTMLStrings();
}

function linkifyHTMLStrings(){
    jQuery("#title_"+window.currArgumentObj.id).html(jQuery("#title_"+window.currArgumentObj.id).text().linkify()).children("a").toggleClass('linkRegular heading2Link');
    jQuery("#currentArgumentDesc").html(jQuery("#currentArgumentDesc").text().linkify());
}

function Sync() {
    var loggedInmemberId = (!window.loggedInUserMember) ? 0 : window.loggedInUserMember.memberId;
    if (loggedInmemberId != undefined && window.currArgumentObj != undefined) {
        jQuery.ajax({
            url:"sync/detail",
            type:"POST",
            data:'memberId=' + loggedInmemberId + '&argumentId=' + window.currArgumentObj.id,
            dataType:"json",
            beforeSend:function () {
                return (loggedInmemberId != undefined && window.currArgumentObj != undefined);
            },
            success:function (res) {

                //process votes
                if (res.data.votes) {
                    postVoteHTML(res.data.votes[0]);
                }

                //process comments
                if (res.data.comments) {
                    jQuery.each(res.data.comments, function (i, e) {
                        postCommentHTML(e);
                    });
                }
                //process replyCounts
                if (res.data.replyCount) {
                    for (var commentid in res.data.replyCount) {
                        var count = parseInt(jQuery("#replyViewSwitch-" + commentid).children("span").text()) + parseInt(res.data.replyCount[commentid]);
                        jQuery("#replyViewSwitch-" + commentid).children("span").text(count);
                    }
                }

                //process favorite argument update
                if (res.data.favorite != undefined) {
                    if (jQuery("#argumentDetailHead .favIcon i").hasClass("favIconOnG") != res.data.favorite) {
                        var result = new Object;
                        result.response = true;
                        favoriteCallBack(result);
                    }
                }
            }
        });
    } else {
        //something went wrong. javascript loading not yet completed to send this call.
    }
}

function loadVotedPeople(gender,vote){
    jQuery.ajax({
        url:DA.base_url+'action/loadVotedPeople',
        data:{argumentId:window.currArgumentObj.id,gender:gender,vote:vote},
        type:'post',
        dataType:'json',
        success:function(res){
            var headingText = '';
            var isMultiLine = false;
            if( gender === 'M' && parseInt(vote) === window.agreeVoteID ){
                headingText = '<h3>Male(s) who <span class="agreementText">agreed</span></h3>'
            }else if( gender === 'M' && parseInt(vote) === window.disagreeVoteID){
                headingText = '<h3>Male(s) who <span class="disagreementText">disagreed</span></h3>'
            }else if( gender === 'F' && parseInt(vote) === window.agreeVoteID ){
                headingText = '<h3>Female(s) who <span class="agreementText">agreed</span></h3>'
            }else if( gender === 'F' && parseInt(vote) === window.disagreeVoteID){
                headingText = '<h3>Female(s) who <span class="disagreementText">disagreed</span></h3>'
            }else if( gender === '' && parseInt(vote) === window.agreeVoteID ){
                headingText = '<h3>People who <span class="agreementText">agreed</span></h3>'
            }else if( gender === '' && parseInt(vote) === window.disagreeVoteID){
                headingText = '<h3>People who <span class="disagreementText">disagreed</span></h3>'
            }else if( gender === '' && vote === ''){
                isMultiLine= true;
                headingText = '<h3>People who <span class="agreementText">agreed</span></h3>';
                headingText += '<h3>People who <span class="disagreementText">disagreed</span></h3>';
            }

            var content = '<div id="votesPopupContentWrapper"><div class="headText">';
            content += headingText;
            content += '</div><div class="votersList primaryBorder">';
            var contentAgrees = '<div class="agreeVotes">';
            var contentDisagrees = '<div class="disagreeVotes">';
            var contentHtml ='';

            for(var counter = 0;counter<res.data.length;counter++){
                if(isMultiLine){
                    if(parseInt(res.data[counter].vote)===window.agreeVoteID){
                        contentAgrees += loadVotedUserProfile(res.data[counter]);
                    }else if(parseInt(res.data[counter].vote)===window.disagreeVoteID){
                        contentDisagrees += loadVotedUserProfile(res.data[counter]);
                    }
                }else{
                    contentHtml += loadVotedUserProfile(res.data[counter]);
                }
            }
            contentAgrees +='</div>';
            contentDisagrees +='</div>';
            if(isMultiLine){
                if(contentAgrees=='<div class="agreeVotes"></div>'){contentAgrees='<div class="agreeVotes">&nbsp;</div>'}
                if(contentDisagrees=='<div class="disagreeVotes"></div>'){contentDisagrees='<div class="disagreeVotes">&nbsp;</div>'}
                content += contentAgrees;
                content += contentDisagrees;
            }else{
                content += contentHtml;
            }
            content += '</div></div>';
            jQuery(".votedUserDisplayBox").html(content);
            baseObj.OpenModel(jQuery(".votedUserDisplayBox"));
            if(jQuery(".votedUserDisplayBox").outerHeight(true) >= 511){
                /*
                 * add for scroll func
                 */
                var scrollHTML = '<div class="scrollbar_track" id="scrollbar_track_votes_popup"><div class="scrollbar_handle" id="scrollbar_handle_votes_popup" ></div></div>';
                jQuery('.votedUserDisplayBox').prepend(scrollHTML);
                baseObj.initScroll('votesPopupContentWrapper','scrollbar_track_votes_popup');
                jQuery("#scrollbar_track_votes_popup").trigger('mouseleave');
            }
        }
    });
}

function favoriteCallBack(result, input) {
        var favoriteObj = jQuery("#argumentDetailHead .favIcon");
        jQuery(favoriteObj).children("i").toggleClass('favIconOffG favIconOnG');
        jQuery(favoriteObj).children("span").text((jQuery(favoriteObj).children("i").hasClass("favIconOnG")) ? "UNFAVORITE ARGUMENT" : "FAVORITE ARGUMENT");
}

function memberFollowCallBack(result, input){
    jQuery(input.clickObj).toggleClass('followMember unfollowMember').toggleClass('agreementGradient disagreementGradient').children().toggleClass('followMemberSpan unfollowMemberSpan').text('Unfollow');
}

function memberUnFollowCallBack(result, input){
    jQuery(input.clickObj).toggleClass('followMember unfollowMember').toggleClass('agreementGradient disagreementGradient').children().toggleClass('followMemberSpan unfollowMemberSpan').text('Follow');
}

function postOpinionCallBack(result, input) {
    if (input.vote == window.replyID) {                            //process Reply
        var targetObj = jQuery("#commentReply-" + input.parentId);
        jQuery("#replyViewSwitch-" + input.parentId).children("span").text(parseInt(jQuery("#replyViewSwitch-" + input.parentId).text()) + 1);
        jQuery(targetObj).children(".addReplyLink,.showReplyLink ").trigger("click");
    } else {                                            //process Comment / vote
        var resultData = result.data;

        if (input.vote == 1) input.buttonSelector = jQuery("#agree-" + input.argumentId);
        else input.buttonSelector = jQuery("#disagree-" + input.argumentId);


        if (resultData.voted) {
            window.voted = true;
            input.agreedCount       = parseInt(jQuery(window.selectors.totalAgreeVoteCount).html());
            input.disagreedCount    = parseInt(jQuery(window.selectors.totalDisagreeVoteCount).html());
            input.maleagreed        = parseInt(jQuery(window.selectors.maleAgreeVoteCount).html());
            input.maledisagreed     = parseInt(jQuery(window.selectors.maleDisagreeVoteCount).html());
            input.femaleagreed      = parseInt(jQuery(window.selectors.femaleAgreeVoteCount).html());
            input.femaledisagreed   = parseInt(jQuery(window.selectors.femalDisagreeVoteCount).html());

            var prevoteSelector = (window.loggedInUserMember.gender == 'M')?'male':'female';                                // selects male / female depending on logged in member gender
            var voteCountSelector = (input.vote == 1)?prevoteSelector+"AgreeVoteCount":prevoteSelector+"DisagreeVoteCount"; //selects male/femaleagreed/disagreed on html page depending on gender and vote of loggedin user
            var postvoteSelector = (input.vote==1)?'agreed':'disagreed';                                                    //selects agred / disagred depending on user vote
            var postCountSelector = postvoteSelector+'Count';                                                               //selects agreedcount or disagreedcount js variable
            var totalCountSelector = (input.vote == 1)?'totalAgreeVoteCount':'totalDisagreeVoteCount';                       //selects totalagree/disagreedVotecount depending on user vote

            input[prevoteSelector+postvoteSelector] +=1;                                                                    //increase male/femaleagreed/disagreed as selected js variable
            input[postCountSelector] += 1;                                                                                  //increase totalagreed/disagreed as selected js variable


            postVoteHTML(input);


        }else{
            window.voted = false;
        }
        if (resultData.commented) {
            input.id = resultData.comment.id;
            postCommentHTML(input);
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
}

/*
 * @param array input
 * input -> agreeCount
 * input -> argumentId
 * input -> commenttext
 * input -> memberId
 * input -> vote / uservote
 * input -> id
 * input -> userName    (optional)
 * input -> profileThumb    (optional)
 * input -> memberId    (optional)
 * input -> createdtime    (optional)
 */
function postCommentHTML(input){
    //remove no comments tip if exists
    jQuery(".noCommets").remove();
    var htmlData = loadTimelineComment(input);
    //alert(htmlData)
    jQuery(".graphContent ul").append(baseObj.nl2br(htmlData));
    //scroll to recentely added comment
    jQuery("html, body").animate({ scrollTop: jQuery("#c"+input.id+"").offset().top},1000);
}

/*
 *   @param array input
 *   input -> agreedCount
 *   input -> disagreedCount
 *   input -> maleagreed
 *   input -> maledisagreed
 *   input -> femaleagreed
 *   input -> femaledisagreed
 */
function postVoteHTML(input){
    var selector = window.selectors;
    var currentTotalVoteCount = parseInt(jQuery(window.selectors.currentTotalVoteCount).html());
    var currentTotalAgreeVoteCount = parseInt(jQuery(window.selectors.totalAgreeVoteCount).html());
    var newTotalVoteCount = parseInt(input.agreedCount)+parseInt(input.disagreedCount);                                 //calculating total vote count
    var currentAgreedPercentage = baseObj.CalculatePercentage(Math.round(((currentTotalAgreeVoteCount) / currentTotalVoteCount) * 100));    //calculates new agree percentage
    var newagreedPercentage = baseObj.CalculatePercentage(Math.round((parseInt(input.agreedCount) / newTotalVoteCount) * 100));    //calculates new agree percentage
    var currentSectorSelector = (currentTotalVoteCount == 0) ? "sectord" : "sector" + currentAgreedPercentage;            //selects circle selector according to old agree percentage


    /*if agree / disagre percentage is 100 to fit into width 97px reduce font size to 44px else make it 48px*/
    if (newagreedPercentage== 100 || newagreedPercentage == 0) {
        jQuery(window.selectors.disagreeTotalPercentage,window.selectors.agreeTotalPercentage).toggleClass('fontlarge fontsmall');
    } else {
        jQuery(window.selectors.disagreeTotalPercentage,window.selectors.agreeTotalPercentage).toggleClass('fontlarge fontsmall');
    }

    jQuery(selector.maleAgreeVoteCount).html(parseInt(input.maleagreed));
    jQuery(selector.maleDisagreeVoteCount).html(parseInt(input.maledisagreed));
    jQuery(selector.femaleAgreeVoteCount).html(parseInt(input.femaleagreed));
    jQuery(selector.femalDisagreeVoteCount).html(parseInt(input.femaledisagreed));
    jQuery(selector.totalAgreeVoteCount).html(parseInt(input.agreedCount));
    jQuery(selector.totalDisagreeVoteCount).html(parseInt(input.disagreedCount));
    jQuery(selector.currentTotalVoteCount).html(newTotalVoteCount);
    jQuery(selector.agreeTotalPercentage).html(newagreedPercentage);                                                       //setting agree percentage in agreeTip
    jQuery(selector.disagreeTotalPercentage).html(100 - newagreedPercentage);                                              //setting disagree percentage to disagreeTip
    jQuery("." + currentSectorSelector).toggleClass("" + currentSectorSelector + " sector" + newagreedPercentage);         //updating graph circle considering new vote
    jQuery(window.selectors.agreeButtonSelector).metadata().voted = true;
    jQuery(window.selectors.disAgreeButtonSelector).metadata().voted = true;
}

function fetchComments(isFirstLoad) {
    if (window.commentsLowerLimit >= 0 && window.hasMoreComments /*&& window.currArgumentObj.commentsCount != "0"*/) {
        jQuery.ajax({
            url:"bootstrap/getComments",
            type:"POST",
            data:{argumentId:window.currArgumentObj.id, lowerLimit:window.commentsLowerLimit, noofrecords:window.commentsPerLoad },
            dataType:"json",
            beforeSend:function () {
                window.isloading = true;
                jQuery("#argumentDetailBody .profileGraph .graphContent ul").after('<img alt="loading..." src="/images/da-loader.gif" style="text-align: center;position: relative;">');
            },
            success:function (res) {
                jQuery("#argumentDetailBody .profileGraph .graphContent ul").next("img").remove();
                var data = loadTimelineBatchComment(res.data.comments, res.data.replyCount/*, isFirstLoad*/);
               /* alert('fetching comments');*/
                if (res.response) {             //if any comments on this argument
                    var processedHTML = baseObj.decodeEntities(data);

                    jQuery("#argumentDetailBody .profileGraph .graphContent ul").append(processedHTML);
                    if (res.data.comments.length < 10) {  //if comments loaded are lesthan 10 means no more comment on this argumetn
                        window.commentsLowerLimit = -1;
                        window.hasMoreComments = false;
                    }
                } else {                        //if no more comments on this argument
                    if (isFirstLoad) {          //if no comments on argument means comments count on argument are zero
                        jQuery("#argumentDetailBody .profileGraph .graphContent ul").append(data);
                    } else {                    //if all comments are loaded on this argument
                        /*jQuery("#argumentDetailBody .profileGraph .graphContent ul").append(loadNoComments());*/
                    }
                    window.commentsLowerLimit = -1;
                    window.hasMoreComments = false;
                }
            },
            complete:function () {
                window.isloading = false;
            }
        });
    }
    /* else {
     var data = loadTimelineBatchComment(false,false);
     jQuery("#argumentDetailBody .profileGraph .graphContent ul").append(data);
     window.commentsLowerLimit = -1;
     window.hasMoreComments = false;
     }*/
}

function fetchReplies(commentId) {
    jQuery.ajax({
        url:"action/getReplies",
        type:"POST",
        data:{commentId:commentId},
        dataType:"json",
        beforeSend:function () {
            jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html('<div style="display: block; width: 100%;"><img style="text-align: center;" src="/images/da-loader.gif" alt="loading..."></div>');
        },
        success:function (res) {
            if (res.data) {
                jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html(baseObj.decodeEntities(loadTimelineCommentReply(res.data)));
            } else {
                jQuery("#replyViewSwitch-" + commentId).siblings(".replyContentWrapper").html("No Replies Yet.");
            }
            if(jQuery("#replyContentWrapper-"+window.commentId).outerHeight(true) > 369){
                /*
                 add for scroll func
                 */
                var comentReply = '<div class="scrollbar_track" id="scrollbar_track'+window.commentId+'"><div class="scrollbar_handle" id="scrollbar_handle'+window.commentId+'" ></div></div>';
                jQuery('#wrapperData-'+ window.commentId).before((comentReply));
                baseObj.initScroll('wrapperData-'+ window.commentId,'scrollbar_track'+window.commentId);
            }
        }
    });
}

function togglecommentReply(e) {
    if (jQuery(e).hasClass("addReplyLink")) {
        jQuery(e).toggleClass("on off");
        jQuery(e).children("i").toggleClass("moreSmallIconG moreSmallIconS");

        jQuery(e).siblings(".showReplyLink.on").children("i").toggleClass("commentSmallIconG commentSmallIconS");
        jQuery(e).siblings(".showReplyLink.on").toggleClass("on off");
        jQuery(e).siblings(".replyContentWrapper").hide();
    } else if (jQuery(e).hasClass("showReplyLink")) {
        jQuery(e).toggleClass("on off");
        jQuery(e).children("i").toggleClass("commentSmallIconG commentSmallIconS");

        jQuery(e).siblings(".addReplyLink.on").children("i").toggleClass("moreSmallIconG moreSmallIconS");
        jQuery(e).siblings(".addReplyLink.on").toggleClass("on off");
        jQuery(e).siblings(".replyContentWrapper").hide();
    }

    var commentId = jQuery(e).parent().attr("id");
    commentId = commentId.split('-')[1];
    return commentId;
}

function lockArgumentCallBack(result, input) {

    if (result.response) {
        var lockObj = jQuery("#argumentDetailHead .lockButton");
        jQuery(lockObj).toggleClass("disabled");
        jQuery(lockObj).children("i").toggleClass('lockOnG lockOffG');
        jQuery(lockObj).children("span").text((jQuery(lockObj).children("i").hasClass("lockOnG")) ? "LOCKED ARGUMENT" : "LOCK ARGUMENT");
        window.currArgumentObj.status = result.data;
        jQuery("#agree-"+window.currArgumentObj.id).metadata().locked =  jQuery(lockObj).children("i").hasClass('lockOnG');
        /*jQuery(lockObj).attr("title",(jQuery(lockObj).children("i").hasClass("lockOnG") ? "Argument already locked. you cant alter this argument.":"click here to lock this Argument" ));*/
        var msg = (result.data==1)?DA.ARGUMENT_UNLOCKED_PERMINENTLY:DA.ARGUMENT_LOCKED_PERMINENTLY;
        baseObj.Showmsg(msg,true);
    } else {
        baseObj.Showmsg(DA.ISSUES_WHILE_LOCKING_ARGUMENT,false);
    }
}

function updateArgumentCallBack(result, input){
    if(result.response){    //update argument
        jQuery("#argumentDetailHead").find(".argumentTitle").text(baseObj.cleanHTMLEntityString(input.argumentTitle));
        jQuery("#currentArgumentDesc").text(baseObj.cleanHTMLEntityString(input.argumentDesc));
        linkifyHTMLStrings();
        jQuery("#topicLink").attr('href',DA.base_url+'home?category='+input.topic).text(window.topicArray[input.topic]).parent().siblings(".topicArgCount").text(result.data.topicArgumentCount);
        updateCurrArgumentObjMetada(result.data);

        var options = new Object;
        if(input.fbFlag){
            options.link = DA.base_url+"detail?id="+window.currArgumentObj.id;
            options.title = window.currArgumentObj.title;
            options.description = jQuery("#currentArgumentDesc").html();
            options.img = window.currArgumentObj.profilephoto;
            if(input.twFlag){
                options.twFlag = input.twFlag;
                options.twurl = DA.base_url+"detail?id="+window.currArgumentObj.id;
                options.twdescription = window.currArgumentObj.title;
            }
            setTimeout(function(){baseObj.postToFB(options);},3000);
        }else{
            if(input.twFlag){
                options.url = DA.base_url+"detail?id="+window.currArgumentObj.id;
                options.description = window.currArgumentObj.title;
                baseObj.postToTW(options);
            }
        }
        baseObj.Showmsg('Argument Update Success',true);
    }else{                  //unable to update argument
        baseObj.Showmsg('Argument Update failed',false)
    }
}

function updateCurrArgumentObjMetada(input){

        if(!baseObj.is_empty_String(input.title))
            jQuery("#argumentObj").metadata().title = input.title;
        if(!baseObj.is_empty_String(input.topic))
            jQuery("#argumentObj").metadata().topic = input.topic;
        if(!baseObj.is_empty_String(input.lastModifiedTime))
            jQuery("#argumentObj").metadata().lastModifiedTime = input.lastmodified;
        if(!baseObj.is_empty_String(input.createdTime))
            jQuery("#argumentObj").metadata().createdTime = input.createdtime;
        if(!baseObj.is_empty_String(input.status))
            jQuery("#argumentObj").metadata().status = input.status;
        if(!baseObj.is_empty_String(input.isFavorite))
            jQuery("#argumentObj").metadata().isFavorite = input.isFavorite;
        if(!baseObj.is_empty_String(input.maleagreed))
            jQuery("#argumentObj").metadata().maleagreed = input.maleagreed;
        if(!baseObj.is_empty_String(input.maledisagreed))
            jQuery("#argumentObj").metadata().maledisagreed = input.maledisagreed;
        if(!baseObj.is_empty_String(input.femaleagreed))
            jQuery("#argumentObj").metadata().femaleagreed = input.femaleagreed;
        if(!baseObj.is_empty_String(input.agreed))
            jQuery("#argumentObj").metadata().agreed = input.agreed;
        if(!baseObj.is_empty_String(input.disagreed))
            jQuery("#argumentObj").metadata().disagreed = input.disagreed;
        if(!baseObj.is_empty_String(input.commentsCount))
            jQuery("#argumentObj").metadata().commentsCount = input.commentsCount;

        window.currArgumentObj = jQuery("#argumentObj").metadata();
}

function updateOpinionCallBack(result,input){
    if(result.response){    //update comment success
        jQuery("#c"+input.commentId).find(".argumentContent").text(baseObj.cleanHTMLEntityString(result.data.commenttext));
        linkifyHTMLStrings();
        var options = new Object;
        if(input.fbFlag){
            options.link = DA.base_url+"detail?id="+window.currArgumentObj.id;
            options.title = window.currArgumentObj.title;
            options.description = jQuery("#currentArgumentDesc").html();
            options.img = window.currArgumentObj.profilephoto;
            if(input.twFlag){
                options.twFlag = input.twFlag;
                options.twurl = DA.base_url+"detail?id="+window.currArgumentObj.id;
                options.twdescription = window.currArgumentObj.title;
            }
            setTimeout(function(){baseObj.postToFB(options);},3000);
            }else{
                if(input.twFlag){
                    options.url = DA.base_url+"detail?id="+window.currArgumentObj.id;
                    options.description = window.currArgumentObj.title;
                    baseObj.postToTW(options);
                }
            }
        baseObj.Showmsg(input.type+' updated successfully.',true);
    }else{
        baseObj.Showmsg(input.type+' updation failed',false);
    }
}