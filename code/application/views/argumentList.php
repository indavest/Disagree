<div id="SocialMediaBox">
    <div id="SocialMediaTabCloseAction">
        <span id="SocialMediaBoxHide" class="secondaryTextColor smallText">HIDE&nbsp;&nbsp;X</span>
        <span id="SocialMediaBoxShow" class="secondaryTextColor smallText">SOCIAL MEDIA&nbsp;&nbsp;></span>
    </div>
    <div id="SocialMediaSwitchAction" >
        <span id="SocialMediaBoxSwitchFacebook" class="secondaryTextColor smallText" title="Facebook Feeds"><img src="/images/f_logo.png" width="25px" height="25px" alt="Facebook"/></span>
        <span id="SocialMediaBoxSwitchTwitter" class="secondaryTextColor smallText" title="Twitter Tweets"><img src="/images/Twitter_logo.png" width="25px" height="25px" alt="Twitter"/></span>
    </div>
    <div id="SocialMediaBoxBody">
        <div id="SocialMediaTabHeading">
            <h1>Start an argument with your Facebook friends</h1>

            <!--<h1>Facebook Friends and Twitter Followers</h1>-->
        </div>
        <div id="SocialMediaContentWrapper">
            <div class="scrollLeft">&nbsp;</div>
            <div class="FeedContent" id="SocialMediaContentHolder">
            </div>
            <div class="scrollRight">&nbsp;</div>
        </div>
        <div id="SocialMediaPaging">&nbsp;</div>
    </div>
</div>
<div id="homeArgumentNav" class="primaryBackground primaryBorder">
	<ul class="horizontalMenu">
		<li class="userInfoTab" id="feedMenu"><a href="javascript:void(0)" class="feed heading6 linkStrong {id:'feed'}">Stream</a></li>
		<li class="userInfoTab" id="allMenu"><a href="javascript:void(0)" class="allArguments heading6 linkStrong {id:'all'}">All</a></li>
		<li class="userInfoTab" id="interestingMenu"><a href="javascript:void(0)" class="interesting heading6 linkStrong {id:'interesting'}">Interesting</a></li>
        <li class="userInfoTab" id="ArgumentsStartedMenu"><a href="javascript:void(0)" class="ArgumentsStarted heading6 linkStrong {id:'ArgumentsStarted'}">My Arguments</a></li>
        <li class="userInfoTab" id="ArgumentsFollowedMenu"><a href="javascript:void(0)" class="ArgumentsFollowed heading6 linkStrong {id:'ArgumentsFollowed'}">My Favorites</a></li>
		<li class="userInfoTab hasSubMenu" id="categoryMenu">
            <a href="javascript:void(0)" class="heading6 linkStrong category categoryLink {id:'category'}"> <span id="categoryText">Categories</span><span class="sprite-icon darrowSmallIconG"></span></a>
            <div class="hozontalMenuTopicList secondaryContainer popupActive" id="categoryMenuSubMenu">
                <span class="sprite-icon larrowIconG horizontalMenuTopicListTip"></span>
                <ul>
                    <?php foreach ($topicList as $key => $value):?>
                    <li class="linkRegular" id="<?php echo $key;?>"><?php echo $value;?></li>
                    <?php endforeach;?>
                    <li class="linkRegular" id="1">All</li>
                </ul>
            </div>
        </li>
	</ul>

	<div class="newUserMsg heading3"><span class="startArgHandle">Start posting your opinion</span></div>
</div>
<div id="profileContentWrapper" class="argumentFeed" style="text-align: center;"></div>
<span id="footerLoader"><img src="<?php echo base_url();?>images/da-loader.gif" alt="Loading..."/></span>