<?php

namespace App\Models;
use \DateTime;


/**
 * @author Guilhem S.
 * @version 1.0
 */
class User 
{
	protected $erreurs = [],
			$id,
			$login,
			$firstName,
			$lastName,
			$email,
			$bio,
			$gender,
			$sexuality,
			$password,
			$hash,
			$longitude,
			$latitude,
			$map,
			$city,
			$birthDate,
			$created_at,
			$updated_at,
			$isactive,
			$mainpicture,
			$popularity,
			$isOnline,
			$pictures = [],
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
		return !(empty($this->login) || empty($this->email) || empty($this->password));
	}

	public function isComplete()
	{
		return !(empty($this->bio) || empty($this->sexuality) || empty($this->gender) || empty($this->hobbies));
	}
	
	
	// SETTERS //
	
	public function setId($id)
	{
		$this->id = (int) $id;
	}
	
	public function setlogin($login)
	{
		if (!is_string($login) || empty($login))
		{
			$this->erreurs[] = self::AUTEUR_INVALIDE;
		}
		else
		{
			$this->login = $login;
		}
	}

	public function setFirstName($firstName)
	{
		if (!is_string($firstName) || empty($firstName))
		{
			$this->erreurs[] = self::AUTEUR_INVALIDE;
		}
		else
		{
			$this->firstName = $firstName;
		}
	}

	public function setLastName($lastName)
	{
		if (!is_string($lastName) || empty($lastName))
		{
			$this->erreurs[] = self::AUTEUR_INVALIDE;
		}
		else
		{
			$this->lastName = $lastName;
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

	public function setIsOnline($isOnline) {
		$this->isOnline = $isOnline;
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

	public function setMap($map) {
		$this->map = $map;
	}

	public function setHash($hash)
	{
		if (!is_string($hash) || empty($hash))
		{
			$this->erreurs[] = self::PASSWORD_INVALIDE;
		}
		else
		{
			$this->hash = $hash;
		}
	}
	
	public function setPictures($src) {

		$this->pictures = $src;
	}

	public function setCoordonates($latitude, $longitude) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	public function setMainPicture ($src) {
		$this->mainpicture = $src;
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

	public function setBirthDate($birthDate) {
		$this->birthDate = $birthDate;
	}

	public function setisactive($isactive){
		$this->isactive = $isactive;
	}

	public function setGender($gender){
		$this->gender = $gender;
	}

	public function setPopularity($score){
		$this->popularity = $score;
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
	
	public function pictures() {
		return $this->pictures;
	}

	public function mainpicture() {
		return $this->mainpicture;
	}

	public function popularity() {

		return $this->popularity;
	}

	public function login()
	{
		return $this->login;
	}

	public function firstName()
	{
		return $this->firstName;
	}
	
	public function lastName()
	{
		return $this->lastName;
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

	public function longitude() {
		return $this->longitude;
	}

	public function latitude() {
		return $this->latitude;
	}

	public function hash()
	{
		return $this->hash;
	}

	public function birthDate()
	{
		return $this->birthDate;
	}
	
	public function created_at()
	{
		return $this->created_at;
	}

	public function map() {
		return $this->map;
	}

	public function updated_at()
	{
		return $this->updated_at;
	}

	public function isactive() {
		return $this->isactive;
	}

	public function isOnline() {
		return $this->isOnline;
	}
}