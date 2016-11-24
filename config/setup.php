<?php
require '../vendor/autoload.php';

$app = new \Slim\App((['settings' => [
	'displayErrorDetails' => true
	]]));

require ('../app/container.php');

$DB_PDO = $container->db;

$DB_REQ = $DB_PDO->query("
	SELECT COUNT(*) AS count
	FROM information_schema.tables
	WHERE table_name = 'users'
		AND TABLE_SCHEMA='matcha'
	;");

$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

if (intval($result['count']) == 0) {
	$DB_REQ = $DB_PDO->prepare("
		CREATE TABLE users (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`login` varchar(32) NOT NULL,
			`firstName` varchar(32) NOT NULL,
			`lastName` varchar(32) NOT NULL,			
			`password` char(128) NOT NULL,
			`gender` char(1) DEFAULT NULL,
			`email` varchar(255) NOT NULL UNIQUE,
			`hash` varchar(128) DEFAULT NULL,
			`isactive` tinyint(1) NOT NULL DEFAULT '1',
			`bio` text,
			`sexuality` char(8) DEFAULT 'bisexual',
			`created_at` datetime NOT NULL,
			`updated_at` datetime NOT NULL,
			`rank` int NOT NULL DEFAULT '0'
			);");

	$DB_REQ->execute();
			
}

$DB_REQ = $container->db->query("
	SELECT COUNT(*) AS count
	FROM information_schema.tables
	WHERE table_name = 'hobbies'
		AND TABLE_SCHEMA='matcha'
	;");

$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

if (intval($result['count']) == 0) {
	$DB_REQ = $DB_PDO->prepare("
		CREATE TABLE hobbies (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_owner` int(11) NOT NULL,
			`morph` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Morph into creep colony',
			`eat` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Eat Terrans',
			`invade` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Invade Aiur',
			`obey` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Obey to the Overmind',
			`gather` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Gather minerals',
			`infest` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Infest command centers',
			`praises` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Praise sAviOr',
			`praisej` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Praise Jaedong',
			`burrow` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Burrow',
			`explode` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Die while exploding',
			`spawn` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Spawn more overlords',
			`killVessels` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Kill science vessels',
			`plague` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Plague marines',
			`hide` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Hide in dark swarms'
		);");
	$DB_REQ->execute();
	$DB_REQ = $DB_PDO->prepare("
		ALTER TABLE `hobbies`
		ADD FOREIGN KEY (id_owner)
		REFERENCES users(id)
		ON DELETE CASCADE
		;");

		$DB_REQ->execute();
}

$DB_REQ = $container->db->query("
	SELECT COUNT(*) AS count
	FROM information_schema.tables
	WHERE table_name = 'pictures'
		AND TABLE_SCHEMA='matcha'
	;");

$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

if (intval($result['count']) == 0) {
	$DB_REQ = $DB_PDO->prepare("
		CREATE TABLE pictures (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_owner` int(11) NOT NULL,
			`date` datetime DEFAULT CURRENT_TIMESTAMP,
			`src` varchar(255) DEFAULT NULL,
			`ismainpic` tinyint(1) UNSIGNED DEFAULT '0' COMMENT 'Value for main picture'
		);");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
		ALTER TABLE `pictures`
		ADD FOREIGN KEY (id_owner)
		REFERENCES users(id)
		ON DELETE CASCADE
		;");

		$DB_REQ->execute();
}

$DB_REQ = $container->db->query("
	SELECT COUNT(*) AS count
	FROM information_schema.tables
	WHERE table_name = 'pictures'
		AND TABLE_SCHEMA='matcha'
	;");

$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

if (intval($result['count']) == 0) {
	$DB_REQ = $DB_PDO->prepare("
		CREATE TABLE visitors (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_owner` int(11) NOT NULL,
			`id_visitor` int(11) NOT NULL,
			`visited_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
		);");

	$DB_REQ->execute();

	$DB_REQ = $DB_PDO->prepare("
		ALTER TABLE `visitors`
		ADD FOREIGN KEY (id_owner)
		REFERENCES users(id)
		ON DELETE CASCADE
		;");

	$DB_REQ->execute();

	$DB_REQ = $DB_PDO->prepare("
		ALTER TABLE `visitors`
		ADD FOREIGN KEY (id_visitor)
		REFERENCES users(id)
		ON DELETE CASCADE
		;");

	$DB_REQ->execute();
}

$DB_REQ = $container->db->query("
	SELECT COUNT(*) AS count
	FROM information_schema.tables
	WHERE table_name = 'popularity'
		AND TABLE_SCHEMA='matcha'
	;");
	
$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

if (intval($result['count']) == 0) {
	$DB_REQ = $DB_PDO->prepare("
		CREATE TABLE popularity (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_owner` int(11) NOT NULL,
			`score` int(11) NOT NULL DEFAULT 0
		);");

	$DB_REQ->execute();

	$DB_REQ = $DB_PDO->prepare("
		ALTER TABLE `popularity`
		ADD FOREIGN KEY (id_owner)
		REFERENCES users(id)
		ON DELETE CASCADE
		;");

	$DB_REQ->execute();

}

$DB_REQ = $container->db->query("
	SELECT COUNT(*) AS count
	FROM information_schema.tables
	WHERE table_name = 'likes'
		AND TABLE_SCHEMA='matcha'
	;");

$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

if (intval($result['count']) == 0) {

	$DB_REQ = $DB_PDO->prepare("
		CREATE TABLE likes (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_owner` int(11) NOT NULL,
			`id_liker` int(11) NOT NULL
		);");

	$DB_REQ->execute();

	$DB_REQ = $DB_PDO->prepare("
		ALTER TABLE `likes`
		ADD FOREIGN KEY (id_owner)
		REFERENCES users(id)
		ON DELETE CASCADE
		;");

	$DB_REQ->execute();

	$DB_REQ = $DB_PDO->prepare("
		ALTER TABLE `likes`
		ADD FOREIGN KEY (id_liker)
		REFERENCES users(id)
		ON DELETE CASCADE
		;");

	$DB_REQ->execute();
}
