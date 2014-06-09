function Admin() {
    
}
Admin.prototype.initScroll = function(contentElement,scrollWrapper){
	
    /*
     added for scroll fn
     */
	
    new Control.ScrollBar(contentElement,scrollWrapper);
}
Admin.prototype.Ellipsis = function (text, length) {
    if (text == null || text == "" || text == undefined) {
        return '';
    } else {
        var maxText = length;
        if (text.length > maxText) {
            var x = new Object;
            var trimLen = parseInt(length);
            x.string1 = text.substring(0, trimLen).concat('...');
            x.string2 = text.substring(trimLen, text.length);
            return x.string1;
        } else {
            return text;
        }
    }
};
Admin.prototype.processPlaceHolder = function () {
    var metadata = null;
    jQuery.each(jQuery(".placeholder"), function (i, e) {
        if (jQuery(e).val() == '' || jQuery(e).text() == '' || jQuery(e).val() == null || jQuery(e).text() == null) {
            setPlaceholderDefaultText(jQuery(e), jQuery(e).metadata().defaultText);
        }
    });
    jQuery.fn.selectRange = function (start, end) {
        return this.each(function () {
            if (this.setSelectionRange) {
                this.focus();
                this.setSelectionRange(start, end);
            } else if (this.createTextRange) {
                var range = this.createTextRange();
                range.collapse(true);
                range.moveEnd('character', end);
                range.moveStart('character', start);
                range.select();
            }
        });
    };
    Admin.prototype.Showmsg = function (message,type) {
        switch (type){
            case false : type = 'errorNotification';break;
            case true: type = 'sucessNotification';break;
            default : type = 'messageNotification'
        }
        jQuery.jGrowl(
                        message,
                        {
                            theme:type,
                            position: 'top-right',
                            corners:'6px',
                            closer:true,
                            glue:'before',
                            life:5000
                        }
        );
    
    };
    
    jQuery(".placeholder").live('focusout', function (e) {
        metadata = jQuery(this).metadata();
        if (jQuery(this).val() == '' || jQuery(this).val() == metadata.defaultText) {
            setPlaceholderDefaultText(jQuery(this), metadata.defaultText);
        }
    });
    jQuery(".placeholder").live('mousedown', function (e) {
        metadata = jQuery(this).metadata();
        if (jQuery(this).val() == metadata.defaultText) {
            jQuery(this).selectRange(0, 0);
        }
    });
    jQuery(".placeholder").live('mouseup keyup', function (e) {
        metadata = jQuery(this).metadata();
        if (jQuery(this).val() == '' || jQuery(this).val() == metadata.defaultText) {
            setPlaceholderDefaultText(jQuery(this), metadata.defaultText);
            jQuery(this).addClass('defaultContent');
            jQuery(this).selectRange(0, 0);
        }
    });
    jQuery(".placeholder").live('keydown', function (e) {
    
        window.keyMapArray = new Array();
        metadata = jQuery(this).metadata();
        for (var numKeys = 48, numKeys2 = 96, charkeys = 65; numKeys < 58, numKeys2 < 106, charkeys < 90; numKeys++, numKeys2++, charkeys++) {
            window.keyMapArray.push(numKeys);
            window.keyMapArray.push(numKeys2);
            window.keyMapArray.push(charkeys);
        }
        if (jQuery.inArray(e.which, window.keyMapArray)) {
            if (jQuery(this).val() == '' || jQuery(this).val() == metadata.defaultText) {
                jQuery(this).val('');
                jQuery(this).removeClass('defaultContent');
            }
        } else if (e.which == 8) {
            if (jQuery(this).val() == '' || jQuery(this).val() == metadata.defaultText) {
                setPlaceholderDefaultText(jQuery(this), metadata.defaultText);
                jQuery(this).addClass('defaultContent');
                jQuery(this).selectRange(0, 0);
            }
        }
    });

    function setPlaceholderDefaultText(element, text) {
        jQuery(element).val(text);
        /*jQuery(element).text(text);*/
    }
   
};

