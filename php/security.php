<?php

function hashPassword($password)
{
	return password_hash($password, PASSWORD_BCRYPT);
}

function hashPasswordCustomSalt($password, $salt)
{
	return password_hash($password, PASSWORD_BCRYPT, array("salt" => $salt));
}

function checkPassword($password, $passhash)
{
	return password_verify($password, $passhash);
}