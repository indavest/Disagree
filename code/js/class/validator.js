function validator() {

}

validator.prototype.validateForm = function (form) {
    var globalStatus = true;
    window.isvalidatingForm = true;
    window.validationEngine.clearvalidationErrors();
    jQuery.each(jQuery(form).find(".validate"), function (i, e) {
        var status = window.validationEngine.validateField(e);
        var metaData = jQuery(e).metadata();
        if (status) {
            jQuery(e).removeClass('error');
            //adding error on referring selectBox.
            if(jQuery("#"+metaData.target)){    //target value specified
                if(jQuery("#"+metaData.target).is(":visible")){ //target select box is visible not customized so add error class to select box directly
                    jQuery("#"+metaData.target).removeClass('error');
                }else{                                          //target select box is not visible, customized so add error class to relavent custom select box
                    jQuery("#"+metaData.target+"_scrollbar_container").removeClass('error');
                }
            }else{                              //targer value not specified. no actop required

            }
        } else {
            jQuery(e).addClass('error');
            //adding error on referring selectBox.
            if(jQuery("#"+metaData.target)){    //target value specified
                if(jQuery("#"+metaData.target).is(":visible")){ //target select box is visible not customized so add error class to select box directly
                    jQuery("#"+metaData.target).addClass('error');
                }else{                                          //target select box is not visible, customized so add error class to relavent custom select box
                    jQuery("#"+metaData.target+"_scrollbar_container").addClass('error');
                }
            }else{                              //targer value not specified. no actop required

            }
        }

        globalStatus = globalStatus && status;
    });
    return globalStatus;
};

validator.prototype.validateField = function (element) {
    var val = '';
    if (jQuery(element).val() == jQuery(element).metadata().defaultText || jQuery(element).val() == '') {
        jQuery(element).val('');
    } else {
        val = jQuery(element).val();
    }

    var validationRules = new Array();//0-required,1-minLength,2-maxLength,3-email,4-contactNumber,5-webUrl,6-uname,7-confirm password,8- multiple email box,9-select box
    var status = true;
    if (!window.isvalidatingForm) {
        window.validationEngine.clearvalidationErrors();
    }

    if (jQuery(element).hasClass("required")) {
        validationRules.push("0");
    }
    if (jQuery(element).hasClass("minLength")) {
        validationRules.push("1");
    }
    if (jQuery(element).hasClass("maxLength")) {
        validationRules.push("2");
    }
    if (jQuery(element).hasClass("email")) {
        validationRules.push("3");
    }
    if (jQuery(element).hasClass("contactNumber")) {
        validationRules.push("4");
    }
    if (jQuery(element).hasClass("webUrl")) {
        validationRules.push("5");
    }
    if (jQuery(element).hasClass("uname")) {
        validationRules.push("6");
    }
    if (jQuery(element).hasClass("cpass")) {
        validationRules.push("7");
    }
    if(jQuery(element).hasClass('multiemail')){
        validationRules.push("8")
    }
    if(jQuery(element).hasClass('selectBox')){
        validationRules.push("9");
    }

    var username = /^[A-Za-z0-9_.]{3,32}$/;    //username allows alphabets + numbers + _+. and No Special Chars (!,@,#,$,%,^,&,*,(,))
    var empty_string = new RegExp("^['\\s\\t\\v\\n\\r]*$", "g");    //create RegExp object for re-use for empty strig test (space,tab,newLine are evaluated)
    var email_string = new RegExp("^[\\_]*([a-z0-9]+(\\.|\\_*)?)+@([a-z][a-z0-9\\-]+(\\.|\\-*\\.))+[a-z]{2,6}$", "gim"); //create RegExp object for re-use for email address
    var webUrl_string = new RegExp("^([a-z][a-z0-9\\-]+(\\.|\\-*\\.))+[a-z]{2,6}$", "gim");//create RegExp object for re-use for phone Number with std code
    //var phone_string =   new RegExp("^\+?[\d\s]+\(?[\d\s]{10,}$", "gim");//create RegExp object for re-use for phone Number with std code

    for (var rule = 0; rule <= validationRules.length; rule++) {
        switch (validationRules[rule]) {
            case "0":
                if (empty_string.test(jQuery(element).val()) || jQuery(element).val().length == 0) {
                    status = status && false;
                    window.validationErrors.emptystring.push(element);
                } else {
                    status = status && true;
                }
                break;
            case "1":
                if (jQuery(element).val().length < jQuery(element).metadata().minLength) {
                    status = status && false;
                    window.validationErrors.minlength.push(element);
                } else {
                    status = status && true;
                }
                break;
            case "2":
                if (jQuery(element).val().length > jQuery(element).metadata().maxLength) {
                    status = status && false;
                    window.validationErrors.maxlength.push(element);
                } else {
                    status = status && true;
                }
                break;
            case "3":
                if (!email_string.test(jQuery(element).val())) {
                    status = status && false;
                    window.validationErrors.email.push(element);
                }
                else
                    status = status && true;
                break;
            case "4":
                /*if(phone_string.test(jQuery(element).val()))
                 status = status&&false;
                 window.validationErrors.push('phone number constraint);
                 else
                 status = status&&true;*/
                break;
            case "5":
                if (webUrl_string.test(jQuery(element).val())) {
                    status = status && false;
                    window.validationErrors.weburl.push(element);
                }
                else
                    status = status && true;
                break;
            case "6":
                if (!username.test(jQuery(element).val())) {
                    status = status && false;
                    window.validationErrors.username.push(element);
                }
                else
                    status = status && true;
                break;
            case "7":
                status = window.validationEngine.validateConfirmPass(jQuery(element).val(), jQuery(element).closest("form,.form").find(".pass").val());
                if (!status) {
                    var passElement = jQuery(element).closest("form,.form").find(".pass");
                    var cpassElement = jQuery(element).closest("form,.form").find(".cpass");
                    if (jQuery(passElement).val() == '') {

                    } else {
                        jQuery(passElement).val('').addClass('error');
                        jQuery(cpassElement).val('').addClass('error');
                        window.validationErrors.confirmpass.push(jQuery(element).closest("form,.form").find(".pass"));
                        window.validationErrors.pass.push(element);
                    }
                }
                break;
            case '8':
                if (window.validationEngine.validateMultipleEmailFiled(element)) {
                    status = status && true;
                }
                else{
                    status = status && false;
                }
                break;
            case '9':
                if (jQuery(element).val() =='' || jQuery(element).val() == null || jQuery(element).val() == 0) {
                    status = status && false;
                    window.validationErrors.selectBox.push(element);
                }
                else{
                    status = status && true;
                }
        }
    }
    return status;
};

