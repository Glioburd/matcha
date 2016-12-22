<?php

namespace App\Models;
use \PDO;

class DBFactory {

	public static function getMysqlConnexionWithPDO() {
			$DB_PDO = new PDO('mysql:host=localhost;dbname=matcha', 'root', 'root');
			$DB_PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return $DB_PDO;
		}
		
		public static function getMysqlConnexionWithMySQLi() {
			return new MySQLi('localhost', 'root', 'root', 'matcha');
		}
}