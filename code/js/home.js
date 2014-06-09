jQuery(document).ready(function () {

    /*** Sync Scripts ***/
    jQuery(window).scroll(function () {
        if (jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height()) {
            window.homeloadLimit = window.homeloadLimit + 1;
            init(window.homeActiveTab, window.homeloadLimit, window.homeActiveTabParam);
        }
    });
    setInterval("baseObj.SyncArgument(null)", DA.AJAX_TIME_INTERVAL * 60000);
    setInterval("syncArgumentData(null)", DA.AJAX_TIME_INTERVAL * 60000);

    jQuery(".userInfoTab").click(function (e) {
        e.stopPropagation();
        if (jQuery(this).hasClass('activeMenu')) {        //already active menu
            if (jQuery(this).hasClass('hasSubMenu')) {    //has sub menu
                jQuery("#" + jQuery(this).attr('id') + "SubMenu").toggle();        //if sub menu is show visbile / hidden toggle it
            } else {                                              //no sub menu, so no action req
                //no action
            }
        } else {
            if (jQuery(this).hasClass('hasSubMenu')) {    //has sub menu
                jQuery("#" + jQuery(this).attr('id') + "SubMenu").toggle();        //if sub menu is show visbile / hidden toggle it
            } else {                                      //no sub menu, so no action req
                //no submenu. load content
                jQuery(this).addClass('activeMenu').siblings().removeClass('activeMenu');
                jQuery("#profileContentWrapper").html('');
                window.homeloadLimit = 0;
                window.homeActiveTab = jQuery(this).attr('id');
                init(window.homeActiveTab, window.homeloadLimit, window.homeActiveTabParam);
            }
        }
    });


    jQuery(".hozontalMenuTopicList > ul > li").click(function (e) {
        e.stopPropagation();
        var topicId = jQuery(this).attr('id');
        jQuery("#profileContentWrapper").html('');
        showTopic(topicId, e);
        var parentMenu = jQuery("#" + jQuery(this).closest('.popupActive').attr('id').slice(0, -7));
        if (!jQuery(parentMenu).hasClass('activeMenu')) {         // if element in main memu is not active menu
            jQuery(parentMenu).addClass('activeMenu').siblings().removeClass('activeMenu');
        }
        jQuery(document).trigger('click');
        window.homeActiveTab = jQuery(parentMenu).attr("id");
        window.homeActiveTabParam = topicId;
        window.homeloadLimit = 0;
        init(window.homeActiveTab, window.homeloadLimit, topicId);
    });

    jQuery("#SocialMediaBoxShow,#SocialMediaBoxHide").click(function (e) {
        jQuery("#SocialMediaBoxShow,#SocialMediaBoxHide,#SocialMediaBoxBody,#SocialMediaSwitchAction").toggle();
        if (jQuery(e.target).attr('id') == 'SocialMediaBoxShow') {      //clicked on Social Media> link
            if (jQuery("#SocialMediaSwitchAction").children(":visible").length == 0) {    //first time user oped sicial media box
                jQuery("#SocialMediaBoxSwitchFacebook img").trigger('click');
            } else {  //user opened socialmediabox once and close it and reoped it.

            }
        }
    });
    jQuery("#SocialMediaBoxSwitchTwitter,#SocialMediaBoxSwitchFacebook").click(function (e) {
        if (jQuery(e.target).parent().attr('id') == 'SocialMediaBoxSwitchTwitter') {
            twTweetReader();
        } else if (jQuery(e.target).parent().attr('id') == 'SocialMediaBoxSwitchFacebook') {
            fbFeedReader();
        }
    });
    jQuery("#SocialMediaContentWrapper .scrollLeft").click(function () {
        if (!jQuery(".activePage").is(':first-child')) {
            var obj = jQuery("#SocilaMediaContent .activePage");
            var prevObj = jQuery(obj).prev();
            jQuery(obj).hide();
            jQuery(obj).removeClass("activePage");
            jQuery(prevObj).addClass('activePage');
            jQuery(prevObj).show();
            jQuery("#SocialMediaPaging").children("div").eq(jQuery("#SocilaMediaContent").children('div').index(jQuery("#SocilaMediaContent .activePage"))).addClass('active').siblings(".active").removeClass('active');
        }

            jQuery("#SocialMediaContentWrapper .scrollLeft").css({'visibility':jQuery(".activePage").is(':first-child')?'hidden':'visible'});
            jQuery("#SocialMediaContentWrapper .scrollRight").css({'visibility':'visible'});

    });
    jQuery("#SocialMediaContentWrapper .scrollRight").click(function () {
        if (!jQuery(".activePage").is(':last-child')) {
            var obj = jQuery("#SocilaMediaContent .activePage");
            var nextObj = jQuery(obj).next();
            jQuery(obj).hide();
            jQuery(obj).removeClass("activePage");
            jQuery(nextObj).addClass('activePage');
            jQuery(nextObj).show();
            jQuery("#SocialMediaPaging").children("div").eq(jQuery("#SocilaMediaContent").children('div').index(jQuery("#SocilaMediaContent .activePage"))).addClass('active').siblings(".active").removeClass('active');
        }
        jQuery("#SocialMediaContentWrapper .scrollLeft").css({'visibility':'visible'});
        jQuery("#SocialMediaContentWrapper .scrollRight").css({'visibility':jQuery(".activePage").is(':last-child')?'hidden':'visible'});
    });
    jQuery("#SocialMediaPaging .pagingCircle").live('click',function(){
        var count = jQuery(this).prevAll().length;
        var obj = jQuery("#SocilaMediaContent .activePage");
        var nextObj = jQuery("#SocilaMediaContent").children().eq(count);
        jQuery(obj).hide();
        jQuery(obj).removeClass("activePage");
        jQuery(nextObj).addClass('activePage');
        jQuery(nextObj).show();
        jQuery("#SocialMediaPaging").children("div").eq(jQuery(this).prevAll().length).addClass('active').siblings(".active").removeClass('active');
        if (jQuery(".activePage").is(':last-child')) {
            jQuery("#SocialMediaContentWrapper .scrollLeft").css({'visibility':'visible'});
            jQuery("#SocialMediaContentWrapper .scrollRight").css({'visibility':jQuery(".activePage").is(':last-child')?'hidden':'visible'});
        }else if (jQuery(".activePage").is(':first-child')) {
            jQuery("#SocialMediaContentWrapper .scrollLeft").css({'visibility':jQuery(".activePage").is(':first-child')?'hidden':'visible'});
            jQuery("#SocialMediaContentWrapper .scrollRight").css({'visibility':'visible'});
        }else{
            jQuery("#SocialMediaContentWrapper .scrollLeft").css({'visibility':'visible'});
            jQuery("#SocialMediaContentWrapper .scrollRight").css({'visibility':'visible'});
        }
    });
    jQuery(".fbFeedPost").live('click', function () {
        if (loggedInUserMember) {
            window.feedPostObject = jQuery(this).closest('.twTweet,.fbFeed');
            var sourceLocation = jQuery(window.feedPostObject).hasClass('twTweet')?'Twitter':'facebook';
            var description = jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+' said \"'+jQuery(window.feedPostObject).children(".argumentDescFull").html()+'\" I Disagree. Do you Agreee with ME?.';
            var title = 'I Disagree with '+jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+'\'s post on '+sourceLocation +' '+jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().permaLink;
            jQuery(window.feedPostObject).children(".argumentDescFull").html();
            jQuery(".startArgumentContainer").children(":first-child").after('<div id="userVoteOnThirdPartyPost" ><label>Your Vote</label><div id="userVoteButtonWrapper"><button class="agreementGradient agreeButtonLarge gradient">AGREE</button><button class="disagreementGradient disagreeButtonLarge gradient">DISAGREE</button></div><input type="checkbox" id="userVoteOnPost" class="displaynone"/> </div>');
            jQuery("#TopicSelector").after('<input type="checkbox" id="userSelectedTopic" class="displaynone"/>');
            baseObj.OpenModel(jQuery(".startArgumentContainer"));
            jQuery("#newArgTitle").val(title);
            jQuery("#newArgDesc").val(description);
            jQuery("#newArgSource").val('Facebook');
            jQuery("#postArgumentFBCheck").attr("checked", true);
        } else {
            baseObj.OpenModel(jQuery(".loginContainer"));
        }
    });
    jQuery(".twTweetPost").live('click', function () {
        if (loggedInUserMember) {
            window.feedPostObject = jQuery(this).closest('.twTweet');
            var sourceLocation = jQuery(window.feedPostObject).hasClass('twTweet')?'Twitter':'facebook';
            var description = jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+' said \"'+jQuery(window.feedPostObject).children(".argumentDescFull").html()+'\" I Disagree. Do you Agreee with ME?.';
            var title = 'I Disagree with '+jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+'\'s post on '+sourceLocation +' '+jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().permaLink;
            jQuery(window.feedPostObject).children(".argumentDescFull").html();
            jQuery(".startArgumentContainer").children(":first-child").after('<div id="userVoteOnThirdPartyPost" ><label>Your Vote</label><div id="userVoteButtonWrapper"><button class="agreementGradient agreeButtonLarge gradient">AGREE</button><button class="disagreementGradient disagreeButtonLarge gradient">DISAGREE</button></div><input type="checkbox" id="userVoteOnPost" class="displaynone"/> </div>');
            jQuery("#TopicSelector").after('<input type="checkbox" id="userSelectedTopic" class="displaynone"/> ');
            baseObj.OpenModel(jQuery(".startArgumentContainer"));
            jQuery("#newArgTitle").val(title);
            jQuery("#newArgDesc").val(description);
            jQuery("#newArgSource").val('Twitter');
            jQuery("#postArgumentTWCheck").attr("checked", true);
        } else {
            baseObj.OpenModel(jQuery(".loginContainer"));
        }
    });
    jQuery("#userVoteButtonWrapper .agreeButtonLarge").live('mousedown',function(){
        var sourceLocation = jQuery(window.feedPostObject).hasClass('twTweet')?'Twitter':'facebook';
        var description = jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+' said \"'+jQuery(window.feedPostObject).children(".argumentDescFull").html()+'\" I Agree. Do you Agreee with ME?.';
        var title = 'I Agree with '+jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+'\'s post on '+sourceLocation +' '+ jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().permaLink;
        jQuery("#newArgTitle").val(title);
        jQuery("#newArgDesc").val(description);
        jQuery("#userVoteOnPost").attr('checked','checked');
        jQuery("#userVoteButtonWrapper").css({"border":"0px solid transparent"});
        jQuery(this).removeClass("agreementGradient").addClass("agreementGradientActive");
        jQuery(this).siblings().removeClass('disagreementGradientActive').addClass("disagreementGradient");
    });
    jQuery("#userVoteButtonWrapper .disagreeButtonLarge").live('mousedown',function(){
        var sourceLocation = jQuery(window.feedPostObject).hasClass('twTweet')?'Twitter':'facebook';
        var description = jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+' said \"'+jQuery(window.feedPostObject).children(".argumentDescFull").html()+'\" I Disagree. Do you Agreee with ME?.';
        var title = 'I Disagree with '+jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().username+'\'s post on '+sourceLocation +' '+jQuery(window.feedPostObject).children(".argumentHead").children('.username').metadata().permaLink;
        jQuery("#newArgTitle").val(title);
        jQuery("#newArgDesc").val(description);
        jQuery("#userVoteOnPost").attr('checked','checked');
        jQuery("#userVoteButtonWrapper").css({"border":"0px solid transparent"});
        jQuery(this).removeClass("disagreementGradient").addClass("disagreementGradientActive");
        jQuery(this).siblings().removeClass('agreementGradientActive').addClass("agreementGradient");
    });
    preInit();
});

