<?php

namespace App\Controllers;

use \Datetime;

use App\Models\User;
use App\Controllers\Debug;
use App\Models\UserManagerPDO;
use \PDO;
use App\Models\Notification;
use App\Models\NotificationManager;

include __DIR__ . '../../../debug.php';
// include "sort_array.php";

/**
* 
*/
class ChatController extends Controller {

	public function getLOL($request, $response) {
		$nom = $request->getParam('nom');						//On récupère le pseudo et on le stocke dans une variable
		$message = $request->getParam('login');				//On fait de même avec le message
		$ligne = $nom.' > '.$message.'<br>';		//Le message est créé 
		$leFichier = file('ac.htm');				//On lit le fichier ac.htm et on stocke la réponse dans une variable (de type tableau)
		array_unshift($leFichier, $ligne);			//On ajoute le texte calculé dans la ligne précédente au début du tableau
		file_put_contents('ac.htm', $leFichier);	//On écrit le contenu du tableau $leFichier dans le fichier ac.htm

		$this->render($response, 'pages/chat.twig',[
		]);
	}

}
