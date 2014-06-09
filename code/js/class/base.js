function Base() {
    /*this.width = data.width;
     this.height = data.height;
     this.content = data.content;
     this.bgcolor = data.bgcolor;
     this.content = data.content;*/
}

/*Global javascript custom functions*/
/**
 *search for a string in given array of strings and returns index's of matching string members of array
 *
 * @param stringArray
 * @param searchString
 * @return {Array} array of index's matches string of given search string
 */
Base.prototype.searchStringInArray = function (stringArray, searchString) {
    var res = new Array();
    searchString = new RegExp(searchString, 'gi');
    for (var j = 0; j < stringArray.length; j++) {
        if (stringArray[j].match(searchString)) {
            res.push(j);
        }
    }
    return res;
}

/**
 * converts Facebook DateTime string (UTC +00:00) to time_difference (like 1min, 0sec,2h, 1day, 21 Mar) according to local time
 *
 * @param dateString (Example: Thu Aug 30 10:36:33 +0000 2012)
 * @return {String}
 */
Base.prototype.time_difference_fb = function (dateString) {
    return baseObj.time_difference(dateString.substr(0,4) + '-'+ dateString.substr(5,2) + '-' + dateString.substr(8,2) + ' ' + dateString.substr(11,2) + ':' + dateString.substr(14,2) + ':' + dateString.substr(17,2));
}

/**
 * converts Twitter DateTime string (UTC +00:00) to time_difference (like 1min, 0sec,2h, 1day, 21 Mar) according to local time
 *
 * @param dateString (Example: Thu Aug 30 10:36:33 +0000 2012)
 * @return {String}
 */
Base.prototype.time_difference_tw = function (dateString) {
    dateString = new Date(dateString).toISOString();
    return baseObj.time_difference(dateString.substr(0,4) + '-'+ dateString.substr(5,2) + '-' + dateString.substr(8,2) + ' ' + dateString.substr(11,2) + ':' + dateString.substr(14,2) + ':' + dateString.substr(17,2));
}

/**
 * converts standard time string (UTC +00:00) to time_difference (like 1min, 0sec,2h, 1day, 21 Mar) according to local time
 *
 * @param date1  (Example: 2012-07-30 05:02:21)
 * @return {String}
 */