function fbFeedReader() {
    if (!window.isLoading) {
        jQuery.ajax({
            url:DA.base_url + 'base/fbFeedReader',
            type:"post",
            dataType:'json',
            beforeSend:function () {
                window.isLoading = true;
                jQuery("#SocialMediaBoxSwitchFacebook,#SocialMediaBoxSwitchTwitter").css({'display':'block'});
                jQuery("#SocialMediaBoxSwitchFacebook").addClass('activeSwitch');
                jQuery("#SocialMediaBoxSwitchTwitter").removeClass('activeSwitch');
                jQuery("#SocialMediaTabHeading h1").text(DA.START_ARGUMENT_WITh_FB_HEADING);
                jQuery("#SocialMediaContentWrapper .scrollLeft,#SocialMediaContentWrapper .scrollRight,#SocialMediaPaging").css({'visibility':'hidden'});
                jQuery("#SocialMediaContentHolder").html('<img src="/images/da-loader.gif" alt="Loading ...">');
            },
            success:function (res) {
                if(res){
                if (res.response) {
                    var newsfeed = res.data.newsfeed;
                    var wallfeed = res.data.wallfeed;
                    var htmlContent = '<div id="SocilaMediaContent">';
                    var pagingContent = '<div class="slidePage pagingCircle active">&nbsp;</div>';
                    htmlContent += '<div class="page0 activePage">';
                    jQuery.each(newsfeed, function (i, e) {
                        if (i > 1 && i % 3 == 0) {
                            htmlContent += '</div><div class="page' + (i / 3) + '" style="display: none;">';
                            pagingContent += '<div class="pagingCircle">&nbsp;</div>';
                        }
                        htmlContent += loadFBFeedHTML(e);
                    });
                    for (var counter = 0; counter < wallfeed.length; counter++) {
                        if (i % 3 == 0) {
                            htmlContent += '<div class="page' + i + '">';
                        }
                        htmlContent += loadFBFeedHTML(wallfeed);
                        if (i % 3 == 0) {
                            htmlContent += '</div>';
                        }
                    }
                    htmlContent += '</div>';
                    jQuery("#SocialMediaPaging").html(pagingContent);
                    jQuery("#SocialMediaContentWrapper .scrollRight,#SocialMediaPaging").css({'visibility':'visible'});
                    jQuery("#SocialMediaContentHolder").html(htmlContent);
                } else {
                    jQuery("#SocialMediaContentHolder").html(res.data);
                    jQuery("#SocialMediaContentWrapper .scrollLeft,#SocialMediaContentWrapper .scrollRight,#SocialMediaPaging").css({'visibility':'hidden'});
                }
                }else{          //call failed, reinit call
                    window.isLoading = false;
                    fbFeedReader();
                }
            },
            complete:function(){
                window.isLoading = false;
            }
        });
    }
}

