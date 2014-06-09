<div id="popupWrapper">
    <div id="popupContainer">
        <span id="dragHandle">&nbsp;</span>
        <span class="closex sprite-icon"></span>
        <div class="popupContent secondaryText loginContainer">
            <button class="primaryButton gradient" onclick="window.open('<?php echo base_url();?>base/fbLogin', 'Facebook','menubar=no,width=930,height=560,toolbar=no');"><span>SIGN IN WITH FACEBOOK</span></button>
            <!--<div class="divider"><span>or</span></div>
                               <button class="primaryButton gradient" onclick="window.open('/gplus/gplus_login.php', 'GMail','menubar=no,width=930,height=560,toolbar=no');"><span>SIGN IN WITH GOOGLE</span></button>-->
            <div class="divider"><span>or</span></div>
            <form action="<?php echo base_url();?>base/userMemberAuthenticate" method="post">
                <fieldset>
                <input type="text" name="username" class="placeholder defaultContent {defaultText : 'Username / Email',label:'Username'} validate required" />
                <i class="sprite-icon  profileIconG"></i>
                <span class="checkAvailability"></span>
                <input type="password" name="password" class="{label:'Password'} validate required " />
                <i class="sprite-icon  lockOnG"></i>
                <span class="checkAvailability"></span>
                <div class="loginActionContainer">
                    <a href="javascript:void(0)" id='forgetPassLink' class="linkRegular disabled">Forgot Password?</a>
                    <input type="submit" value="SIGN IN" class="primaryButton gradient signInButton button" />
                </div>
                </fieldset>
            </form>
            <div class="accountActionContainer smallText">
                <span class="lable">Don't have an account?</span><span class="toggleSignIn smallText">Lets create one.</span>
            </div>
        </div>
        <div class="popupContent secondaryText creatAccountContainer">
            <button class="primaryButton gradient" onclick="window.open('<?php echo base_url();?>base/fbLogin', 'Facebook','menubar=no,width=930,height=560,toolbar=no');"><span>SIGN IN WITH FACEBOOK</span></button>
            <div class="divider"><span>or</span></div>
            <form action="<?php echo base_url();?>base/userMemberCreate" method="post" id="createAccountForm">
                <fieldset>
                <input type="text" id="newUserName" name="username" class="placeholder defaultContent {defaultText : 'Username', minLength:4,maxLength:32, label:'Username' } validate required minLength maxLength uname" />
                <i class="sprite-icon  profileIconG"></i>
                <span id="uNameAvalabilityCheck" class="checkAvailability available"></span>
                <input id="newUserPass" type="password" name="password" class="{minLength:4,maxLength:32, label:'password'} validate required minLength maxLength" />
                <i class="sprite-icon  lockOnG"></i>
                <span id="uPassStrengthDisplay" class="checkAvailability available"></span>
                <input type="text" id="newUserEmail" name="email" class="placeholder defaultContent {defaultText : 'Email',label:'Email'} validate required email"/>
                <i class="sprite-icon atIconG"></i>
                <span id="uEmailavalabilityCheck" class="checkAvailability available"></span>
                <div id="genderCheck">
                    <div id="itoggle" class="project">
                    <div id="genderSelect">
                        <label for="genderSelectCheck">Gender: </label>
                        <input type="checkbox" checked="checked" id="genderSelectCheck" />
                        <input type="hidden" value="M" id="newGender" name="gender">
                    </div>
                    </div>
                </div>
                <div class="createActionContainer">
                    <input type="hidden" id="location" value=""/>
                    <input type="submit" value="CREATE ACCOUNT" class="primaryButton gradient signInButton button"/>
                </div>
                </fieldset>
            </form>
            <div class="accountActionContainer smallText">
                <span class="lable">Already have an account?&nbsp;</span><span class="toggleSignUp smallText">Sign in.</span>
            </div>
        </div>
        <div class="popupContent votedUserDisplayBox" id="votesPopUp"></div>
        <div id="forgetPassForm" class="popupContent secondaryText form">
            <input type="text" name="email"  class="placeholder defaultContent {defaultText : 'Email',label: 'email'} validate required email" id="forgetPassEmail"/>
            <div class="loginActionContainer" id="forgetPassSubmitWrapper">
                <input type="submit" value="Request New Password" class="primaryButton gradient button" id="forgetPassSubmit"/>
            </div>
        </div>
    </div>
</div>
<div id="backgroundPopup"></div>
</div>
<div class="secondaryContainer" id="messageContainer">
    <div class="daLogo"><img src="<?php echo base_url();?>images/logo-icon.png" alt="Disagree Me"/></div>
    <div class="daMessage">You have already reported this argument as spam. Our team is reviewing it.</div>
    <span class="sprite-icon closeSmallIconG"></span>
</div>
<div id="footer">
    <ul id="footerMenu" class="horizontalMenu">
        <li><a href="<?php echo base_url();?>about" class="linkRegular">About Us</a></li>
        <li><a href="<?php echo base_url();?>privacyPolicy" class="linkRegular">Privacy Policy</a></li>
        <li><a href="<?php echo base_url();?>terms" class="linkRegular">Terms and Conditions</a></li>
        <li class="last"><a href="<?php echo base_url();?>contactUs" class="linkRegular">Contact Us</a></li>
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