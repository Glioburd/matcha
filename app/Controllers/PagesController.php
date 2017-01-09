<?php

namespace App\Controllers;

use \Datetime;
use App\Controllers\Validator;
use App\Models\User;
use App\Controllers\Debug;
use App\Models\UserManagerPDO;
use \PDO;
use App\Models\Notification;
use App\Models\NotificationManager;

include __DIR__ . '../../../debug.php';
include "sort_array.php";
include "mails.php";

/**
*
*/
class PagesController extends Controller {

	const LOGIN_DOESNT_EXISTS = 0;

	public function home($request, $response) {

		$user = NULL;
		$data = '';
		$notifs = '';
		$nbUnread = '';



		if (Validator::isConnected()) {

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));

			if (empty($user)) {
				session_destroy();
			}

			// If user has a profile picture, we can display the matches!
			if ($user->mainpicture()){

				if(!(isset($_GET['distance'])) || empty($_GET['distance']) || $_GET['distance'] < 0)
					$distance = 1000;
				else {
					$distance = $_GET['distance'];
				}

				if(!(isset($_GET['ageMin'])) || empty($_GET['ageMin']) || $_GET['ageMin'] < 0)
					$ageMin = 18;

				else {
					$ageMin = $_GET['ageMin'];
				}

				if(!(isset($_GET['ageMax'])) || empty($_GET['ageMax']) || $_GET['ageMax'] < 0)
					$ageMax = 123;

				else {
					$ageMax = $_GET['ageMax'];
				}

				if (isset($_GET['min']) && $_GET['minPopularity'] >= 0){
					$minPopularity = $_GET['minPopularity'];
				}

				if (isset($_GET['minCommonHobbies']) && $_GET['minCommonHobbies'] >= 0) {
					$minCommonHobbies = $_GET['minCommonHobbies'];
				}

				$data = $UserManagerPDO->getMatches($user, $distance);

				$i = 0;

				foreach ($data as $key => $value) {

					$user_to_compare = $UserManagerPDO->getUnique($value['to_user_id']);
					$data[$key]['to_user_age'] = Validator::getAge($data[$key]['to_user_age']);

					if (($data[$key]['to_user_age'] >= $ageMin && $data[$key]['to_user_age'] <= $ageMax) && $data[$key]['popularity'] >= $minPopularity) {

							$hobbiesInCommon = $UserManagerPDO->countSimilarsHobbies($user, $user_to_compare);
							$data[$key]['hobbiesInCommon'] = $hobbiesInCommon;
							$data[$key]['hobbies'] = $user_to_compare->hobbies();
							if ($hobbiesInCommon < $minCommonHobbies) {
								unset($data[$key]);
							}
							echo ('<pre>Avec ' . $user_to_compare->login() . ' : ' . $hobbiesInCommon . '<br></pre>');
							$i++;

					}

					else {
						unset($data[$key]);
					}

				}

				if (!empty($_GET['sortBy']) && isset($_GET['sortBy'])) {
					switch ($_GET['sortBy']) {
						case 'age':
							$data = array_sort($data, 'to_user_age');
							break;
						case 'distance':
							$data = array_sort($data, 'distance_in_km');
							break;
						case 'popularity':
							$data = array_sort($data, 'popularity', SORT_DESC);
							break;
						case 'hobbiesInCommon':
							$data = array_sort($data, 'hobbiesInCommon', SORT_DESC);
						default:
							break;
					}
				}

				// echo $i;
				debug($_GET);

				$notificationManager = new NotificationManager($this->db);
				$notifs = $notificationManager->get($user);

				$i = 0;
				foreach ($notifs as $notif) {
					if ($notif->unread() == 1)
						$i++;
				}
				$nbUnread = $i;

			}

			else {

				$this->flash('
					You don\'t have a profile picture! Please set one to be able to get matched with other people.'
					,'warning');
			}

			if ($user->isComplete()) {
			$this->render($response, 'home.twig',[
				'user' => $user,
				'data' => $data,
				'sortBy' =>$_GET['sortBy'],
				'ageMin' =>$_GET['ageMin'],
				'ageMax' => $_GET['ageMax'],
				'distance' => $_GET['distance'],
				'minPopularity' => $_GET['minPopularity'],
				'minCommonHobbies' => $_GET['minCommonHobbies'],
				'notifs' => $notifs,
				'nbUnread' => $nbUnread
				]);
			}

