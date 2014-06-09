var userMemberId = "";
var domainUrl = "http://gs.indavest.com:8084";
window.globalArgumentHtml = "";
window.argumentHtml = "";
window.topicArray = new Array('Brands', 'Business', 'Education', 'Entertainment', 'Facebook', 'Health', 'History', 'Science / Environment', 'Sport', 'Technology', 'Travel', 'Twitter', 'Weather', 'Web');
window.topicIdArray = new Array('1319449750', '1319449955', '1319449960', '1319449350', '1319449967', '1319449950', '1319449650', '1319449965', '1319449212', '1319449850', '1319449550', '1319449969', '1319449300', '1319449970');
window.parentObj = "";
window.source = window.location.hostname;
window.argumentCreateCallBack = 0; 
load = function () {
    load.getScript(domainUrl + "/js/jQuery.js");
    load.tryReady(0); // We will write this function later. It's responsible for waiting until jQuery loads before using it.
};

// dynamically load any javascript file.
load.getScript = function (filename) {
    var script = document.createElement('script');
    script.setAttribute("type", "text/javascript");
    script.setAttribute("src", filename);
    if (typeof script != "undefined")
        document.getElementsByTagName("head")[0].appendChild(script);
}
load.tryReady = function (time_elapsed) {
    // Continually polls to see if jQuery is loaded.
    if (typeof jQuery == "undefined") { // if jQuery isn't loaded yet...
        if (time_elapsed <= 5000) { // and we havn't given up trying...
            setTimeout("load.tryReady(" + (time_elapsed + 200) + ")", 200); // set a timer to check again in 200 ms.
        } else {
            alert("Timed out while loading jQuery.");
        }
    } else {
    	jQuery("head").append("<link>"); 
        css = jQuery("head").children(":last"); 
        css.attr({ 
	        rel: "stylesheet", 
	        type: "text/css", 
	        href: "http://gs.indavest.com:8084/css/argMark.css" 
        }); 
        // Any code to run after jQuery loads goes here!
        // for example:
        getDisagreeUser();
        //loadTopicList();

        if (document.domain == "facebook.com") {
            loadFbPost();
            loadPopup();
            centerPopup();
        } else if (document.domain == "twitter.com") {
            loadTweetPost();
            loadPopup();
            centerPopup();
        } else {
            loadGeneralContent();
            loadGeneralPopup();
            //var selectedText = getSelText();
            if (!window.Kolich) {
                Kolich = {};
            }

            Kolich.Selector = {};
            Kolich.Selector.getSelected = function () {
                var t = '';
                if (window.getSelection) {
                    t = window.getSelection();
                } else if (document.getSelection) {
                    t = document.getSelection();
                } else if (document.selection) {
                    t = document.selection.createRange().text;
                }
                return t;
            }

            Kolich.Selector.mouseup = function () {
                var st = Kolich.Selector.getSelected();
                if (st != '') {
                    jQuery("#generalArgument-desc").attr('value', st);
                    //jQuery("#argumentFieldContainer").css({'visibility':'visible'});
                    //alert("You selected:\n"+st);
                }
            }

            //$(document).ready(function(){
            jQuery(document).bind("mouseup", Kolich.Selector.mouseup);
            //});
        }

        jQuery("#argumentContainerClosex").click(function () {
            disablePopup();
        });

        jQuery('#backgroundPopup').click(function () {
            disablePopup();
        });

        jQuery(document).bind('keydown', function (e) {
            if (e.which == 27) {
                disablePopup();
            }
        });

        jQuery(".createArgument").live('click', function () {
            if (document.domain == "facebook.com") {
                exportFBForm(jQuery(this));
            } else if (document.domain == "twitter.com") {
                exportTweetForm(jQuery(this));
            } else {
                var argumentTitle = jQuery("#generalArgument-title").val();
                var argumentTopic = jQuery(".topicSelected").attr('id');
                var argumentDescription = jQuery("#generalArgument-desc").val();
                exportGeneralArgument(argumentTitle, argumentTopic, argumentDescription, window.source);
            }
        });
        
        jQuery(".cancelLink").click(function(){
        	jQuery(".argueMarkletWrapper").remove();
        });
        
        jQuery("#topicDropDown").live('click',function(){
        	jQuery("#topicListWrapper").slideToggle();
        });
        
        jQuery("#fbTopicDropDown").live('click',function(){
        	jQuery("#fbTopicListWrapper").slideToggle();
        });
        
        jQuery("#topicListCotainer div").live('click',function(){
        	jQuery(".topicSelected").html(jQuery(this).html());
        	jQuery(".topicSelected").attr('id',jQuery(this).attr('id'));
        	jQuery(this).parent().parent().slideToggle('fast');
        });
        jQuery("#generalArgument-swap").click(function(){
        	var titleObj =jQuery("#generalArgument-title");
        	var descObj = jQuery("#generalArgument-desc");
        	var result = swapDescTitle(jQuery(titleObj).val(), jQuery(descObj).val());
        	jQuery(titleObj).val(result[0]);
        	jQuery(descObj).val(result[1]);
        });
        /*Fb Action*/
        jQuery('.fbArgumentMarkletCloser').click(function(){
        	jQuery('.fbArgueMarkletWrapper').remove();
        	jQuery('#backgroundPopup').remove();
        });
        
        jQuery('.fbArgumentWrapper').live('mouseover',function(){
        	var thisObj = jQuery(this);
        	jQuery(thisObj).children('.fbArgument').css({'width':'575px'})
        	jQuery(thisObj).children('.fbArgueButton').show();
        });
        
        jQuery('.fbArgumentWrapper').live('mouseout',function(){
        	var thisObj = jQuery(this);
        	jQuery(thisObj).children('.fbArgument').css({'width':'643px'});
        	jQuery(thisObj).children('.fbArgueButton').hide();
        });
        
        jQuery('.fbArgueButton').live('click',function(){
        	var thisObj = jQuery(this);
        	//window.parentObj = jQuery(this).parent().parent();
        	exportFBForm(thisObj);
        });
        
        jQuery(".revertArgument").live('click', function(){
        	var thisObj = jQuery(this);
        	jQuery(thisObj).parent().removeClass('fbArgumentCreateBox');
        	jQuery(thisObj).parent().html(window.globalArgumentHtml);
        });
        
        jQuery('.fbPostButton').live('click', function(){
        	var thisObj = jQuery(this);
        	var thisParentObj = jQuery(thisObj).parent();
        	var argumentTitle = jQuery(thisParentObj).children('.fbArgumentTitleValue').children(":input").val();
        	var argumentDesc = jQuery(thisParentObj).children('.fbArgumentDescValue').children(":input").val();
        	var argumentTopic = jQuery(thisObj).prev().children('span').attr('id');
        	var argumentSource = window.source;
        	if(userMemberId != ""){
        		exportFTArgument(argumentTitle, argumentTopic, argumentDesc, argumentSource);
        	}else {
        		window.open (domainUrl+"/baseActivity/login.php?popUp=true&at="+argumentTitle+"&ad="+argumentDesc+"&tp="+argumentTopic+"&source="+argumentSource, "mywindow","status=1,toolbar=1");
			}
        	jQuery(thisObj).parent().removeClass('fbArgumentCreateBox');
        	jQuery(thisObj).parent().html(window.globalArgumentHtml);
        });
    }
}

