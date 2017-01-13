<?php

namespace App\Controllers;

use \Datetime;

use App\Models\User;
use App\Controllers\Validator;
use App\Controllers\Debug;
use App\Models\UserManagerPDO;
use \PDO;
use App\Models\Notification;
use App\Models\NotificationManager;

include __DIR__ . '../../../debug.php';

/**
* 
*/
class ChatController extends Controller {

	public function getChat($request, $response, $args) {
		if (Validator::isConnected() && isset($args['interlocutor']) && !empty($args['interlocutor'])) {

			$UserManagerPDO = new UserManagerPDO($this->db);
			$user = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));
			$interlocutor = $UserManagerPDO->getIdfromLogin($args['interlocutor']);
			$interlocutor = $UserManagerPDO->getUnique($interlocutor);

			// Check if we are mutual friends
			$mutualFriend = $UserManagerPDO->mutualFriendlist($user, $interlocutor);

			// Check if none of us is blocked by the interlocutor
			$canBlock = $UserManagerPDO->canBlock($user->id(), $interlocutor->id());

			$amINotBlocked = $UserManagerPDO->canBlock($interlocutor->id(), $user->id());

			$notificationManager = new NotificationManager($this->db);
			$notifs = $notificationManager->get($user);

			$i = 0;
			foreach ($notifs as $notif) {
				$notif->setPictureSender('../' . $notif->pictureSender());
				if ($notif->unread() == 1)
					$i++;
			}
			$nbUnread = $i;

			if ($canBlock && $amINotBlocked && $mutualFriend) {
				$this->render($response, 'pages/chat.twig',[
					'user' => $user,
					'interlocutor' => $interlocutor,
					'notifs' => $notifs,
					'nbUnread' => $nbUnread,
				]);
			}

			else {
				$this->flash('You must be mutual friends to chat. One of you doesn\'t have liked the other, or the user has blocked you.', 'error');
				return $this->redirect($response, 'home', 302);		
			}
		}

		else {
			$this->flash('You must be logged to access this page.', 'error');
			return $this->redirect($response, 'auth.login', 302);
		}
	}

	public function postChatmsg($request, $response) {

		if (Validator::isConnected()){

			$UserManagerPDO = new UserManagerPDO($this->db);

			$message = $_POST['message'];

			$poster = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));

			$receptor = $UserManagerPDO->getIdfromLogin($_POST['receptor']);
			$receptor = $UserManagerPDO->getUnique($receptor); 

			$canBlock = $UserManagerPDO->canBlock($poster->id(), $receptor->id());

			$amINotBlocked = $UserManagerPDO->canBlock($receptor->id(), $poster->id());

			if ($canBlock && $amINotBlocked) {
				$notification = new Notification([
					'owner' => $receptor->id(),
					'sender' => $poster->id(),
					'unread' => TRUE,
					'type' => "chat",
					'referenceId' => 0
					]);

				$notificationManager = new NotificationManager($this->db);
				$notificationManager->add($notification);
				$UserManagerPDO->chatToDB($poster, $receptor, $message);
			}
		}

		return (true);
	}

	public function postGetChatMsg() {
		if (Validator::isConnected()) {

			$UserManagerPDO = new UserManagerPDO($this->db);

			$poster = $UserManagerPDO->getUnique(unserialize($_SESSION['id']));

			$receptor = $UserManagerPDO->getIdfromLogin($_POST['receptor']);
			$receptor = $UserManagerPDO->getUnique($receptor);

			if (!empty($_POST['lastID']) && isset($_POST['lastID'])) {
				$lastID = $_POST['lastID'];			
			}

			else {
				$lastID = 0;
			}

			$chatMsgs = $UserManagerPDO->getChatMsg($poster, $receptor, $lastID);
			if ($chatMsgs) {

				// Add login of users in the datas returned
				foreach ($chatMsgs as $key => $value) {

					$login_poster = array('login_poster' => $UserManagerPDO->getLoginFromId($chatMsgs[$key]['id_poster']));
					$login_receptor = array('login_receptor' => $UserManagerPDO->getLoginFromId($chatMsgs[$key]['id_receptor']));
					$logins = $login_poster + $login_receptor;

					$chatMsgs[$key] = array_merge($value, $logins);

				}

				echo(json_encode($chatMsgs));
				return true;
			}
			return false;
		}
		return false;
	}
}