function mysqlTimeStampToDate(timestamp) {
    //function parses mysql datetime string and returns javascript Date object
    //input has to be in this format: 2007-06-05 15:26:02
    var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
    var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
    return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
}

function getLocalTime(gmt)  {
    var min = gmt.getTime() / 1000 / 60; // convert gmt date to minutes
    var localNow = new Date().getTimezoneOffset(); // get the timezone
    // offset in minutes
    var localTime = min - localNow; // get the local time
    return new Date(localTime * 1000 * 60); // convert it into a date
}

Admin.prototype.time_difference = function (date1) {
    var difference = null;
    if (empty(date1)) {
        return "No date provided";
    }

    var periods = new Array("s", "m", "h", "day", "week", "month", "year", "decade");
    var lengths = new Array("60", "60", "24", "7", "4.35", "12", "10");

    var split1 = date1.split(' ');
    var datestr = split1[0].split('-');
    var timestr = split1[1].split(':');
    var serverdateobj = new Date(datestr[0],parseInt(datestr[1],10)-1,datestr[2],timestr[0],timestr[1],timestr[2],0);
    var serverdateobj1 = new Date(datestr[0],(parseInt(datestr[1],10)-1).toString(),datestr[2],timestr[0],(parseInt(timestr[1])-serverdateobj.getTimezoneOffset()),timestr[2],0);

    var now = time();
    var unix_date = Math.round(parseInt(serverdateobj1.getTime())/1000);

    // check validity of date
    if (empty(unix_date)) {
        return "Bad date";
    }


    // is it future date or past date
    if (now > unix_date) {
        difference = now - unix_date;

    } else {
        difference = unix_date - now;
    }
    var hourDifference = difference / (60 * 60);
    if (hourDifference < 24) {
        for (j = 0; difference >= lengths[j] && j < lengths.length - 1; j++) {
            difference /= lengths[j];
        }
        difference = round(difference);

        var dateDifference = "" + difference + "" + periods[j] + "";
    } else {
        difference = date("j M", unix_date);
        dateDifference = "" + difference + "";
    }

    return dateDifference;
};
Admin.prototype.clearForm = function () {
    jQuery("form,.form").children('input, select, textarea').val('');
    jQuery("form,.form").children('input[type=radio], input[type=checkbox]').each(function () {
        jQuery(this).attr('checked', false);
    });
    jQuery("form,.form").children('input, textarea').each(function(){
        jQuery(this).val(jQuery(this).metadata().defaultText);
    });
    jQuery("form,.form").children('input, textarea, select').each(function(){
        jQuery(this).removeClass("error");
    });
};
Admin.prototype.getCurrentPage = function () { 
	 var anchors = jQuery("#leftContainer > ul > li > a");
	 var thisPage = location.href; 
	 thisPage = thisPage.split('?')[0];
	 if(thisPage.match('\/$') && thisPage != AD.base_url){
		 thisPage = thisPage.slice(0,-1);
	 }
	if(thisPage == AD.base_url || thisPage == AD.base_url+'index.php/dashboard' || thisPage == AD.base_url+'index.php') {
		var currentanchor = jQuery("#leftContainer > ul:first-child li a");
		jQuery("#leftContainer > ul li:first-child a").attr('id','current-left-nav-link');
		return;
	}
	
	 for (var i=0; i<anchors.length; i++) {  
		 var anchor = anchors[i]; 
		 thisHREF = anchor.getAttribute("href");
		 thisHREF = thisHREF.split('?')[0];
		 if ((thisHREF == thisPage) || (location.protocol + "//" + location.hostname + thisHREF == thisPage)) { 
			 anchor.id = "current-left-nav-link";
			 return; 
		 } 
	 }  
}  
Admin.prototype.decodeEntities = function (string) { //converts &lt; and aother html entitles to equalent html
    var y = document.createElement('textarea');
    y.innerHTML = string;
    return (y.value=='')?jQuery(y).val():y.value;
}


