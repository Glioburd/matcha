<?php

namespace App\Controllers;

use App\Controllers\Validator;
use App\Models\UserManagerPDO;
use App\Models\User;
use \PDO;

include __DIR__ . '../../../debug.php';

/**
* 
*/
class PagesController extends Controller {

	const NAME_DOESNT_EXISTS = 0;

	public function home($request, $response) {
		session_start();
		$user = unserialize($_SESSION['user']);
		if (!isset($user) || empty($user)) {
			return $this->redirect($response, 'auth.login', 200);
		}

		$this->render($response, 'pages/home.twig',[
			'user' => $user
			]);
	}

	public function getContact($request, $response) {

		return $this->render($response, 'pages/contact.twig');
	}

	public function postContact($request, $response){

	}

	public function getSignUp($request, $response) {

			return $this->render($response, 'pages/signUp.twig');
	}

	public function postSignUp($request, $response){

		$errors = [];

		if (!Validator::nameLengthCheck($request->getParam('name'))) {
			$errors['name'] = 'Your username must contain between 2 and 32 characters.';
		}

		if (Validator::mailCheck($request->getParam('email'), $this->container->db) === INVALID_EMAIL) {
			$errors['email'] = 'E-mail adress is invalid.';
		}

		elseif (Validator::mailCheck($request->getParam('email'), $this->container->db) === EMAIL_ALREADY_EXISTS) {
			$errors['email'] = 'E-mail is already used.';
		}

		if (Validator::passwordCheck($request->getParam('password'))) {
			switch (Validator::passwordCheck($request->getParam('password'))) {
				case 1:
					$errors['password'] = 'Password too short : minimum 6 characters.';
					break;
				case 2:
					$errors['password'] = 'Password must contain at least 1 number.';
					break;
				case 3:
					$errors['password'] = 'Password must contain at least 1 letter.';
					break;
			}
		}

		if (!Validator::passwordConfirm($request->getParam('password'), $request->getParam('passwordConfirm'))) {
				$errors['passwordConfirm'] = 'Invalid password confirmation';
		}		

		if (empty($errors)) {

			$user = new User([
				'name' => $request->getParam('name'),
				'email' => $request->getParam('email'),	
				'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
				]);

			$UserManagerPDO = new UserManagerPDO($this->db);
			$UserManagerPDO->save($user);
			$last_id = $this->db->lastInsertId();

			session_start();
			$_SESSION['id'] = $last_id;
			$_SESSION['name'] = $request->getParam('name');

		}

		else {
			$this->flash('Un champ n\'a pas été rempli correctement', 'error');	
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'auth.signup', 302);
		}

			return $this->redirect($response, 'auth.signupinfos', 200);
	}

	public function getSignUpInfos($request, $response) {

		return $this->render($response, 'pages/signupinfos.twig');
	}

	public function postSignUpInfos($request, $response) {

		$errors = [];

		if (!Validator::bioLengthCheck($request->getParam('bio'))) {
			$errors['bio'] = 'Your description must contain at least 20 characters. Don\'t be shy!';
		}

		if (!Validator::sexualityCheck($request->getParam('sexuality'))) {
			$errors['sexuality'] = 'You must check a sexual orientation';
		}

		if (!Validator::hobbiesCheck($request->getParam('hobbies'))) {
			$errors['hobbies'] = 'You must check at least 4 hobbies';
		}

		if (empty($errors)) {

			session_start();
			$id = $_SESSION['id'];
			$name = $_SESSION['name'];
			$hobbies = $request->getParam('hobbies');

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique((int) $id);

			$user->setBio($request->getParam('bio'));
			$user->setSexuality($request->getParam('sexuality'));
			$user->setHobbies($hobbies);

			/* Save object user's profile to database in users table */
			$UserManagerPDO->save($user);

			/* Save object user's hobbies to database in hobbies table */
			$UserManagerPDO->addHobbies($user, $hobbies);
		}

		else {
			$this->flash('Un champ n\'a pas été rempli correctement', 'error');	
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'auth.signupinfos', 302);
		}
		unset($_SESSION['id']);
		return $this->redirect($response, 'home', 200);
	}

	public function getLogIn($request, $response) {

		return $this->render($response, 'pages/login.twig');
	}

	public function postLogIn($request, $response) {

		$errors = [];
		$name = $request->getParam('name');
		$password = $request->getParam('password');

		if (Validator::loginNameCheck($name, $this->db)) {
			if (Validator::isActive($name, $this->db)) {
				if (!Validator::passwordLogin($name, $password, $this->db)) {

					$UserManagerPDO = new UserManagerPDO($this->db);
					$id = $UserManagerPDO->getIdFromName($name);
					$user = $UserManagerPDO->getUnique($id);

					session_start();
					$_SESSION['user'] = serialize($user);
					setcookie("matcha_cookie", $_SESSION['user'], time() + 36000, "/");
				}

				else {
					$errors['password'] = 'Wrong password!';
				}
			}

			else {
				$errors['name'] = 'This account hasn\'t been activated yet.';
			}
		}

		else {
			$errors['name'] = 'This username doesn\'t exist.';
		}

		if (!empty($errors)) {
			$this->flash('Un champ n\'a pas été rempli correctement', 'error');	
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'auth.login', 302);
		}

		return $this->redirect($response, 'home', 200);
	}

	public function getLogOut($request, $response) {
		session_start();
		unset($_SESSION['user']);
		setcookie("matcha_cookie", null, -1, "/");
		session_destroy();
		return $this->redirect($response, 'home', 200);
	}
}