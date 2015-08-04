<?php

function hashPassword($password)
{
	return password_hash($password, PASSWORD_BCRYPT);
}

function checkPassword($password, $passhash)
{
	return password_verify($password, $passhash);
}