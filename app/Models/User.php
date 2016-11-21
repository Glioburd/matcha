<?php

namespace App\Models;
use \DateTime;


/**
 * Classe représentant une user, créée à l'occasion d'un TP du tutoriel « La programmation orientée objet en PHP » disponible sur http://www.openclassrooms.com/
 * @author Victor T.
 * @version 2.0
 */
class User 
{
	protected $erreurs = [],
			$id,
			$name,
			$email,
			$bio,
			$gender,
			$sexuality,
			$password,
			$created_at,
			$updated_at,
			$isactive,
			$hobbies = [];
	
	/**
	 * Constantes relatives aux erreurs possibles rencontrées lors de l'exécution de la méthode.
	 */
	const AUTEUR_INVALIDE = 1;
	const MAIL_INVALIDE = 2;
	const CONTENU_INVALIDE = 3;
	const PASSWORD_INVALIDE = 4;
	
	
	/**
	 * Constructeur de la classe qui assigne les données spécifiées en paramètre aux attributs correspondants.
	 * @param $valeurs array Les valeurs à assigner
	 * @return void
	 */
	public function __construct($valeurs = [])
	{
		if (!empty($valeurs)) // Si on a spécifié des valeurs, alors on hydrate l'objet.
		{
			$this->hydrate($valeurs);
		}
	}
	
	/**
	 * Méthode assignant les valeurs spécifiées aux attributs correspondant.
	 * @param $donnees array Les données à assigner
	 * @return void
	 */
	public function hydrate($donnees)
	{
		foreach ($donnees as $attribut => $valeur)
		{
			$methode = 'set'.ucfirst($attribut);
			
			if (is_callable([$this, $methode]))
			{
				$this->$methode($valeur);
			}
		}
	}
	
	/**
	 * Méthode permettant de savoir si la user est nouvelle.
	 * @return bool
	 */
	public function isNew()
	{
		return empty($this->id);
	}
	
	/**
	 * Méthode permettant de savoir si la user est valide.
	 * @return bool
	 */
	public function isValid()
	{
		return !(empty($this->name) || empty($this->email) || empty($this->password));
	}
	
	
	// SETTERS //
	
	public function setId($id)
	{
		$this->id = (int) $id;
	}
	
	public function setName($name)
	{
		if (!is_string($name) || empty($name))
		{
			$this->erreurs[] = self::AUTEUR_INVALIDE;
		}
		else
		{
			$this->name = $name;
		}
	}

	public function setBio($bio) {

		if (!is_string($bio) || empty($bio)) {
			$this->erreurs[] = self::CONTENU_INVALIDE;
		}

		else {
			$this->bio = $bio;
		}
	}

	public function setSexuality($sexuality) {

		$this->sexuality = $sexuality;
	}

	public function setEmail($email)
	{
		if (!is_string($email) || empty($email))
		{
			$this->erreurs[] = self::MAIL_INVALIDE;
		}
		else
		{
			$this->email = $email;
		}
	}
	
	public function setPassword($password)
	{
		if (!is_string($password) || empty($password))
		{
			$this->erreurs[] = self::PASSWORD_INVALIDE;
		}
		else
		{
			$this->password = $password;
		}
	}

	public function setTitre($titre)
	{
		if (!is_string($titre) || empty($titre))
		{
			$this->erreurs[] = self::TITRE_INVALIDE;
		}
		else
		{
			$this->titre = $titre;
		}
	}
	
	public function setContenu($contenu)
	{
		if (!is_string($contenu) || empty($contenu))
		{
			$this->erreurs[] = self::CONTENU_INVALIDE;
		}
		else
		{
			$this->contenu = $contenu;
		}
	}
	
	public function setHobbies($hobbies) {
		$tmp = array_filter($hobbies);
		$this->hobbies = array_keys($tmp);
	}

	public function updateHobbies($hobbies) {
		$this->hobbies = $hobbies;
	}

	public function setDateCreation(DateTime $created_at)
	{
		$this->created_at = $created_at;
	}
	
	public function setDateUpdate(DateTime $updated_at)
	{
		$this->updated_at = $updated_at;
	}

	public function setisactive($isactive){
		$this->isactive = $isactive;
	}

	public function setGender($gender){
		$this->gender = $gender;
	}
	
	// GETTERS //
	
	public function erreurs()
	{
		return $this->erreurs;
	}

	public function hobbies() {
		return $this->hobbies;
	}
	
	public function id()
	{
		return $this->id;
	}
	
	public function sayhi()
	{
		echo 'Hi';
	}

	public function name()
	{
		return $this->name;
	}
	
	public function email()
	{
		return $this->email;
	}
	
	public function bio() {

		return $this->bio;
	}

	public function sexuality() {

		return $this->sexuality;
	}

	public function gender()
	{
		return $this->gender;
	}

	public function password()
	{
		return $this->password;
	}
	
	public function created_at()
	{
		return $this->created_at;
	}

	public function updated_at()
	{
		return $this->updated_at;
	}

	public function isactive() {
		return $this->isactive;
	}
}