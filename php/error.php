<?php
/* Can have a class with const variables. Then, use those in place of the numbers here in the array.
*/

class ERR{
	const OK = 0;

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
200 => "Username & Password do not match",
201 => "Account is already in use",
202 => "Token authentication failure",
203 => "Token expired",
204 => "User does not have a token"
);