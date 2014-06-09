    <div id="popupWrapper">
        <div id="popupContainer">
            <span id="dragHandle">&nbsp;</span>
            <span class="closex sprite-icon"></span>

            <div class="popupContent primarytext startArgumentContainer form">
                <h2>Start an Argument</h2>
                <label class="lable">Title</label>
                <input type="text" name="title" class="placeholder defaultContent {defaultText : 'Enter your Argument Title here', label:'Argument Title'} validate required" id="newArgTitle">
                <label class="lable">Opinion</label>
                <textarea rows="6" cols="39" id="newArgDesc" class="placeholder defaultContent {defaultText : 'Enter your Argument Opinion here', label:'Argument Description'} validate required"></textarea>
                <label class="lable">Topic</label>
                <select id="TopicSelector" class="topicListSelector">
                    <option value="">-- Select Topic --</option>
                    <?php foreach ($topicList as $key => $value): ?>
                    <option value="<?php echo $key;?>"><?php echo $value;?></option>
                    <?php endforeach;?>
                </select>
                <input name="topic" id="TopicSelectorSelectedValue" type="hidden" class="selectedValue validate required {label:'Topic',target:'TopicSelector'} selectBox"/>
                <label class="lable">Get others to argue with you</label>
                <div>
                    <input id="postArgumentFBCheck" type="checkbox" name="share" value="fb"><label
                    for="postArgumentFBCheck"></label><span>Post on Facebook</span>
                    <input id="postArgumentTWCheck" type="checkbox" name="share" value="tw"><label
                    for="postArgumentTWCheck"></label><span>Share on Twitter</span>
                </div>
                <input type="hidden" id="newArgSource" value="" name="source"/>
                <input type="hidden" id="newArgId" value=""/>
                <button class="primaryButton gradient" id="postNewArgButton"><span>CREATE ARGUMENT</span></button>
                <button class="primaryButton gradient" id="updateArgButton"><span>UPDATE ARGUMENT</span></button>
            </div>

            <div class="popupContent primarytext agreeing form">
                <h3>You <span class="agreementText adToggle">agree</span> with<br/><span id="agreeModalUserName"></span>'s argument</h3>
                <label class="lable">Add a comment (optional)</label>
                <textarea rows="6" cols="39" id="agreeCommentText" class="placeholder defaultContent {defaultText : '',label:'Comment'}"></textarea>
                <label class="lable">Get others to agree with you</label>
                <div>
                    <input id="postAgreeFBCheck" type="checkbox" name="share" value="fb"><label for="postAgreeFBCheck"></label><span>Post on Facebook</span>
                    <input id="postAgreeTWCheck" type="checkbox" name="share" value="tw"><label for="postAgreeTWCheck"></label><span>Share on Twitter</span>
                </div>
                <button class="primaryButton gradient agreementGradient" id="postAgree"><span>I AGREE</span></button>
            </div>

            <div class="popupContent primarytext disagreeing form">
                <h3>You <span class="disagreementText adToggle">disagree</span> with<br/><span id="disagreeModalUserName"></span>'s argument</h3>
                <label class="lable">Add a comment (optional)</label>
                <textarea rows="6" cols="39" id="disagreeCommentText" class="placeholder defaultContent {defaultText : '',label:'Comment'}"></textarea>
                <label class="lable">Get others to disagree with you</label>
                <div>
                    <input id="postDisagreeFBCheck" type="checkbox" name="share" value="fb"><label for="postDisagreeFBCheck"></label><span>Post on Facebook</span>
                    <input id="postDisagreeTWCheck" type="checkbox" name="share" value="tw"><label for="postDisagreeTWCheck"></label><span>Share on Twitter</span>
                </div>
                <button class="primaryButton gradient disagreementGradient" id="postDisagree"><span>I DISAGREE</span></button>
            </div>

            <div class="popupContent primarytext updateComment form">
                <h3 class="popupHeading">Edit your comment</h3>
                <label class="lable popupLable">Edit your comment</label>
                <textarea rows="6" cols="39" id="updateCommentReplyText" class="validate required"></textarea>
                <label class="lable">Get others to agree with you</label>
                <div>
                    <input id="updateCommentReplyFBCheck" type="checkbox" name="share" value="fb"><label for="updateCommentReplyFBCheck"></label><span>Post on Facebook</span>
                    <input id="updateCommentReplyTWCheck" type="checkbox" name="share" value="tw"><label for="updateCommentReplyTWCheck"></label><span>Share on Twitter</span>
                </div>
                <button class="primaryButton gradient primaryGradient" id="updateCommentReply"><span>Submit Changes</span></button>
            </div>

            <div class="popupContent tbox"></div>

            <div class="popupContent votedUserDisplayBox" id="votesPopUp"></div>

            <div class="popupContent inviteUserDisplayBox" id="invitePopUp"></div>

            <div id="forgetPassForm" class="popupContent secondaryText">
                <input type="text" name="email"  class="placeholder defaultContent {defaultText : 'Email',label:'Email'} validate required email" id="forgetPassEmail"/>
                <div class="loginActionContainer">
                    <input type="submit" value="Request New Password" class="primaryButton gradient button" id="forgetPassSubmit">
                </div>
            </div>

            <div id="imageCropWindow" class="popupContent secondaryText">
                <h3>Crop Your Image</h3>
                <span class="linkRegular smallText">Select area to crop</span>
                <div id="imageContainer"></div>
                <div id="croppedThumb"></div>
                <div style="margin-left: 10px;" id="saveThumb"><button class="primaryButton" id="saveCropThumb">Save</button></div>
                <form action="" id="imageCropForm">
                    <fieldset>
                    <input type="hidden" name="x1" value="" id="x1" />
                    <input type="hidden" name="y1" value="" id="y1" />
                    <input type="hidden" name="x2" value="" id="x2" />
                    <input type="hidden" name="y2" value="" id="y2" />
                    <input type="hidden" name="w" value="" id="w" />
                    <input type="hidden" name="h" value="" id="h" />
                    <input type="hidden" name="image" value="" id="originalImageName" />
                    </fieldset>
                </form>
            </div>

        </div>
    </div>

    <div id="backgroundPopup"></div>

    <div id="searchWrapper" class="secondaryContainer popup">
        <span class="sprite-icon larrowIconW quickMenuTip"></span>
        <div id="searchMenu">
            <span class="activeMenu heading6 {id:'searchArgumentList'}" id="searchArgumentListTrigger">Search Arguments</span>
            <span id="searchMemberListTrigger" class="heading6 {id:'searchMemberList'}">Search Users</span>
        </div>
        <div id="searchList">
            <div id="searchArgumentList"></div>
            <div id="searchMemberList"></div>
        </div>
    </div>

    <div class="secondaryContainer" id="messageContainer">
        <div class="daLogo"><img src="<?php echo base_url();?>images/logo-icon.png" alt="Disagree Me"/></div>
        <div class="daMessage">You have already reported this argument as spam. Our team is reviewing it.</div>
        <span class="sprite-icon closeSmallIconG"></span>
    </div>

</div>
    <div id="footer">
        <ul id="footerMenu" class="horizontalMenu">
            <li><a href="<?php echo base_url();?>about" class="linkRegular">About Us</a></li>
            <li><a href="<?php echo base_url();?>privacyPolicy" class="linkRegular">Privacy Policy</a></li>
            <li><a href="<?php echo base_url();?>terms" class="linkRegular">Terms and Conditions</a></li>
            <li ><a href="<?php echo base_url();?>contactUs" class="linkRegular">Contact Us</a></li>
            <li id="SuggestionBoxWrapper" class="last"><input type="text" id="SuggestionBox" class="placeholder defaultContent {defaultText : 'Type Your Feedback / Suggestion and press Enter...'}" /><label class="suggestionSuccessMessage">Your message has been submitted</label></li>
            <li><span class="copyright secondaryTextColor">&copy; 2012 Disagree.me</span></li>
        </ul>
    </div>
</div>
<!-- Piwik --> 
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.disagree.me/" : "http://piwik.disagree.me/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", <?php echo config_item('PIWIK_ID'); ?>);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://piwik.disagree.me/piwik.php?idsite=<?php echo config_item('PIWIK_ID'); ?>" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->

</body>
</html>
