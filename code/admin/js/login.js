/**
 * 
 */
jQuery(document).ready(function(){
	if(loginfailed) {
		//jQuery.jGrowl("login failed",{ sticky: true, glue:'before',life:5000});
		jQuery.jGrowl(
                AD.loginfailedmsg,
                {
                    theme: 'errorNotification',
                    position: 'top-right',
                    corners:'6px',
                    closer:true,
                    glue:'before',
                    life:5000
                }
		);
	}
});