function swapDescTitle(title, desc){
	if(title != "Enter title")var thirdVar = title;
	else var thirdVar = "Select content for argument Description";
	if(desc != "Select content for argument Description")title = desc;
	else title = "Enter title";
	desc = thirdVar;
	return [title, desc];
}
load();

var popupStatus = 0;
function loadPopup() {
    if (popupStatus == 0) {
        jQuery('#backgroundPopup').css({'background':'none repeat scroll 0 0 #000000'});
        jQuery('#backgroundPopup').css({'height':'100%'});
        jQuery('#backgroundPopup').css({'left':'0'});
        jQuery('#backgroundPopup').css({'top':'0'});
        jQuery('#backgroundPopup').css({'width':'100%'});
        jQuery('#backgroundPopup').css({'z-index':'9998'});
        jQuery('#backgroundPopup').css({'position':'fixed'});
        jQuery('#backgroundPopup').css({'opacity':'0.6'});
        jQuery('#backgroundPopup').fadeIn('slow');
        jQuery('#argumentContainerClosex').css({'cursor':'pointer'});
        jQuery('#argumentContainerClosex').css({'left':'570px'});
        jQuery('#argumentContainerClosex').css({'position':'absolute'});
        jQuery('#argumentContainerClosex').css({'top':'8px'});
        jQuery('#createArgumentContainer').fadeIn('slow');
        jQuery('#createArgumentContainer').css({'height':'300px'});
        jQuery('#createArgumentContainer').css({'padding':'20px 10px'});
        jQuery('#createArgumentContainer').css({'width':'570px'});
        jQuery('#createArgumentContainer').css({'z-index':'9999'});
        jQuery('#createArgumentContainer').css({'overflow':'hidden'});
        jQuery('#createArgumentContainerWrapper').css({'background-color':'#62B4D9'});
        jQuery('#createArgumentContainerWrapper').css({'float':'left'});
        jQuery('#createArgumentContainerWrapper').css({'padding':'10px'});
        popupStatus = 1;
    }

}

