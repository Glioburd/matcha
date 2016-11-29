<?php

function mailResetPwd($email, $hash) {
	$host = "localhost:8080/matcha/public/settings/changeMail";
	$subject = "Matcha - You have changed your e-mail!";
	$message = "
	Hello,\n
	You changed your e-mail, please confirm this adress by clicking on the following link:\n
	http://".$host."?email=".$email."&hash=".$hash;
	mail($email, $subject, $message);
	}