Base.prototype.time_difference = function (date1) {
    var difference = null;
    if (empty(date1)) {
        return "No date provided";
    }

    var periods = new Array("s", "m", "h", "day", "week", "month", "year", "decade");
    var lengths = new Array("60", "60", "24", "7", "4.35", "12", "10");

    var split1 = date1.split(' ');
    var datestr = split1[0].split('-');
    var timestr = split1[1].split(':');
    var serverdateobj = new Date(datestr[0], parseInt(datestr[1], 10) - 1, datestr[2], timestr[0], timestr[1], timestr[2], 0);
    var serverdateobj1 = new Date(datestr[0], (parseInt(datestr[1], 10) - 1).toString(), datestr[2], timestr[0], (parseInt(timestr[1]) - serverdateobj.getTimezoneOffset()), timestr[2], 0);

    var now = time();
    var unix_date = Math.round(parseInt(serverdateobj1.getTime()) / 1000);

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

/**
 * return time difference in seconds from given server time string to local time
 *
 * @param date1 {String}    - server time string (ideally GMT+00:00)
 * @return Senconds{Number} - difference between given time and now in no.of seconds
 */
Base.prototype.time_difference_seconds = function (date1){
        var difference = null;
        if (empty(date1)) {
            return "No date provided";
        }

        var periods = new Array("s", "m", "h", "day", "week", "month", "year", "decade");
        var lengths = new Array("60", "60", "24", "7", "4.35", "12", "10");

        var split1 = date1.split(' ');
        var datestr = split1[0].split('-');
        var timestr = split1[1].split(':');
        var serverdateobj = new Date(datestr[0], parseInt(datestr[1], 10) - 1, datestr[2], timestr[0], timestr[1], timestr[2], 0);
        var serverdateobj1 = new Date(datestr[0], (parseInt(datestr[1], 10) - 1).toString(), datestr[2], timestr[0], (parseInt(timestr[1]) - serverdateobj.getTimezoneOffset()), timestr[2], 0);

        var now = time();
        var unix_date = Math.round(parseInt(serverdateobj1.getTime()) / 1000);

        var currDateTimeObj = new Date();
        var curr_timestamp = (currDateTimeObj.getTime()- currDateTimeObj.getTimezoneOffset())/1000;
        var time_difference_in_sec = Math.round(curr_timestamp - unix_date);

        return time_difference_in_sec;
    }

/**
 * Syncs time intervals per each minute
 */
Base.prototype.time_String_Sync = function(){
    jQuery(".timeStringSync").each(function(i,e){
       jQuery(e).text(baseObj.time_difference(jQuery(e).metadata().timestring));
    });
}

/**
 * removes all html tags, entity codes from given string.
 *
 * @param string
 * @return string
 */
Base.prototype.cleanHTMLEntityString = function (string) {
    string = baseObj.decodeEntities(string);                //convert entity codes string to html string
    var regHTMlEntityCodes = new RegExp("&[^\s]*;", "gim"); //clean entity codes if any
    string = string.replace(regHTMlEntityCodes, "");
    var regHTMLTags = new RegExp("(<([^>]+)>)", "gim");    //clean html tags with attributes and values
    string = string.replace(regHTMLTags, "");
    return string;
}

/**
 * converts html entitles (&lt;, &gt;, &nbsp;) to equalent html
 *
 * @param string
 * @return string  html string
 */
Base.prototype.decodeEntities = function (string) { //converts &lt; and other html entitles to equalent html
    var y = document.createElement('textarea');
    var res = '';
    y.innerHTML = string;
    if (y.childNodes.length > 0) {
        if (y.childNodes[0].nodeValue == null) {      //works with all browsers except safari
            for (var count = 0; count < y.childNodes.length; count++) {
                res += y.childNodes[count].outerHTML;
            }
        } else {                                      // works with safari (mac)
            for (var count = 0; count < y.childNodes.length; count++) {
                res += y.childNodes[count].nodeValue;
            }
        }
    } else {
        res = '';
    }
    return res;
}

/**
 * replaces newline feeds(\n) to break tag in html(<br/>).
 *
 * @param string
 * @return string
 */
Base.prototype.nl2br = function (string) {        //replaces newline feeds(\n) to break tag in html(<br/>). textarea to p
    var regnl2br = new RegExp("\\r\\n|\\n", "g");
    string = string.replace(regnl2br, "<br/>");
    return string;
}

/**
 * returns given element is
 * @param element
 * @return {Boolean}
 */
Base.prototype.is_empty_String = function (element) {
    return (element == '' || element == "" || element == undefined || element == null);
}

/**
 * ellipsises given string to given length and adds ... at end of string
 *
 * @param text
 * @param length
 * @return {string}
 */
Base.prototype.Ellipsis = function (text, length) {
    var textElement = document.createElement('span');
    textElement.title = text;
    if (text == null || text == "" || text == undefined) {
        textElement.innerHTML = '';
    } else {
        var maxText = length;
        if (text.length > maxText) {
            var x = new Object;
            var trimLen = parseInt(length);
            x.string1 = text.substring(0, trimLen).concat('...');
            x.string2 = text.substring(trimLen, text.length);
            textElement.innerHTML = x.string1;
        } else {
            textElement.innerHTML = text;
        }
    }
    return textElement.outerHTML;
};

/**
 * scroll the page to specified position refreced from specified htmlelement
 *
 * @param element {HTMLElement} - reference HTMLElement
 * @param newPostIntVal {int}   - no of pixels to scroll from referende element
 * @return null
 */
Base.prototype.scrollPageTo = function (element, newPostIntVal) {
    element = (element) ? element : jQuery('html, body');
    newPostIntVal = (newPostIntVal) ? newPostIntVal : 0;
    jQuery(element).animate({scrollTop:newPostIntVal + 'px'}, 500);
}

/******placeholder functionality goes here*********/
/**
 * placeholder functionality of modern browsers
 *
 * changes text opacity of text if it is default text for distinguition of user text from default text
 *
 * @param element | NULL     jQuery Object to preprocess placeholder
 */
Base.prototype.processPlaceHolder = function (element) {
    var metadata = null;
    //detecting aloowed keyboard keys
    window.keyMapArray = new Array();
    for (var numKeys = 48, numKeys2 = 96, charkeys = 65; numKeys < 58, numKeys2 < 106, charkeys < 90; numKeys++, numKeys2++, charkeys++) {
        window.keyMapArray.push(numKeys);
        window.keyMapArray.push(numKeys2);
        window.keyMapArray.push(charkeys);
    }

    //initialization script
    if (element == null || element == '') {           //placeholder called globally to process all elements
        jQuery.each(jQuery(".placeholder"), function (i, e) {
            if (jQuery(e).val() == '' || jQuery(e).text() == '' || jQuery(e).val() == null || jQuery(e).text() == null) {
                setPlaceholderDefaultText(jQuery(e), jQuery(e).metadata().defaultText);
            } else {

            }
        });
    } else {                                          // placeholder called with an element scope
        if (jQuery(element).val() == '' || jQuery(element).text() == '' || jQuery(element).val() == null || jQuery(element).text() == null) {
            setPlaceholderDefaultText(jQuery(element), jQuery(element).metadata().defaultText);
        } else {

        }
    }

    //move cursor to start of element
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

    //on blur (mouse out)
    jQuery(".placeholder").live('blur', function (e) {
        metadata = jQuery(e.target).metadata();
        if (jQuery(this).val() == '' || jQuery(e.target).val() == metadata.defaultText) {
            setPlaceholderDefaultText(jQuery(e.target), metadata.defaultText);
            jQuery(this).css({'color':'#A8A096'});
        } else {

        }
    });

    //on keydown(key clicked in field)
    jQuery(".placeholder").live('keydown', function (e) {
        metadata = jQuery(this).metadata();
        if (jQuery.inArray(e.which, window.keyMapArray)) {                                  // some number / charector key pressed
            if (jQuery(this).val() == '' || jQuery(this).val() == metadata.defaultText) {   //text box is emapty
                jQuery(this).val('');
                jQuery(this).removeClass('defaultContent');
                jQuery(this).css({'color':'#A8A096'});
            }
        } else if (e.which == 8) {                                                          //esc key pressed
            if (jQuery(this).val() == '' || jQuery(this).val() == metadata.defaultText) {   //text box is emapty
                setPlaceholderDefaultText(jQuery(this), metadata.defaultText);
                jQuery(this).addClass('defaultContent');
                jQuery(this).selectRange(0, 0);
                jQuery(this).css({'color':'rgba(168, 160, 150, 0.3)'});
            } else {

            }
        } else {                                                                              //neither known chars / numbers nor esc key pressed

        }
    });

    //on mouseup / keyup (mouse click release or key button release in field)
    jQuery(".placeholder").live('mouseup keyup', function (e) {
        metadata = jQuery(this).metadata();
        if (jQuery(this).val() == '' || jQuery(this).val() == metadata.defaultText) {
            setPlaceholderDefaultText(jQuery(this), metadata.defaultText);
            jQuery(this).css({'color':'rgba(168, 160, 150, 0.3)'});
            jQuery(this).addClass('defaultContent');
            jQuery(this).selectRange(0, 0);
        } else {
            jQuery(this).css({'color':'#A8A096'});
        }
    });

    //on mousedown (mouse clicked in field)
    jQuery(".placeholder").live('mousedown', function (e) {
        metadata = jQuery(this).metadata();
        if (jQuery(this).val() == metadata.defaultText) {
            jQuery(this).selectRange(0, 0);
            jQuery(this).css({'color':'rgba(168, 160, 150, 0.3)'});
        } else {
            jQuery(this).css({'color':'#A8A096'});
        }
    });

    //set default text into textbox
    function setPlaceholderDefaultText(element, text) {
        jQuery(element).val(text);
    }
};

/*custom scroll bar*/
/**
 * adds a custom scrollbar scrollWrapper to give contentElement
 *
 * @param {String} contentElement id of html contentElement
 * @param {String} scrollWrapper  id of html scroll wrapper
 */
Base.prototype.initScroll = function (contentElement, scrollWrapper) {
    /*
     added for scroll fn
     */
    new Control.ScrollBar(contentElement, scrollWrapper);
}

/**
 * returns object of parameters and values from current page url
 *
 * @return {Object}
 */
Base.prototype.getUrlVars = function () {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

/**
 * check's whether given string starts with given another string
 *
 * @param str
 * @return {Boolean}
 */
String.prototype.startsWith = function (str) {
    return (this.match("^" + str) == str)
}

/**
 * check's whether given string ends with given another string
 *
 * @param str
 * @return {Boolean}
 */
String.prototype.endsWith = function (str) {
    return (this.match(str + "$") == str)
}

/**
 * trims leding and trailing slashes from a given string
 * @return {String}
 */
String.prototype.trim = function () {
    return (this.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
}

/**
 * equalsIgnoreCase method of the String object
 *
 * @param strTerm
 * @param strToSearch
 * @return {Boolean}
 */
String.prototype.equalsIgnoreCase = function MatchIgnoreCase(strToSearch) {
    strToSearch = strToSearch.toLowerCase();
    var strTerm = this.toLowerCase();
    if (strToSearch == strTerm) {
        return true;
    } else {
        return false;
    }
}

/**
 * Parse the string and replace url with clickable html tags
 * parses all protocols http / https / ftp
 * parses with / out schema
 * Parses url with port numbers
 * Parses youtube / vimeo links as well
 * @return {String}
 */
String.prototype.addSchema = function () {
    return this.replace(/^(?!(?:http|https|ftp):\/\/)/i, 'http://');
    /*return this.replace(/(?!(http|ftp|https|ftps):\/\/)^[a-z0-9-]+(\.[a-z0-9-]+)+([\/?].*)?/gim,'http://$&')*/
}
String.prototype.linkify = function () {
    var url_pattern = /(\()((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&'()*+,;=:\/?#[\]@%]+)(\))|(\[)((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&'()*+,;=:\/?#[\]@%]+)(\])|(\{)((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&'()*+,;=:\/?#[\]@%]+)(\})|(<|&(?:lt|#60|#x3c);)((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&'()*+,;=:\/?#[\]@%]+)(>|&(?:gt|#62|#x3e);)|((?:^|[^=\s'"\]])\s*['"]?|[^=\s]\s+)(\b(?:ht|f)tps?:\/\/[a-z0-9\-._~!$'()*+,;=:\/?#[\]@%]+(?:(?!&(?:gt|#0*62|#x0*3e);|&(?:amp|apos|quot|#0*3[49]|#x0*2[27]);[.!&',:?;]?(?:[^a-z0-9\-._~!$&'()*+,;=:\/?#[\]@%]|$))&[a-z0-9\-._~!$'()*+,;=:\/?#[\]@%]*)*[a-z0-9\-_~$()*+=\/#[\]@%])/img;
    var url_replace = '$1$4$7$10$13<a href="$2$5$8$11$14" class="linkRegular" target="_blank">$2$5$8$11$14</a>$3$6$9$12';
    return this.replace(url_pattern, url_replace);
}

/**
 * on resize of window set overflow of webpage visible / hidden
 */
function resizeWindowCallback() {
    if (jQuery(window).width() <= 860) {
        jQuery("body").css({"overflow-x":"visible"});
    } else {
        jQuery("body").css({"overflow-x":"hidden"});
    }
}
/**
 * actions to be performe before opening dialogbox
 * if logged in user prepare select dropdown on start argument
 * if not loggdin user prepare checkbox on create account
 */
function preModelDialogInit() {
    if (window.loggedInUserMember) {
        if (window.isSelectBoxStyled) {
            //already select drop down styled. no action required
        } else {
            window.isSelectBoxStyled = true;
            jQuery("#TopicSelector").customSelectBox().prepare();
        }
    } else {
        if (window.isCheckboxStyled) {
            //already checkbox styled. no action required
        } else {
            window.isCheckboxStyled = true;
            jQuery('#itoggle #genderSelect').iToggle({
                easing: 'easeInSine',
                onClickOn: function(){
                    jQuery("#newGender").val('M');
                },
                onClickOff: function(){
                    jQuery("#newGender").val('F');
                }
            });
        }
    }
}

/*custom select box*/
/**
 * create custome select box.
 *
 * usage:
 *
 * <select id="selectId">
 *     <option value="0">--default select option like select a topic--</option>
 *     <option value="153835">option1</option>
 *     <option value="153835">option1</option>
 *     <option value="153835">option1</option>
 *     <option value="153835">option1</option>
 *     <option value="153835">option1</option>
 * </select>
 * <input name="topic" id="selectIdSelectedValue" type="hidden" class="selectedValue"/>
 *
 * <scrit type="text/javascript">
 *      jQuery(function () {
 *          jQuery("#TestSelector").customSelectBox().prepare();    //simple select box without default select (selected first automatically)
 *          jQuery("#TestSelector").customSelectBox().prepare({"defaultOption":1319449965}); //select box with default select (option will be selected based on value / test in option element)
 *      });
 * </script>
 *
 * failes when initialized twice on same element
 * @return {Object}
 */
jQuery.fn.customSelectBox = function () {
    var thisObj = jQuery(this);
    var _constants = {
        default:{}
    }
    var _variables = {
        options:{},
        jElement:undefined,
        jCustomElement:undefined,
        jCustomElementScrollbar:undefined
    }
    var _private = {

        init:function () {
            jQuery(_variables.jElement).hide();
            _private.cloneSelectBox(_variables.jElement);
            jQuery(_variables.jElement).before(_variables.jCustomElement);
            _private.addScrollBar();
            jQuery(_variables.jCustomElement).find('.cSelectBoxHandle').bind('click', _private.clickAction);
            jQuery(_variables.jCustomElement).find('.cSelectOption').bind('click', _private.clickAction);
            jQuery(_variables.jCustomElement).bind('mouseover', _private.mouseOverAction);
            jQuery(_variables.jCustomElement).bind('mouseleave', _private.mouseLeaveAction);
        },
        /**
         * clones a select box element to ul li custom element structures
         * @param jElement
         * @return {HTMLUListElement}
         */
        cloneSelectBox:function (jElement) {
            var objId = jQuery(jElement).attr('id');                    //getting id of element
            var valueSet = new Array(),
                optionSet = new Array();
            jQuery.each(jQuery(jElement).children(), function (i, e) {
                valueSet.push(e.value);
                optionSet.push(e.text);
            });
            _variables.jCustomElement = jQuery("<ul><div class='cSelectBoxHandle'></div></ul>");
            jQuery(_variables.jCustomElement).attr("id", objId + '_scrollbar_content').addClass('cscrollbar_content secondaryContainer');
            jQuery.each(valueSet, function (i, e) {
                var liObj = jQuery("<li></li>");
                jQuery(liObj).attr('data-value', e).addClass('cSelectOption').text(optionSet[i]);
                jQuery(_variables.jCustomElement).append(liObj);
            });
            _private.setDefaultSelectBox();
            return _variables.jCustomElement;
        },
        /**
         *
         */
        addScrollBar:function () {
            var elementId = jQuery(_variables.jElement).attr('id');

            jQuery(_variables.jCustomElement).wrapAll('<div id="' + elementId + '_scrollbar_container_wrapper" class="cscrollbar_container_wrapper"/>');
            jQuery(_variables.jCustomElement).wrapAll('<div id="' + elementId + '_scrollbar_container" class="cscrollbar_container"/>');
            jQuery(_variables.jCustomElement).before('<div id="' + elementId + '_scrollbar_track" class="cscrollbar_track"><div id="' + elementId + '_scrollbar_handle" class="cscrollbar_handle"></div></div>');

            var selectedIndex = jQuery(_variables.jCustomElement).children(".cDefaultSelectedOption").show().index();
            var scrollToValue = (selectedIndex - 1) * 30;
            _variables.jCustomElementScrollbar = new Control.ScrollBar(elementId + '_scrollbar_content', elementId + '_scrollbar_track');
            _variables.jCustomElementScrollbar.scrollBy(scrollToValue);
            jQuery(_variables.jCustomElement).css('height', '30px');
        },
        /**
         *
         * @param e
         */
        clickAction:function (e) {
            var selectedElement = jQuery(e.target),
                selectElementContent = jQuery(selectedElement).closest('.cscrollbar_content'),
                selectElementContainer = jQuery(selectedElement).closest(".cscrollbar_container");
            if (jQuery(selectElementContent).hasClass('activeMenu')) {          //already select menu opened - closing dropdown
                _private.closeSelectBox(selectedElement, selectElementContent, selectElementContainer);
            } else {
                _private.openSelectBox(selectedElement, selectElementContent, selectElementContainer);
            }

        },
        /**
         *
         * @param e
         */
        mouseOverAction:function (e) {
        },
        /**
         *
         * @param e
         */
        mouseLeaveAction:function (e) {
        },
        /**
         *
         * @param selectedElement
         * @param selectElementContent
         * @param selectElementContainer
         */
        openSelectBox:function (selectedElement, selectElementContent, selectElementContainer) {
            jQuery(selectElementContent).find(".cSelectBoxHandle").hide();
            jQuery(selectElementContent).addClass('activeMenu');
            jQuery(selectedElement).siblings(".cSelectOption").show();
            jQuery(selectElementContainer).css({'height':'90px'});
            jQuery(_variables.jCustomElement).css('height', '90px');
            _variables.jCustomElementScrollbar.recalculateLayout();
            var selectedIndex = jQuery(selectElementContent).children(".cDefaultSelectedOption").show().index();
            var scrollToValue = (selectedIndex - 2) * 30;
            _variables.jCustomElementScrollbar.scrollBy(scrollToValue);
            if (selectedIndex > 1) {
                jQuery(selectElementContainer).css({'margin-top':'-30px'});
            }
        },
        /**
         *
         * @param selectedElement
         * @param selectElementContent
         * @param selectElementContainer
         */
        closeSelectBox:function (selectedElement, selectElementContent, selectElementContainer) {
            _private.setSelectedOption(jQuery(selectedElement).attr("data-value"));
            jQuery(selectElementContent).find(".cSelectBoxHandle").show();
            jQuery(selectElementContent).removeClass('activeMenu');
            jQuery(selectedElement).addClass('cDefaultSelectedOption').show().siblings(".cSelectOption").hide();
            jQuery(selectedElement).siblings('.cDefaultSelectedOption').removeClass('cDefaultSelectedOption');
            jQuery(selectElementContainer).css({'height':'30px'});
            jQuery(_variables.jCustomElement).css('height', '30px');
            jQuery(selectElementContainer).css({'margin-top':'0px'});
            _variables.jCustomElementScrollbar.recalculateLayout();
        },
        /**
         *
         */
        setDefaultSelectBox:function () {
            var selectedOptionIndex = (jQuery(_variables.jElement).children(":selected").index() == 0) ? 0 : jQuery(_variables.jElement).children(":selected").index();
            var selectedOptionValue = (jQuery(_variables.jElement).children(":selected").index() == 0) ? 0 : jQuery(_variables.jElement).children(":selected").attr('value');
            jQuery(_variables.jCustomElement).children("li").eq(selectedOptionIndex).addClass('cDefaultSelectedOption').siblings('.cDefaultSelectedOption').removeClass('cDefaultSelectedOption');
            _private.setSelectedOption(selectedOptionValue);
        },
        /**
         *
         * @param jCustomElement
         */
        removeDefaultSelectBox:function () {
            jQuery(_variables.jCustomElement).children("li").eq(0).addClass('cDefaultSelectedOption').siblings('.cDefaultSelectedOption').removeClass('cDefaultSelectedOption');
            _private.setSelectedOption(jQuery(_variables.jElement).children("option").eq(0).attr('value'));
        },
        /**
         *
         * @param index
         */
        setSelectedOption:function (index) {
            var prevSelectedOption = jQuery(_variables.jElement).children("option[selected='selected']");
            jQuery(prevSelectedOption).prop("selected", false).removeAttr('selected');
            if (index == '') {        //if index is empty , then select first default option
                jQuery(_variables.jCustomElement).children(".cSelectOption").eq(0).show().addClass("cDefaultSelectedOption").siblings(".cDefaultSelectedOption").removeClass("cDefaultSelectedOption").hide();
                jQuery(_variables.jElement).children("option[value='']").attr("selected", 'selected');
                jQuery(_variables.jElement).siblings("#" + jQuery(_variables.jElement).attr('id') + "SelectedValue").val('');
            } else if (isNaN(index)) { //not a number so set by value - Eg: Sports
                jQuery(_variables.jCustomElement).children().contains(index).show().addClass("cDefaultSelectedOption").siblings(".cDefaultSelectedOption").removeClass("cDefaultSelectedOption").hide();
                jQuery(_variables.jElement).children("option[value='" + jQuery(index).attr('data-value') + "']").attr("selected", 'selected');
                jQuery(_variables.jElement).siblings("#" + jQuery(_variables.jElement).attr('id') + "SelectedValue").val(window.topicIdArray[window.topicArray.indexOf(index)]);
            } else {                  //a number so set by integer index - Eg: 1319449212
                var selection = '[data-value="' + index + '"]';
                var obj = jQuery(_variables.jCustomElement).children(selection);
                jQuery(obj).show().addClass("cDefaultSelectedOption").siblings(".cDefaultSelectedOption").removeClass("cDefaultSelectedOption").hide();
                jQuery(_variables.jElement).children("option[value='" + index + "']").attr("selected", 'selected');
                jQuery(_variables.jElement).siblings("#" + jQuery(_variables.jElement).attr('id') + "SelectedValue").val(index);
            }
        }
    }
    return {
        /**
         * preares a select box. use this function to make a normal select box to custom select box
         * options: an array with user specific options.
         *          as of now only supported option is :
         *          defaultOption : "value / text of any option".
         *
         * @param options {Array}
         */
        prepare:function (options) {
            _variables.jElement = thisObj;
            _variables.options = options;
            _private.init();
            if (options != undefined) {
                _private.setSelectedOption((options.defaultOption == undefined) ? 0 : options.defaultOption);
            }
        },
        /**
         * makes an element selected by default other than first element
         * @param option {String}   text / value of an option
         */
        setSelection:function (option) {
            _variables.jElement = thisObj;
            _variables.jCustomElement = jQuery("#" + jQuery(thisObj).attr('id') + '_scrollbar_content')
            _variables.options = option;
            _private.setSelectedOption(option);
        },
        /**
         * reset select box and clears user selection an their values.
         */
        resetSelectBox:function () {
            _variables.jElement = thisObj;
            _variables.jCustomElement = jQuery("#" + jQuery(thisObj).attr('id') + '_scrollbar_content')
            _private.removeDefaultSelectBox();
        },
        /**
         *
         */
        close:function () {

        }
    }

}

/*Disagree.me site specific common functionalities*/
Base.prototype.OpenModel = function (element) {
    jQuery('#backgroundPopup').css('opacity', '0.4');
    jQuery('#popupWrapper').fadeIn('slow');
    jQuery("#popupContainer").children(".popupContent").hide();
    preModelDialogInit();
    element.show();
    jQuery('#backgroundPopup').fadeIn('slow');
    jQuery("#popupWrapper").css({'width':document.documentElement.clientWidth, 'height':document.documentElement.clientHeight, 'position':"fixed", "top":0, "left":0});
    jQuery("#popupContainer").css({'position':"absolute", "top":((document.documentElement.clientHeight / 2) - (jQuery("#popupContainer").outerHeight(true) / 2)), "left":((document.documentElement.clientWidth / 2) - (jQuery("#popupContainer").outerWidth(true) / 2))});
};

Base.prototype.CloseModel = function () {
    window.baseObj.clearForm();
    jQuery('#backgroundPopup').hide();
    jQuery('#popupWrapper').hide();
    jQuery('#postNewArgButton').show().siblings('#updateArgButton').hide();
    jQuery("#userVoteOnThirdPartyPost").remove();       //removes extra tab added for facebok / twitter feedpost
    if (window.loggedInUserMember) {
        jQuery("#TopicSelector").customSelectBox().resetSelectBox();
    }
    jQuery(".agreeing>h3").html("You <span class='agreementText adToggle'>agree</span> with<br/><span id='agreeModalUserName'></span>'s argument");
    jQuery(".agreeing>h3").next("label").text("Add a comment (optional)");
    jQuery(".disagreeing>h3").html("You <span class='disagreementText adToggle'>disagree</span> with<br/><span id='disagreeModalUserName'></span>'s argument");
    jQuery(".disagreeing>h3").next("label").text("Add a comment (optional)");
    jQuery(".tbox").html('');
};

Base.prototype.clearForm = function () {
    jQuery("form,.form").find('input[type=radio], input[type=checkbox]').each(function (i, e) {
        jQuery(e).attr('checked', false);
    });
    jQuery("#popupContainer").find("select,input[type='text'],input[type='password'],textarea").each(function (i, e) {
        jQuery(e).removeClass('error');
        jQuery(e).val('');
        jQuery(e).trigger('click').trigger('focusout');
    });
};

/**
 *
 * @param message
 * @param type = true / false
 * @constructor
 */
Base.prototype.Showmsg = function (message, type) {
    switch (type) {
        case false :
            type = 'errorNotification';
            break;
        case true:
            type = 'sucessNotification';
            break;
        default :
            type = 'messageNotification'
    }
    jQuery.jGrowl(
        message,
        {
            theme:type,
            position:'top-right',
            corners:'6px',
            closer:true,
            glue:'before',
            life:5000
        }
    );
    /*jQuery("#messageContainer>.daMessage").html(message);
     var messgeWidth = jQuery("#messageContainer").outerWidth(true) / 2;
     var windowHalfWidth = document.documentElement.clientWidth / 2;
     jQuery("#messageContainer").css("left", (windowHalfWidth - messgeWidth) + "px");
     jQuery("#messageContainer").slideDown();*/
};

Base.prototype.Hidemsg = function () {
    jQuery("#daMsgWrapper").show().animate({'right':'-100px'}, 400, 'swing');
};

Base.prototype.GenerateProfileTip = function (tipObj, e) {
    jQuery(tipObj).parent().css({'position':'relative'});
    var link = jQuery(tipObj).attr('href');
    var memberId = link.substring(link.indexOf('=') + 1);
    var tipHtmlContent = ''; // fill this variable with dynamic content
    jQuery.ajax({
        url:DA.base_url + 'action/getUserProfile',
        dataType:'json',
        type:'post',
        data:{id:memberId},
        beforeSend:function (i, e) {
            if (window.isProfileTipLoading) { //check is der any call for profile tip is going
                return false;
            } else {
                window.isProfileTipLoading = true;
                return true;
            }
        },
        success:function (result) {
            var userMemberData = result.data;
            tipHtmlContent = loadProfileTip(userMemberData);
            var DAtoolTipDownTemplate = '<div class="tipContent obtuseGradient">' + tipHtmlContent + '</div><i class="tipPointer"></i>';
            var DAtoolTipUpTemplate = '<i class="tipPointer"></i><div class="tipContent obtuseGradient">' + tipHtmlContent + '</div>';
            var DAtoolTipLeftTemplate = '<i class="tipPointer"></i><div class="tipContent obtuseGradient">' + tipHtmlContent + '</div>';
            var DAtoolTipRightTemplate = '<div class="tipContent obtuseGradient">' + tipHtmlContent + '</div><i class="tipPointer"></i>';
            var tipClass = '', tipTemplate = '', leftOffset = 'auto', rightOffset = 'auto', topOffset = 'auto', bottomOffset = 'auto';
            var offset = jQuery(tipObj).css("line-height");

            if (jQuery(tipObj).hasClass('left')) {
                tipClass = 'DAtoolTipLeft';
                tipTemplate = 'DAtoolTipLeftTemplate';
                leftOffset = jQuery(tipObj).width() + "px";
                topOffset = '-55px';
            } else if (jQuery(tipObj).hasClass('right')) {
                tipClass = 'DAtoolTipRight';
                tipTemplate = 'DAtoolTipRightTemplate';
                rightOffset = jQuery(tipObj).width() + "px";
                topOffset = '-55px';
            } else if (jQuery(tipObj).hasClass('down')) {
                tipClass = 'DAtoolTipDown';
                tipTemplate = 'DAtoolTipDownTemplate';
                bottomOffset = offset;
            } else if (jQuery(tipObj).hasClass('up')) {
                tipClass = 'DAtoolTipUp';
                tipTemplate = 'DAtoolTipUpTemplate';
                topOffset = offset;
                leftOffset = '-35px';
            }

            if (!(jQuery(tipObj).next("." + tipClass).length)) {
                jQuery(tipObj).after('<div class="' + tipClass + '"> ' + eval(tipTemplate) + '</div>');
                jQuery(tipObj).next("." + tipClass).css({'left':leftOffset, 'right':rightOffset, 'top':topOffset, 'bottom':bottomOffset});
            }
        },
        complete:function () {
            window.isProfileTipLoading = false;
        }
    });
};

Base.prototype.HideTip = function () {
    if (!jQuery('.DAtoolTipUp').is(':hover')) {
        jQuery(".DAtoolTipDown,.DAtoolTipUp,.DAtoolTipLeft,.DAtoolTipRight").remove();
        jQuery(".DAtip").css({'position':''});
    }
};

Base.prototype.CalculatePercentage = function (input) {
    var ul , ll = 0;
    var result = 0;

    for (var n = 0; n < map.length; n++) {
        ll = map[n];
        ul = map[n + 1];

        if (input > ll && input < ul) {
            if ((input - ll) > (ul - input)) {
                result = ul;
            }
            else {
                result = ll;
            }
            break;
        } else if (input == ll || input == ul) {
            result = input;
            break;
        }
    }

    return result;
};

Base.prototype.CreateArgument = function (input) {
    var thisObj = this;
    input.fbFlag = (jQuery("#postArgumentFBCheck").attr('checked')) ? true : false;
    input.twFlag = (jQuery("#postArgumentTWCheck").attr('checked')) ? true : false;
    /*clean content from html tags*/
    input.argumentTitle = baseObj.cleanHTMLEntityString(input.argumentTitle);
    input.argumentDesc = baseObj.cleanHTMLEntityString(input.argumentDesc);
    if (input.memberId != "") {
        jQuery.ajax({
            url:DA.base_url + "action/argumentCreate",
            dataType:'json',
            type:'post',
            data:input,
            beforeSend:function () {
                if (jQuery("#userVoteOnPost").length > 0) {
                    var uservote = jQuery("#userVoteOnPost").attr('checked');
                    userVote = jQuery("#userVoteOnPost").length > 0 ? (jQuery("#userVoteOnPost").attr('checked') == 'checked') : true;
                    var status = ( window.validationEngine.validateForm(jQuery("#postNewArgButton").closest(".form")) && userVote);
                    if (!status) {
                        baseObj.Showmsg('You should Agree / Disagree with this argument.', false)
                        jQuery("#userVoteButtonWrapper").addClass('error');
                    }
                    var userSelectedTopic = jQuery("#userSelectedTopic").attr('checked');
                    userSelectedTopic = jQuery("#userSelectedTopic").length > 0 ? (jQuery("#userSelectedTopic").attr('checked') == 'checked') : true;
                    var status = ( window.validationEngine.validateForm(jQuery("#postNewArgButton").closest(".form")) && userSelectedTopic);
                    if (!status) {
                        baseObj.Showmsg('Please select a suitable topic for your argument.', false)
                        jQuery("#TopicSelector").addClass('error');
                    }
                    return status;
                } else {
                    if (!window.validationEngine.validateForm(jQuery("#postNewArgButton").closest(".form"))) {
                        baseObj.Showmsg(errorProcessing(), false);
                        return false;
                    } else {
                        return true;
                    }
                }


            },
            success:function (result) {
                if (result.response) {
                    thisObj.CloseModel();
                    var argument = result.data;
                    window.location = DA.base_url + "detail?id=" + argument.id + ((input.fbFlag)?"&fbshare=true":'')+ ((input.twFlag)?'&twshare=true':'');
                    /*if (input.fbFlag) {
                        window.location = DA.base_url + "detail?id=" + argument.id + "&share=fb";
                    } else if (input.twFlag) {
                        window.location = DA.base_url + "detail?id=" + argument.id + "&share=tw";
                    } else {
                        window.location = DA.base_url + "detail?id=" + argument.id;
                    }*/
                    //createArgumentCallBack(result, input);
                } else {
                }
            },
            complete:function () {        //removes extra tab added for facebok / twitter feedpost
                jQuery("#userVoteOnThirdPartyPost").remove();
            }
        });
    } else {
        alert("Please login to Create an Argument");
    }
};

Base.prototype.updateArgument = function (input) {
    var thisObj = this;
    input.fbFlag = (jQuery("#postArgumentFBCheck").attr('checked')) ? true : false;
    input.twFlag = (jQuery("#postArgumentTWCheck").attr('checked')) ? true : false;
    /*clean content from html tags*/
    input.argumentTitle = baseObj.cleanHTMLEntityString(input.argumentTitle);
    input.argumentDesc = baseObj.cleanHTMLEntityString(input.argumentDesc);
    if (input.memberId != "") {
        jQuery.ajax({
            url:DA.base_url + "action/argumentUpdate",
            dataType:'json',
            type:'post',
            data:input,
            beforeSend:function () {
                if (!window.validationEngine.validateForm(jQuery("#postNewArgButton").closest(".form"))) {
                    baseObj.Showmsg(errorProcessing(), false);
                    return false;
                } else {
                    return true;
                }
            },
            success:function (result) {
                if (result.response) {
                    thisObj.CloseModel();
                    var argument = result.data;
                    updateArgumentCallBack(result, input);
                } else {
                }
            },
            complete:function () {        //removes extra tab added for facebok / twitter feedpost
                jQuery("#userVoteOnThirdPartyPost").remove();
            }
        });
    } else {
        alert("Please login to Create an Argument");
    }
};

Base.prototype.Follow = function (input) {
    jQuery.ajax({
        url:DA.base_url + 'action/followMember',
        data:{followMemberId:input.followMemberId, memberId:input.memberId},
        type:'post',
        dataType:'json',
        async:false,
        success:function (result) {
            if (result.response) {
                memberFollowCallBack(result, input);
            }
        }
    });
};

Base.prototype.Unfollow = function (input) {
    jQuery.ajax({
        url:DA.base_url + 'action/unfollowMember',
        data:{followMemberId:input.followMemberId, memberId:input.memberId},
        type:'post',
        dataType:'json',
        async:false,
        success:function (result) {
            if (result.response) {
                memberUnFollowCallBack(result, input);
            }
        }
    });
};

Base.prototype.LockArgument = function (input) {
    jQuery.ajax({
        url:DA.base_url + 'action/lockArgument',
        data:input,
        type:'post',
        dataType:'json',
        success:function (result) {
            if (result.response) {
                lockArgumentCallBack(result, input);
            } else {
                alert('Sorry, Some thing went terrible. You may not allowed to lock this argument. Please try again later.');
            }
        }
    });
};

Base.prototype.Favorite = function (input) {
    jQuery.ajax({
        url:DA.base_url + 'action/argumentFollow',
        dataType:'json',
        type:'post',
        async:false,
        data:{memberId:input.memberId, argumentId:input.argumentId},
        success:function (result) {
            if (result.response) {
                favoriteCallBack(result, input);
            } else {
                baseObj.Showmsg(DA.ARGUMENT_FOLLOW_FAIL, false);//'An error occured while favoriting this argument. This argument may be locked / removed.'
            }
        }
    });
};

Base.prototype.Postopinion = function (input) {
    jQuery.ajax({
        url:DA.base_url + 'action/postCommentVote',
        dataType:'json',
        type:'post',
        data:input,
        beforeSend:function () {
            if (!window.validationEngine.validateForm(jQuery(".opinion").closest(".form"))) {
                baseObj.Showmsg(errorProcessing(), false);
                return false;
            } else {
                return true;
            }
        },
        success:function (result) {
            if (result.response) {
                //update Vote Count and Comment Count
                baseObj.CloseModel();
                postOpinionCallBack(result, input);
            } else {
                //show error msg
                baseObj.CloseModel();
                baseObj.Showmsg(DA.ARGUMENT_VOTE_FAIL, false)
            }
        }
    });
};

Base.prototype.updateOpinion = function (input) {
    jQuery.ajax({
        url:DA.base_url + 'action/updateCommentReply',
        dataType:'json',
        type:'post',
        data:input,
        beforeSend:function () {
            if (!window.validationEngine.validateForm(jQuery("#updateCommentReply").closest(".form"))) {
                baseObj.Showmsg(errorProcessing(), false);
                return false;
            } else {
                return true;
            }
        },
        success:function (result) {
            if (result.response) {
                //update Vote Count and Comment Count
                baseObj.CloseModel();
                updateOpinionCallBack(result, input);
            } else {
                //show error msg
                baseObj.CloseModel();
                baseObj.Showmsg(DA.ARGUMENT_VOTE_FAIL, false)
            }
        }
    });
};

Base.prototype.postToFB = function (options) {
    var obj = {
        method:'feed',
        link:options.link,
        /*picture: DA.base_url+options.img,*/
        picture:DA.base_url + 'images/logo-icon-64.png',
        /*name: 'Disagree.Me',*/
        name:(options.title != '' || options.title != undefined || options.title != null) ? options.title : '',
        caption:'',
        description:(options.description != '' || options.description != undefined || options.description != null) ? options.description : ''
    };
    FB.ui(obj, function () {
        if(options.twFlag){
            options.url = options.twurl;
            options.description = options.twdescription;
            baseObj.postToTW(options);
        }
    });
};

Base.prototype.postToTW = function (options) {
    twttr.anywhere(function (T) {
        T(".tbox").tweetBox({
            height:100,
            width:400,
            defaultContent:(options.description != '' || options.description != undefined || options.description != null) ? options.description + " @Disagree_Me on www.disagree.me" : 'voted ' + " @Disagree_Me on www.disagree.me",
            onTweet:function () {
                jQuery('.tbox').html('');
                baseObj.CloseModel();
            }
        });
        baseObj.OpenModel(jQuery('.tbox'));
    });
};

Base.prototype.SetUserMemberOnline = function () {
    if (loggedInUserMember) {
        jQuery.ajax({
            url:DA.base_url + "action/memberOnline",
            dataType:"json",
            type:'post',
            data:{memberId:loggedInUserMember.memberId},
            success:function (result) {
                if (result) {
                    var notification = result.data;
                    if (parseInt(notification) > 0) {
                        jQuery("#notificationCountContainer>a").html(notification);
                        jQuery("#notificationCountContainer").show();
                        jQuery("title").html("(" + notification + ") Disagree.me");
                    }
                }
            }
        });
    }
};

Base.prototype.getLoggedInUserMember = function () {
    if (jQuery("#loggedInMemberObj").length > 0) {
        return jQuery("#loggedInMemberObj").metadata();
    } else {
        return false;
    }
};

Base.prototype.GetNotification = function () {
    if (loggedInUserMember) {
        jQuery.ajax({
            url:DA.base_url + 'action/getNotification',
            dataType:'json',
            success:function (result) {
                if (result && result.response && result.data) {
                    jQuery("#notificationCountContainer>a").html(result.data);
                    jQuery("#notificationCountContainer").show();
                    jQuery('title').html("(" + result.data + ") Disagree.me");
                }
            }
        });
    }
};

Base.prototype.SyncArgument = function (input) {
    jQuery.ajax({
        url:DA.base_url + "sync/argument",
        dataType:'json',
        type:'post',
        success:function (result) {
            if (result) {
                syncArgumentCallBack(input, result);
            }
        }
    });
};

Base.prototype.CloseDropDownList = function () {
    jQuery(".popupActive").hide();
    jQuery("#settingButton").removeClass('active');
    jQuery('.userInfoTab>a').removeClass('active');

};

window.fbAsyncInit = function () {
    FB.init({
        appId:(jQuery("#apiData").length > 0) ? jQuery("#apiData").metadata().fb_api : '', // App ID
        status:true, // check login status
        cookie:true, // enable cookies to allow the server to access the session
        xfbml:true  // parse XFBML
    });

    // Load the SDK Asynchronously
    (function (d) {
        var js, id = 'facebook-jssdk';
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement('script');
        js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        d.getElementsByTagName('head')[0].appendChild(js);
    }(document));
};

function errorProcessing() {
    var errorString = '';
    jQuery.each(window.validationErrors.username, function (i, e) {
        errorString += jQuery(e).metadata().label + ' is allowed enter (A-Z),(a-z),(0-9),(.),(_) only.<br/>';
    });
    jQuery.each(window.validationErrors.minlength, function (i, e) {
        var metaData = jQuery(e).metadata();
        errorString += metaData.label + ' must contain minimum ' + metaData.minLength + ' charecters<br/>';
    });
    jQuery.each(window.validationErrors.maxlength, function (i, e) {
        var metaData = jQuery(e).metadata();
        errorString += metaData.label + ' can contain maximum ' + metaData.maxLength + ' charecters<br/>';
    });
    jQuery.each(window.validationErrors.email, function (i, e) {
        var metaData = jQuery(e).metadata();
        errorString += metaData.label + '  containes invalid email (Eg: someone@example.com)<br/>';
    });
    jQuery.each(window.validationErrors.emptystring, function (i, e) {
        var metaData = jQuery(e).metadata();
        errorString += metaData.label + ' Should not be empty.<br/>';
    });
    jQuery.each(window.validationErrors.selectBox, function (i, e) {
        var metaData = jQuery(e).metadata();
        errorString += "You must select any one " + metaData.label + " from drop down.<br/>";
    });
    jQuery.each(window.validationErrors.weburl, function (i, e) {
        var metaData = jQuery(e).metadata();
        errorString += metaData.label + ' containes invalid url string (Eg: http://wwww.disagree.me)<br/>';
    });
    jQuery.each(window.validationErrors.confirmpass, function (i, e) {
        errorString += 'Password and Confirm password fields are not matching or empty.<br/>'
    });
    jQuery.each(window.validationErrors.multiemail, function (i, e) {
        errorString += 'email address you entered ' + e + ' is invalid. please correct it.<br/>'
    });
    validator.prototype.clearvalidationErrors();
    return errorString;
}

jQuery(document).ready(function () {
    window.baseObj = new Base;
    window.loggedInUserMember = baseObj.getLoggedInUserMember();
    window.apiData = (jQuery("#apiData").length > 0) ? jQuery("#apiData").metadata() : null;
    window.map = new Array(0, 5, 15, 25, 35, 45, 50, 55, 65, 75, 85, 95, 100);

    window.agreeVoteID = 1;
    window.disagreeVoteID = 0;
    window.replyID = -1;
    window.agreeCommentID = -2;
    window.disagreeCommentID = -3;
    window.mouseOver = false;
    window.isProfileTipLoading = false;

    resizeWindowCallback();
    jQuery(window).bind('resize', resizeWindowCallback);

    window.validationEngine = new validator();
    window.validationErrors = new Array();
    var agreeCommentH3 = jQuery(".agreeing>h3").html();
    var disagreeCommentH3 = jQuery(".disagreeing>h3").html();

    var urlvar = baseObj.getUrlVars();
    if (urlvar.length != 0 && urlvar['res'] != undefined) {
        var msg = '';
        var action = null;
        switch (urlvar['res']) {
            case '0':
                msg = DA.PROFILE_UPDATE_FAIL;
                action = false;
                break;
            case '1':
                msg = DA.PROFILE_UPDATE_SUCCESS;
                action = true;
                break;
            case '3':
                msg = DA.ACCOUNT_CREATE_EMAIL_ALREADY_EXISTS;
                action = '';
                baseObj.OpenModel(jQuery(".creatAccountContainer"));
                break;
            case '4':
                msg = DA.ACCOUNT_ACTIVATE_NOTIFICATION;
                action = '';
                break;
            case '5':
                msg = DA.ACCOUNT_LOGIN_FAIL;
                action = false;
                baseObj.OpenModel(jQuery(".loginContainer"));
                break;
            case '6':
                msg = DA.ACCOUNT_NOT_ACTIVATED;
                action = '';
                break;
            case '8':
                msg = DA.PASSWORD_RESET_SUCCESSFULL;
                action = true;
                break;
            case '9':
                msg = DA.PASSWORD_RESET_FAIL;
                action = false;
                break;
            case '10':
                msg = DA.ACCOUNT_LOGIN_FACEBOOK_FAIL;
                action = false;
                break;
            case '11':
                msg = DA.PASSWORD_RESET_SUCCESSFULL;
                baseObj.OpenModel(jQuery(".loginContainer"));
                action = true;
                break;
            case '12':
                msg = DA.UNKONOWN_ERROR;
                action = false;
                break;
            case '13':
                msg = DA.USER_DATA_VALIDATION_FAIL;
                action = false;
                break;
        }
        baseObj.Showmsg(msg, action);
    }

    setInterval('baseObj.time_String_Sync()',60*1000);

    /*
     baseObj.prepareTextareaAutoResize();
     */
    jQuery(window).scroll(function () {
        if (jQuery(window).scrollTop() >= 85) {
            jQuery("#ScrollTopButton").css("top", ((document.documentElement.clientHeight / 2) - 64) + "px").show();
        } else {
            jQuery("#ScrollTopButton").hide();
        }
    });
    jQuery("#ScrollTopButton").mouseover(function () {
        jQuery(this).css({'opacity':'1'});
    });
    jQuery("#ScrollTopButton").mouseleave(function () {
        jQuery(this).css({'opacity':'0.2'});
    });

    jQuery("#postNewArgButton").bind('click', function () {
        var data = new Object;
        data.argumentTitle = jQuery("#newArgTitle").val();
        data.argumentDesc = jQuery("#newArgDesc").val();
        data.memberId = loggedInUserMember.memberId;
        data.topic = jQuery("#TopicSelectorSelectedValue").val();
        data.source = jQuery("#newArgSource").val();
        baseObj.CreateArgument(data);
    });
    jQuery("#updateArgButton").bind('click', function () {
        var data = new Object;
        data.argumentId = jQuery("#newArgId").val();
        data.argumentTitle = jQuery("#newArgTitle").val();
        data.argumentDesc = jQuery("#newArgDesc").val();
        data.memberId = loggedInUserMember.memberId;
        data.topic = jQuery("#TopicSelectorSelectedValue").val();
        data.source = jQuery("#newArgSource").val();
        baseObj.updateArgument(data);
    });

    //Universal Sign In Modal
    jQuery(".signIn").click(function () {
        baseObj.OpenModel(jQuery(".loginContainer"));
    });

    //Create Account Modal
    jQuery("#createAccount").click(function () {
        //createAccount clicked
        baseObj.OpenModel(jQuery(".creatAccountContainer"));
    });

    //Universal Start Argument Modal
    jQuery("#startArgumentWrapper button").click(function () {
        if (loggedInUserMember) {
            baseObj.OpenModel(jQuery(".startArgumentContainer"));
        } else {
            baseObj.OpenModel(jQuery(".loginContainer"));
        }
    });


    // Universal Close Model
    jQuery('#backgroundPopup,#popupWrapper').click(function (e) {
        e.stopPropagation();
        baseObj.CloseModel();
    });

    jQuery("#popupContainer").click(function (e) {
        e.stopPropagation();
    });

    jQuery('.closex').click(function () {
        baseObj.CloseModel();
    });

    jQuery(document).bind('keydown', function (e) {
        if (e.which == 27) {
            baseObj.CloseModel();
        }
    });

    /*jQuery(".msgclosex").click(function () {
     baseObj.Hidemsg();
     });*/
    jQuery("#messageContainer").live('click', function () {
        baseObj.Hidemsg();
    });
    jQuery(document).click(function () {
        baseObj.HideTip();
        baseObj.CloseDropDownList();
        jQuery(".popup").hide();
        jQuery(".popupActive").hide();      // topic submenu
        jQuery(".quickMenu").hide().siblings('#settingButton').removeClass('active');
        jQuery(".hozontalMenuTopicList").hide().closest('#homeArgumentNav').find(".categoryLink").removeClass('active');
        jQuery(".topicListSelector.active").css({"height":""}).children(":not(:first-child)").hide();
    });

    //Enables Navigation Menu
    jQuery(".navigationLink").live('click', function () {
        /*baseObj.CloseDropDownList();*/
        if (jQuery(this).hasClass("active")) {
            jQuery(this).removeClass("active");
            jQuery(".quickMenu").hide();
        } else {
            jQuery(this).addClass("active");
            jQuery(".quickMenu").show();
        }
        return false;
    });

    //Opinion Modal
    jQuery(".AgreeButton").live('click', function () {
        var metaData = jQuery(this).metadata();
        if (loggedInUserMember) {                                                                   //check whether user logged in or not to vote
            if (!metaData.locked) {                                                                 //check whether argument was locked or not
                if (metaData.owner) {                                                               //check if user is owner of the argument
                    jQuery(".agreeing>h3").html(DA.ARGUMENT_OWNER_AGREE_MESSAGE);
                    jQuery(".agreeing>textarea").addClass("validate required");
                    jQuery(".agreeing>h3").next("label").text("Add a comment");
                    jQuery("#agreeCommentText").metadata().defaultText = "Enter Your Comment";
                    jQuery(".agreeing>textarea").next("label").text("Get others to agree with you");
                    jQuery(".opinion").removeClass("opinion");
                    jQuery("#postAgree").addClass("opinion");
                    jQuery("#postAgree span").text("Submit Comment");
                } else if (metaData.voted) {                                                        //check if user already vote on this argument
                    jQuery(".agreeing>h3").html("Nice try. You already voted on this one.  But feel free to comment below");
                    jQuery(".agreeing>textarea").addClass("validate required");
                    jQuery(".agreeing>h3").next("label").text("");
                    jQuery("#agreeCommentText").metadata().defaultText = "What do you think?";
                    jQuery(".agreeing>textarea").next("label").text("Think your friends and followers will agree?");
                    jQuery(".opinion").removeClass("opinion");
                    jQuery("#postAgree").addClass("opinion");
                    jQuery("#postAgree span").text("Submit Comment");
                } else {
                    jQuery("#agreeCommentText").metadata().defaultText = "Enter Your Comment";
                    jQuery(".agreeing>textarea").next("label").text("Get others to agree with you");
                    jQuery(".opinion").removeClass("opinion");
                    jQuery("#postAgree span").text("I AGREE");
                    jQuery("#agreeModalUserName").html(metaData.username);
                    jQuery("#disagreeModalUserName").html(metaData.username);
                }
                jQuery("#agreeCommentText").val(jQuery("#agreeCommentText").metadata().defaultText);
                baseObj.OpenModel(jQuery(".agreeing"));
                jQuery("#postAgree").metadata().argumentId = metaData.argumentId;
            } else {
                baseObj.Showmsg(DA.VOTE_ON_LOCKED_ARGUMENT);
            }
        } else {
            baseObj.OpenModel(jQuery(".loginContainer"));
        }
    });

    jQuery(".DisagreeButton").live('click', function () {
        var metaData = jQuery(this).metadata();
        if (loggedInUserMember) {
            if (!metaData.locked) {
                if (metaData.owner) {
                    jQuery(".disagreeing>h3").html(DA.ARGUMENT_OWNER_DISAGREE_MESSAGE);
                    jQuery(".disagreeing>textarea").addClass("validate required");
                    jQuery(".disagreeing>h3").next("label").text("Add a comment");
                    jQuery(".disagreeing>textarea").next("label").text("Get others to disagree with you");
                    jQuery("#disagreeCommentText").metadata().defaultText = "Enter Your Comment";
                    jQuery(".opinion").removeClass("opinion");
                    jQuery("#postDisagree").addClass("opinion");
                    jQuery("#postDisagree span").text("Submit Comment");
                } else if (metaData.voted) {
                    jQuery(".disagreeing>h3").html("Nice try. You already voted on this one.  But feel free to comment below");
                    jQuery(".disagreeing>textarea").addClass("validate required");
                    jQuery(".disagreeing>h3").next("label").text("");
                    jQuery("#disagreeCommentText").metadata().defaultText = "What do you think?";
                    jQuery(".disagreeing>textarea").next("label").text("Think your friends and followers will disagree?");
                    jQuery(".opinion").removeClass("opinion");
                    jQuery("#postDisagree").addClass("opinion");
                    jQuery("#postDisagree span").text("Submit Comment");
                } else {
                    jQuery(".disagreeing>textarea").next("label").text("Get others to disagree with you");
                    jQuery("#disagreeCommentText").metadata().defaultText = "Enter Your Comment";
                    jQuery(".opinion").removeClass("opinion");
                    jQuery("#postDisagree span").text("I DISAGREE");
                    jQuery("#agreeModalUserName").html(metaData.username);
                    jQuery("#disagreeModalUserName").html(metaData.username);

                }
                jQuery("#disagreeCommentText").val(jQuery("#disagreeCommentText").metadata().defaultText);
                baseObj.OpenModel(jQuery(".disagreeing"));
                jQuery("#postDisagree").metadata().argumentId = metaData.argumentId;
            } else {
                baseObj.Showmsg(DA.VOTE_ON_LOCKED_ARGUMENT);
            }
        } else {
            baseObj.OpenModel(jQuery(".loginContainer"));
        }
    });

    jQuery(".adToggle").live('click', function () {
        if (jQuery(this).parent().parent().hasClass('agreeing')) {
            jQuery(".popupContent").hide();
            jQuery(".disagreeing").show();
        } else {
            jQuery(".popupContent").hide();
            jQuery(".agreeing").show();
        }

    });

    //post Agree Opinion
    jQuery("#postAgree").click(function () {
        var metaData = jQuery(this).metadata();
        var data = new Object;
        data.commenttext = (jQuery("#agreeCommentText").val() == jQuery("#agreeCommentText").metadata().defaultText || jQuery("#agreeCommentText").val() == 'Enter your Comment here' || jQuery("#agreeCommentText").hasClass("defaultContent") || jQuery("#agreeCommentText").val() == null) ? '' : jQuery("#agreeCommentText").val();
        data.vote = window.agreeVoteID;
        data.memberId = loggedInUserMember.memberId;
        data.argumentId = metaData.argumentId;
        data.agreeCount = metaData.agree;
        data.argumentOwnerImage = jQuery("#title_" + data.argumentId).parent().siblings(".contentHead").children(".userImgCircleSmall").children("a").children("img").attr('src');
        data.argumentTitle = jQuery("#title_" + data.argumentId).html();
        data.fbFlag = (jQuery("#postAgreeFBCheck").attr('checked')) ? true : false;
        data.twFlag = (jQuery("#postAgreeTWCheck").attr('checked')) ? true : false;
        baseObj.Postopinion(data);
    });

    //post Disagree Opinion
    jQuery("#postDisagree").click(function () {
        var metaData = jQuery(this).metadata();
        var data = new Object;
        data.commenttext = (jQuery("#disagreeCommentText").val() == jQuery("#disagreeCommentText").metadata().defaultText || jQuery("#disagreeCommentText").val() == 'Enter your Comment here' || jQuery("#disagreeCommentText").hasClass("defaultContent") || jQuery("#disagreeCommentText").val() == null) ? '' : jQuery("#disagreeCommentText").val();
        data.vote = window.disagreeVoteID;
        data.memberId = loggedInUserMember.memberId;
        data.argumentId = metaData.argumentId;
        data.agreeCount = metaData.agree;
        data.argumentOwnerImage = jQuery("#title_" + data.argumentId).parent().siblings(".contentHead").children(".userImgCircleSmall").children("a").children("img").attr('src');
        data.argumentTitle = jQuery("#title_" + data.argumentId).html();
        data.fbFlag = (jQuery("#postDisagreeFBCheck").attr('checked')) ? true : false;
        data.twFlag = (jQuery("#postDisagreeTWCheck").attr('checked')) ? true : false;
        baseObj.Postopinion(data);
    });

    //update Comment / reply
    jQuery("#updateCommentReply").click(function () {
        var metaData = jQuery(this).metadata();
        var data = new Object,
            commentText = jQuery("#updateCommentReplyText").val();
        data.commenttext = (commentText == null || commentText == '') ? '' : commentText;
        data.memberId = loggedInUserMember.memberId;
        data.argumentId = metaData.argumentId;
        data.commentId = metaData.commentId;
        data.type = metaData.type;
        data.fbFlag = (jQuery("#updateCommentReplyFBCheck").attr('checked')) ? true : false;
        data.twFlag = (jQuery("#updateCommentReplyTWCheck").attr('checked')) ? true : false;
        baseObj.updateOpinion(data);
    });

    //User ProfileTip
    jQuery(".profileUserName,.username").live('mouseenter', function (e) {
        mouseOver = true;
        var thisObj = jQuery(this);
        if (!jQuery(this).hasClass("DAtip")) {
            return 0;
        }
        baseObj.HideTip();
        setTimeout(function () {
            if (mouseOver) {
                baseObj.GenerateProfileTip(thisObj, e);
            }
        }, 500);
    });

    jQuery(".profileUserName,.username").live('mouseleave', function () {
        mouseOver = false;
        setTimeout(function () {
            baseObj.HideTip();
        }, 500);
    });

    jQuery(".DAtoolTipUp").live('mouseleave', function () {
        setTimeout(function () {
            baseObj.HideTip();
        }, 500);
    });

    jQuery("#daMsgWrapper").click(function () {
        jQuery(this).animate({'width':'80px'}, 400, 'swing');
    });

    //Follow - UnFollow
    jQuery(".unfollowMember").live('click', function (e) {
        if (!loggedInUserMember) {
            baseObj.OpenModel(jQuery(".loginContainer"));
            return 0;
        }
        var metaData = jQuery(this).metadata();
        var data = new Object;
        data.memberId = loggedInUserMember.memberId;
        data.followMemberId = metaData.followeMemberId;
        data.metaData = metaData;
        data.clickObj = jQuery(this);
        baseObj.Unfollow(data);
    });

    jQuery(".followMember").live('click', function (e) {
        if (!loggedInUserMember) {
            baseObj.OpenModel(jQuery(".loginContainer"));
            return 0;
        }
        var metaData = jQuery(this).metadata();
        var data = new Object;
        data.memberId = loggedInUserMember.memberId;
        data.followMemberId = metaData.followeMemberId;
        data.metaData = metaData;
        data.clickObj = jQuery(this);
        baseObj.Follow(data);
    });

    //favorite Unfavorite
    jQuery(".favIcon").live('click', function () {
        var metaData = jQuery(this).metadata();
        var data = new Object;
        if (loggedInUserMember) {                       //check login user
            if (window.loggedInUserMember.memberId != metaData.ownerId) {   //check owner flag
                if (!metaData.locked) {                 //check locked flag
                    data.argumentId = metaData.argumentId;
                    data.memberId = loggedInUserMember.memberId;
                    data.clickObj = jQuery(this);
                    baseObj.Favorite(data);
                } else {
                    baseObj.Showmsg(DA.VOTE_ON_LOCKED_ARGUMENT);
                }
            } else {
                baseObj.Showmsg(DA.ARGUMENT_LOCK_UNLOCK_NOT_ALLOWED_ON_THIS_PAGE);
            }
        } else {
            baseObj.OpenModel(jQuery(".loginContainer"));
        }
    });

    //post reply
    jQuery(".addReplyButton").live('click', function (e) {
        jQuery(".opinion").removeClass("opinion");
        jQuery(e.target).addClass('opinion'); // added to support validation on reply textarea.
        var metaData = new Object;
        metaData.vote = window.replyID;
        metaData.commenttext = jQuery(this).parent(".replyActions").siblings(".replyTextArea").val();
        metaData.argumentId = window.currArgumentObj.id;
        metaData.memberId = window.loggedInUserMember.memberId;
        metaData.parentId = jQuery(this).metadata().parentId;
        baseObj.Postopinion(metaData);
    });

    //Search
    jQuery("#searchBox").live('keyup', function () {
        var thisObj = jQuery(this);
        var searchTerm = jQuery(thisObj).val();
        if (searchTerm.length > 2 && searchTerm != jQuery(thisObj).metadata().defaultText) {
            jQuery.ajax({
                url:DA.base_url + "action/search",
                dataType:'json',
                type:'post',
                data:{keyword:jQuery(thisObj).val()},
                beforeSend:function () {
                    var offset = jQuery("#searchBox").offset();
                    jQuery("#searchWrapper").css({'left':offset.left + 'px', 'top':(offset.top + jQuery("#searchBox").height(true)) + 'px'});
                    jQuery("#searchWrapper").show();
                    jQuery("#searchArgumentList").html('<span class="searchLoader"><img src="/images/da-loader.gif" alt="Loading..."></span>');
                    jQuery("#searchMemberList").html('<span class="searchLoader"><img src="/images/da-loader.gif" alt="Loading..."></span>');
                },
                success:function (result) {
                    var argumentList = result.data.argumentList;
                    var userMemberList = result.data.userMemberList;
                    var argumentListHtml = "";
                    var userMemberListHtml = "";
                    if (argumentList) {
                        for (var argumentCount = 0; argumentCount < argumentList.length; argumentCount++) {
                            argumentListHtml += '<a href="' + DA.base_url + 'detail?id=' + argumentList[argumentCount].id + '"><div class="contentHead">';
                            argumentListHtml += '<div class="userImgCircleSmall"><img src="' + argumentList[argumentCount].profileThumb + '" alt="user name on Disagree.me" width="35" height="35"></div>';
                            argumentListHtml += '<p class="heading5 secondaryText"><span class="profileUserName">' + baseObj.Ellipsis(argumentList[argumentCount].username, 22) + '</span><span class="argueText">posted</span><br/> <i class="timeagoText timeStringSync{timestring:\''+ argumentList[argumentCount].createdtime +'\'">'+ baseObj.time_difference(argumentList[argumentCount].createdtime) +'</i></p>';
                            argumentListHtml += '</div>';
                            argumentListHtml += '<div class="contentBody smallText">' + argumentList[argumentCount].title + '</div></a>';
                        }
                        /*argumentListHtml += "<div class='more'><a href='"+DA.base_url+"base/searchResult?s="+searchTerm+"&t=a' class='secondaryTextColor'><i>see more</i></a></div>"*/
                    } else {
                        argumentListHtml += "<h6>No Arguments Found</h6>";
                        if (userMemberList) {        //if search dont lead any arguments and if users redirect user to users tab
                            jQuery("#searchMemberListTrigger").trigger('click');
                        }
                    }
                    if (userMemberList) {
                        for (var userMemberCount = 0; userMemberCount < userMemberList.length; userMemberCount++) {
                            if (userMemberList[userMemberCount].id != window.loggedInUserMember.memberId) {
                                var resusername = baseObj.is_empty_String(userMemberList[userMemberCount].fullname) ? userMemberList[userMemberCount].username : userMemberList[userMemberCount].fullname;
                                userMemberListHtml += '<a href="' + DA.base_url + 'profile?id=' + userMemberList[userMemberCount].id + '"><div class="contentHead">';
                                userMemberListHtml += '<div class="userImgCircleSmall"><img src="' + userMemberList[userMemberCount].profileThumb + '" alt="user name on Disagree.me" width="35" height="35"></div>';
                                userMemberListHtml += '<p class="heading5 secondaryText">' + resusername + '<br/> <i class="timeagoText timeStringSync {timestring:\''+userMemberList[userMemberCount].createdTime+'\'}">' + baseObj.time_difference(userMemberList[userMemberCount].createdTime) + '</i></p>';
                                userMemberListHtml += '</div></a>';
                            } else {
                                if (userMemberList.length == 1) {
                                    userMemberListHtml += "<h6>No Users Found</h6>";
                                }
                            }
                        }
                        /*userMemberListHtml += '<div class="more"><a href="'+DA.base_url+'base/searchResult?s='+searchTerm+'&t=m" class="secondaryTextColor"><i>see more</i></a></div>';*/
                    } else {
                        userMemberListHtml += "<h6>No Users Found</h6>";
                    }
                    jQuery("#searchWrapper").show();
                    jQuery("#searchArgumentList").html(argumentListHtml);
                    jQuery("#searchMemberList").html(userMemberListHtml);
                }
            });
        } else {
            jQuery("#searchWrapper").hide();
        }
    });

    jQuery("#searchMenu>span").bind('click', function (e) {
        e.stopPropagation();
        jQuery(e.target).addClass('activeMenu').siblings().removeClass('activeMenu');
        jQuery("#searchList").children().hide();
        var id = jQuery(this).metadata().id;
        jQuery("#" + id).show();
    });

    /** Search for contents in HTML content
     * binded element: .contentsearch
     * metadata to searchbox: (.)searchTextHolder(class of element which holds text to search (Eg: profileUserName on invite fb friends))
     *                        (.)searchWrapper (class of element which is a contaner of array of search results(Eg: memberSection on invite fb friends))
     *                        (#)searchArea (id of element which is parent of all searchable content(Eg: fbFriendListWrapper on invite fb friends))
     */
    jQuery(".contentSearchBox").live('keyup', function () {
        var thisObj = jQuery(this);
        var searchTerm = jQuery(thisObj).val();
        var searchWrapperClass = jQuery(thisObj).metadata().searchWrapper;
        var searchTextHolderClass = jQuery(thisObj).metadata().searchTextHolder;
        var searchArea = jQuery(thisObj).metadata().searchArea;
        searchArea = jQuery("#" + searchArea);
        if (searchTerm.length == 0 || searchTerm == jQuery(thisObj).metadata().defaultText) {
            //search box is empty
            jQuery(searchArea).children().show();
        } else {
            jQuery(searchArea).children().hide();
            var users = new Array();
            jQuery(searchArea).find("." + searchTextHolderClass).each(function (i, e) {
                users.push(jQuery(e).text());
            });
            var result = baseObj.searchStringInArray(users, searchTerm);
            if (result.length == 0) {
                //no matches for search term
            } else {
                jQuery.each(result, function (i, e) {
                    jQuery(searchArea).children().eq(e).show();
                });
            }
        }
    });

    //lock argument
    jQuery(".lockButton").live("click", function () {  //assumes locking an argument is functionality only in detail page.
        var data = new Object();
        data.argumentId = window.currArgumentObj.id;
        data.memberId = window.loggedInUserMember.memberId;
        var statusTxt = jQuery(this).children("i").hasClass("lockOffG") ? 'Lock' : 'Unlock';
        if (confirm("Are you, sure you want to " + statusTxt + " this argument?") && (window.loggedInUserMember.memberId == window.currArgumentObj.memberId)) {
            baseObj.LockArgument(data);
        }
    });

    //validate form input fields

    jQuery("form,.form").on('submit', function (e) {
        e.stopPropagation();
        if (window.validationEngine.validateForm(jQuery(this))) {
            return true;
        } else {
            baseObj.Showmsg(errorProcessing(), false);
            baseObj.processPlaceHolder();
            return false;
        }
    });

    /*make profile image clickable*/
    jQuery(".userImgCircleSmall:not(.nofollow)").live('click', function (e) {
        e.stopPropagation();
        window.location = "" + DA.base_url + "" + jQuery(this).siblings("a").attr("href");
    })

    /*** Before Logout ***/
    jQuery(window).bind("beforeunload", function () {
        jQuery.ajax({
            url:DA.base_url + "action/memberOffline",
            dataType:"json",
            type:'post',
            data:{memberId:loggedInUserMember.memberId},
            success:function (result) {

            }
        });
    });

    /*no arguments created action*/
    jQuery(".startArgHandle").live('click', function () {
        jQuery("#startArgumentWrapper button").trigger('click');
    });


    /*forget password*/
    jQuery("#forgetPassLink").click(function (e) {
        e.stopPropagation();
        jQuery(".loginContainer").hide();
        jQuery("#forgetPassForm").show();
    });

    jQuery("#forgetPassSubmit").click(function () {
        jQuery.ajax({
            url:DA.base_url + "base/forgetPassword",
            dataType:'json',
            data:{email:jQuery("#forgetPassEmail").val()},
            type:'post',
            beforeSend:function () {
                if (!window.validationEngine.validateForm(jQuery("#forgetPassForm"))) {
                    baseObj.Showmsg(errorProcessing(), false);
                    return false;
                } else {
                    jQuery("#forgetPassSubmit").after('<p id="forgetLoader"><img src="/images/da-loader.gif" alt="Loading..."> Requesting....</p>');
                    return true;
                }
            },
            success:function (res) {
                if (res != null) {
                    if (res.response == 1) {
                        baseObj.Showmsg(DA.EMAIL_SENT_MSG_FOR_FORGOT_PASSWORD, true);
                        baseObj.CloseModel();
                    } else {
                        baseObj.Showmsg(DA.NOT_REGISTERD_USER_MSG_FOR_FORGOT_PASSWORD, false);
                    }
                } else {
                    baseObj.Showmsg(DA.UNKONOWN_ERROR, false);
                }
            },
            complete:function () {
                jQuery("#forgetLoader").remove();
            }
        });
        return false;
    });

    jQuery("#newUserName,#newUserEmail").blur(function () {
        jQuery(this).trigger('check', jQuery(this).val());
    });


    //ajax member availability check
    jQuery("#newUserName,#newUserEmail").bind('check', function (e, data) {
        var thisObj = jQuery(this);
        jQuery.ajax({
            type:'post',
            dataType:'json',
            url:DA.base_url + 'base/checkUserByUsernameOrEmail',
            data:{data:jQuery(thisObj).val()},
            beforeSend:function () {
                if (!window.validationEngine.validateField(jQuery(thisObj))) return false;
                if (jQuery(thisObj).val() == '' || jQuery(thisObj).val() == null)return false;
                jQuery(thisObj).next().next().html('<img src="/images/da-loader.gif" alt="Loading..."/>Checking');
            },
            success:function (res) {
                if (res.data) {
                    jQuery(thisObj).next().next().html('unavailable');
                    jQuery(thisObj).addClass('na');
                } else {
                    jQuery(thisObj).next().next().html('available');
                    jQuery(thisObj).removeClass('na');
                }
            }
        });
    });
    jQuery(".signInButton").click(function () {
        if (!(jQuery(this).parent().siblings(".na").length < 1))return false;
    });

    jQuery("#editProfileButtonWrapper").click(function () {
        if (window.loggedInUserMember) {
            window.location = DA.base_url + "editUser";
        }
    });

    /*edit profile js*/
    jQuery("#dobpicker").datepicker({
        changeMonth:true,
        changeYear:true,
        dateFormat:'yy-mm-dd',
        defaultDate:(window.loggedInUserMember.birthdate == '0000-00-00') ? '' : window.loggedInUserMember.birthdate,
        minDate:new Date(1910, 1 - 1, 1),
        maxDate:new Date(),
        yearRange:'1910:' + (new Date()).getFullYear()
    });
    jQuery("#updateProfile").click(function (e) {
        e.stopPropagation();
        jQuery("#profilepictureEdit").val(jQuery("#profilePicImage img").attr('src'));

    });

    jQuery("#imgFileHolder").live('change', function (e) {
        e.stopPropagation();
        jQuery(this).closest('#imageAjax').ajaxForm({
            /*target: 'profilePicImage',*/
            dataType:'json',
            success:function (responseText, statusText, xhr, $form) {
                switch (responseText.responseCode) {
                    case '100':
                        jQuery("#profilePicImage img").attr("src", '/images/temporaryLocation/' + responseText.data);
                        break;
                    case '101':
                        baseObj.Showmsg('The image you tried to upload is too small. It needs to be at least 180 pixels width. Please try again with a larger image.', false);
                        break;
                    case '102':
                        baseObj.Showmsg('Unknown Error. Please Try again.', false);
                        break;
                    case '103':
                        baseObj.Showmsg('Maximun allowed image file size is 1 MB', false);
                        break;
                    case '104':
                        baseObj.Showmsg('Unable to process your photo. Please check your photo\'s format and try again. We support these photo formats: JPG, JPEG, PNG, BMP.', false);
                        break;
                    case '104':
                        baseObj.Showmsg('Please select image..!', false);
                        break;
                }
                /*baseObj.OpenModel(jQuery("#imageCropWindow"));
                 jQuery("#croppedThumb").html(data);
                 jQuery('#imageContainer>img').imgAreaSelect({ aspectRatio: '2:2', onSelectChange: preview });
                 jQuery("#originalImageName").val(jQuery(data).attr('src'));*/
            }
        }).submit();
    });
    jQuery("#tabs").tabs();
    window.changePWFlag = false;
    jQuery("#initChangePW").click(function () {
        jQuery(this).closest('.formRecord').hide().next().show().next().show();
        jQuery(this).closest("form,.form").find(".pass").addClass('validate');
        jQuery(this).closest("form,.form").find(".cpass").addClass('validate');
        window.changePWFlag = true;
    });

    jQuery("#geoLocation").click(function () {
        jQuery.getJSON("http://www.geoplugin.net/json.gp?jsoncallback=?",
            function (data) {
                jQuery("#changeProfileLocation").val(data.geoplugin_city + ', ' + data.geoplugin_countryCode);
            });
    });

    /*contact us page*/
    jQuery("#contactSubmit").click(function (e) {
        e.preventDefault();
        var thisObj = jQuery(this);
        jQuery.ajax({
            url:DA.base_url + 'action/contactus',
            type:'post',
            dataType:'json',
            data:{name:jQuery("#contactUname").val(), email:jQuery("#contactEmail").val(), Subject:jQuery("#contactSubject").val(), text:jQuery("#contacttextarea").val()},
            beforeSend:function () {
                if (!window.validationEngine.validateForm(jQuery(thisObj).closest('form,.form'))) {
                    baseObj.Showmsg(errorProcessing(), false);
                    return false
                }
                ;
            },
            success:function (res) {
                if (res.response) {
                    baseObj.Showmsg(DA.CONTACTUS_SUCCESS_MESSAGE, true);
                } else {
                    baseObj.Showmsg(DA.CONTACTUS_ERROR_MESSAGE, false);
                }
            }
        });
    });


    /* Argument hide/Report Action */
    jQuery(".secondaryContainer").live('mouseenter', function () {
        jQuery(this).children(".actionCotainer").show();
    });

    jQuery(".secondaryContainer").live('mouseleave', function () {
        jQuery(this).children(".actionCotainer").hide();
    });

    jQuery(".hideArgument").live('click', function () {
        var metaData = jQuery(this).parent().parent().metadata();
        jQuery.ajax({
            url:DA.base_url + "action/hideArgument",
            dataType:'json',
            type:'post',
            data:{argumentId:metaData.id, memberId:loggedInUserMember.memberId},
            success:function (result) {
                if (result.response) {
                    jQuery("#title_" + metaData.id).closest('.secondaryContainer').remove();
                }
            }
        });
    });

    jQuery(".reportArgument").live('click', function () {
        var metaData = jQuery(this).metadata();
        jQuery.ajax({
            url:DA.base_url + 'action/spam',
            dataType:'json',
            type:'post',
            data:{memberId:loggedInUserMember.memberId, type:metaData.type, recordId:metaData.id},
            success:function (result) {
                if (result.response) {
                    baseObj.Showmsg(metaData.type + '' + DA.ARGUMENT_SPAM_SUCCESS, true);
                } else {
                    baseObj.Showmsg("You already reported this Argument / comment  " + metaData.type + " as Spam. Our administrators are taking a look.", false);
                }
            }
        });
    });

    jQuery("#messageContainer").live('click', function () {
        jQuery("#messageContainer").slideUp();
    });


    jQuery(".shortArgument .contentBody,.longArgument  .contentBody").live('click', function () {
        //alert(jQuery(this).closest(".shortArgument").metadata().id);
        window.location = DA.base_url + "detail?id=" + jQuery(this).closest(".shortArgument,.longArgument").metadata().id;
    });

    jQuery(".toggleSignUp").click(function () {
        jQuery(".closex").trigger('click');
        jQuery(".signIn").trigger('click');
    });

    jQuery(".toggleSignIn").click(function () {
        jQuery(".closex").trigger('click');
        jQuery("#createAccount").trigger('click');
    });

    jQuery("#SuggestionBox").keypress(function (e) {
        if (e.which == 13) { //user pressed Enter /  return key
            jQuery.ajax({
                url:DA.base_url + 'action/getUserSuggestion',
                type:'post',
                dataType:'json',
                data:{suggestText:jQuery("#SuggestionBox").val()},
                beforeSend:function () {
                    if (jQuery("#SuggestionBox").val() != jQuery("#SuggestionBox").metadata().defaultText) {
                        jQuery("#SuggestionBoxWrapper").children().toggle();
                        return true;
                    } else {
                        return false;
                    }
                },
                success:function (res) {
                    if (res) {
                        jQuery("#SuggestionBox").val('');
                        baseObj.clearForm();
                        /*baseObj.Showmsg(res.response?DA.SUGGEST_FEEDBACK_SUCCESS:DA.SUGGEST_FEEDBACK_FAILURE);*/
                    } else {
                        /*baseObj.Showmsg(DA.SUGGEST_FEEDBACK_FAILURE);*/
                    }
                },
                complete:function () {
                    jQuery("#SuggestionBoxWrapper").children().toggle();
                    baseObj.processPlaceHolder(jQuery("#SuggestionBox"));
                }
            });
        }
    });
    jQuery("#inviteFriendsOverlay").click(function () {
        window.location.href = DA.base_url + 'invite';
    });
    jQuery("#ScrollTopButton").click(function () {
        baseObj.scrollPageTo();
    });


    jQuery("#popupWrapper").draggable({handle:"#dragHandle", cancel:".popupContent "});
    if (window.loggedInUserMember) {
        baseObj.SetUserMemberOnline();
        setInterval("baseObj.GetNotification()", 60000);

    } else if (!window.loggedInUserMember) {

    }
    baseObj.processPlaceHolder();
});