jQuery(document).ready(function () {
	 window.adminObj = new Admin;
	 window.validationEngine = new validator();
	 window.validationErrors = new Array();
	 window.agreeVoteID = 1;
	 window.disagreeVoteID = 0;
	 window.replyID = -1;
	 window.agreeCommentID = -2;
	 window.disagreeCommentID = -3;
	 window.isLoading = false;
	 window.mouseOver = false;
	 window.argumentCommentsIsLoading = false;
	 window.commentLowerLimit = 0;
	 window.commentsPerLoad = 10;
	 window.hasMoreComments = true;

	 
	 adminObj.processPlaceHolder();
	
	jQuery("form,.form").submit(function (e) {
		e.stopPropagation();
		var status = window.validationEngine.validateForm(jQuery(this));
		return status;
	});
	jQuery(".validate").blur(function () {
		if (!window.validationEngine.validateField(jQuery(this))) {
			jQuery(this).addClass('error');
	    } else {
	    	jQuery(this).removeClass('error');
	    }
	});

	jQuery("#date-submit").live('click',function() {
		
		if(validDateFields()) {
			    var input = {};
			    input.sqlFromDate = jQuery("#alt-fromdate").val();
			    input.sqlToDate = jQuery("#alt-todate").val();
			    input.userFromdate = jQuery("#fromdatepicker").val();
				input.userTodate = jQuery("#todatepicker").val();				
			    jQuery("#args a").attr("href",AD.base_url+'index.php/argumentdashboard?fromDate='+input.sqlFromDate+'&toDate='+input.sqlToDate);
			    jQuery("#stats a").attr("href",AD.base_url+'index.php/dashboard?fromDate='+input.sqlFromDate+'&toDate='+input.sqlToDate);
			    jQuery("#users a").attr("href",AD.base_url+'index.php/dashboard/users?fromDate='+input.sqlFromDate+'&toDate='+input.sqlToDate);
			    jQuery("#spams a").attr("href",AD.base_url+'index.php/dashboard/spam?fromDate='+input.sqlFromDate+'&toDate='+input.sqlToDate);
			    window.location = jQuery("#current-left-nav-link").attr('href');
		}
		
	});
	
	
	adminObj.getCurrentPage();
	
		jQuery("#fromdatepicker").datepicker({
			altField: "#alt-fromdate",
			altFormat: "yy-mm-dd",
			maxDate:new Date(), 
			onSelect: function(selectedDate) {
				jQuery( "#todatepicker" ).datepicker("option", "minDate", selectedDate);
				
			}
		});
		jQuery("#fromdatepicker").datepicker('setDate', jQuery.datepicker.parseDate('yy-mm-dd', AD.fromDate));
		
		jQuery( "#todatepicker" ).datepicker({ 
			altField: "#alt-todate",
			altFormat: "yy-mm-dd",
			maxDate:new Date(), 
			onSelect: function(selectedDate) {
				jQuery( "#fromdatepicker" ).datepicker("option", "maxDate", selectedDate);
			}
		});
		
		jQuery( "#todatepicker" ).datepicker('setDate',jQuery.datepicker.parseDate('yy-mm-dd', AD.toDate));
		jQuery( "#todatepicker" ).datepicker("option", "minDate", jQuery.datepicker.parseDate('yy-mm-dd', AD.fromDate));
		jQuery( "#fromdatepicker" ).datepicker("option", "maxDate", jQuery.datepicker.parseDate('yy-mm-dd', AD.toDate));
});
function validDateFields() {
	if(jQuery("#todatepicker").val() == "") {
		
		jQuery("#todatepicker").addClass('error');
	} else {
		jQuery("#todatepicker").removeClass('error');
	}
	if(jQuery("#fromdatepicker").val() == "") {
		jQuery("#fromdatepicker").addClass('error');
	}
	else {
		jQuery("#fromdatepicker").removeClass('error');
	}
	
	if(jQuery("#todatepicker").hasClass('error') || jQuery("#fromdatepicker").hasClass('error')) {
		return false;
	}
	else {
		return true;
	}
}
function clearDateForm() {
	jQuery("#fromdatepicker").val('');
	jQuery("#todatepicker").val('');
	jQuery("#alt-fromdate").val('');
	jQuery("#alt-todate").val('');
}