<?php
/* Can have a class with const variables. Then, use those in place of the numbers here in the array.
100: For username, display name, user-table related stuff
200: Security stuff, including permissions and sanctions blocking actions
300: Forum and thread related stuff, such as them not existing or you can't create them
*/

class ERR{
	const OK = 0;
	const UNKNOWN = 1;
	const USER_NOT_EXIST = 113;
	const FORUM_NOT_EXIST = 300;
	const POST_NOT_EXIST = 302;
}

$ERRORS = array(
ERR::OK => "OK",
1 => "Unknown Error",
100 => "Username has already been taken",
101 => "Username does not exist",
102 => "Username is malformed",
110 => "Display Name has already been taken",
111 => "Display Name does not exist",
112 => "Display Name is malformed",
113 => "User does not exist",
200 => "Username & Password do not match",
201 => "Account is already in use",
202 => "Token authentication failure",
203 => "Token expired",
204 => "User does not have a token",
205 => "Insufficient permission level",
206 => "User does not exist",
300 => "Forum does not exist",
301 => "Thread does not exist",
302 => "Post does not exist"
);