validator.prototype.validateConfirmPass = function (pass, cpass) {
    var status = (status == null || status == undefined)?true:status;
    if (pass != cpass) {
        status = status && false;
    } else {
        status = status && true;
    }
    return status;
};

validator.prototype.validateMultipleEmailFiled = function(thidObj){
    var emailstring = jQuery(thidObj).val();
    var emails = emailstring.split(',');
    var status = (status == null || status == undefined)?true:status;
    jQuery("#validateEmailData").addClass("validate required email ");
    for (var i = 0; emails!="" && i < emails.length; i++) {
        jQuery("#validateEmailData").val(emails[i]);
        if (window.validationEngine.validateField(jQuery("#validateEmailData"))) {
            status = status && true;
        } else {
            window.validationErrors.multiemail.push(emails[i]);
            status = status && false;
        }
    }
    jQuery("#validateEmailData").removeClass("validate required email ");
    /*jQuery("#validateEmailData").remove();*/
    return status;
}

validator.prototype.clearvalidationErrors = function () {
    window.validationErrors.emptystring = new Array();
    window.validationErrors.minlength = new Array();
    window.validationErrors.maxlength = new Array();
    window.validationErrors.email = new Array();
    window.validationErrors.weburl = new Array();
    window.validationErrors.username = new Array();
    window.validationErrors.confirmpass = new Array();
    window.validationErrors.pass = new Array();
    window.validationErrors.multiemail = new Array();
    window.validationErrors.selectBox = new Array();
}

validator.prototype.validationErrorFields = function () {
    window.validationErrorFields = new Array();
    return jQuery.merge(
        window.validationErrorFields,
        window.validationErrors.emptystring,
        window.validationErrors.minlength,
        window.validationErrors.maxlength,
        window.validationErrors.email,
        window.validationErrors.weburl,
        window.validationErrors.username,
        window.validationErrors.pass,
        window.validationErrors.confirmpass,
        window.validationErrors.multiemail,
        window.validationErrors.selectBox
    );
}