function twTweetReader() {
    if (!window.isLoading) {
        jQuery.ajax({
            url:DA.base_url + 'base/twFeedReader',
            type:"post",
            dataType:'json',
            beforeSend:function () {
                window.isLoading = true;
                jQuery("#SocialMediaBoxSwitchFacebook,#SocialMediaBoxSwitchTwitter").css({'display':'block'});
                jQuery("#SocialMediaBoxSwitchFacebook").removeClass('activeSwitch');
                jQuery("#SocialMediaBoxSwitchTwitter").addClass('activeSwitch');
                jQuery("#SocialMediaContentWrapper .scrollLeft,#SocialMediaContentWrapper .scrollRight,#SocialMediaPaging").css({'visibility':'hidden'});
                jQuery("#SocialMediaTabHeading h1").text(DA.START_ARGUMENT_WITh_TW_HEADING);
                jQuery("#SocialMediaContentHolder").html('<img src="/images/da-loader.gif" alt="Loading ...">');
            },
            success:function (res) {
                if(res){
                if (res.response) {
                    var htmlContent = '<div id="SocilaMediaContent">';
                    var pagingContent = '<div class="pagingCircle active">&nbsp;</div>';
                    htmlContent += '<div class="page0 activePage">';
                    jQuery.each(res.data, function (i, e) {
                        if (i > 1 && i % 3 == 0) {
                            htmlContent += '</div><div class="slidePage page' + (i / 3) + '">';
                            pagingContent += '<div class="pagingCircle">&nbsp;</div>';
                        }
                        htmlContent += loadTWTweetHTML(e);
                    });
                    htmlContent += '</div>';
                    jQuery("#SocialMediaPaging").html(pagingContent);
                    jQuery("#SocialMediaContentWrapper .scrollRight,#SocialMediaPaging").css({'visibility':'visible'});
                    jQuery("#SocialMediaContentHolder").html(htmlContent);
                } else {
                    //user dont have tweets or fail to fetch tweets
                    jQuery("#SocialMediaContentHolder").html(res.data);
                    jQuery("#SocialMediaContentWrapper .scrollLeft,#SocialMediaContentWrapper .scrollRight,#SocialMediaPaging").css({'visibility':'hidden'});
                }
                }else{  //call failed, reinit the call.
                    window.isLoading = false;
                    twTweetReader();
                }
            },
            complete:function(){
                window.isLoading = false;
            }
        });
    }
}