			else {
				return $this->redirect($response, 'auth.signupinfos', 200);
			}


		Debug::debugUser($this->container, $user);
		Debug::debugUser($this->container, $_SESSION['id']);
		Debug::debugNotifs($this->container, $notifs);

		}
		else
			return $this->redirect($response, 'auth.login', 200);
	}

	public function getContact($request, $response) {

		$i = 0;

		if (Validator::isConnected()) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$notificationManager = new NotificationManager($this->db);
			$notifs = $notificationManager->get($user);

			foreach ($notifs as $notif) {
				if ($notif->unread() == 1)
					$i++;
			}
			$nbUnread = $i;

			return $this->render($response, 'pages/contact.twig', [
				'user' => $user,
				'notifs' => $notifs,
				'nbUnread' => $nbUnread
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
			$errors['lastname'] = 'The last name must contain only letters, no numbers or spaces allowed.';
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

		if (Validator::birthDayCheck($request->getParam('birthDate')) === INVALID_BIRTHDATE) {
			$errors['birthDate'] = 'Wrong date format';
		}

		elseif (Validator::birthDayCheck($request->getParam('birthDate')) === TOO_YOUNG) {
			$errors['birthDate'] = 'You must be at least 18 years old to enter this website';
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
				'birthDate' =>$request->getParam('birthDate'),
				]);

			$UserManagerPDO = new UserManagerPDO($this->db);
			$UserManagerPDO->save($user);
			$last_id = $this->db->lastInsertId();

			// $_SESSION['id'] = $last_id;
			// $_SESSION['login'] = $request->getParam('login');

		}

		else {
			$this->flash('A field hasn\'t been filled correctly.', 'error');
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'auth.signup', 302);
		}

			$this->flash('A mail has been sent to confirm your inscription.', 'info');
			return $this->redirect($response, 'auth.login', 200);
			// return $this->redirect($response, 'auth.signupinfos', 200);
	}

	public function getSignUpInfos($request, $response) {

		if (Validator::isConnected()) {
			$id = unserialize($_SESSION['id']);
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique($id);
			if (!($user->isComplete())) {
				return $this->render($response, 'pages/signUpInfos.twig', [
					'user' => $user
					]);
			}
			else {
				$this->flash('You can\'t access this page.', 'error');
				return $response->withRedirect($this->router->pathFor('user.profile', ['userprofile' => $user->login()]));
			}
		}

		else {
			$this->flash('You must be logged to access this page.', 'error');
			return $this->redirect($response, 'auth.signup', 302);
		}
	}

	public function postSignUpInfos($request, $response) {

		$errors = [];

		// debug($request->getParams());
		// die();

		if (!Validator::bioLengthCheck($request->getParam('bio'))) {
			$errors['bio'] = 'Your description must contain at least 20 characters. Don\'t be shy!';
		}

		if (!Validator::radioCheck($request->getParam('gender'))) {
			$errors['gender'] = 'You must pick a gender';
		}

		if (!Validator::hobbiesCheck($request->getParam('hobbies'))) {
			$errors['hobbies'] = 'You must check at least 4 hobbies';
		}

		if (empty($errors)) {

			$id = unserialize($_SESSION['id']);
			$hobbies = $request->getParam('hobbies');

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique((int) $id);

			// An user object is set with all the params, and will be registered in database with save()
			$user->setBio($request->getParam('bio'));

			if (!Validator::radioCheck($request->getParam('sexuality'))) {
				$user->setSexuality('bi');
			}
			else {
				$user->setSexuality($request->getParam('sexuality'));
			}

			$user->setGender($request->getParam('gender'));
			$user->setHobbies($hobbies);

			// If user allowed geolocalisation, let's register the coordonates
			if ($request->getParam('latitude') && $request->getParam('longitude')) {
				$latitude = floatval($request->getParam('latitude'));
				$longitude = floatval($request->getParam('longitude'));
				// debug($latitude);
				// debug($longitude);
				// die();

				$user->setCoordonates($latitude, $longitude);
				$user->setMap($request->getParam('map'));
				// $user->setCity($request->getParam('city'));
			}

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

		// unset($_SESSION['id']);
		return $this->redirect($response, 'home', 200);
	}

	public function getLogIn($request, $response) {

		$user = '';

		if (!Validator::isConnected()) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			Debug::debugUser($this->container, $user);
			return $this->render($response, 'pages/login.twig');
		} else {
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

	public function getForgotPwd($request, $response) {
		$user = '';

		if (!Validator::isConnected()) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			Debug::debugUser($this->container, $user);
			return $this->render($response, 'pages/forgotpwd.twig');
		} else {
			return $this->redirect($response, 'home', 200);
		}			
	}

	public function postForgotPwd($request, $response) {
		if (!Validator::isConnected()) {

			$errors = [];
			$email = $request->getParam('email');

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors['email'] = 'E-mail adress is invalid.';
			}

			else {
				$UserManagerPDO = new UserManagerPDO($this->db);
				$user = $UserManagerPDO->getUserFromEmail($email);

				if (!empty($user)) {
					$user->setHash(md5(uniqid(rand(), true)));
					$UserManagerPDO->save($user);
					confirmResetPwd($email, $user->hash());
				}
			}
		}

		if (!empty($errors)) {
			$this->flash('A field hasn\'t been filled correctly', 'error');
			return $this->redirect($response, 'forgotpwd', 302);
		}
		else {
			$this->flash('A mail has been sent to confirm the password change request.', 'info');
			return $this->redirect($response, 'auth.login', 200);
		}

	}

	public function getNewPwd($request, $response) {
		$user = '';

		if (!Validator::isConnected() && $_GET['email'] && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
			$UserManagerPDO = new UserManagerPDO($this->db);

			$email = $_GET['email'];
			$_SESSION['email'] = $email;
			$hash = $_GET['hash'];
			$_SESSION['hash'] = $hash;
			$user = $UserManagerPDO->getUserFromEmail($email);

			if ($user && $user->hash() === $_GET['hash']){
				return $this->render($response, 'pages/newpwd.twig');
			}

			else {
				$this->flash('Invalid link.', 'error');
				return $this->redirect($response, 'auth.login', 302);
			}
		} 

		else {
			return $this->redirect($response, 'home', 200);
		}	
	}

	public function postNewPwd($request, $response) {
		if (!Validator::isConnected()) {

			$UserManagerPDO = new UserManagerPDO($this->db);
			$email = $_SESSION['email'];
			$hash = $_SESSION['hash'];
			$user = $UserManagerPDO->getUserFromEmail($email);
			$newPassword = $request->getParam('newPassword');
			$newPasswordConfirm = $request->getParam('newPasswordConfirm');
			$errors = [];

			if ($newPassword != $newPasswordConfirm) {
				$errors['newPasswordConfirm'] = 'Your password doesn\'t match with the confirmation.';
			}

			switch (Validator::passwordCheck($newPassword)) {
				case 1:
					$errors['newPassword'] = 'Password too short : minimum 6 characters.';
					break;
				case 2:
					$errors['newPassword'] = 'Password must contain at least 1 number.';
					break;
				case 3:
					$errors['newPassword'] = 'Password must contain at least 1 letter.';
					break;
			}

			if (!empty($errors)) {

				$this->flash('A field hasn\'t been filled correctly.', 'error');
				$this->flash($errors, 'errors');
				return $response->withRedirect('newpwd' . '?' . 'email=' . $email . '&hash=' . $hash);
			}

			else {
				$user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
				$UserManagerPDO->save($user);

				$this->flash('Your password has been changed ☺.', 'success');
				return $this->redirect($response, 'auth.login', 200);
			}
		}
	}

	public function getLogOut($request, $response) {
		unset($_SESSION['id']);
		setcookie("matcha_cookie", null, -1, "/");
		session_destroy();
		return $this->redirect($response, 'home', 200);
	}

	public function getProfile($request, $response, $args) {

		$canLike = '';
		$canBlock = '';
		$mutualFriend = '';

		// If user is loged in, and if there is an arg in /profile/
		if (Validator::isConnected() && isset($args['userprofile']) && !empty($args['userprofile'])) {

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			// If user profile is complete (hobbies, bio, sexuality...)
			if ($user->isComplete()) {
				$notificationManager = new NotificationManager($this->db);
				$notifs = $notificationManager->get($user);

				// We add a '../' to images src, because we are one step deeper in the tree : /profile/{name}
				$i = 0;
				foreach ($notifs as $notif) {
					$notif->setPictureSender('../' . $notif->pictureSender());
					if ($notif->unread() == 1)
						$i++;
				}
				$nbUnread = $i;
				$userprofilearg = $args['userprofile'];

				// Check if arg profile exists
				if ($idprofile = $UserManagerPDO->getIdFromLogin($userprofilearg)) {

					$userProfile = $UserManagerPDO->getUnique($idprofile);

					// If arg profile is an other user
					if ($user->id() != $userProfile->id()) {

						//Are we mutual friends with the user ?
						$mutualFriend = $UserManagerPDO->mutualFriendlist($user, $userProfile);

						$idLike = $UserManagerPDO->addVisit($user->id(), $userProfile->id());
						$canLike = $UserManagerPDO->canLike($user->id(), $userProfile->id());

						// Have I blocked the user or does the user have blocked me ?
						$canBlock = $UserManagerPDO->canBlock($user->id(), $userProfile->id());
						$amINotBlocked = $UserManagerPDO->canBlock($userProfile->id(), $user->id());

						if ($canBlock && $amINotBlocked) {
							$notification = new Notification([
								'owner' => $userProfile->id(),
								'sender' => $user->id(),
								'unread' => TRUE,
								'type' => "visit",
								'referenceId' => $idLike["LAST_INSERT_ID()"]
								]);

							$notificationManager = new NotificationManager($this->db);
							$notificationManager->add($notification);
						}
					}

					// If arg profile is user him/herself
					else {
						$visits = $UserManagerPDO->getVisits($userProfile->id());
						$likes = $UserManagerPDO->getLikes($userProfile->id());
						$eventsHistory = $UserManagerPDO->mergeVisitsLikes($visits, $likes);
					}

					$age = Validator::getAge($userProfile->Birthdate());

					if (!$user->mainpicture()) {
						$this->flash('
							You don\'t have a profile picture! Please set one to be able to get matched with other people.'
							,'warning');
					}

					Debug::debugUsers($this->container, $user, $userProfile);
					Debug::debugNotifs($this->container, $notifs);

					return $this->render($response, 'pages/profile.twig',[
						'userprofile' => $userProfile,
						'user' => $user,
						'eventsHistory' => $eventsHistory,
						'canLike' => $canLike,
						'canBlock' => $canBlock,
						'age' => $age,
						'notifs' => $notifs,
						'nbUnread' => $nbUnread,
						'mutualFriend' => $mutualFriend
					]);

				}

				else {
					//Arg profile doesn't exist
					echo 'doesnt exists'; // 404, a modifier + tard
				}
			}

			// If profile is not incomplete (hobbies, bio, sexuality...)
			else {
				return $this->redirect($response, 'auth.signupinfos', 200);
			}

		}

		else {
			// If user is not loged in
			$this->flash('You must be logged to access this page.', 'error');
			return $this->redirect($response, 'auth.login', 302);
		}
	}

	public function getSettings($request, $response) {

		if (Validator::isConnected()) {

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$blockedUsers = $UserManagerPDO->getBlockedUsers($user->id());
			$notificationManager = new NotificationManager($this->db);
			$notifs = $notificationManager->get($user);

			$i = 0;

			foreach ($notifs as $notif) {
				if ($notif->unread() == 1)
					$i++;
			}
			$nbUnread = $i;

			foreach($blockedUsers as $key => $blockedUser) {
				if ($blockedUser) {
					$blockedUsers[$key] = $UserManagerPDO->getUnique($blockedUser['id_blocked']);
				}
			}

			return $this->render($response, 'pages/settings.twig',[
				'user' => $user,
				'blockedUsers' => $blockedUsers,
				'notifs' => $notifs,
				'nbUnread' => $nbUnread
			]);
		}

		else {
			$this->flash('You must be logged to access this page.', 'error');
			return $this->redirect($response, 'auth.login', 302);
		}
	}

	public function PostSettings($request, $response) {

		if (Validator::isConnected()) {
			$errors = [];

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));

			$oldPassword = $request->getParam('oldPassword');
			$newPassword = $request->getParam('newPassword');
			$newPasswordConfirm = $request->getParam('newPasswordConfirm');
			$newMail = $request->getParam('email');

			if (!empty($oldPassword) || !empty($newPassword) || !empty($newPasswordConfirm)) {

				if (!password_verify($oldPassword, $user->password())) {
					$errors['oldPassword'] = 'Wrong old password.';
				}

				else {
					switch (Validator::passwordCheck($newPassword)) {
						case 1:
							$errors['newPassword'] = 'Password too short : minimum 6 characters.';
							break;
						case 2:
							$errors['newPassword'] = 'Password must contain at least 1 number.';
							break;
						case 3:
							$errors['newPassword'] = 'Password must contain at least 1 letter.';
							break;
					}
				}

				if (!Validator::passwordConfirm($newPassword, $newPasswordConfirm)) {
					$errors['newPasswordConfirm'] = 'Invalid password confirmation';
				}
			}

			if ($newMail != $user->email()) {

				if (Validator::mailCheck($newMail, $this->container->db) === INVALID_EMAIL) {
					$errors['email'] = 'E-mail adress is invalid.';
				}

				elseif (Validator::mailCheck($newMail, $this->container->db) === EMAIL_ALREADY_EXISTS) {
					$errors['email'] = 'E-mail is already used.';
				}

				$user->setHash(md5(uniqid(rand(), true)));
				$UserManagerPDO->save($user);
				$success = 'An e-mail has been sent to confirm the new e-mail.';
				$type = 'info';
				mailResetPwd($newMail, $user->hash());

			}
		}

		if (!empty($errors)) {
			$this->flash('A field hasn\'t been filled correctly.', 'error');
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'user.settings', 302);
		}

		if (!empty($newPassword) && empty($errors)) {
			$user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
		}

		$user->setEmail($newMail);
		$UserManagerPDO->save($user);

		$success = 'Your settings have been updated ☺';
		$type = 'success';	
		$this->flash($success, $type);
		return $this->redirect($response, 'user.settings', 200);
	}

	public function getChangeMail($request, $response, $args) {

		if (Validator::isConnected() && isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$email = $_GET['email'];
			$hash = $_GET['hash'];

			if ($hash != $user->hash()) {
				$this->flash("Your mail hasn't been updated, the link was incorrect or expired.", 'error');
				return $this->redirect($response, 'home', 302);
			}

			$user->setEmail($email);
			$UserManagerPDO->save($user);
			$this->flash('Your e-mail has been succesfully updated ☺', 'success');
			return $this->redirect($response, 'home', 200);
		}

		else{
			$this->flash('You can\'t access this page like that.', 'error');
			return $this->redirect($response, 'home', 302);
		}
	}

	public function getEdit($request, $response) {

		if (Validator::isConnected()) {

			// $user = unserialize($_SESSION['user']);
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$notificationManager = new NotificationManager($this->db);
			$notifs = $notificationManager->get($user);

			$i = 0;

			foreach ($notifs as $notif) {
				if ($notif->unread() == 1)
					$i++;
			}
			$nbUnread = $i;

			if (!$user->mainpicture()) {
				$this->flash('
				You don\'t have a profile picture! Please set one to be able to get matched with other people.','warning');
			}

			Debug::debugUser($this->container, $user);
			return $this->render($response, 'pages/editProfile.twig',[
				'user' => $user,
				'notifs' => $notifs,
				'nbUnread' => $nbUnread
			]);
		}

		else {
			$this->flash('You must be logged to access this page.', 'error');
			return $this->redirect($response, 'auth.login', 302);
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

		if ($request->getParam('latitude') && $request->getParam('longitude')) {
				$latitude = floatval($request->getParam('latitude'));
				$longitude = floatval($request->getParam('longitude'));

				$user->setCoordonates($latitude, $longitude);
				$user->setMap(1);

			}

		if (empty($errors)) {

			$user->setBio($request->getParam('bio'));
			$user->setLogin($request->getParam('login'));
			$user->setEmail($request->getParam('email'));
			$user->setGender($request->getParam('gender'));
			$user->setSexuality($request->getParam('sexuality'));
			$user->updateHobbies($request->getParam('hobbies'));

			$UserManagerPDO = new UserManagerPDO($this->db);
			$UserManagerPDO->save($user);
			$UserManagerPDO->updateHobbies($user, $request->getParam('hobbies'));
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
		$UserManagerPDO = new UserManagerPDO($this->db);
		$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
		$errors = [];
		// $target_dir = '../../matcha/uploads/' . $user->id() . '/';
		$target_dir = '../uploads/' . $user->id() . '/';
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if image file is a actual image or fake image

		if(!empty($request->getParam('submit'))) {

			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$errors['imageupload'] = "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			}

			else {
				$errors['imageupload'] = "File is not an image.";
				$uploadOk = 0;
			}
		}

		if (!file_exists($target_dir)) {
			if (!mkdir($target_dir, 0777)) {
				$errors['imageupload'] =  $target_dir;
				$uploadOk = 0;
			}
		}

		if (file_exists($target_file)) {
			$errors['imageupload'] =  "Sorry, file already exists.";
			$uploadOk = 0;
		}

		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 5 * MB) {
			$errors['imageupload'] =  "Sorry, your file is too large.";
			$uploadOk = 0;
		}

		// Allow certain file formats

		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			$errors['imageupload'] =  "Only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}

		$count = $UserManagerPDO->countPictures($user);

		if ($count >= 5) {
			$errors['imageupload'] =  "Sorry, max 5 pictures allowed";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error

		if ($uploadOk == 0) {
			$this->flash("Sorry, your file was not uploaded.", 'error');
			$this->flash($errors, 'errors');
			return $this->redirect($response, 'user.edit', 302);

		// if everything is ok, try to upload file

		}

		else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$this->flash("The file ". basename($_FILES["fileToUpload"]["name"]). " has been uploaded ☺.");

				$UserManagerPDO->addPicture($target_file, $user);
				return $this->redirect($response, 'user.edit', 200);
			} else {
				$errors['imageupload'] =  "Sorry, there was an error uploading your file.";
				$this->flash($errors, 'errors');
				return $this->redirect($response, 'user.edit', 302);
			}
		}
	}

	public function postDeletePicture($request, $response) {

		if (Validator::isConnected() && !empty($request->getParams())) {

			$errors = [];
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$imgSrc = $request->getParam('deletePicture');

			$basenameSrc = basename($imgSrc);
			$target_dir = getcwd() . '/../uploads/' . $user->id();

			if (!file_exists($target_dir . '/old')){
				if (!mkdir($target_dir . '/old', 0777)) {
					$errors['image'] = 'An error occured when trying to delete image.';
					$this->flash($errors, 'errors');
					return $this->redirect($response, 'user.edit', 302);
				}
			}

			if (!rename($target_dir . '/' . $basenameSrc, $target_dir . '/old/' . $basenameSrc)) {
				$errors['image'] = 'An error occured when trying to delete image.';
				$this->flash($errors, 'errors');
				return $this->redirect($response, 'user.edit', 302);
			}

			$idPic = $UserManagerPDO->getIdFromPicSrc($imgSrc);

			$UserManagerPDO->deletePicture($idPic, $user);
			return $this->redirect($response, 'user.edit', 200);
		}

		else
			echo 'huh?';
	}

	public function postChangeAvatar($request, $response) {
		if (Validator::isConnected() && !empty($request->getParams())) {
			$errors = [];
			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$imgSrc = $request->getParam('changePicture');
			$idPic = $UserManagerPDO->getIdFromPicSrc($imgSrc);
			$UserManagerPDO->setMainPicture($idPic, $user);
			return $this->redirect($response, 'user.edit', 200);

		}
	}

	public function postLike($request, $response) {

		if (Validator::isConnected() && !empty($request->getParam('likeButton'))) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			$id_liker = unserialize($_SESSION['id']);
			$id_liked = $request->getParam('likeButton');
			$user = $UserManagerPDO->getUnique($id_liker);
			$userProfile = $UserManagerPDO->getUnique($id_liked);

			$hasUserProfileHasLikedMe = $UserManagerPDO->hasLiked($user, $userProfile);
			$canBlock = $UserManagerPDO->canBlock($user->id(), $userProfile->id());
			$amINotBlocked = $UserManagerPDO->canBlock($userProfile->id(), $user->id());
			$UserManagerPDO->like($id_liker, $id_liked);

				if ($canBlock && $amINotBlocked) {
					if ($hasUserProfileHasLikedMe) {
						$type = 'likeback';
					}
					else {
						$type = 'like';
					}

				$notification = new Notification([
					'owner' => $userProfile->id(),
					'sender' => $user->id(),
					'unread' => TRUE,
					'type' => $type,
					'referenceId' => $idLike["LAST_INSERT_ID()"]
					]);

				$notificationManager = new NotificationManager($this->db);
				$notificationManager->add($notification);
			}

			return $response->withRedirect($this->router->pathFor('user.profile', ['userprofile' => $userProfile->login()]));
		}
		else {
			echo 'huh?';
		}
	}

	public function postUnlike($request, $response) {

		if (Validator::isConnected() && !empty($request->getParams())) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			$id_unliker = unserialize($_SESSION['id']);
			$id_unliked = $request->getParam('unlikeButton');
			$user = $UserManagerPDO->getUnique($id_unliker);
			$UserManagerPDO->unlike($id_unliker, $id_unliked);
			$userProfile = $UserManagerPDO->getUnique($id_unliked);

			$notification = new Notification([
				'owner' => $userProfile->id(),
				'sender' => $user->id(),
				'unread' => TRUE,
				'type' => "unlike",
				'referenceId' => $idLike["LAST_INSERT_ID()"]
				]);

			$notificationManager = new NotificationManager($this->db);
			$notificationManager->add($notification);

			return $response->withRedirect($this->router->pathFor('user.profile', ['userprofile' => $userProfile->login()]));
		}
		else {
			echo 'Huh?';
		}
	}

	public function postBlockUser($request, $response) {

		if (Validator::isConnected() && !empty($request->getParam('blockButton'))) {
				$UserManagerPDO = new UserManagerPDO($this->db);
				$id_blocker = unserialize($_SESSION['id']);
				$id_blocked = $request->getParam('blockButton');
				$UserManagerPDO->block($id_blocker, $id_blocked);
				$user = $UserManagerPDO->getLoginFromId($id_blocked);

				return $response->withRedirect($this->router->pathFor('user.profile', ['userprofile' => $user]));
			}
			else {
				echo 'huh?';
			}
	}

	public function postUnblockUser($request, $response) {

		if (Validator::isConnected() && !empty($request->getParam('unblockButton'))) {
			$UserManagerPDO = new UserManagerPDO($this->db);
			$id_unblocker = unserialize($_SESSION['id']);
			$id_unblocked = $request->getParam('unblockButton');
			$UserManagerPDO->unblock($id_unblocker, $id_unblocked);
			$user = $UserManagerPDO->getLoginFromId($id_unblocked);
			return $response->withRedirect($this->router->pathFor('user.profile', ['userprofile' => $user]));
		}
		else {
			echo 'huh?';
		}
	}

	public function postUnblockUserFromSettings($request, $response) {
		$UserManagerPDO = new UserManagerPDO($this->db);
		$id_unblocker = unserialize($_SESSION['id']);
		$id_unblocked = $_POST['postid'];
		if ($UserManagerPDO->unblock($id_unblocker, $id_unblocked)){
			return 'ok';
		}
		else {
			return false;
		}
	}

	public function postNotifsRead($request, $response) {
		$idUser = unserialize($_SESSION['id']);
		$NotificationManager = new NotificationManager($this->db);
		$NotificationManager->setAllNotifsAsRead($idUser);
	}

	public function postCountNotifsUnread($request, $response) {
		$idUser = unserialize($_SESSION['id']);
		$NotificationManager = new NotificationManager($this->db);
		return $count = $NotificationManager->countUnread($idUser);

		return $count;
	}

}
