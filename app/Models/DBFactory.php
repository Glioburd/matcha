<?php

namespace App\Models;
use \PDO;

class DBFactory {

	public static function getMysqlConnexionWithPDO() {
		try{
			$DB_PDO = new PDO('mysql:host=localhost;dbname=matcha', 'root', 'root');
			$DB_PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e) {
			die('SQL error : '.$e->getMessage());
		}

		return $DB_PDO;
		}

		public static function getMysqlConnexionWithMySQLi() {
			return new MySQLi('localhost', 'root', 'root', 'matcha');
		}
}