function preInit() {
    /**** Fixed constants !!!Do not Change Starts Here****/
    window.homeloadLimit = 0;
    /**** Fixed constants !!!Do not Change Ends Here****/
    window.isLoading = false;
    window.feedPostObject = null;


    var hash = window.location.hash.substring(1);
    var urlVar = baseObj.getUrlVars();
    if (hash != '' && hash != null && hash != undefined) {
        window.homeActiveTab = hash.split('#')['0'];
        jQuery("#" + window.homeActiveTab + "Menu").trigger('click');
    } else if (jQuery(urlVar).size() > 0 && !jQuery.isEmptyObject(urlVar)) {
        window.homeActiveTab = urlVar.hasOwnProperty('category') ? 'categoryMenu' : 'feedMenu';
        window.homeActiveTabParam = urlVar['category'];
        jQuery("#" + window.homeActiveTab + "Menu").trigger('click');
        hash = "#" + urlVar['category'] + "";
        jQuery("" + hash + "").trigger('click');
    } else {
        window.homeActiveTab = (window.homeActiveTab == undefined || window.homeActiveTab == null || window.homeActiveTab == '') ? jQuery("#homeArgumentNav ul.horizontalMenu li").eq(0).attr("id") : window.homeActiveTab;
        jQuery("#" + window.homeActiveTab + "").trigger('click');
    }
}

