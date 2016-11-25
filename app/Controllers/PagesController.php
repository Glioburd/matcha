<?php

namespace App\Controllers;

use \Datetime;
use App\Controllers\Validator;
use App\Models\User;
use App\Controllers\Debug;
use App\Models\UserManagerPDO;
use \PDO;

include __DIR__ . '../../../debug.php';

/**
* 
*/
class PagesController extends Controller {

	const LOGIN_DOESNT_EXISTS = 0;

	public function home($request, $response) {

		if (Validator::isConnected()) {

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$this->render($response, 'home.twig',[
				'user' => $user
				]);

		Debug::debugUser($this->container, $user);

		}
		else
			return $this->redirect($response, 'auth.login', 200);

	}

	public function getContact($request, $response) {

		if (Validator::isConnected()) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			return $this->render($response, 'pages/contact.twig', [
				'user' => $user
				]);
		}
		else {
			return $this->redirect($response, 'auth.login', 200);
		}
	}

	public function postContact($request, $response){

	}

	public function getSignUp($request, $response) {
		if (!Validator::isConnected()) {
			return $this->render($response, 'pages/signUp.twig');
		}
		else {
			return $this->redirect($response, 'home', 200);	
		}
	}

	public function postSignUp($request, $response){

		$errors = [];

		if (!Validator::loginLengthCheck($request->getParam('login'))) {
			$errors['login'] = 'Your username must contain between 2 and 32 characters.';
		}

		if (!Validator::nameCheck($request->getParam('firstname'))) {
			$errors['firstname'] = 'The first name must contain only letters, no numbers or spaces allowed.';
		}

		if (!Validator::nameCheck($request->getParam('lastname'))) {
			$errors['firstname'] = 'The last name must contain only letters, no numbers or spaces allowed.';
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
				'login' => $request->getParam('login'),
				'email' => $request->getParam('email'),
				'firstName' => $request->getParam('firstname'),
				'lastName' => $request->getParam('lastname'),
				'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
				]);

			$UserManagerPDO = new UserManagerPDO($this->db);
			$UserManagerPDO->save($user);
			$last_id = $this->db->lastInsertId();

			$_SESSION['id'] = $last_id;
			$_SESSION['login'] = $request->getParam('login');

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

		if (!Validator::radioCheck($request->getParam('sexuality'))) {
			$errors['sexuality'] = 'You must pick a sexual orientation';
		}

		if (!Validator::radioCheck($request->getParam('gender'))) {
			$errors['gender'] = 'You must pick a gender';
		}

		if (!Validator::hobbiesCheck($request->getParam('hobbies'))) {
			$errors['hobbies'] = 'You must check at least 4 hobbies';
		}

		if (empty($errors)) {

			$id = $_SESSION['id'];
			$login = $_SESSION['login'];
			$hobbies = $request->getParam('hobbies');

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique((int) $id);

			$user->setBio($request->getParam('bio'));
			$user->setSexuality($request->getParam('sexuality'));
			$user->setGender($request->getParam('gender'));
			$user->setHobbies($hobbies);

			/* Save object user's profile to database in users table */
			$UserManagerPDO->save($user);

			/* Save object user's hobbies to database in hobbies table */
			$UserManagerPDO->addExtras($user, $hobbies);


		}

		else {
			$this->flash('Un champ n\'a pas été rempli correctement', 'error');	
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'auth.signupinfos', 302);
		}

		unset($_SESSION['id']);
		unset($_SESSION['login']);
		return $this->redirect($response, 'home', 200);
	}

	public function getLogIn($request, $response) {

		if (!Validator::isConnected()) {
			Debug::debugUser($this->container, $user);
			return $this->render($response, 'pages/login.twig');
		}
		else {
			return $this->redirect($response, 'home', 200);				
		}

	}

	public function postLogIn($request, $response) {

		$errors = [];
		$login = $request->getParam('login');
		$password = $request->getParam('password');

		if (Validator::loginCheck($login, $this->db)) {
			if (Validator::isActive($login, $this->db)) {
				if (Validator::passwordLogin($login, $password, $this->db)) {

					$UserManagerPDO = new UserManagerPDO($this->db);

					$id = $UserManagerPDO->getIdFromLogin($login);

					$user = $UserManagerPDO->getUnique($id);

					$UserManagerPDO->updateLastSeen($user);					

					$_SESSION['id'] = serialize($id);
					setcookie("matcha_cookie", $_SESSION['id'], time() + 36000, "/");
				}

				else {
					$errors['password'] = 'Wrong password!';
				}
			}

			else {
				$errors['login'] = 'This account hasn\'t been activated yet.';
			}
		}

		else {
			$errors['login'] = 'This username doesn\'t exist.';
		}

		if (!empty($errors)) {
			$this->flash('Un champ n\'a pas été rempli correctement', 'error');	
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'auth.login', 302);
		}

		return $this->redirect($response, 'home', 200);
	}

	public function getLogOut($request, $response) {
		unset($_SESSION['user']);
		setcookie("matcha_cookie", null, -1, "/");
		session_destroy();
		return $this->redirect($response, 'home', 200);
	}

	public function getProfile($request, $response, $args) {

		if (Validator::isConnected()) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$userprofilearg = $args['userprofile'];
	

			if ($idprofile = $UserManagerPDO->getIdFromLogin($userprofilearg)) {

				$userProfile = $UserManagerPDO->getUnique($idprofile);
				
				if ($user->id() != $userProfile->id()) {
					$UserManagerPDO->addVisit($user->id(), $userProfile->id());
				}

				else {
					$visits = $UserManagerPDO->getVisits($userProfile->id());
				}

				Debug::debugUsers($this->container, $user, $userProfile);

				return $this->render($response, 'pages/profile.twig',[
					'userprofile' => $userProfile,
					'user' => $user,
					'visits' => $visits
				]);


			}

			else {

				echo 'doesnt exists'; // a modifier + tard
			}

		}

		else {
			return $this->redirect($response, 'auth.login', 200);	
		}
	}

	public function getSettings($request, $response) {

		if (Validator::isConnected()) {

			// $user = unserialize($_SESSION['user']);
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			return $this->render($response, 'pages/settings.twig',[
				'user' => $user
			]);
		}

		else {

			return $this->redirect($response, 'auth.login', 200);
		}
	}

	public function getEdit($request, $response) {

		if (Validator::isConnected()) {

			// $user = unserialize($_SESSION['user']);
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			return $this->render($response, 'pages/editProfile.twig',[
				'user' => $user
			]);
		}

		else {

			return $this->redirect($response, 'auth.login', 200);
		}
	}

	public function postEdit($request, $response) {

		$errors = [];
		// $user = unserialize($_SESSION['user']);
		$UserManagerPDO = new UserManagerPDO($this->db);
		$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));

		if ($request->getParam('login') != $user->login() && !Validator::loginAvailability($request->getParam('login'), $this->db)) {
			$errors['login'] = 'Username already used.';
		}

		if (!Validator::loginLengthCheck($request->getParam('login'))) {
			$errors['login'] = 'Your username must contain between 2 and 32 characters.';
		}

		if ($request->getParam('email') != $user->email()) {

			if (Validator::mailCheck($request->getParam('email'), $this->container->db) === INVALID_EMAIL) {
				$errors['email'] = 'E-mail adress is invalid.';
			}

			elseif (Validator::mailCheck($request->getParam('email'), $this->container->db) === EMAIL_ALREADY_EXISTS) {
				$errors['email'] = 'E-mail is already used.';
			}
		}	

		if (!Validator::bioLengthCheck($request->getParam('bio'))) {
			$errors['bio'] = 'Your description must contain at least 20 characters. Don\'t be shy!';
		}

		if (!Validator::radioCheck($request->getParam('sexuality'))) {
			$errors['sexuality'] = 'You must pick a sexual orientation';
		}

		if (!Validator::radioCheck($request->getParam('gender'))) {
			$errors['gender'] = 'You must pick a gender';
		}

		if (!Validator::hobbiesCheck($request->getParam('hobbies'))) {
			$errors['hobbies'] = 'You must pick at least 4 hobbies';
		}

		if (empty($errors)) {

			$user->setBio($request->getParam('bio'));
			$user->setLogin($request->getParam('login'));
			$user->setEmail($request->getParam('email'));
			$user->setGender($request->getParam('gender'));
			$user->updateHobbies($request->getParam('hobbies'));

			$UserManagerPDO = new UserManagerPDO($this->db);
			$UserManagerPDO->save($user);
			$UserManagerPDO->updateHobbies($user, $request->getParam('hobbies'));
			// $_SESSION['user'] = serialize($user);
		}

		else {
			$this->flash('One field has not been filled correctly ☹', 'error');	
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'user.edit', 302);
		}

		$this->flash('Your informations have been succesfully updated ☺', 'success');	
		return $this->redirect($response, 'user.edit', 200);
	}

	public function postUploadPicture($request, $response) {

		define('MB', 1048576);
		// $user = unserialize($_SESSION['user']);
		$UserManagerPDO = new UserManagerPDO($this->db);
		$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
		$errors = [];
		// $target_dir = __DIR__ . '/../../uploads/' . $user->id() . '/';
		$target_dir = '../../matcha/uploads/' . $user->id() . '/';
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if image file is a actual image or fake image

		if(!empty($request->getParam('submit'))) {

			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$errors['image'] = "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			}

			else {
				$errors['image'] = "File is not an image.";
				$uploadOk = 0;
			}
		}


		if (!file_exists($target_dir)) {
			if (!mkdir($target_dir)) {
				// $errors['image'] =  "An error occured when making your gallery folder.";
				$errors['image'] =  $target_dir;

				$uploadOk = 0;
			}
		}

		if (file_exists($target_file)) {
			$errors['image'] =  "Sorry, file already exists.";
			$uploadOk = 0;
		}

		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 5 * MB) {
			$errors['image'] =  "Sorry, your file is too large.";
			$uploadOk = 0;
		}

		// Allow certain file formats

		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			$errors['image'] =  "Only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error

		if ($uploadOk == 0) {
			$this->flash("Sorry, your file was not uploaded.", 'error');
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'user.edit', 302);

		// if everything is ok, try to upload file

		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$this->flash("The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded ☺.");

				$UserManagerPDO->addPicture($target_file, $user);
				// $_SESSION['user'] = serialize($UserManagerPDO->getUnique($user->id()));
				return $this->redirect($response, 'user.edit', 200);
			} else {
				$errors['image'] =  "Sorry, there was an error uploading your file.";
				$this->flash($errors, 'errors');
				return $this->redirect($response, 'user.edit', 302);
			}
		}
	}

	public function postLike($request, $response) {
		if (Validator::isConnected() && !empty($request->getParams())) {
			$UserManagerPDO = new UserManagerPDO;
			$id = unserialize($_SESSION['id']);
			$user = $UserManagerPDO->getUnique($id);
			
		}
		else {
			echo 'Huh?';
		}
	}

}