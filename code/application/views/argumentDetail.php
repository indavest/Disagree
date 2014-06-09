<?php
$this->load->helper('text');
$argumentFollowFlag = $argumentData->isFollowing == '0'?false:true;
$lockFlag = ($argumentData->status==0)?'true':'false';
$lockFlagBool = ($lockFlag == 'true')?true:false;
$favoriteStatus = '';
if($isArgumentOwner):
    $favoriteStatus = ($lockFlagBool)?"lockOnG":"lockOffG";
elseif($argumentFollowFlag):
    $favoriteStatus = ($lockFlagBool)?"lockOnG":"favIconOnG";
else:
    $favoriteStatus = ($lockFlagBool)?"lockOnG":"favIconOffG";
endif;

$hasVotes= ($totalVotes==0)?false:true;                             //has Votes or not (true / false)
$hasCommentFlag = ($argumentData->commentsCount == 0)?false:true;   //has Comments or not (true / false)
$haslockedFlag = ($argumentData->status==0)?true:false;             //has argument Locked (true / false)
$hasFollowersFlag = ($argumentData->FollowingUserCount==0)?false:true;              //has Followers (false / count)
$arugumentActivityFlag = ($hasCommentFlag || $hasVotes || $hasFollowersFlag || $haslockedFlag);
?>
<div id="argumentObj"
     class="argumentDataObj {id:'<?php echo $argumentData->id;?>',title:'<?php echo $this->input->clean($argumentData->title);?>',createdTime:'<?php echo $argumentData->createdtime;?>',lastModifiedTime:'<?php echo $argumentData->lastmodified;?>',memberId:'<?php echo $argumentData->memberId;?>',status:'<?php echo $argumentData->status;?>',topic:'<?php echo $argumentData->topic;?>',source:'<?php echo $argumentData->source;?>',profilephoto:'<?php echo $argumentOwner->profilephoto;?>',agreed:'<?php echo $argumentData->agreed;?>',disagreed:'<?php echo $argumentData->disagreed;?>',commentsCount:'<?php echo $argumentData->commentsCount;?>',maleagreed:'<?php echo $argumentData->maleagreed;?>',femaleagreed:'<?php echo $argumentData->femaleagreed;?>',maledisagreed:'<?php echo $argumentData->maledisagreed;?>',femaledisagreed:'<?php echo $argumentData->femaledisagreed;?>',username:'<?php echo $argumentOwner->username;?>', isFavorite:'<?php echo ($argumentData->isFollowing==0)?'false':'true';?>', owner:<?php echo ($isArgumentOwner) ? 'true' : 'false';?>, voted:<?php echo ($argumentData->isVoted==0) ? 'false' : 'true';?>}"></div>
