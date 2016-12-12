<?php

namespace App\Models;
use \DateTime;

abstract class Notification
{
	protected $owner;
	protected $sender;
	protected $unread;
	protected $type;
	protected $referenceId;
	protected $created_at;

	public function __construct($valeurs = [])
	{
		if (!empty($valeurs)) // Si on a spécifié des valeurs, alors on hydrate l'objet.
		{
			$this->hydrate($valeurs);
		}
	}

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
	 * Message generators that have to be defined in subclasses
	 */
	abstract public function messageForNotification(Notification $notification) : string;
	abstract public function messageForNotifications(array $notifications) : string;

	/**
	 * Generate message of the current notification.
	 */ 
	public function message() : string
	{
		return $this->messageForNotification($this);
	}

	public function setId($id)
	{
		$this->id = (int) $id;
	}

	public function setowner($owner)
	{
		$this->owner = (int) $owner;
	}

	public function setSender($sender)
	{
		$this->sender = (int) $sender;
	}

	public function setUnread($unread)
	{
		$this->unread = (int) $unread;
	}

	public function setType($type)
	{
		$this->type =$type;
	}

	public function setReferenceId($referenceId)
	{
		$this->referenceId = (int) $referenceId;
	}

	public function setCreatedAt($created_at)
	{
		$this->created_at = $created_at;
	}

	public function id() {
		return $this->id;
	}

	public function owner() {
		return $this->owner;
	}

	public function sender() {
		return $this->sender;
	}

	public function unread() {
		return $this->unread;
	}

	public function type() {
		return $this->type;
	}

	public function referenceId() {
		return $this->referenceId;
	}

	public function createdAt() {
		return $this->created_at;
	}
}
