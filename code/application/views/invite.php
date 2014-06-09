<div id="homepageWrapper">
	<div id="staticMenuContainer">
		<ul id="staticMenu">
            <li class="primaryButton disabled active {id:'fb'}"><span class="secondaryText">Facebook</span></li>
			<li class="{id:'email'}"><span class="secondaryText">Email</span></li>
			<!--<li class="{id:'tw'}"><span class="secondaryText">Twitter</span></li>-->
		</ul>
	</div>
	<div id="staticContent">
        <div id="fbInviteContainer" class="staticContentChild">
		</div>
        <div id="emailInviteContainer" class="staticContentChild">
            <div id="emailInviteContent" class="from">
                <input type="text" id="emailAddress" class="placeholder defaultContent validate required multiemail {defaultText : 'Email address..',label:'Email'}" name="email1"/>
                <label class="label smallText secondaryTextColor">Enter multiple email addresses with Comma(,) seperated</label>
                <input type="hidden" class="{label:'Email'}" id="validateEmailData"/>
                <textarea id="message" name="message" rows='10' cols='40'></textarea>
                <button class="invite primaryButton gradient {site:'email','memberId':'<?php echo $loggedInUserMember->id; ?>'}">Invite</button>
            </div>
        </div>
		<div id="twInviteContainer" class="staticContentChild">
		</div>
	</div>
</div>