function init(tab, limit, topicId) {
    //CALL THE FUNCTIONS ON TAB CLICK
    switch (tab) {
        case 'feedMenu':
            closeTopicMenu();
            var thisObj = jQuery(".userInfoTab>a.feed");
            loadArgumentFeed(thisObj, limit);
            break;

        case 'allMenu':
            closeTopicMenu();
            var thisObj = jQuery(".userInfoTab>a.allArguments");
            loadAllArguments(thisObj, limit);
            break;

        case 'interestingMenu':
            closeTopicMenu();
            var thisObj = jQuery(".userInfoTab>a.interesting");
            loadInterestedArguments(thisObj, limit);
            break;

        case 'popularMenu':
            closeTopicMenu();
            var thisObj = jQuery(".userInfoTab>a.popular");
            loadPopularArguments(thisObj);
            break;

        case 'peopleMenu':
            closeTopicMenu();
            var thisObj = jQuery(".userInfoTab>a.people");
            loadPeopleArguments(thisObj);
            break;

        case 'categoryMenu':
            /*showTopic(topicId,null);*/
            var thisObj = jQuery(".userInfoTab>a.category");
            loadCategoryArguments(thisObj, limit, topicId);
            break;

        case 'ArgumentsStartedMenu':
            closeTopicMenu();
            var thisObj = jQuery("#ArgumentsStartedMenu");
            loadStartedArguments(thisObj, limit);
            break;

        case 'ArgumentsFollowedMenu':
            closeTopicMenu();
            var thisObj = jQuery("#ArgumentsFollowedMenu");
            loadFollowdArguments(thisObj, limit);
            break;

        default:
            closeTopicMenu();
            var thisObj = jQuery(".userInfoTab>a.ArgumentsFollowed");
            loadArgumentFeed(thisObj, limit);
            break;
    }
}

function showTopic(topicId, e) {
    jQuery('#categoryText').html(baseObj.Ellipsis('Categories (' + jQuery("#" + topicId).text() + ')', 26));
}

function closeTopicMenu() {
    jQuery(".categoryLink").removeClass('active').children("span:first-child").html('Categories');
    jQuery(document).trigger('click');
}

function loadArgumentFeed(thisObj, limit) {

    //LOAD THE ARGUMENTS CREATED BY THE MEMBER THROUGH AJAX
    jQuery.ajax({
        url:"action/feed",
        data:{memberId:loggedInUserMember.memberId, limit:limit},
        dataType:'json',
        type:'post',
        cache:false,
        beforeSend:function () {
            jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
            jQuery('.horizontalMenu li').removeClass('activeMenu');
            jQuery(thisObj).parent().addClass('activeMenu');
        },
        success:function (result) {
            if (result.response) {
                var argumentListHtml = "";
                var argumentList = result.data;
                if (result.isNewMember) {
                    jQuery("#homeArgumentNav>.horizontalMenu").hide();
                    jQuery(".newUserMsg").show();
                }
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    argumentListHtml += loadShortArgument(argumentList[argumentCount]);
                }
                jQuery('#profileContentWrapper').append(argumentListHtml);
                if (argumentList.length < 6) {
                    jQuery("#footerLoader").html("No more Arguments");
                }
            } else {
                jQuery("#footerLoader").html("No more Arguments");
            }
        }
    });
}

