<?php

namespace App\Controllers;

use \Datetime;
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

		if (Validator::isConnected()) {

			$user = unserialize($_SESSION['user']);
			$this->render($response, 'home.twig',[
				'user' => $user
				]);

			if ($this->container->debug) {
				echo "<pre><h2>USER:</h2></pre>";
				debug($user);
			}

		}
		else
			return $this->redirect($response, 'auth.login', 200);

	}

	public function getContact($request, $response) {

		if (Validator::isConnected()) {
			$user = unserialize($_SESSION['user']);
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
			$name = $_SESSION['name'];
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
			$UserManagerPDO->addHobbies($user, $hobbies);
		}

		else {
			$this->flash('Un champ n\'a pas été rempli correctement', 'error');	
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'auth.signupinfos', 302);
		}

		unset($_SESSION['id']);
		unset($_SESSION['name']);
		// debug($request->getParams());
		return $this->redirect($response, 'home', 200);
	}

	public function getLogIn($request, $response) {
		if ($this->container->debug) {
			echo "USER:<br>";
			debug($user);
		}

		if (!Validator::isConnected()) {
			return $this->render($response, 'pages/login.twig');
		}
		else {
			return $this->redirect($response, 'home', 200);				
		}

	}

	public function postLogIn($request, $response) {

		$errors = [];
		$name = $request->getParam('name');
		$password = $request->getParam('password');

		if (Validator::loginNameCheck($name, $this->db)) {
			if (Validator::isActive($name, $this->db)) {
				if (Validator::passwordLogin($name, $password, $this->db)) {

					$UserManagerPDO = new UserManagerPDO($this->db);

					$id = $UserManagerPDO->getIdFromName($name);

					$user = $UserManagerPDO->getUnique($id);

					$UserManagerPDO->updateLastSeen($user);					

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
		unset($_SESSION['user']);
		setcookie("matcha_cookie", null, -1, "/");
		session_destroy();
		return $this->redirect($response, 'home', 200);
	}

	public function getProfile($request, $response, $args) {

		if (Validator::isConnected()) {

			$user = unserialize($_SESSION['user']);
			$userprofilearg = $args['userprofile'];
			$UserManagerPDO = new UserManagerPDO($this->db);

			if ($idprofile = $UserManagerPDO->getIdFromName($userprofilearg)) {

				$userprofile = $UserManagerPDO->getUnique($idprofile);
				
				if ($this->container->debug) {
					echo "<pre><h2>USERPROFILE:" . $userprofile->name() . "</h2></pre>";
					debug($userprofile);
					echo "<pre><h2>USER:" . $user->name() . "</h2></pre>";
					debug($user);
				}

				return $this->render($response, 'pages/profile.twig',[
					'userprofile' => $userprofile,
					'user' => $user
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

			$user = unserialize($_SESSION['user']);
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

			$user = unserialize($_SESSION['user']);
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
		$user = unserialize($_SESSION['user']);

		if ($request->getParam('name') != $user->name() && !Validator::nameAvailability($request->getParam('name'), $this->db)) {
			$errors['name'] = 'Username already used.';
		}

		if (!Validator::nameLengthCheck($request->getParam('name'))) {
			$errors['name'] = 'Your username must contain between 2 and 32 characters.';
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
			$user->setName($request->getParam('name'));
			$user->setEmail($request->getParam('email'));
			$user->setGender($request->getParam('gender'));
			$user->updateHobbies($request->getParam('hobbies'));

			$UserManagerPDO = new UserManagerPDO($this->db);
			$UserManagerPDO->update($user);
			$UserManagerPDO->updateHobbies($user, $request->getParam('hobbies'));
			$_SESSION['user'] = serialize($user);
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
		$user = unserialize($_SESSION['user']);
		$errors = [];
		$target_dir = __DIR__ . '/../../uploads/' . $user->id() . '/';
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
				$UserManagerPDO = new UserManagerPDO($this->db);
				$UserManagerPDO->addPicture($target_file, $user);
				$_SESSION['user'] = serialize($UserManagerPDO->getUnique($user->id()));
				return $this->redirect($response, 'user.edit', 200);
			} else {
				$errors['image'] =  "Sorry, there was an error uploading your file.";
				$this->flash($errors, 'errors');
				return $this->redirect($response, 'user.edit', 302);
			}
		}
	}

}