<?php
/* Can have a class with const variables. Then, use those in place of the numbers here in the array.
100: For username, display name, user-table related stuff
200: Security stuff, including permissions and sanctions blocking actions
300: Forum and thread related stuff, such as them not existing or you can't create them
*/

class ERR{
	const OK = 0;
	const UNKNOWN = 1;
	const CONNECT = 2;
	const USERNAME_TAKEN = 100;
	const USERNAME_NOT_EXIST = 101;
	const USERNAME_BAD = 102;
	const DISPNAME_TAKEN = 110;
	const DISPNAME_NOT_EXIST = 111;
	const DISPNAME_BAD = 112;
	const PASSWORD_BAD = 113;
	const POSTS_PER_PAGE_BAD = 114;
	const LOCATION_BAD = 115;
	const EMAIL_BAD = 116;
	const SEX_BAD = 117;
	const MSG_TITLE_BAD = 118;
	const TIME_ZONE_BAD = 119;
	const AUTH_FAIL = 200;
	const ACC_IN_USE = 201;
	const TOKEN_FAIL = 202;
	const TOKEN_EXPIRED = 203;
	const USER_NO_TOKEN = 204;
	const PERMIS_FAIL = 205;
	const USER_NOT_EXIST = 206;
	const USER_NOT_LOGGED = 207;
	const FRIEND_NOT_EXIST = 208;
	const FRIEND_ALREADY_FRIEND = 209;
	const USER_NOT_SPECIFIED = 210;
	const FORUM_NOT_EXIST = 300;
	const THREAD_NOT_EXIST = 301;
	const POST_NOT_EXIST = 302;
	const THREAD_LOCKED = 303;
	const FORUM_NOTHREAD = 304;
}

$ERRORS = array(
ERR::OK => "OK",
ERR::UNKNOWN => "Unknown Error",
ERR::CONNECT => "Connection failure",
ERR::USERNAME_TAKEN => "Username has already been taken",
ERR::USERNAME_NOT_EXIST => "Username does not exist",
ERR::USERNAME_BAD => "Username is malformed",
ERR::DISPNAME_TAKEN => "Display Name has already been taken",
ERR::DISPNAME_NOT_EXIST => "Display Name does not exist",
ERR::DISPNAME_BAD => "Display Name is malformed",
ERR::PASSWORD_BAD => "Password is malformed",
ERR::POSTS_PER_PAGE_BAD => "Posts per page malformed",
ERR::LOCATION_BAD => "Location is malformed",
ERR::EMAIL_BAD => "Email is malformed",
ERR::SEX_BAD => "Gender is malformed",
ERR::MSG_TITLE_BAD => "Message Title is malformed",
ERR::TIME_ZONE_BAD => "Time Zone is malformed",
ERR::AUTH_FAIL => "Username and/or Password do not match",
ERR::ACC_IN_USE => "Account is already in use",
ERR::TOKEN_FAIL => "Token authentication failure",
ERR::TOKEN_EXPIRED => "Token expired",
ERR::USER_NO_TOKEN => "User does not have a token",
ERR::PERMIS_FAIL => "Insufficient permission level",
ERR::USER_NOT_EXIST => "User does not exist",
ERR::USER_NOT_LOGGED => "User not logged in",
ERR::FRIEND_NOT_EXIST => "Friend does not exist",
ERR::FRIEND_ALREADY_FRIEND => "User already your friend",
ERR::USER_NOT_SPECIFIED => "No User ID Specified",
ERR::FORUM_NOT_EXIST => "Forum does not exist",
ERR::THREAD_NOT_EXIST => "Thread does not exist",
ERR::POST_NOT_EXIST => "Post does not exist",
ERR::THREAD_LOCKED => "Thread is locked",
ERR::FORUM_NOTHREAD => "Forum does not allow threads"
);