function loadAllArguments(thisObj, limit) {
    jQuery.ajax({
        url:"action/topicArguments",
        data:{memberId:loggedInUserMember.memberId, limit:limit, topic:'1'},
        dataType:'json',
        type:'post',
        cache:false,
        beforeSend:function () {
            jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
            jQuery('.horizontalMenu li').removeClass('activeMenu');
            jQuery(thisObj).parent().addClass('activeMenu');
        },
        success:function (result) {
            if (result.response) {
                var argumentListHtml = "";
                var argumentList = result.data;
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    argumentListHtml += loadShortArgument(argumentList[argumentCount]);
                }
                jQuery('#profileContentWrapper').append(argumentListHtml);
                if (argumentList.length < 6) {
                    jQuery("#footerLoader").html("No more Arguments");
                }
            } else {
                jQuery("#footerLoader").html("No more Arguments");
            }
        }
    });
}

function loadInterestedArguments(thisObj, limit) {
    /*jQuery("#profileContentWrapper").html('');
     jQuery("#footerLoader").html('<span id="footerLoader"><img src="/images/da-loader.gif"></span>');
     jQuery('.horizontalMenu li').removeClass('activeMenu');
     jQuery(thisObj).parent().addClass('activeMenu');*/

    //LOAD THE ARGUMENTS CREATED BY THE MEMBER THROUGH AJAX
    jQuery.ajax({
        url:"action/interest",
        data:{memberId:loggedInUserMember.memberId, limit:limit},
        dataType:'json',
        type:'post',
        cache:false,
        beforeSend:function () {
            jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
            jQuery('.horizontalMenu li').removeClass('activeMenu');
            jQuery(thisObj).parent().addClass('activeMenu');
        },
        success:function (result) {
            if (result.response) {
                var argumentListHtml = "";
                var argumentList = result.data;
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    argumentListHtml += loadShortArgument(argumentList[argumentCount]);
                }
                jQuery('#profileContentWrapper').append(argumentListHtml);
                if (argumentList.length < 6) {
                    jQuery("#footerLoader").html("No more Arguments");
                }
            } else {
                jQuery("#footerLoader").html("No more Arguments");
            }
        }
    });
}

function loadStartedArguments(thisObj, limit) {
    jQuery.ajax({
        url:"action/profileStartedArgument",
        data:{memberId:loggedInUserMember.memberId, limit:limit, load:6},
        dataType:'json',
        type:'post',
        cache:false,
        beforeSend:function () {
            jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
        },
        success:function (result) {
            if (result.response == 1) {
                var argumentListHtml = "";
                var argumentList = result.data;
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    argumentListHtml += loadShortArgument(argumentList[argumentCount]);
                }
                jQuery('#profileContentWrapper').append(argumentListHtml);
                if (argumentList.length < 6) {
                    jQuery("#footerLoader").html("No more arguments.");
                }
            } else {
                if (limit == 0)
                    jQuery("#footerLoader").html("No arguments to display. Get started and post your first argument");
                else
                    jQuery("#footerLoader").html("No more arguments.");
            }
        }
    });
}

function loadFollowdArguments(thisObj, limit) {
    jQuery.ajax({
        url:"action/profileFollowingArgument",
        data:{memberId:loggedInUserMember.memberId, limit:limit, load:6},
        dataType:'json',
        type:'post',
        cache:false,
        beforeSend:function () {
            jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
        },
        success:function (result) {
            if (result.response == 1) {
                var argumentListHtml = "";
                var argumentList = result.data;
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    argumentListHtml += loadShortArgument(argumentList[argumentCount]);
                }
                jQuery('#profileContentWrapper').append(argumentListHtml);
                if (argumentList.length < 6) {
                    jQuery("#footerLoader").html("No more Arguments");
                }
            } else {
                jQuery("#footerLoader").html("No more Favorite Arguments to show");
            }
        }
    });
}

