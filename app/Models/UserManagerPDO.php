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
		$DB_REQ = $this->DB_REQ->prepare('
			INSERT INTO users(login, email, firstName, lastName, password, birthDate, created_at, updated_at)
			VALUES(:login, :email, :firstName, :lastName, :password, :birthDate, NOW(), NOW())');
		
		$DB_REQ->bindValue(':login', $user->login());
		$DB_REQ->bindValue(':email', $user->email());
		$DB_REQ->bindValue(':firstName', $user->firstName());
		$DB_REQ->bindValue(':lastName', $user->lastName());
		$DB_REQ->bindValue(':password', $user->password());
		$DB_REQ->bindValue(':birthDate', $user->birthDate());
		$DB_REQ->execute();
	}

	public function addExtras(User $user, $hobbies) {

		$DB_REQ = $this->DB_REQ->prepare('
			INSERT INTO popularity(id_owner)
			VALUES(:id_owner)
			');
		$DB_REQ->bindValue(':id_owner', $user->id());
		$DB_REQ->execute();
		$DB_REQ->closeCursor();

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
			'killVessels',
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
				killVessels,
				plague,
				hide
			) 
			VALUES(
				:id_owner,
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
				:killVessels,
				:plague,
				:hide
		)');
		$DB_REQ->bindValue(':id_owner', $user->id());
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
		$DB_REQ->bindValue(':killVessels', $values['killVessels']);
		$DB_REQ->bindValue(':plague', $values['plague']);
		$DB_REQ->bindValue(':hide', $values['hide']);

		$DB_REQ->execute();
	}

	public function updateHobbies(User $user, $hobbies) {

		$hobbiesArray = array(
			'morph',
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
			'killVessels',
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
					$values[$selection] = NULL;
				}
			}
		}

		$DB_REQ = $this->DB_REQ->prepare('
			UPDATE hobbies 
			SET
				morph = :morph,
				eat = :eat,
				invade = :invade,
				obey = :obey,
				gather = :gather,
				infest = :infest,
				praises = :praises,
				praisej = :praisej,
				burrow = :burrow,
				explode = :explode,
				spawn = :spawn,
				killVessels = :killVessels,
				plague = :plague,
				hide = :hide
			WHERE
				id_owner = :id_owner
			');
		$DB_REQ->bindValue(':id_owner', $user->id());
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
		$DB_REQ->bindValue(':killVessels', $values['killVessels']);
		$DB_REQ->bindValue(':plague', $values['plague']);
		$DB_REQ->bindValue(':hide', $values['hide']);

		$DB_REQ->execute();
	}

	/**
	 * @see UserManager::count()
	 */
	public function count()
	{
		return $this->DB_REQ->query('
			SELECT COUNT(*)
			FROM users')->fetchColumn();
	}
	
	/**
	 * @see UserManager::delete()
	 */
	public function delete($id)
	{
		$this->DB_REQ->exec('
			DELETE FROM users
			WHERE id = '.(int) $id
			);
	}
	
	/**
	 * @see UserManager::getList()
	 */
	public function getList($debut = -1, $limite = -1)
	{
		$sql = '
		SELECT id, login, email, password, created_at, updated_at, isactive
		FROM users
		ORDER BY
			id DESC';
		
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
			$DB_REQ = $this->DB_REQ->prepare('
				SELECT id, login, email, firstName, lastName, birthDate,  password, gender, sexuality, bio, created_at, updated_at, isactive 
				FROM users 
				WHERE id = :id
				');
			$DB_REQ->bindValue(':id', (int) $id, PDO::PARAM_INT);
			$DB_REQ->execute();

			$DB_REQ->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Models\User');
			$user = $DB_REQ->fetch();
			
			$DB_REQ->closeCursor();

			$DB_REQ = $this->DB_REQ->prepare('
				SELECT morph, eat, invade, obey, gather, infest, praises, praisej, burrow, explode, spawn, killVessels, plague, hide
				FROM hobbies
				WHERE id_owner = :id_owner
				');
			$DB_REQ->bindValue(':id_owner', $id, PDO::PARAM_INT);
			$DB_REQ->execute();
			$hobbies = $DB_REQ->fetch(PDO::FETCH_ASSOC);

			$user->setHobbies($hobbies);

			$DB_REQ->closeCursor();

			$DB_REQ = $this->DB_REQ->prepare('

				SELECT src FROM pictures
				WHERE id_owner = :id_owner
					AND ismainpic = :ismainpic'
					);

			$DB_REQ->bindValue(':id_owner', $id, PDO::PARAM_INT);
			$DB_REQ->bindValue(':ismainpic', 0, PDO::PARAM_INT);
			$DB_REQ->execute();
			$pictures= $DB_REQ->fetchAll(PDO::FETCH_COLUMN);

			$_SESSION['debug'] = $pictures;

			$user->setPictures($pictures);
			
			$DB_REQ->closeCursor();

			$DB_REQ = $this->DB_REQ->prepare('

				SELECT src FROM pictures
				WHERE id_owner = :id_owner
					AND ismainpic = :ismainpic'
					);

			$DB_REQ->bindValue(':id_owner', $id, PDO::PARAM_INT);
			$DB_REQ->bindValue(':ismainpic', 1, PDO::PARAM_INT);
			$DB_REQ->execute();
			$mainpicture= $DB_REQ->fetch(PDO::FETCH_ASSOC);

			$user->setMainPicture($mainpicture['src']);

			return $user;
		}
	}

	public function getIdFromLogin($login) {
		if ($login) {
			$DB_REQ = $this->DB_REQ->prepare('
				SELECT id 
				FROM users
				WHERE login = :login');
			$DB_REQ->bindValue(':login', $login);
			$DB_REQ->execute();
			$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
			return $data['id'];
		}
		return NULL;
	}

	public function getLoginFromId($id) {
		if ($id) {
			$DB_REQ = $this->DB_REQ->prepare('
				SELECT login 
				FROM users
				WHERE id = :id');
			$DB_REQ->bindValue(':id', $id);
			$DB_REQ->execute();
			$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
			return $data['login'];
		}
		return NULL;
	}
	

	/**
	 * @see UserManager::update()
	 */
	protected function update(User $user)
	{
		$DB_REQ = $this->DB_REQ->prepare('
			UPDATE users
			SET login = :login, email = :email, gender = :gender, sexuality = :sexuality, bio = :bio, updated_at = NOW(), isactive = :isactive
			WHERE id = :id
			');
		
		$DB_REQ->bindValue(':email', $user->email());
		$DB_REQ->bindValue(':login', $user->login());
		$DB_REQ->bindValue(':gender', $user->gender());	
		$DB_REQ->bindValue(':id', $user->id(), PDO::PARAM_INT);
		$DB_REQ->bindValue(':sexuality', $user->sexuality());
		$DB_REQ->bindValue(':bio', $user->bio());
		$DB_REQ->bindValue(':isactive', $user->isactive());
		
		$DB_REQ->execute();

	}

	public function updateLastSeen(User $user) {

		$DB_REQ = $this->DB_REQ->prepare('
			UPDATE users
			SET updated_at = NOW()
			WHERE id = :id
			');
		
		$DB_REQ->bindValue(':id', $user->id(), PDO::PARAM_INT);
		
		$DB_REQ->execute();

		$date = new Datetime('now');
		$user->setDateUpdate($date);
	}

	public function countPictures(User $user) {
		$DB_REQ = $this->DB_REQ->prepare('
			SELECT COUNT(*) as count
			FROM pictures
			WHERE id_owner = :id_owner
			');
		$DB_REQ->bindValue(':id_owner', $user->id());
		$DB_REQ->execute();
		$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);
		return intval($result['count']);
	}

	public function addPicture($src, User $user) {

		$DB_REQ = $this->DB_REQ->prepare('
			SELECT COUNT(*) as count
			FROM pictures
			WHERE id_owner = :id_owner
			');
		$DB_REQ->bindValue(':id_owner', $user->id());
		$DB_REQ->execute();
		$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

		if (intval($result['count']) < 5) {

			$DB_REQ = $this->DB_REQ->prepare('
				SELECT COUNT(ismainpic) as count
				FROM pictures
				WHERE id_owner = :id_owner AND ismainpic = 1
				');

			$DB_REQ->bindValue(':id_owner', $user->id());
			$DB_REQ->execute();
			$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);

			if ($data['count'] == 1) {
				$ismainpic = 0;
			} else {
				$ismainpic = 1;
			}
			$DB_REQ = $this->DB_REQ->prepare('

				INSERT INTO pictures (id_owner, src, ismainpic)
				VALUES (:id_owner, :src, :ismainpic)
				');

			$DB_REQ->bindValue(':id_owner', $user->id());
			$DB_REQ->bindValue(':src', $src);
			$DB_REQ->bindValue(':ismainpic', $ismainpic);
			$DB_REQ->execute();

		}

		return NULL;
	}

	public function deletePicture($idPic, User $user) {
			$DB_REQ = $this->DB_REQ->prepare('
				DELETE
				FROM pictures
				WHERE id = :id AND id_owner = :id_owner
				');

			$DB_REQ->bindValue(':id_owner', $user->id());
			$DB_REQ->bindValue(':id', $idPic);
			$DB_REQ->execute();
	}

	public function getIdFromPicSrc($src) {
		if ($src) {
			$DB_REQ = $this->DB_REQ->prepare('
				SELECT id 
				FROM pictures
				WHERE src = :src
				');
			$DB_REQ->bindValue(':src', $src);
			$DB_REQ->execute();
			$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
			return $data['id'];
		}
		return NULL;
	}

	public function setMainPicture($idPic, User $user) {
		if ($idPic){
			$DB_REQ = $this->DB_REQ->prepare('
				UPDATE pictures
				SET ismainpic = :value
				WHERE id_owner = :id_owner
				');
			$DB_REQ->bindValue(':value', 0, PDO::PARAM_INT);
			$DB_REQ->bindValue(':id_owner', $user->id());
			$DB_REQ->execute();

			$DB_REQ->closeCursor();

			$DB_REQ = $this->DB_REQ->prepare('
				UPDATE pictures
				SET ismainpic = :value
				WHERE id = :id
				');
			$DB_REQ->bindValue(':value', 1, PDO::PARAM_INT);
			$DB_REQ->bindValue(':id', $idPic);
			$DB_REQ->execute();
		}

	}

	public function addVisit ($idVisitor, $idVisited) {

		if (!empty($idVisitor) && !empty($idVisited)) {

			$DB_REQ = $this->DB_REQ->prepare('SELECT id_visitor
				FROM visitors
				WHERE id_owner = :id_owner
					AND id_visitor = :id_visitor
				');
			$DB_REQ->bindValue(':id_owner', $idVisited, PDO::PARAM_INT);
			$DB_REQ->bindValue(':id_visitor', $idVisitor, PDO::PARAM_INT);	
			$DB_REQ->execute();

			$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
			$DB_REQ->closeCursor();

			if($data['id_visitor']) {

				$DB_REQ = $this->DB_REQ->prepare('
					UPDATE visitors
					SET visited_at = NOW()
					WHERE id_visitor = :id_visitor
						AND id_owner = :id_owner
					');
				$DB_REQ->bindValue(':id_owner', $idVisited, PDO::PARAM_INT);
				$DB_REQ->bindValue(':id_visitor', $idVisitor, PDO::PARAM_INT);
				$DB_REQ->execute();

			} else {

				$DB_REQ = $this->DB_REQ->prepare('
					INSERT INTO visitors (id_owner, id_visitor, visited_at)
					VALUES (:id_owner, :id_visitor, NOW())
					');
				$DB_REQ->bindValue(':id_owner', $idVisited, PDO::PARAM_INT);
				$DB_REQ->bindValue(':id_visitor', $idVisitor, PDO::PARAM_INT);
				$DB_REQ->execute();
				$DB_REQ->closeCursor();

				$DB_REQ = $this->DB_REQ->prepare('
					UPDATE popularity
					SET score = score + 1
					WHERE id_owner = :id_owner
					');
				$DB_REQ->bindValue(':id_owner', $idVisited, PDO::PARAM_INT);
				$DB_REQ->execute();
			}
		}

		return NULL;
	}

	public function getVisits($id_owner) {
		if (!empty($id_owner)) {

			$DB_REQ = $this->DB_REQ->prepare('
				SELECT id_owner, users.login, visited_at
				FROM visitors
				INNER JOIN users
				ON users.id = visitors.id_visitor
				WHERE id_owner = :id_owner
				-- UNION
				-- SELECT 
				ORDER BY visited_at DESC
				LIMIT 5;
				');
			$DB_REQ->bindValue(':id_owner', $id_owner, PDO::PARAM_INT);
			$DB_REQ->execute();
			$data = $DB_REQ->fetchAll(PDO::FETCH_ASSOC);
			return $data;
		}

		return NULL;
	}

	public function like($id_liker, $id_liked) {
		$DB_REQ = $this->DB_REQ->prepare('
			INSERT INTO likes (id_owner, id_liker) 
			VALUES (:id_owner, :id_liker) 
			');
		$DB_REQ->bindValue(':id_owner', $id_liked, PDO::PARAM_INT);
		$DB_REQ->bindValue(':id_liker', $id_liker, PDO::PARAM_INT);
		$DB_REQ->execute();

		$DB_REQ->closeCursor();

		$DB_REQ = $this->DB_REQ->prepare('
				UPDATE popularity
				SET score = score + 2
				WHERE id_owner = :id_owner
				');
		$DB_REQ->bindValue(':id_owner', $id_liked, PDO::PARAM_INT);
		$DB_REQ->execute();
	}

	public function unlike($id_unliker, $id_unliked) {
		$DB_REQ = $this->DB_REQ->prepare('
			DELETE FROM likes
			WHERE id_owner = :id_owner AND id_liker = :id_unliker 
			');
		$DB_REQ->bindValue(':id_owner', $id_unliked, PDO::PARAM_INT);
		$DB_REQ->bindValue(':id_unliker', $id_unliker, PDO::PARAM_INT);
		$DB_REQ->execute();

		$DB_REQ->closeCursor();

		$DB_REQ = $this->DB_REQ->prepare('
				UPDATE popularity
				SET score = score - 2
				WHERE id_owner = :id_owner
				');
		$DB_REQ->bindValue(':id_owner', $id_unliked, PDO::PARAM_INT);
		$DB_REQ->execute();
	}

/*
** Has the visitor already liked the profile ? So can he like ?
** Case no: false
** Case yes: true
*/

	public function canLike($id_liker, $id_liked) {
		if (!empty($id_liker) && !empty($id_liked)) {

			$DB_REQ = $this->DB_REQ->prepare('SELECT id_liker
				FROM likes
				WHERE id_owner = :id_owner
					AND id_liker = :id_liker
				');
			$DB_REQ->bindValue(':id_owner', $id_liked, PDO::PARAM_INT);
			$DB_REQ->bindValue(':id_liker', $id_liker, PDO::PARAM_INT);	
			$DB_REQ->execute();

			$data = $DB_REQ->fetch(PDO::FETCH_ASSOC);
			$DB_REQ->closeCursor();

			if($data['id_liker']) {
				return false;
			}
		}
		return true;
	}

/*
** DEBUG
*/

	public function debugHobbies($id) {
		$hobbies_user = [];

		$DB_REQ = $this->DB_REQ->prepare('
			SELECT morph, eat, invade, obey, gather, infest, praises, praisej, burrow, explode, spawn, killVessels, plague, hide
			FROM hobbies
			WHERE :id_owner = :id_owner
			');

		$DB_REQ->bindValue(':id_owner', (int) $id, PDO::PARAM_INT);
		$DB_REQ->execute();
		$hobbies = $DB_REQ->fetch(PDO::FETCH_ASSOC);
		echo 'HOBBIES IN DD' . '<br>';

		$tmp = array_filter($hobbies);
		$tmp = array_keys($tmp);

		debug($tmp);

	}
}
