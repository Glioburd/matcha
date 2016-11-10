<?php


namespace App\Models;
use App\Models\User;

abstract class UserManager
{
	/**
	 * Méthode permettant d'ajouter une user.
	 * @param $user User La user à ajouter
	 * @return void
	 */
	abstract protected function add(User $user);
	
	/**
	 * Méthode renvoyant le nombre de user total.
	 * @return int
	 */
	abstract public function count();
	
	/**
	 * Méthode permettant de supprimer une user.
	 * @param $id int L'identifiant de la user à supprimer
	 * @return void
	 */
	abstract public function delete($id);
	
	/**
	 * Méthode retournant une liste de user demandée.
	 * @param $debut int La première user à sélectionner
	 * @param $limite int Le nombre de user à sélectionner
	 * @return array La liste des user. Chaque entrée est une instance de User.
	 */
	abstract public function getList($debut = -1, $limite = -1);
	
	/**
	 * Méthode retournant une user précise.
	 * @param $id int L'identifiant de la user à récupérer
	 * @return User La user demandée
	 */
	abstract public function getUnique($id);
	
	/**
	 * Méthode permettant d'enregistrer une user.
	 * @param $user User la user à enregistrer
	 * @see self::add()
	 * @see self::modify()
	 * @return void
	 */
	public function save(User $user)
	{
		if ($user->isValid())
		{
			$user->isNew() ? $this->add($user) : $this->update($user);
		}
		else
		{
			throw new \RuntimeException('One of the field is empty.');
		}
	}
	
	/**
	 * Méthode permettant de modifier une user.
	 * @param $user user la user à modifier
	 * @return void
	 */
	abstract protected function update(User $user);
}