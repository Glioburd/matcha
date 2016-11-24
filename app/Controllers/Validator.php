<?php

namespace App\Controllers;
use \PDO;

/**
* 
*/
class Validator

{

	public static function isConnected() {
		if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
			return FALSE;
		}
		return TRUE;
	}

	public static function loginLengthCheck($login) {

		if (strlen($login) < 2 || strlen($login) > 32) {
			return FALSE;
		}

		return TRUE;
	}

	public function nameCheck($name) {

		if (!preg_match("/^[a-zA-Z]*$/",$name)) {
			return FALSE;
		}
		return TRUE;
	}

	public static function loginAvailability($login, $db) {

		$DB_REQ = $db->prepare('SELECT login FROM users WHERE login = :login');
		$DB_REQ->bindParam(':login', $login);
		$DB_REQ->execute();
		if($DB_REQ->rowCount() > 0){
			return FALSE;
		}

		return TRUE;
	}

	public static function mailCheck($email, $db) {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return INVALID_EMAIL;
		}

		$DB_REQ = $db->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
		$DB_REQ->bindParam(':email', $email);
		$DB_REQ->execute();

		$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

		if(intval($result['count']) > 0){
			return EMAIL_ALREADY_EXISTS;
		}

		return TRUE;
	}

	public static function bioLengthCheck($bio) {
		if (strlen($bio) < 20) {
			return FALSE;
		}
		return TRUE;
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

		return FALSE;
	}

	public static function passwordConfirm($pwd, $pwdConfirm) {

		if ($pwd != $pwdConfirm){
			return FALSE;
		}

		return TRUE;
	}

	public static function radioCheck($data) {
		if (empty($data)) {
			return FALSE;
		}
		return TRUE;
	}


	public function hobbiesCheck($hobbies) {
		$checked_arr = $hobbies;
		$count = count($checked_arr);
		if ($count < 4) {
			return FALSE;
		}
		return TRUE;
	}

	public function loginCheck($login, $db) {
		$DB_REQ = $db->prepare('SELECT login FROM users WHERE login = :login');
		$DB_REQ->bindParam(':login', $login);
		$DB_REQ->execute();

		if($DB_REQ->rowCount() === 0) {
			return FALSE;
		}
		return TRUE;

	}

	public function isActive($login, $db) {
		$DB_REQ = $db->prepare('SELECT isactive FROM users WHERE login = :login');
		$DB_REQ->bindParam(':login', $login);
		$DB_REQ->execute();

		$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
		if ($data['isactive'] == '0') {
			return FALSE;

		}
		return TRUE;
	}

	public function passwordLogin($login, $password, $db) {
		$DB_REQ = $db->prepare('SELECT password FROM users WHERE login = :login');
		$DB_REQ->bindParam(':login', $login);
		$DB_REQ->execute();

		$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);

		if (!password_verify($password, $data['password'])) {

			return FALSE;
		}
		
		return TRUE;
	}

}