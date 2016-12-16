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

			if ($canBlock && $amINotBlocked) {
				$this->render($response, 'pages/chat.twig',[
					'user' => $user,
					'interlocutor' => $interlocutor
				]);
			}

			else {
				$this->flash('You must be mutual friends to chat. One of you doesn\'t have liked the other, or the user has blocked you.', 'error');
				return $this->redirect($response, 'auth.login', 302);		
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

			debug($message);

			$poster = $UserManagerPDO->getIdfromLogin($_POST['poster']);
			$poster = $UserManagerPDO->getUnique($poster);

			debug($poster);

			$receptor = $UserManagerPDO->getIdfromLogin($_POST['receptor']);
			$receptor = $UserManagerPDO->getUnique($receptor); 

			debug($receptor);

			$UserManagerPDO->chatToDB($poster, $receptor, $message);
		}

		// else {
		// 	$this->flash('You must be logged to access this page.', 'error');
		// 	return $this->redirect($response, 'auth.login', 302);
		// }
		// debug($_POST);

		// die();
		// $nom = $user->login();								//On récupère le pseudo et on le stocke dans une variable
		// $message = $request->getParam('message');
		// $ligne = $nom.' > '.$message.'<br>';		//Le message est créé 
		// $leFichier = file('ac.htm');				//On lit le fichier ac.htm et on stocke la réponse dans une variable (de type tableau)
		// array_unshift($leFichier, $ligne);			//On ajoute le texte calculé dans la ligne précédente au début du tableau
		// file_put_contents('ac.htm', $leFichier);
		return (true);
	}

}