function loadGeneralPopup() {
    if (popupStatus == 0) {
        jQuery('#argumentContainerClosex').css({'cursor':'pointer'});
        jQuery('#argumentContainerClosex').css({'float':'right'});
        jQuery('#argumentContainerClosex').css({'position':'absolute'});
        jQuery('#createArgumentContainer').css({'opacity':'0.95'});
        jQuery('#createArgumentContainer').fadeIn('slow');
        jQuery('#createArgumentContainer').css({'height':'80px'});
        jQuery('#createArgumentContainer').css({'padding':'20px 10px'});
        jQuery('#createArgumentContainer').css({'width':'100%'});
        jQuery('#createArgumentContainer').css({'z-index':'9999'});
        jQuery('#createArgumentContainer').css({'overflow':'hidden'});
        jQuery('#createArgumentContainer').css({'background-color':'#04689C'});
        jQuery('#createArgumentContainer').css({'position':'fixed'});
        popupStatus = 1;
    }
}
function disablePopup() {
    if (popupStatus == 1) {
        jQuery('#backgroundPopup').fadeOut('slow');
        jQuery('#createArgumentContainer').fadeOut('slow');
        popupStatus = 0;
    }

}

function centerPopup() {
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupWidth = jQuery('#createArgumentContainer').width();
    var popupHeight = jQuery('#createArgumentContainer').height();
    jQuery('#createArgumentContainer').css({
        'position':'fixed',
        'top':'135px',
        'left':'350px'
    });

}

function getDisagreeUser() {
    jQuery.ajax({
        url:domainUrl + '/baseActivity/getUserCookie.php',
        data:{name:'Chad'},
        dataType:'jsonp',
        jsonp:'callback',
        jsonpCallback:'jsonpCallback',
        success:function (data) {
            userMemberId = data.message;
        }
    });
}


function strip(html) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText;
}