<div id="argumentDetailHead" class="secondaryBorderColor {id:'<?php echo $argumentData->id;?>'}">
    <div class="userProfile">
        <div id="argumentOwnerImg" class="userImgCircleFull">
            <img src="<?php echo $argumentOwner->profilephoto; ?>"
                 alt="<?php echo $argumentOwner->username; ?> on Disagree.me" <?php echo ($argumentOwner->fromThirdParty)?"class='thirdPartyImgLarge'":"";?>/>
        </div>
        <div id="profileInfo">
            <div id="profileBasicInfo">
                <a href="profile?id=<?php echo $argumentOwner->id;?>" class="heading3Link profileUserName DAtip up " >
                    <?php echo ellipsis((($argumentOwner->fullname=='')?$argumentOwner->username:$argumentOwner->fullname), 18);?></a>
                <?php if($argumentOwner->location!= null && $argumentOwner->location != ''){?><address><i class="sprite-icon locationSmallIconG"></i><span><?php echo $argumentOwner->location;?></span></address><?php }?>
            </div>
        </div>
    </div>
    <div class="argumenContent secondaryContainer">
        <div class="argumentContentTipLeft"></div>
        <?php if (!$isArgumentOwner):?>
            <div class="favIcon {argumentId:'<?php echo $argumentData->id;?>',locked:<?php echo $lockFlag;?>,ownerId:'<?php echo $argumentOwner->id;?>'}">
                <i class="sprite-icon <?php echo $favoriteStatus?>"></i>
            </div>
        <?php else: ?>
            <div class="lockButton" >
                    <i class="sprite-icon <?php  echo($argumentData->status == '1')?"lockOffG":"lockOnG"?>"></i>
            </div>
            <div class="inviteIcon {argumentId:'<?php echo $argumentData->id;?>',ownerId:'<?php echo $argumentOwner->id;?>'}" title="Invite people to this argument.">
                <i class="sprite-icon profileIconG"></i>
            </div>
            <?php if(!$arugumentActivityFlag){?>
                <div class="editArgIcon {argumentId:'<?php echo $argumentData->id;?>',ownerId:'<?php echo $argumentOwner->id;?>'}" title="Edit Argument.">
                    <i class="sprite-icon editIconG"></i>
                </div>
            <?php }?>
        <?php endif;?>

        <p class="argumentTitle heading2" id="title_<?php echo $argumentData->id;?>"><?php echo $argumentData->title;?></p>

        <p class="argumentDesc" id="currentArgumentDesc"><?php echo html_entity_decode ($argumentData->argument);?></p>

        <div class="timeagoTextFull secondaryTextColor timeStamp">
            <i class="sprite-icon timeIconG"></i>
            <i id="createdTimePlaceHolder"><?php /*echo $argumentData->createdtime;*/?></i>
            <?php if(strcasecmp($argumentData->source,'facebook')==0):?>
                <span class="dataSource"> via FaceBook</span></i>
            <?php elseif(strcasecmp($argumentData->source,'twitter')==0):?>
                <span class="dataSource"> via Twitter</span></i>
            <?php endif?>
        </div>
        <div class="topicLableWrapper disabled">
            <div class="topicLable"><a id="topicLink" href="home?category=<?php echo $argumentData->topic;?>" class="linkRegular"><?php echo $topicList[$argumentData->topic]; ?></a></div>
            <div class="topicArgCount"><?php echo $topicArgumentCount[$argumentData->topic]; ?></div>
        </div>

    </div>
    <div class="shareToolBar">
        <!--<div><img src="/images/static-twitter-image.png" alt="Share on Twitter" id="postToFB" /></div>-->
        <div style="width:85px">
            <a href="https://twitter.com/share" class="twitter-share-button" data-text="<?php echo $argumentData->title; ?>" data-via="Disagree_Me">Tweet</a>
            <script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>
        <div>
            <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(base_url()."/detail?id=".$argumentData->id);?>&amp;media=<?php echo urlencode($argumentData->profilephoto);?>&amp;description=<?php echo $argumentData->title;?>" class="pin-it-button" count-layout="horizontal" target="_blank"><img border="0" src="//assets.pinterest.com/images/PinExt.png" alt="Pin It" /></a>
        </div>
        <div>
            <div class="customFBShareButton"></div>
            <!--<div class="fb-like" data-send="true" data-width="175" data-show-faces="false" data-font="arial"></div>-->
        </div>
    </div>
