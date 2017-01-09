<?php

function mailResetPwd($email, $hash) {
	$host = "localhost:8080/matcha/public/settings/changeMail";
	$subject = "Matcha - You have changed your e-mail!";
	$message = "
	Hello,\n
	You have changed your e-mail, please confirm this adress by clicking on the following link:\n
	http://".$host."?email=".$email."&hash=".$hash;
	mail($email, $subject, $message);
	}

function confirmResetPwd($email, $hash) {
	$host = "localhost:8080/matcha/public/auth/newpwd";
	$subject = "Matcha - You have requested a reset of your password";
	$message = "
	Hello,\n
	You asked to reset your password, please confirm this adress by clicking on the following link:\n
	http://".$host."?email=".$email."&hash=".$hash;
	mail($email, $subject, $message);	
}