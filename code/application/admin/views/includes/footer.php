				<div id="popupWrapper">
					<div id="popupContainer">
						<span class="closex sprite-icon"></span>
						<div class="popupContent secondaryText loginContainer">		
							<button class="primaryButton gradient" onclick="window.open('<?php echo base_url();?>base/fbLogin', 'Facebook','menubar=no,width=930,height=560,toolbar=no');"><span>SIGN IN WITH FACEBOOK</span></button>
							<!--<div class="divider"><span>or</span></div>
							<button class="primaryButton gradient" onclick="window.open('/gplus/gplus_login.php', 'GMail','menubar=no,width=930,height=560,toolbar=no');"><span>SIGN IN WITH GOOGLE</span></button>-->
							<div class="divider"><span>or</span></div>
							<form action="<?php echo base_url();?>base/userMemberAuthenticate" method="post">
                                <fieldset>
								<input type="text" name="email" class="placeholder defaultContent {defaultText : 'Email'} validate required email">
								<input type="password" name="password" class="placeholder defaultContent {defaultText : 'password'} validate required ">
								<div class="loginActionContainer">
									<span>Forgot Password?</span>
									<input type="submit" value="SIGN IN" class="primaryButton gradient signInButton button">
								</div>
                                </fieldset>
							</form>
							<div class="accountActionContainer smallText">
								<span class="lable">Don't have an account?</span><span>Lets create one.</span>
							</div>
						</div>
						<div class="popupContent secondaryText creatAccountContainer">		
							<button class="primaryButton gradient" onclick="window.open('<?php echo base_url();?>base/fbLogin', 'Facebook','menubar=no,width=930,height=560,toolbar=no');"><span>SIGN IN WITH FACEBOOK</span></button>
							<!--<div class="divider"><span>or</span></div>
							<button class="primaryButton gradient" onclick="window.open('/gplus/gplus_login.php', 'GMail','menubar=no,width=930,height=560,toolbar=no');"><span>SIGN IN WITH GOOGLE</span></button>-->
							<div class="divider"><span>or</span></div>
							<form action="<?php echo base_url();?>base/userMemberCreate" method="post">
                                <fieldset>
								<input type="text" name="username" class="placeholder defaultContent {defaultText : 'Username'} validate required">
								<input type="password" name="password" class="placeholder defaultContent {defaultText : 'Password'} validate required">
								<input type="text" name="email" class="placeholder defaultContent {defaultText : 'Email'} validate required email">
								<div class="createActionContainer">
									<input type="submit" value="CREATE ACCOUNT" class="primaryButton gradient signInButton button">
								</div>
                                </fieldset>
							</form>
							<div class="accountActionContainer smallText">
								<span class="lable">Already have an account?&nbsp;</span><span>Sign in.</span>
							</div>
						</div>
						<div class="popupContent primarytext startArgumentContainer form">
							<h2>Start an Argument</h2>
							<label class="lable">Title</label>
							<input type="text" name="title" class="placeholder defaultContent {defaultText : 'Type your Argument Title here'} validate required" id="newArgTitle">
							<label class="lable">OPINION</label>
							<textarea rows="6" cols="31" id="newArgDesc" class="placeholder defaultContent {defaultText : 'Type your Argument Opinion here'} validate required"></textarea>
							<label class="lable">Topic</label>
							<select name="topic" id="topic" class="validate required {defaultText : '- Choose one -'}">
								<option value="">- Choose one -</option>
								<?php foreach ($topicList as $key => $value):?>
								<option value="<?php echo $key;?>"><?php echo $value;?></option>
								<?php endforeach;?>
							</select>
							<label class="lable">Get others argue with you</label>
							<div>
								<input id="postArgumentFBCheck" type="checkbox" name="share" value="fb"><label for="postArgumentFBCheck"></label><span>Post on Facebook</span>
								<input id="postArgumentTWCheck" type="checkbox" name="share" value="tw"><label for="postArgumentTWCheck"></label><span>Post on Twitter</span>
							</div>
							<button class="primaryButton gradient" id="postNewArgButton"><span>CREATE ARGUMENT</span></button>
						</div>
						<div class="popupContent primarytext agreeing form">
							<h3>You <span class="agreementText adToggle">agree</span> with<br/><span id="agreeModalUserName"></span>'s argument</h3>
							<label class="lable">Add a comment (optional)</label>
							<textarea rows="6" cols="31" id="agreeCommentText" class="placeholder defaultContent {defaultText : 'Type your Comment here'}"></textarea>
							<label class="lable">Get others to agree with you</label>
							<div>
								<input id="postAgreeFBCheck" type="checkbox" name="share" value="fb"><label for="postAgreeFBCheck"></label><span>Post on Facebook</span>
								<input id="postAgreeTWCheck" type="checkbox" name="share" value="tw"><label for="postAgreeTWCheck"></label><span>Post on Twitter</span>
							</div>
							<button class="primaryButton gradient" id="postAgree"><span>I AGREE</span></button>
						</div>
						<div class="popupContent primarytext disagreeing form">
							<h3>You <span class="disagreementText adToggle">disagree</span> with<br/><span id="disagreeModalUserName"></span>'s argument</h3>
							<label class="lable">Add a comment (optional)</label>
							<textarea rows="6" cols="31" id="disagreeCommentText" class="placeholder defaultContent {defaultText : 'Type your Comment here'}"></textarea>
							<label class="lable">Get others to disagree with you</label>
							<div>
								<input id="postDisagreeFBCheck" type="checkbox" name="share" value="fb"><label for="postDisagreeFBCheck"></label><span>Post on Facebook</span>
								<input id="postDisagreeTWCheck" type="checkbox" name="share" value="tw"><label for="postDisagreeTWCheck"></label><span>Post on Twitter</span>
							</div>
							<button class="primaryButton gradient" id="postDisagree"><span>I DISAGREE</span></button>
						</div>
						<div class="popupContent tbox"></div>
					</div>
				</div>
				<div id="backgroundPopup"></div>
				<div id="daMsgWrapper" class="timeagoText"><span class="msgclosex sprite-icon"></span><span id="daMsg"></span></div>
				<div id="searchWrapper" class="secondaryContainer popup">
					<div id="searchMenu">
						<span class="activeMenu heading6 {id:'searchArgumentList'}">Search Arguments</span>
						<span class="heading6 {id:'searchMemberList'}">Search Users</span>
					</div>
					<div id="searchList">
						<div id="searchArgumentList"></div>
						<div id="searchMemberList"></div>
					</div>
				</div>
				<div id="fb-root"></div>
			</div>
		</div>
	</body>
</html>