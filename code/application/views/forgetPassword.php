<?php if ($keyObj) { ?>
        <div class="passResetPage">
    <h3> Reset your password:  <?php echo $keyObj[0]->username; ?></h3>
    <h6 class="keyData {userKey:'<?php echo $this->input->get('key');?>'}"></h6>
    <p>To verify your new password, please enter it once in each field below.</p>
    <p>Passwords are case-sensitive and must be at least 6 characters long. </p>
    <p>A good password should contain a mix of capital and lower-case letters, numbers and symbols.</p>
    <hr/>
    <br/>
    <form id="userPassResetForm" method="post" action="<?php echo base_url()?>base/forgetPassword">
        <div class="formCaption">Enter new password:</div>
        <div class="formPlaceholder">
            <input type="password" size="32" name="pass" id="userPassResetValue" class="newpasswd validate required pass minLength maxLength {minLength:6, maxLength:32, label:'Password'}"/>
        </div>
        <div class="formCaption">Re-enter new password:</div>
        <div class="formPlaceholder">
            <input type="password" size="32" name="cpass" class="newcpasswd validate required cpass minLength maxLength {minLength:6, maxLength:32, label:'Confirm password'}"/>
        </div>
        <div class="formPlaceholder">
            <input type="hidden" name="key" value="<?php echo $this->input->get('key');?>">
            <input type="submit" id="userPassResetSubmit" class="primaryButton gradient button" value="Reset Password"/>
        </div>
    </form>
    <?php } else { ?>
        <div class="passResetPage" style="width: 400px;">
        <h3>Insufficent data to process. please try again later</h3>
    <?php } ?>
</div>