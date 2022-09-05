<?php
$pwd = $_GET['pass'];
$errors=array();
if (strlen($pwd) < 8) {
	$errors[] = "Password too short!";
}

if (!preg_match("#[0-9]+#", $pwd)) {
	$errors[] = "Password must include at least one number!";
}

if (!preg_match("#[A-Z]+#", $pwd)) {
	$errors[] = "Password must include at least one upper case letter!";
} 
if (!preg_match("#[a-z]+#", $pwd)) {
	$errors[] = "Password must include at least one lower case letter!";
} 

print_r($errors);
?>