function loadFbPost() {
	var fbContainerWrapperHtml = "";
    if (jQuery(".fbArgueMarkletWrapper").length == 0) {
    	fbContainerWrapperHtml += '<div class="fbArgueMarkletWrapper">';
    		fbContainerWrapperHtml += '<div class="fbArgumentMarkletHeader">';
    			fbContainerWrapperHtml += '<div class="fbDisagreeLogo"><a href="http://beta.disagree.me" title="disgree.me" target="_blank"><img width="76px" height="33px" src="'+domainUrl+'/images/disagree_logo_icon_small.png" alt="disagree logo"></a></div>';
    			fbContainerWrapperHtml += '<div id="fbHeaderRight">';
    				fbContainerWrapperHtml += '<div id="fbHeaderUserActions"><span>prasad1(<b>SignOut)</b></span></div>';
    				fbContainerWrapperHtml += '<div id="fbHeaderUserPrefrences" style=""><div style=" display: inline;"><a href="'+domainUrl+'/member/userprofile.php?id=1324638445" style="border: 0 none;"><img src="'+domainUrl+'/images/user_img.png" alt="user preferences button"><img src="'+domainUrl+'/images/more_icon_1.png" alt="arrow down"></a></div></div>';
    			fbContainerWrapperHtml += '</div>';
    		fbContainerWrapperHtml += '</div>';
    		fbContainerWrapperHtml += '<div class="fbArgumentMarkletContainer">';
    		jQuery(".mainWrapper").each(function () {
    	        //alert(jQuery(this).children(".uiStreamPassive").length);
    			var actorImage = jQuery(this).parent().prev().children('img').attr('src');
    	        if (jQuery(this).children(".uiStreamPassive").length != 1) {
    	        	fbContainerWrapperHtml += '<div class="fbArgumentsBox" style="float: left; margin: 5px;">';
    	        		fbContainerWrapperHtml += '<div class="fbArgumentWrapper">';
    	        			fbContainerWrapperHtml += '<div class="fbArgument">';
    	        				fbContainerWrapperHtml += '<div class="userimg" style="float: left; margin: 5px 10px;"><img width="43px" height="42px" alt="user profile pic" src="'+actorImage+'"></div>';
    	        				fbContainerWrapperHtml += '<div class="fbArgumentHead"><div class="fbArgumentHeaderLeft" style="text-decoration: none; display: inline;"><a target="_blank" href="#">'+jQuery(this).find(".actorDescription").html()+'</a></div><div class="fbArgumentHeaderRight"><span style="">wrote on Jan 17, 2012 </span></div></div>';
    	        				fbContainerWrapperHtml += '<div class="fbArgumentContent">'+jQuery(this).find(".messageBody").html()+'</div>';
    	        			fbContainerWrapperHtml += '</div>';
    	        			fbContainerWrapperHtml += '<div class="fbArgueButton"><span style="text-align: center;">Argue</span></div>';
    	        		fbContainerWrapperHtml += '</div>';
    	        	fbContainerWrapperHtml += '</div>';
    	        }
    	    });
    		fbContainerWrapperHtml += '</div>';
    		fbContainerWrapperHtml += '<div class="fbArgumentMarkletCloser"><img width="26px" height="26px" alt="close pop up" src="'+domainUrl+'/images/closer-icon.png"></div>';
    	fbContainerWrapperHtml += '</div>';
    	fbContainerWrapperHtml += '<div id="backgroundPopup" style="background: none repeat scroll #b0b0b0; height: 100%; left: 0pt; top: 0pt; width: 100%; z-index: 9998; position: fixed; opacity: 0.6;"></div>';
        jQuery("body").prepend(fbContainerWrapperHtml);
    }
}

function loadTweetPost() {
	var fbContainerWrapperHtml = "";
    if (jQuery(".fbArgueMarkletWrapper").length == 0) {
    	fbContainerWrapperHtml += '<div class="fbArgueMarkletWrapper">';
    		fbContainerWrapperHtml += '<div class="fbArgumentMarkletHeader">';
    			fbContainerWrapperHtml += '<div class="fbDisagreeLogo"><a href="http://beta.disagree.me" title="disgree.me" target="_blank"><img width="76px" height="33px" src="'+domainUrl+'/images/disagree_logo_icon_small.png" alt="disagree logo"></a></div>';
    			fbContainerWrapperHtml += '<div id="fbHeaderRight">';
    				fbContainerWrapperHtml += '<div id="fbHeaderUserActions"><span>prasad1(<b>SignOut)</b></span></div>';
    				fbContainerWrapperHtml += '<div id="fbHeaderUserPrefrences" style=""><div style=" display: inline;"><a href="'+domainUrl+'/member/userprofile.php?id=1324638445" style="border: 0 none;"><img src="'+domainUrl+'/images/user_img.png" alt="user preferences button"><img src="'+domainUrl+'/images/more_icon_1.png" alt="arrow down"></a></div></div>';
    			fbContainerWrapperHtml += '</div>';
    		fbContainerWrapperHtml += '</div>';
    		fbContainerWrapperHtml += '<div class="fbArgumentMarkletContainer">';
    		jQuery(".tweet .content").each(function () {
    	        //alert(jQuery(this).children(".uiStreamPassive").length);
    			var thisObj = jQuery(this);
    			var actorImage = jQuery(thisObj).find('.avatar').attr('src');
    	        if (jQuery(thisObj).children(".uiStreamPassive").length != 1) {
    	        	fbContainerWrapperHtml += '<div class="fbArgumentsBox" style="float: left; margin: 5px;">';
    	        		fbContainerWrapperHtml += '<div class="fbArgumentWrapper">';
    	        			fbContainerWrapperHtml += '<div class="fbArgument">';
    	        				fbContainerWrapperHtml += '<div class="userimg" style="float: left; margin: 5px 10px;"><img width="43px" height="42px" alt="user profile pic" src="'+actorImage+'"></div>';
    	        				fbContainerWrapperHtml += '<div class="fbArgumentHead"><div class="fbArgumentHeaderLeft" style="text-decoration: none; display: inline;"><a target="_blank" href="#">'+jQuery(thisObj).find(".js-action-profile-name").html()+'</a></div><div class="fbArgumentHeaderRight"><span style="">wrote on Jan 17, 2012 </span></div></div>';
    	        				fbContainerWrapperHtml += '<div class="fbArgumentContent">'+jQuery(thisObj).find(".js-tweet-text").html()+'</div>';
    	        			fbContainerWrapperHtml += '</div>';
    	        			fbContainerWrapperHtml += '<div class="fbArgueButton"><span style="text-align: center;">Argue</span></div>';
    	        		fbContainerWrapperHtml += '</div>';
    	        	fbContainerWrapperHtml += '</div>';
    	        }
    	    });
    		fbContainerWrapperHtml += '</div>';
    		fbContainerWrapperHtml += '<div class="fbArgumentMarkletCloser"><img width="26px" height="26px" alt="close pop up" src="'+domainUrl+'/images/closer-icon.png"></div>';
    	fbContainerWrapperHtml += '</div>';
    	fbContainerWrapperHtml += '<div id="backgroundPopup" style="background: none repeat scroll #b0b0b0; height: 100%; left: 0pt; top: 0pt; width: 100%; z-index: 9998; position: fixed; opacity: 0.6;"></div>';
        jQuery("body").prepend(fbContainerWrapperHtml);
    }
}

