<?php

use App\Models\User;

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

function reportMail(User $user, User $reportedProfile, $reportReason) {
	$subject = "Matcha - " . $user->login() . " has reported " . $reportedProfile->login();
	$email = "glioburd@gmail.com";
	$message = "
	Hello\n
	The user " . $user->login() . " (ID: " . $user->id() .") has reported the user" . $reportedProfile->login() ." (ID: " . $reportedProfile->id() .") for the following reason:\n
	". $reportReason . "
	";
	mail($email, $subject, $message);
}