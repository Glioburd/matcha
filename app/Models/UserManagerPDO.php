<?php

namespace App\Models;

use \PDO;
use \DateTime;
use App\Models\User;

class UserManagerPDO extends UserManager
{
	/**
	 * Attribut contenant l'instance représentant la BDD.
	 * @type PDO
	 */
	protected $DB_REQ;
	
	/**
	 * Constructeur étant chargé d'enregistrer l'instance de PDO dans l'attribut $DB_REQ.
	 * @param $DB_REQ PDO Le DAO
	 * @return void
	 */
	public function __construct(PDO $DB_REQ)
	{
		$this->DB_REQ = $DB_REQ;
	}
	
	/**
	 * @see UserManager::add()
	 */
	protected function add(User $user)
	{
		$DB_REQ = $this->DB_REQ->prepare('INSERT INTO users(name, email, password, created_at, updated_at) VALUES(:name, :email, :password, NOW(), NOW())');
		
		$DB_REQ->bindValue(':name', $user->name());
		$DB_REQ->bindValue(':email', $user->email());
		$DB_REQ->bindValue(':password', $user->password());
		
		$DB_REQ->execute();
	}
	
	public function addHobbies(User $user, $hobbies) {

		$hobbiesArray = array('morph',
			'eat',
			'invade',
			'obey',
			'gather',
			'infest',
			'praises',
			'praisej',
			'burrow',
			'explode',
			'spawn',
			'kill',
			'plague',
			'hide'	
		);

		if (isset($hobbies)) {
			$values = array();
			foreach ($hobbies as $selection) {
				if (in_array($selection, $hobbiesArray)) {
					$values[$selection] = 1;
				}
				else {
					$values[$selection] = 0;
				}
			}
		}

		$DB_REQ = $this->DB_REQ->prepare('
			INSERT INTO hobbies(
				id_owner,
				name_owner,
				morph,
				eat,
				invade,
				obey,
				gather,
				infest,
				praises,
				praisej,
				burrow,
				explode,
				spawn,
				kill_vessels,
				plague,
				hide
			) 
			VALUES(
				:id_owner,
				:name_owner,
				:morph,
				:eat,
				:invade,
				:obey,
				:gather,
				:infest,
				:praises,
				:praisej,
				:burrow,
				:explode,
				:spawn,
				:kill_vessels,
				:plague,
				:hide
		)');
		$DB_REQ->bindValue(':id_owner', $user->id());
		$DB_REQ->bindValue(':name_owner', $user->name());
		$DB_REQ->bindValue(':morph', $values['morph']);
		$DB_REQ->bindValue(':eat', $values['eat']);
		$DB_REQ->bindValue(':invade', $values['invade']);
		$DB_REQ->bindValue(':obey', $values['obey']);
		$DB_REQ->bindValue(':gather', $values['gather']);
		$DB_REQ->bindValue(':infest', $values['infest']);
		$DB_REQ->bindValue(':praises', $values['praises']);
		$DB_REQ->bindValue(':praisej', $values['praisej']);
		$DB_REQ->bindValue(':burrow', $values['burrow']);
		$DB_REQ->bindValue(':explode', $values['explode']);
		$DB_REQ->bindValue(':spawn', $values['spawn']);
		$DB_REQ->bindValue(':kill_vessels', $values['kill_vessels']);
		$DB_REQ->bindValue(':plague', $values['plague']);
		$DB_REQ->bindValue(':hide', $values['hide']);

		$DB_REQ->execute();
	}

	/**
	 * @see UserManager::count()
	 */
	public function count()
	{
		return $this->DB_REQ->query('SELECT COUNT(*) FROM users')->fetchColumn();
	}
	
	/**
	 * @see UserManager::delete()
	 */
	public function delete($id)
	{
		$this->DB_REQ->exec('DELETE FROM users WHERE id = '.(int) $id);
	}
	
	/**
	 * @see UserManager::getList()
	 */
	public function getList($debut = -1, $limite = -1)
	{
		$sql = 'SELECT id, name, email, password, created_at, updated_at, isactive FROM users ORDER BY id DESC';
		
		// On vérifie l'intégrité des paramètres fournis.
		if ($debut != -1 || $limite != -1)
		{
			$sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
		}
		
		$DB_REQ = $this->DB_REQ->query($sql);
		$DB_REQ->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\models\User');

		$listeUser = $DB_REQ->fetchAll();

		// On parcourt notre liste de user pour pouvoir placer des instances de DateTime en guise de dates d'ajout et de modification.
		foreach ($listeUser as $user)
		{	
			// $user->setDateCreation(new DateTime($user->created_at()));
			// $user->setDateUpdate(new DateTime($user->updated_at()));
		}
		
		$DB_REQ->closeCursor();
		
		return $listeUser;
	}
	
	/**
	 * @see UserManager::getUnique()
	 */
	public function getUnique($id)
	{
		if (isset($id) && !empty($id)) {
			$DB_REQ = $this->DB_REQ->prepare('SELECT id, name, email, password, sexuality, bio, created_at, updated_at, isactive FROM users WHERE id = :id');
			$DB_REQ->bindValue(':id', (int) $id, PDO::PARAM_INT);
			$DB_REQ->execute();

			$DB_REQ->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Models\User');
			$user = $DB_REQ->fetch();
			
			$DB_REQ->closeCursor();

			$DB_REQ = $this->DB_REQ->prepare('SELECT morph, eat, invade, obey, gather, infest, praises, praisej, burrow, explode, spawn, kill_vessels, plague, hide FROM hobbies WHERE :id_owner = :id_owner');
			$DB_REQ->bindValue(':id_owner', $id, PDO::PARAM_INT);
			$DB_REQ->execute();
			$hobbies = $DB_REQ->fetch(PDO::FETCH_ASSOC);

			$user->setHobbies($hobbies);
			
			return $user;
		}
	}

		public function getIdFromName($name) {
			if ($name) {
				$DB_REQ = $this->DB_REQ->prepare('SELECT id from users WHERE name = :name');
				$DB_REQ->bindValue(':name', $name);
				$DB_REQ->execute();
				$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
				return $data['id'];
			}
			return NULL;
		}
	
		// public function getHobbies(User $user)
		// {
		// 	if (!empty($user)){ 
		// 		$DB_REQ = $this->DB_REQ->prepare('SELECT morph, eat, invade, obey, gather, infest, praises, praisej, burrow, explode, spawn, kill_vessels, plague, hide WHERE :id_owner = :id_owner');
		// 		$DB_REQ->bindValue(':id_owner', $user->id());
		// 		$DB_REQ->execute();
		// 		$hobbies = $DB_REQ->fetchAll(PDO::FETCH_ASSOC);
		// 		$user->setHobbies($hobbies);
		// 		return true;
		// 	}
		// 	return false;	
		// }

		// public function getHobbiesByName(User $user)
		// {
		// 	if (!empty($user)){ 
		// 		$DB_REQ = $this->DB_REQ->prepare('SELECT morph, eat, invade, obey, gather, infest, praises, praisej, burrow, explode, spawn, kill_vessels, plague, hide WHERE name_owner = :name_owner');
		// 		$DB_REQ->bindValue(':name_owner', $user->name());
		// 		$DB_REQ->execute();
		// 		$hobbies = $DB_REQ->fetchAll(PDO::FETCH_ASSOC);
		// 		$user->setHobbies($hobbies);
		// 		return true;
		// 	}
		// 	return false;	
		// }


	/**
	 * @see UserManager::update()
	 */
	protected function update(User $user)
	{
		$DB_REQ = $this->DB_REQ->prepare('UPDATE users SET name = :name, email = :email, sexuality = :sexuality, bio = :bio, updated_at = NOW(), isactive = :isactive WHERE id = :id');
		
		$DB_REQ->bindValue(':email', $user->email());
		$DB_REQ->bindValue(':name', $user->name());
		$DB_REQ->bindValue(':id', $user->id(), PDO::PARAM_INT);
		$DB_REQ->bindValue(':sexuality', $user->sexuality());
		$DB_REQ->bindValue(':bio', $user->bio());
		$DB_REQ->bindValue(':isactive', $user->isactive());
		
		$DB_REQ->execute();

	}

/*
** DEBUG
*/

	public function debugHobbies($id) {
		$hobbies_user = [];

		$DB_REQ = $this->DB_REQ->prepare('SELECT morph, eat, invade, obey, gather, infest, praises, praisej, burrow, explode, spawn, kill_vessels, plague, hide FROM hobbies WHERE :id_owner = :id_owner');
		$DB_REQ->bindValue(':id_owner', (int) $id, PDO::PARAM_INT);
		$DB_REQ->execute();
		$hobbies = $DB_REQ->fetch(PDO::FETCH_ASSOC);
		echo 'HOBBIES IN DD' . '<br>';

		$tmp = array_filter($hobbies);
		$tmp = array_keys($tmp);

		debug($tmp);




	}

	// protected function updateHobbies(User $user) {
	// 	$DB_REQ = $this->DB_REQ->prepare('UPDATE hobbies SET morph = :morph, eat = :eat, invade = :invade, obey = :obey, gather = :gather, infest = :infest, praises = :praises, praisej = :praisej, burrow = :burrow, explose = :explose, spawn = :spawn, kill_vessels = :kill_vessels, plague = :plague, hide = :hide WHERE id_owner = :id_owner');
	// 	$DB_REQ->bindValue(':morph', $user->['morph']);
	// 	$DB_REQ->bindValue(':eat', $values['eat']);
	// 	$DB_REQ->bindValue(':invade', $values['invade']);
	// 	$DB_REQ->bindValue(':obey', $values['obey']);
	// 	$DB_REQ->bindValue(':gather', $values['gather']);
	// 	$DB_REQ->bindValue(':infest', $values['infest']);
	// 	$DB_REQ->bindValue(':praises', $values['praises']);
	// 	$DB_REQ->bindValue(':praisej', $values['praisej']);
	// 	$DB_REQ->bindValue(':burrow', $values['burrow']);
	// 	$DB_REQ->bindValue(':explode', $values['explode']);
	// 	$DB_REQ->bindValue(':spawn', $values['spawn']);
	// 	$DB_REQ->bindValue(':kill_vessels', $values['kill_vessels']);
	// 	$DB_REQ->bindValue(':plague', $values['plague']);
	// 	$DB_REQ->bindValue(':hide', $values['hide']);
		
	// 	$DB_REQ->execute();
	// }
}