function loadGeneralContent() {
    var topicListHtml = "";
    for (var topicIndex = 0; topicIndex < window.topicArray.length; topicIndex++) {
        topicListHtml += "<option value='" + window.topicIdArray[topicIndex] + "'>" + window.topicArray[topicIndex] + "</option>";
    }
    if (jQuery("#createArgumentContainer").length == 0) {
    	generalPopupHtml = '<div class="argueMarkletWrapper gradientBg">';
    		generalPopupHtml += '<div class="argumentsCanvas">';
    			generalPopupHtml += '<div class="argumentsCanvas">';
    			generalPopupHtml += '<div class="disagreeLogo"><a href="'+domainUrl+'" title="disgree.me" target="_blank"><img src="'+domainUrl+'/images/disagree_logo_icon.png" alt="disagree logo" width="128px" height="62px"/></a></div>';
    			generalPopupHtml += '<div class="argumentTitleHead">Argument Title</div>';
    			generalPopupHtml += '<div class="argumentTopicHead">Topic</div>';
    			generalPopupHtml += '<div class="argumentTitleValue insetBoxShadow"><input type="text" value="Enter title" id="generalArgument-title"/></div>';
    			generalPopupHtml += '<div class="argumentTopicValue insetBoxShadow"><span class="topicSelected">Politics</span><img src="'+domainUrl+'/images/drop_down_arrow.png" alt="drop down button" width="8px" height="7px" id="topicDropDown"/></div>';
    			generalPopupHtml += '<div class="createArgument">Post Argument</div>';
    			generalPopupHtml += '<span class="amp">&</span>';
    			generalPopupHtml += '<div class="checkboxgroup"><div class="checkBox"><div class="checkBoxImg"><input type="radio" name="generalArgument-collapse" value="0" checked/></div><span>Collapse this bar</span></div><div class="checkBox"><div class="checkBoxImg"><input type="radio" name="generalArgument-collapse" value="1" /><span>Take me to disagree me</span></div></div></div>';
    			generalPopupHtml += '<div class="argumentDescHead"><img src="'+domainUrl+'/images/conversation_icon.png" alt="conversation icon" width="16px" height="16px" id="generalArgument-swap"/><span>Argument Description</span></div>';
    			generalPopupHtml += '<div class="argumentDescValue "><textarea rows="1" cols="100" class="descValue insetBoxShadow" id="generalArgument-desc">Select content for argument Description</textarea></div>';
    			generalPopupHtml += '<div class="loggedInInfo"><img src="'+domainUrl+'/images/sign-in-icon.png" alt="sign in user info" width="16px" height="16px"><span>User (Signed In)</span></div>';
    			generalPopupHtml += '<div class="cancelLink"><img src="'+domainUrl+'/images/cancel_icon.png" alt="cancel icon" width="16px" height="16px"><span>Cancel & Collapse</span></div>';
			generalPopupHtml += '</div>';
			generalPopupHtml += '<div id="topicListWrapper"><div id="topicListCotainer">';
			for(var topicCount=0;topicCount < window.topicArray.length;topicCount++){
				generalPopupHtml += '<div id="'+window.topicIdArray[topicCount]+'">'+window.topicArray[topicCount]+'</div>';
			}
			generalPopupHtml += '</div></div>';
		generalPopupHtml += '</div>';
		jQuery("body").prepend(generalPopupHtml);
    }
}
function exportFBForm(thisObj) {
    window.globalArgumentHtml = jQuery(thisObj).parent().parent().html();
    window.parentObj = jQuery(thisObj).parent();
    var argumentObj = jQuery(thisObj).prev();
    var argumentDesc = jQuery(argumentObj).children('.fbArgumentContent').html();
    var topicListHtml = "";
    /*for (var topicIndex = 0; topicIndex < window.topicArray.length; topicIndex++) {
        topicListHtml += "<option value='" + window.topicIdArray[topicIndex] + "'>" + window.topicArray[topicIndex] + "</option>";
    }*/
    var createArgumentHtml = '<div class="revertArgument" style="float:right;padding:0 10px;cursor: pointer;">x</div>';
    	createArgumentHtml += '<div class="fbArgumentTopicHead">Topic</div>';
    	createArgumentHtml += '<div class="fbArgumentTitleHead">Argument Title</div>';
    	createArgumentHtml += '<div class="fbArgumentTitleValue fbInsetBoxShadow"><input type="text" value="Enter title"></div>';
    	createArgumentHtml += '<div class="fbArgumentTopicValue fbInsetBoxShadow"><span class="topicSelected">Select topic</span><img width="8px" height="7px" alt="drop down button" src="'+domainUrl+'/images/drop_down_arrow.png" id="fbTopicDropDown"></div>';
    	createArgumentHtml += '<div class="fbPostButton">Post Argument</div>';
    	createArgumentHtml += '<div class="fbArgumentDescHead" style=""><img width="16px" height="16px" alt="conversation icon" src="'+domainUrl+'/images/conversation_icon.png" style="float: left;"><span>Argument Description</span></div>';
    	createArgumentHtml += '<div class="fbArgumentDescValue" style="position: absolute; top: 95px; left: 10px;"><textarea rows="1" cols="94" class="descValue fbInsetBoxShadow">'+argumentDesc+'</textarea></div>';
    	createArgumentHtml += '<div id="fbTopicListWrapper"><div id="topicListCotainer">';
		for(var topicCount=0;topicCount < window.topicArray.length;topicCount++){
			createArgumentHtml += '<div id="'+window.topicIdArray[topicCount]+'">'+window.topicArray[topicCount]+'</div>';
		}
		createArgumentHtml += '</div></div>';
    jQuery(thisObj).parent().parent().addClass('fbArgumentCreateBox');	
    jQuery(thisObj).parent().parent().html(createArgumentHtml);
}