</div>
<div id="argumentDetailBody" style="float: left; text-align: center; width: 980px;">
    <div class="profileGraph">
        <div class="agreesTip secondaryContainer statBoardShadow">
            <div class="agreementGradient">AGREEMENTS</div>
            <ul class="agreeStats">
                <li class="agreedPercentage">
                    <span id="agreeTotalPercentage" <?php echo ($totalAgreedPercentage == '100')?'class="fontsmall"':'class="fontlarge"';?>><?php echo $totalAgreedPercentage?></span><sup>%</sup>
                </li>
                <li class="maleAgreed secondaryText <?php echo ($argumentData->maleagreed>0)?"showVotes {gender:'M',vote:'".AGREE_VOTE_ID."'}":"" ?>">
                    <span class="textSmallBold"><i class="sprite-icon maleIconG"></i>MALE</span>
                    <span id="maleAgreeVoteCount"><?php echo $argumentData->maleagreed; ?></span>
                </li>
                <li class="slaneLine"></li>
                <li class="femaleAgreed secondaryText <?php echo ($argumentData->femaleagreed>0)?"showVotes {gender:'F',vote:'".AGREE_VOTE_ID."'}":"" ?>">
                    <span class="textSmallBold"><i class="sprite-icon femaleIconG"></i>FEMALE</span>
                    <span id="femaleAgreeVoteCount"><?php echo $argumentData->femaleagreed; ?></span>
                </li>
                <li class="totalAgreed secondaryText <?php echo ($argumentData->agreed>0)?"showVotes {gender:'',vote:'".AGREE_VOTE_ID."'}":"" ?>">
                    <span class="textSmallBold">TOTAL</span>
                    <span id="totalAgreeVoteCount"><?php echo $argumentData->agreed; ?></span>
                </li>
                <li class="tipAgree">&nbsp;</li>
            </ul>
        </div>
        <div class="graphCircleLarge">
            <div class="graphBoxLarge <?php echo $totalVotes>0?"showVotes {gender:'',vote:''}":""; ?>">
                <!--<div class="overlay">-->
                <div
                    class="circleLarge sector<?php echo ($totalVotes != 0) ? $totalAgreedPercentage : 'd';?> heading4 sprite-graph-large">
                    <p class="opinionCount heading1" id="argumentTotalVoteCount"><?php echo $totalVotes; ?></p>

                    <p class="opinionText heading2">OPINIONS</p>
                    <!--</div>-->
                </div>
            </div>
        </div>
        <div class="disagreesTip secondaryContainer statBoardShadow">
            <div class="disagreementGradient">DISAGREEMENTS</div>
            <ul class="disAgreeStats">
                <li class="tipDisagree">&nbsp;</li>
                <li class="totalDisagreed secondaryText <?php echo ($argumentData->disagreed>0)?"showVotes {gender:'',vote:'".DISAGREE_VOTE_ID."'}":"" ?>">
                    <span class="textSmallBold">TOTAL</span>
                    <span id="totalDisagreeVoteCount"><?php echo $argumentData->disagreed; ?></span>
                </li>
                <li class="maleDisagreed secondaryText <?php echo ($argumentData->maledisagreed>0)?"showVotes {gender:'M',vote:'".DISAGREE_VOTE_ID."'}":"" ?>">
                    <span class="textSmallBold"><i class="sprite-icon maleIconG"></i>MALE</span>
                    <span id="maleDisagreeVoteCount"><?php echo $argumentData->maledisagreed; ?></span>
                </li>
                <li class="slaneLine"></li>
                <li class="femaleDisagreed secondaryText <?php echo ($argumentData->femaledisagreed>0)?"showVotes {gender:'F',vote:'".DISAGREE_VOTE_ID."'}":''?>">
                    <span class="textSmallBold"><i class="sprite-icon femaleIconG"></i>FEMALE</span>
                    <span id="femalDisagreeVoteCount"><?php echo $argumentData->femaledisagreed; ?></span>
                </li>
                <li class="disAgreedPercentage"><span
                    id="disagreeTotalPercentage"
                    <?php echo ($totalDisagreePercentage == '100')?'class="fontsmall"':'class="fontlarge"';?>><?php echo $totalDisagreePercentage?></span><sup>%</sup>
                </li>
            </ul>
        </div>
        <div class="graphContent">
            <div class="timelineBar"></div>
    <?php //if(!$isArgumentOwner){?>
    <div class="voteBadge">
                <button
                    class="AgreeButton agreementGradient {argumentId:'<?php echo $argumentData->id;?>', username:'<?php echo $argumentOwner->username;?>', owner:<?php echo ($isArgumentOwner) ? 'true' : 'false';?>, voted:<?php /*echo ($isLoggedInUserMemberVoted) ? 'true' : 'false';*/echo ($isArgumentOwner) ? 'true' : ($argumentData->isVoted==0) ? 'false' : 'true';?>,locked:<?php echo $lockFlag;?>}"
                    id="agree-<?php echo $argumentData->id;?>">AGREE
                </button>
                <button
                    class="disagreementGradient DisagreeButton {argumentId:'<?php echo $argumentData->id;?>', username:'<?php echo $argumentOwner->username;?>', owner:<?php echo ($isArgumentOwner) ? 'true' : 'false';?>, voted:<?php /*echo ($isLoggedInUserMemberVoted) ? 'true' : 'false';*/echo ($isArgumentOwner) ? 'true' : ($argumentData->isVoted==0) ? 'false' : 'true';?>,locked:<?php echo $lockFlag;?>}"
                    id="disagree-<?php echo $argumentData->id;?>">DISAGREE
                </button>
                <div></div>
            </div>
        <?php //}?>
            <ul id="timeContentHodler">
            </ul>
        </div>

    </div>
    <!--<img alt="body" src="/images/Disagree.jpg" style="">-->
</div>

<!--<pre><?php /*print_r($argumentComments)*/;?></pre>-->

<?php /*if($argumentData->source== 'twitter'){?>
<!--via <span class="linkRegular">Twitter</span>-->
    <?php} elseif ($argumentData->source == 'facebook'){?>
<!--via <span class="linkRegular">Facebook</span>-->
<?php}*/
?>