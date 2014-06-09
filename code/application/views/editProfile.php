<?php if ($loggedInUserMemberFlag): ?>
<div id="editProfileContainer">
    <div class="heading">
        <h1>Edit Your Profile</h1>
    </div>
    <div id="editProfileBody">

        <div id="profilePicSection">
            <div id="profilePicImage" class="userImgCircleFull">
                <img src="<?php echo $loggedInUserMember->profilephoto;?>" alt="default user image" <?php echo ($loggedInUserMember->fromThirdParty)?"class='thirdPartyImgLarge'":"";?>/>
            </div>
            <div id="changeProfilePicWrapper">
                <form action="action/ajaxImgLoad" method="post" enctype="multipart/form-data" id="imageAjax" name="imgAjaxUpload">
                    <fieldset>
                    <input type="file" id="imgFileHolder" value="" name="uimage" accept="image/*"/>
                    <button id="changeProfilePic" class="primaryButton gradient">CHANGE PROFILE IMAGE</button>
                    </fieldset>
                </form>
            </div>
        </div>
        <form name="editProfile" id="editProfile" action="action/updateProfile" method="post">
            <fieldset>
        <div id="profileInfoSection">
            <div class="formRecord">
                <div class="formLabel lable">Name</div>
                <div class="formElement">
                    <input type="text" id="changeProfileName" name="newuserfname"
                           value="<?php echo $loggedInUserMember->fullname;?>"/>
                </div>
            </div>
            <div class="formRecord">
                <div class="formLabel lable">Location</div>
                <div class="formElement">
                    <input type="text" id="changeProfileLocation" name="newulocaion"
                           <?php echo ($loggedInUserMember->location == '' || $loggedInUserMember->location == null)?
                               'class="placeholder defaultContent {defaultText : \'Your Location\'}"':
                               'value="'. $loggedInUserMember->location .'"';?>
                    />
                    <!--<i id="geoLocation" class="sprite-icon locationSmallIconG" ></i>-->
                </div>
            </div>
            <div class="formRecord">
                <div class="formLabel lable">Date of Birth</div>
                <div class="formElement">
                    <input type="text" class="dob" name="newudob" id="dobpicker" value="<?php echo $loggedInUserMember->birthdate;?>" readonly="readonly" class="placeholder defaultContent contentSearchBox {defaultText : \'yyyy-mm-dd\'}"/>
                </div>
            </div>
            <?php if($loggedInUserMember->oauth_provider === null):?>
            <div class="formRecord">
                <div class="formLabel lable">Password</div>
                <div class="formElement">
                    <button id="initChangePW" class="primaryButton gradient" type="button">Change Password</button>
                </div>
            </div>
            <div class="formRecord" style="display: none;">
                <div class="formLabel lable">New Password</div>
                <div class="formElement">
                    <input type="password" name="newpasswd" class="newpasswd required pass minLength maxLength {minLength:6, maxLength:32, label:'Password'}">
                </div>
            </div>
            <div class="formRecord" style="display: none;">
                <div class="formLabel lable">Confirm Password</div>
                <div class="formElement">
                    <input type="password" name="newcpasswd" class="newcpasswd required cpass minLength maxLength {minLength:6, maxLength:32, label:'Confirm password'}">
                </div>
            </div>
                <?php endif?>
            <div class="formRecord">
            	<div class="formLabel lable">Interests</div>
               	<div class="formElement" id="interestsWrapper">
                    <?php foreach ($topicList as $key => $value): ?>
                           <span>
                               <input type="checkbox" name="categories[]" id="<?php echo $key;?>" value="<?php echo $key;?>" <?php echo in_array($key,$interest)?"checked='checked'":""?> />
                               <label for="<?php echo $key;?>"></label>
                               <?php echo $value;?>
                           </span>
                    <?php endforeach;?>
               </div> 
                    
                
          </div>
            
            <div class="formRecord">
                <div class="formLabel lable">Email Notifications</div>
                <div class="formElement">
                    <input type="checkbox" name="notificationReq" <?php echo ($loggedInUserMember->notifyFlag == '1')?'checked=\'checked\'':''?> id="notificationFlag" >
                    <label for="notificationFlag"></label>
                </div>
            </div>
            <div class="formRecord">
                <div class="formLabel">&nbsp;</div>
                <div class="formElement">
                    <input type="hidden" name="profilepictureEdit" id="profilepictureEdit"/>
                    <input type="submit" class="primaryButton gradient" id="updateProfile" value="Save"/>
                </div>
            </div>
        </div>
            </fieldset>
        </form>
    </div>
</div>
<?php else: ?>
<div style="text-align: center;">
    <h3>Anonymous Access Forbidden: please login to access this page</h3>
</div>
<?php endif; ?>