function exportTweetForm(thisObj) {
    window.globalArgumentHtml = jQuery(thisObj).parent().html();
    window.parentObj = jQuery(thisObj).parent();
    var topicListHtml = "";
    for (var topicIndex = 0; topicIndex < window.topicArray.length; topicIndex++) {
        topicListHtml += "<option value='" + window.topicIdArray[topicIndex] + "'>" + window.topicArray[topicIndex] + "</option>";
    }
    var createArgumentHtml = '<div style="width:480px;float:left;padding-top:5px;"><div id="argumentTitleContainer"><span><input type="text" name="argumentTitle" id="argumentTitle" style="width:349px;" value="Title?"/></span><span style="padding-left:10px;"><select name="argumentTopic" style="width:112px;" id="argumentTopic"><option value"">Select Topic</option>' + topicListHtml + '</select></span></div><div style="width:480px;"><textarea name="argumentDescription" id="argumentDescription" rows="2" cols="57">' + jQuery(thisObj).parent().find('.argumentDescription').html() + '</textarea></div></div><div style="width:40px;float:left;"><span style="float:left;" class="exitCreateArg" onclick="javascript:disableArgForm();"><img src="http://beta.disagree.me/images/cross.png"/></span><span onclick="javascript:importArgument();" class="importArgument"><img src="' + domainUrl + '/images/tick.png"/></span></div>';
    jQuery(thisObj).parent().html(createArgumentHtml);
}

