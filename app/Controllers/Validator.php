<?php

namespace App\Controllers;
use \PDO;

/**
* 
*/
class Validator
{
	public static function nameLengthCheck($name) {

		if (strlen($name) < 2 || strlen($name) > 32) {
			return false;
		}

		return true;
	}

	public static function mailCheck($email, $db) {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return INVALID_EMAIL;
		}

		$DB_REQ = $db->prepare('SELECT email FROM users WHERE email = :email');
		$DB_REQ->bindParam(':email', $email);
		$DB_REQ->execute();

		if($DB_REQ->rowCount() > 0){
			return EMAIL_ALREADY_EXISTS;
		}

		return true;
	}

	public static function bioLengthCheck($bio) {
		if (strlen($bio) < 20) {
			return false;
		}
		return true;
	}

	public static function passwordCheck($pwd) {
		if (strlen($pwd) < 6){
			return 1;
		}

		elseif (!preg_match("#[0-9]+#", $pwd)) {
			return 2;
		}

		elseif (!preg_match("#[a-zA-Z]+#", $pwd)) {
			return 3;
		}

		return false;
	}

	public static function passwordConfirm($pwd, $pwdConfirm) {

		if ($pwd != $pwdConfirm){
			return false;
		}

		return true;
	}

	public static function sexualityCheck($sexuality) {
		if (empty($sexuality)) {
			return false;
		}
		return true;
	}

	public function hobbiesCheck($hobbies) {
		$checked_arr = $hobbies;
		$count = count($checked_arr);
		if ($count < 4) {
			return false;
		}
		return true;
	}

	public function loginNameCheck($name, $db) {
		$DB_REQ = $db->prepare('SELECT name FROM users WHERE name = :name');
		$DB_REQ->bindParam(':name', $name);
		$DB_REQ->execute();

		if($DB_REQ->rowCount() === 0) {
			return false;
		}
		return true;

	}

	public function isActive($name, $db) {
		$DB_REQ = $db->prepare('SELECT isactive FROM users WHERE name = :name');
		$DB_REQ->bindParam(':name', $name);
		$DB_REQ->execute();

		$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
		if ($data['isactive'] == '0') {
			return false;

		}
		return true;
	}

	public function passwordLogin($name, $password, $db) {
		$DB_REQ = $db->prepare('SELECT password FROM users WHERE name = :name');
		$DB_REQ->bindParam(':name', $name);
		$DB_REQ->execute();

		$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
		if (password_hash($password, PASSWORD_DEFAULT) != $data['password']) {
			return false;
		}
		return true;
	}

	// public static function sexualityCheck($hobbies) {
	// 	if () {
	// 		return false;
	// 	}
	// 	return true;
	// }

	// public function checkMail($mail) {

	// 	$DB_REQ = $DB_PDO->prepare("SELECT email FROM users WHERE email = :email");
	// 	$DB_REQ->bindParam(':email', $email);
	// 	$DB_REQ->execute();

	// 	if($DB_REQ->rowCount() > 0){
	// 		error_popup("This e-mail is already used.");
	// 		return false;
	// 	}	
	// 	return true;
	// }
}