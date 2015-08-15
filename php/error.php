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
	const AUTH_FAIL = 200;
	const ACC_IN_USE = 201;
	const TOKEN_FAIL = 202;
	const TOKEN_EXPIRED = 203;
	const USER_NO_TOKEN = 204;
	const PERMIS_FAIL = 205;
	const USER_NOT_EXIST = 206;
	const FORUM_NOT_EXIST = 300;
	const THREAD_NOT_EXIST = 301;
	const POST_NOT_EXIST = 302;
	const THREAD_LOCKED = 303;
}

$ERRORS = array(
ERR::OK => "OK",
1 => "Unknown Error",
2 => "Connection failure",
100 => "Username has already been taken",
101 => "Username does not exist",
102 => "Username is malformed",
110 => "Display Name has already been taken",
111 => "Display Name does not exist",
112 => "Display Name is malformed",
113 => "Password is malformed",
114 => "Posts per page malformed",
115 => "Location is malformed",
116 => "Email is malformed",
117 => "Gender is malformed",
200 => "Username and/or Password do not match",
201 => "Account is already in use",
202 => "Token authentication failure",
203 => "Token expired",
204 => "User does not have a token",
205 => "Insufficient permission level",
206 => "User does not exist",
300 => "Forum does not exist",
301 => "Thread does not exist",
302 => "Post does not exist",
303 => "Thread is locked"
);