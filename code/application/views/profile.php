    <span class="userprofileInfo {memberId:'<?php echo $userMemberObject->id; ?>',loggedInMemberId:'<?php echo $loggedInUserMember->id; ?>',username:'<?php echo $userMemberObject->username;?>',profileThumb:'<?php echo $userMemberObject->profileThumb;?>',profilephoto:'<?php echo $userMemberObject->profilephoto;?>',isFollowing:'<?php echo $userMemberObject->isFollowing;?>', argumentCreatedCount:<?php echo $userMemberObject->argumentCreatedCount;?>,followerCount:'<?php echo  $userMemberObject->followerCount; ?>',followedCount:'<?php echo $userMemberObject->followedCount; ?>'}"></span>
    <div id="profileHead">
        <div class="userImgCircleFull">
            <img src="<?php echo $userMemberObject->profilephoto;?>"
                 alt="<?php echo $userMemberObject->username;?> on Disagree.me"
                <?php echo ($userMemberObject->fromThirdParty) ? "class='thirdPartyImgLarge'" : "";?> />
        </div>
        <div id="profileInfo">
            <div id="profileBasicInfo">
                <a href="#" class="heading3Link">
                    <?php echo ellipsis(($userMemberObject->fullname == '' ? $userMemberObject->username : $userMemberObject->fullname), 25);?>
                </a>
                <?php if($argumentOwner->location != ''): ?>
                    <address><i class="sprite-icon locationSmallIconG"></i><span><?php echo $userMemberObject->location;?></span></address>
                <?php endif; ?>
            </div>
            <?php if ($isLoggedInUserProfilePage): ?>
            <button class="primaryButton gradient" id="editProfileButtonWrapper">
                <i class="sprite-icon tickIconW"></i>
                <span id="editProfileButton">Edit Profile</span>
            </button>
            <?php else: ?>
            <button id="followButtonWrapper"
                    class="primaryButton gradient <?php echo $followClass ?> {followeMemberId :'<?php echo $userMemberObject->id ?>', followMain :true} actionSelector">
                <i class="sprite-icon <?php echo $imagetoggleFollow ?>"></i>
                <span><?php echo $followText?></span>
            </button>
            <?php endif;?>
        </div>
        <div id="profileStatBoard" class="statBoardShadow">
            <ul class="horizontalMenu">
                <li>
                    <div class="profileStatTitle obtuseGradient disabled gradient heading4 secondaryTextColor">
                        ARGUMENTS
                    </div>
                    <div class="pofilestatContent argumentCount secondaryContainer gradient">
                        <?php echo $userMemberObject->argumentCreatedCount;?>
                    </div>
                </li>
                <li>
                    <div class="profileStatTitle obtuseGradient disabled gradient heading4 secondaryTextColor">
                        FOLLOWERS
                    </div>
                    <div class="pofilestatContent follwersCount secondaryContainer gradient">
                        <?php echo  $userMemberObject->followerCount; ?>
                    </div>
                </li>
                <li>
                    <div class="profileStatTitle obtuseGradient disabled gradient heading4 secondaryTextColor">
                        FOLLOWING
                    </div>
                    <div class="pofilestatContent followingCount secondaryContainer gradient">
                        <?php echo $userMemberObject->followedCount; ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div id="profileNavMenu" class="primaryBackground primaryBorder">
        <ul class="horizontalMenu">
            <?php if($isLoggedInUserProfilePage): ?>
            <li id="activityFeed" class="userInfoTab {id:'activityFeed'}">
                <i class="sprite-icon notificationIconG"></i>
                <a href="javascript:void(0)" class="heading6 linkStrong">Notifications</a>
                <span class="counter primaryBorder"><?php echo $userMemberObject->notificationCount;?></span>
            </li>
            <?php else: ?>
            <li id="activityFeed" class="userInfoTab {id:'activityFeed'}">
                <i class="sprite-icon notificationIconG"></i>
                <a href="javascript:void(0)" class="heading6 linkStrong">Activity</a>
            </li>
            <?php ENDIF;?>
            <li id="argumentFed" class="userInfoTab {id:'argumentFed'}">
                <i class="sprite-icon daIconOnG"></i>
                <a href="javascript:void(0)" class="heading6 linkStrong">Arguments</a>
                <span class="counter primaryBorder"><?php echo $userMemberObject->argumentCreatedCount;?></span>
            </li>
            <li id="favoriteFeed" class="userInfoTab {id:'favoriteFeed'}">
                <i class="sprite-icon favIconOnG"></i>
                <a href="javascript:void(0)" class="heading6 linkStrong">Favorites</a>
                <span class="counter primaryBorder"><?php echo $userMemberObject->argumentFollowCount;?></span>
            </li>
            <li id="followingFeed" class="userInfoTab {id:'followingFeed'}">
                <i class="sprite-icon tickIconG"></i>
                <a href="javascript:void(0)" class="heading6 linkStrong">Following</a>
                <span class="counter primaryBorder"><?php echo $userMemberObject->followedCount;?></span>
            </li>
            <li id="followersFeed" class="userInfoTab {id:'followersFeed'}">
                <i class="sprite-icon maleIconG"></i>
                <a href="javascript:void(0)" class="heading6 linkStrong">Followers</a>
                <span class="counter primaryBorder"><?php echo $userMemberObject->followerCount?></span>
            </li>
            <li id="statFeed" class="userInfoTab {id:'statFeed'}">
                <i class="sprite-icon statsIconG"></i>
                <a href="javascript:void(0)" class="heading6 linkStrong">Stats</a>
            </li>
        </ul>
    </div>

    <div id="profileContentWrapper"></div>
    <span id="footerLoader"></span>
