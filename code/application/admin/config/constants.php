<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('FB_API_ID', "102419859876735");
define('FB_SECRET', "1dc2fe44539fe8b414f5c0ed063b2a4e");

define('AGREE_VOTE_ID', 1);
define('DISAGREE_VOTE_ID', 0);
define('REPLY_ID', -1);
define('AGREE_COMMENT_ID', -2);
define('DISAGREE_COMMENT_ID', -3);
define('ARGUMENT_AJAX_FETCH_COUNT', 6);

define('AJAX_TIME_INTERVAL',1);


define('RECENT_ACTION_CREATE_ARGUMENT','1');
define('RECENT_ACTION_COMMENT_ARGUMENT','2');
define('RECENT_ACTION_SPAM_REPORT','3');
define('RECENT_ACTION_FOLLOWED_A_ARGUMENT','4');
define('RECENT_ACTION_FOLLOWED_A_MEMBER','5');
define('RECENT_ACTION_FOLLOWED_A_TOPIC','6');
define('RECENT_ACTION_VOTE_AN_ARGUMENT','7');

define('ARGUMENT_RECENT_ACTION_STATUS_CHANGE','argument status change');
define('ARGUMENT_RECENT_ACTION_COMMENT','commenting argument');
define('ARGUMENT_RECENT_ACTION_SPAM_ARGUMENT','spam an argument');
define('ARGUMENT_RECENT_ACTION_SPAM_ARGUMENT_COMMENT','spam an argugment comment');
define('ARGUMENT_RECENT_ACTION_FOLLOWED_BY_MEMBER','followed by member');
define('ARGUMENT_RECENT_ACTION_VOTE_AN_ARGUMENT','vote an argument');
define('LOGIN_FAILED','Login failed.Username or password was entered incorrectly.  Please try again.');


/* End of file constants.php */
/* Location: ./application/config/constants.php */