function validator() {

}

validator.prototype.validateForm = function (form) {
    var globalStatus = true;
    jQuery.each(jQuery(form).find(".validate"), function (i, e) {
        var status = window.validationEngine.validateField(e);
        if (status) {
            jQuery(e).removeClass('error');
        } else {
            jQuery(e).addClass('error');
        }
        if (jQuery(e).hasClass("confirmPass")) {
            status = window.validationEngine.validateConfirmPass(jQuery(e).val(),jQuery(e).closest("form,.form").find(".newcpasswd").val());
            if(!status){
                jQuery(e).addClass('error');
                jQuery(e).closest("form,.form").find(".newcpasswd").addClass('error');
                baseObj.Showmsg(DA.PROFILE_UPDATE_VALIDATION_FAIL,false);
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

    var validationRules = new Array();//0-required,1-minLength,2-maxLength,3-email,4-contactNumber,5-webUrl
    var status = true;
    window.validationErrors=new Array();

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
    /*if (jQuery(element).hasClass("confirmPass")) {
        validationRules.push("6");
    }*/

    var empty_string = new RegExp("^['\\s\\t\\v\\n\\r]*$", "g"); //create RegExp object for re-use for empty strig test (space,tab,newLine are evaluated)
    var email_string = new RegExp("^[\\_]*([a-z0-9]+(\\.|\\_*)?)+@([a-z][a-z0-9\\-]+(\\.|\\-*\\.))+[a-z]{2,6}$", "gim"); //create RegExp object for re-use for email address
    var webUrl_string = new RegExp("^([a-z][a-z0-9\\-]+(\\.|\\-*\\.))+[a-z]{2,6}$", "gim");//create RegExp object for re-use for phone Number with std code
    //var phone_string =   new RegExp("^\+?[\d\s]+\(?[\d\s]{10,}$", "gim");//create RegExp object for re-use for phone Number with std code

    for (var rule = 0; rule <= validationRules.length; rule++) {
        switch (validationRules[rule]) {
            case "0":
                if (empty_string.test(jQuery(element).val()) || jQuery(element).val().length == 0){
                    status = status && false;
                    window.validationErrors.push('Empty_String');
                }else{
                    status = status && true;
                }
                break;
            case "1":
                if (jQuery(element).val().length < jQuery(element).metadata().minLength) {
                    status = status && false;
                    window.validationErrors.push('minLength constriant');
                } else {
                    status = status && true;
                }
                break;
            case "2":
                if (jQuery(element).val().length > jQuery(element).metadata().maxLength) {
                    status = status && false;
                    window.validationErrors.push('maxLength constriant');
                } else {
                    status = status && true;
                }
                break;
            case "3":
                if (!email_string.test(jQuery(element).val())){
                    status = status && false;
                window.validationErrors.push('email error');
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
                if (webUrl_string.test(jQuery(element).val())){
                    status = status && false;
                    window.validationErrors.push('webUrl constraint');
                }
                else
                    status = status && true;
                break;
            case "6":
                break;
        }
    }
    return status;
};

validator.prototype.validateConfirmPass = function(pass,cpass){
    var status = true;
    if(pass != cpass){
        status = status && false;
        window.validationErrors.push('confirm pass missmatch');
    }else{
        status = status && true;
    }
    return status;
};