function loadPopularArguments(thisObj) {
    jQuery("#profileContentWrapper").html('');
    jQuery("#footerLoader").html('<span id="footerLoader"><img src="/images/da-loader.gif" alt="Loading..."></span>');
    jQuery('.horizontalMenu li').removeClass('activeMenu');
    jQuery(thisObj).parent().addClass('activeMenu');

    //LOAD THE ARGUMENTS CREATED BY THE MEMBER THROUGH AJAX
    jQuery.ajax({
        url:"action/popular",
        data:{memberId:loggedInUserMember.memberId},
        dataType:'json',
        type:'post',
        cache:false,
        success:function (result) {
            if (result.response) {
                var argumentListHtml = "";
                var argumentList = result.data;
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    argumentListHtml += loadShortArgument(argumentList[argumentCount]);
                }
                jQuery('#profileContentWrapper').html(argumentListHtml);
            } else {
                alert("Please try again");
            }
        }
    });
}

function loadCategoryArguments(thisObj, limit, topicId) {
    /*jQuery("#profileContentWrapper").html('');
     jQuery("#footerLoader").html('<span id="footerLoader"><img src="/images/da-loader.gif"></span>');
     jQuery('.horizontalMenu li').removeClass('activeMenu');
     jQuery(thisObj).parent().addClass('activeMenu');*/

    //LOAD THE ARGUMENTS CREATED BY THE MEMBER THROUGH AJAX
    jQuery.ajax({
        url:"action/topicArguments",
        data:{memberId:loggedInUserMember.memberId, limit:limit, topic:topicId},
        dataType:'json',
        type:'post',
        cache:false,
        beforeSend:function () {
            jQuery("#footerLoader").html('<img src="/images/da-loader.gif" alt="Loading...">');
            jQuery('.horizontalMenu li').removeClass('activeMenu');
            jQuery(thisObj).parent().addClass('activeMenu');
        },
        success:function (result) {
            if (result.response) {
                var argumentListHtml = "";
                var argumentList = result.data;
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    argumentListHtml += loadShortArgument(argumentList[argumentCount]);
                }
                jQuery('#profileContentWrapper').append(argumentListHtml);
                if (argumentList.length < 6) {
                    jQuery("#footerLoader").html("No more Arguments");
                }
            } else {
                jQuery("#footerLoader").html("No more Arguments");
            }
        }
    });
}

function loadPeopleArguments(thisObj) {
    jQuery('.horizontalMenu li').removeClass('activeMenu');
    jQuery(thisObj).addClass('activeMenu');

    //LOAD THE ARGUMENTS CREATED BY THE MEMBER THROUGH AJAX
    jQuery('#profileContentWrapper').html("<h2>Coming Soon</h2>");
}

