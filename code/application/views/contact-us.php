<div id="homepageWrapper">
	<div id="staticMenuContainer">
		<ul id="staticMenu">
			<li><span class="secondaryText"><a href="<?php echo base_url();?>about" class="secondaryText"><?php echo ABOUT_US;?></a></span></li>
			<li><span class="secondaryText"><a href="<?php echo base_url();?>privacyPolicy" class="secondaryText"><?php echo PRIVACY_POLICY;?></a></span></li>
			<li><span class="secondaryText"><a href="<?php echo base_url();?>terms" class="secondaryText"><?php echo TERMS_AND_CONDITIONS;?></a></span></li>
			<li class="primaryButton disabled active"><span class="secondaryText"><a href="<?php echo base_url();?>contactUs" class="secondaryText"><?php echo CONTACT_US;?></a></span></li>
		</ul>
	</div>
    <div id="staticContent">
        <div id="contactBody">
            <div id="contactHeadText">
                <h1 >Lets get in Touch</h1>
                <h3 >Have a question or suggestion? Contact Us.  Feel free to post your thoughts publicly as an argument as well :)</h3>
            </div>
            <form method="post" action="action/contactUs" id="contactform">
                <fieldset>
                <div id="contactformSection">
                    <div class="formRecord">
                        <div class="formLabel lable">Name</div>
                        <div class="formElement">
                            <input type="text" name="contactuname" id="contactUname" class="validate required {label:'Name'}"/>
                        </div>
                    </div>
                    <div class="formRecord">
                        <div class="formLabel lable">Email</div>
                        <div class="formElement">
                            <input type="text" name="contactemail" id="contactEmail" class="validate required email {label:'Email'}"/>
                        </div>
                    </div>
                    <div class="formRecord">
                        <div class="formLabel lable">Subject</div>
                        <div class="formElement">
                            <input type="text" name="contactsubject" id="contactSubject"/>
                        </div>
                    </div>
                    <div class="formRecord">
                        <div class="formLabel lable">Feedback</div>
                        <div class="formElement">
                            <textarea id="contacttextarea" rows="6" cols="42"></textarea>
                        </div>
                    </div>
                    <div class="formRecord">
                        <div class="formLabel">&nbsp;</div>
                        <div class="formElement">
                            <button id="contactSubmit" class="primaryButton gradient">Send Message</button>
                        </div>
                    </div>
                </div>
                </fieldset>
            </form>
            <div id="altcontact">
                <div id="altcontacttop">
                    <p class="lable">Call us on</p>
                    <h2 >+91 80 4148 3223</h2>
                    <h2 >+1-646-403-4849</h2>
                </div>
                <div id="altcontactmiddle">
                    <p class="lable">Email us:  </p>
                    <h2 >info@disagree.me</h2>

                </div>
                <div id="altcontactbottom">
                    <div class="followicons">
                        <!--<img alt="follow on facebook" src="/images/contact-fb-img.png">
                        <img alt="follow on twitter" src="/images/contact-tw-img.png">
                        <img alt="follow on youtube" src="/images/contact_yt-img.png">-->
                    </div>
                </div>
            </div>
        </div>

    </div>
	<!--<div id="staticContent">
		<div id="contactHead">
			<div id="contactHeadImg">
				<img src="/images/contactus-img-1.png" alt="contactus icon" />
			</div>
			<div id="contactHeadText">
				<h1>Lets get in Touch</h1>
				<h3><?php echo CONTACT_US_BODY;?></h3>
			</div>
		</div>
		<div id="contactBox">
			<div id="contactform">
				<input type="text" class="contacttextfield" value="Your name" /> <input
					type="text" class="contacttextfield" value="Your email" /> <input
					type="text" class="contacttextfield" value="RE: Subject" />
				<textarea class="contacttextarea">Feedback / Comments / Get in touch with us</textarea>
				<div id="submitArea">
					<input type="submit" value="Send Message" id="submitBtn">
				</div>
			</div>
			<div id="altcontact">
				<div id="altcontacttop">
					<p>Or, call us on:</p>
					<h2>+91 80 4148 3223</h2>
					<h2>+1-646-403-4849</h2>
				</div>
				<div id="altcontactmiddle">
					<p>Email us directly at:</p>
					<h2>info@disagree.me</h2>
				</div>
				<div id="altcontactbottom">
					<p>And find us on:</p>
					<div class="followicons">
						<img src="/images/contact-fb-img.png" alt="follow on facebook" />
						<img src="/images/contact-tw-img.png" alt="follow on twitter" /> <img
							src="/images/contact_yt-img.png" alt="follow on youtube" />
					</div>
				</div>
			</div>
		</div>
	</div>-->
</div>