function exportGeneralArgument(argumentTitle, argumentTopic, argumentDescription, source) {
    var argumentMemberId = userMemberId;
    var createCallback = jQuery('input:radio[name=generalArgument-collapse]:checked').val();
    if(argumentMemberId != ""){
    	jQuery.ajax({
            url:domainUrl + '/baseActivity/argumentImport.php',
            data:{argumentTitle:argumentTitle, argumentDescription:argumentDescription, userMember:argumentMemberId, status:"1", argumentTopic:argumentTopic, source:source},
            dataType:'jsonp',
            jsonp:'callback',
            jsonpCallback:'jsonpCallback',
            success:function (result) {
            	if (result.message == 'Success') {
            		if(createCallback == 1){
            			window.location.href = domainUrl;
            		}else{
            			jQuery(".argueMarkletWrapper").remove();
            		}
                }
            }
        });
    } else {
    	window.open (domainUrl+"/baseActivity/login.php?popUp=true&at="+argumentTitle+"&ad="+argumentDescription+"&um="+argumentMemberId+"&tp="+argumentTopic+"&source="+source, "mywindow","status=1,scrollbars=1");
	}
}

function exportFTArgument(argumentTitle, argumentTopic, argumentDescription, source) {
    var argumentMemberId = userMemberId;
    var createCallback = jQuery('input:radio[name=generalArgument-collapse]:checked').val();
    jQuery.ajax({
            url:domainUrl + '/baseActivity/argumentImport.php',
            data:{argumentTitle:argumentTitle, argumentDescription:argumentDescription, userMember:argumentMemberId, status:"1", argumentTopic:argumentTopic, source:source},
            dataType:'jsonp',
            jsonp:'callback',
            jsonpCallback:'jsonpCallback',
            success:function (result) {
            	if (result.message == 'Success') {
            		alert("Argument Created");
                }else {
					alert("Issue while creating Argument. Please make sure that required imformation is filled");
				}
            }
    });
}

function getSelText() {
    var txt = '';
    if (window.getSelection) {
        txt = window.getSelection();
    }
    else if (document.getSelection) {
        txt = document.getSelection();
    }
    else if (document.selection) {
        txt = document.selection.createRange().text;
    }
    else return;
    document.aform.selectedtext.value = txt;
}

function loadTopicList() {
    jQuery.ajax({
        url:domainUrl + '/baseActivity/getTopicList.php',
        data:{name:''},
        dataType:'jsonp',
        jsonp:'callback',
        jsonpCallback:'jsonpCallback',
        success:function (result) {
            window.topicIdArray = result.id;
            window.topicArray = result.topic;
        }
    });
}

function disableArgForm() {
    //alert(window.globalArgumentHtml);
    jQuery(window.parentObj).html(window.globalArgumentHtml);
}

function importArgument() {
    var thisObj = window.parentObj;
    var argumentTitle = jQuery(thisObj).find('#argumentTitle').val();
    var argumentTopic = jQuery(thisObj).find('#argumentTopic').val();
    var argumentDescription = jQuery(thisObj).find('#argumentDescription').val();
    var argumentMemberId = userMemberId;
    alert("Title=" + argumentTitle + "---Topic=" + argumentTopic + "----Description=" + argumentDescription + "-----MemberId=" + argumentMemberId);
    jQuery.ajax({
        url:domainUrl + '/baseActivity/argumentImport.php',
        data:{argumentTitle:argumentTitle, argumentDescription:argumentDescription, userMember:argumentMemberId, status:"1", argumentTopic:argumentTopic},
        dataType:'jsonp',
        jsonp:'callback',
        jsonpCallback:'jsonpCallback',
        success:function (result) {
            if (result.message == 'Success') {
                jQuery(thisObj).html(window.globalArgumentHtml);
                jQuery(thisObj).find('.createArgument').html('Argueing');
                //jQuery(thisObj).html('Argueing');
            }
        }
    });

}
//alert(jQuery(".tweet-text").html());