function postOpinionCallBack(result, input) {
    var resultData = result.data;
    var agreeCount = null;
    if (input.vote == 1) {
        agreeCount = parseInt(jQuery("#agree-" + input.argumentId).metadata().agree);
        input.buttonSelector = jQuery("#agree-" + input.argumentId);
    } else {
        agreeCount = parseInt(jQuery("#disagree-" + input.argumentId).metadata().agree);
        input.buttonSelector = jQuery("#disagree-" + input.argumentId);
        var agreedPercentage = baseObj.CalculatePercentage(Math.round(((agreeCount) / currentCommentCount) * 100));
    }

    var commentHtmlSelector = jQuery(input.buttonSelector).parent().siblings(".postContentActions").find(".commentCount").children('span');
    var voteHtmlSelector = jQuery(input.buttonSelector).siblings(".circleMini");
    var currentCommentCount = parseInt(jQuery(commentHtmlSelector).html());
    var totalVoteCount = (resultData.voted) ? parseInt(jQuery(voteHtmlSelector).html()) + 1 : parseInt(jQuery(voteHtmlSelector).html());
    var currentVoteCount = parseInt(jQuery(voteHtmlSelector).html());
    var currentAgreedPercentage = (!currentVoteCount) ? 'd' : baseObj.CalculatePercentage(Math.round((agreeCount / currentVoteCount) * 100));
    if (input.vote == 1) {
        var agreedPercentage = (!currentVoteCount) ? '100' : baseObj.CalculatePercentage(Math.round(((agreeCount + 1) / totalVoteCount) * 100));
    } else {
        var agreedPercentage = (!currentVoteCount) ? '0' : baseObj.CalculatePercentage(Math.round(((agreeCount) / totalVoteCount) * 100));
    }
    if (!resultData.voted) {
        currentCommentCount += 1;
        jQuery(commentHtmlSelector).html(currentCommentCount);
    } else if (resultData.voted && !resultData.commented) {
        currentVoteCount += 1;
        jQuery(voteHtmlSelector).html(currentVoteCount);
        jQuery(voteHtmlSelector).toggleClass("sector" + currentAgreedPercentage + " sector" + agreedPercentage);
        jQuery(input.buttonSelector).metadata().voted = true;
        jQuery(input.buttonSelector).siblings(".DisagreeButton,.AgreeButton").metadata().voted = true;
    } else {
        currentVoteCount += 1;
        jQuery(voteHtmlSelector).html(currentVoteCount);
        jQuery(voteHtmlSelector).toggleClass("sector" + currentAgreedPercentage + " sector" + agreedPercentage);
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

function createArgumentCallBack(result, input) {
    var argumentHtml = loadShortArgument(result.data);
    jQuery(".argumentFeed").prepend(argumentHtml);
}

function memberFollowCallBack(result, input) {
    jQuery(input.clickObj).toggleClass('followMember unfollowMember').toggleClass('agreementGradient disagreementGradient').children().toggleClass('followMemberSpan unfollowMemberSpan').text('Unfollow');
}

function memberUnFollowCallBack(result, input) {
    jQuery(input.clickObj).toggleClass('followMember unfollowMember').toggleClass('agreementGradient disagreementGradient').children().toggleClass('followMemberSpan unfollowMemberSpan').text('Follow');
}

function favoriteCallBack(result, input) {
    var clickObj = input.clickObj;
    jQuery(clickObj).toggleClass('favIconOnG favIconOffG');
}

function syncArgumentCallBack(input, result) {
    if (result.argumentList != null) {
        var argumentList = result.argumentList;
        var argumentListHtml = "";
        var argumentIdArray = Array();
        jQuery(".shortArgument").each(function () {
            argumentIdArray.push(jQuery(this).metadata().id);
        });
        for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
            if (jQuery.inArray(argumentList[argumentCount].id, argumentIdArray) == -1) {
                argumentListHtml += loadShortArgument(argumentList[argumentCount]);
            }
        }
        if (window.homeActiveTab == "feedMenu")
            jQuery("#profileContentWrapper").prepend(argumentListHtml);
    }
}

function syncArgumentData(input) {
    var argumentIdArray = Array();
    jQuery(".shortArgument").each(function () {
        argumentIdArray.push(jQuery(this).metadata().id);
    });
    jQuery.ajax({
        url:'sync/argumentListData',
        dataType:'json',
        type:'post',
        data:{argumentIdArray:argumentIdArray},
        success:function (result) {
            if (result && result.response) {
                var argumentList = result.data;
                for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                    var agreed = parseInt(argumentList[argumentCount].agreed);
                    var disagreed = parseInt(argumentList[argumentCount].disagreed);
                    var totalCount = parseInt(agreed) + parseInt(disagreed);
                    var commentCount = argumentList[argumentCount].commentsCount;
                    var argumentId = argumentList[argumentCount].id;
                    var oldagree = parseInt(jQuery("#agree-" + argumentId).metadata().agree);
                    var oldTotalCount = parseInt(jQuery("#agree-" + argumentId).siblings('.circleMini').html());
                    var oldPercentage = (!oldTotalCount) ? 'd' : baseObj.CalculatePercentage(Math.round((oldagree / oldTotalCount) * 100));
                    var currentPercentage = (!totalCount) ? 'd' : baseObj.CalculatePercentage(Math.round((agreed / totalCount) * 100));
                    jQuery("#agree-" + argumentId).metadata().agree = agreed;
                    jQuery("#disagree-" + argumentId).metadata().agree = agreed;
                    jQuery("#agree-" + argumentId).siblings('.circleMini').html(totalCount);
                    jQuery("#agree-" + argumentId).siblings('.circleMini').toggleClass("sector" + oldPercentage + " sector" + currentPercentage);
                    jQuery("#agree-" + argumentId).parent().siblings(".postContentActions").find(".commentCount").children('span').html(commentCount);

                }
            }
        }
    });
}
