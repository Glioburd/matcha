<?php

try {
	$DB_PDO = new PDO('mysql:host=localhost;charset=utf8');
	$DB_PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$DB_REQ = $DB_PDO->prepare('CREATE DATABASE IF NOT EXISTS `matcha` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;');
	$DB_REQ->execute();
	$DB_REQ->closeCursor();
} catch (PDOException $e) {
	die("Database creation failed : " . $e->getMessage());
}

require '../vendor/autoload.php';
$app = new \Slim\App((['settings' => [
	'displayErrorDetails' => true
	]]));
require ('../app/container.php');

$DB_PDO = $container->db;

try {
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
				`birthDate` date NOT NULL,
				`password` char(128) NOT NULL,
				`gender` char(1) DEFAULT NULL,
				`email` varchar(255) NOT NULL,
				`hash` varchar(128) DEFAULT NULL,
				`isactive` tinyint(1) NOT NULL DEFAULT '1',
				`bio` text,
				`sexuality` char(8) DEFAULT 'bi',
				`created_at` datetime NOT NULL,
				`updated_at` datetime NOT NULL,
				`rank` int NOT NULL DEFAULT '0',
				`longitude` decimal(9,6) DEFAULT NULL COMMENT 'Longitude',
				`latitude` decimal(9,6) DEFAULT NULL COMMENT 'Latitude',
				`map` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If user has allowed geoloc'
				);");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
			ALTER TABLE `users`
				AUTO_INCREMENT=30;");
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
		WHERE table_name = 'visitors'
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
				`id_liker` int(11) NOT NULL,
				`date_like` datetime NOT NULL
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

	$DB_REQ = $container->db->query("
		SELECT COUNT(*) AS count
		FROM information_schema.tables
		WHERE table_name = 'blocks'
			AND TABLE_SCHEMA='matcha'
		;");

	$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

	if (intval($result['count']) == 0) {

		$DB_REQ = $DB_PDO->prepare("
			CREATE TABLE blocks (
				`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`id_blocked` int(11) NOT NULL,
				`id_blocker` int(11) NOT NULL
			);");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
			ALTER TABLE `blocks`
			ADD FOREIGN KEY (id_blocked)
			REFERENCES users(id)
			ON DELETE CASCADE
			;");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
			ALTER TABLE `blocks`
			ADD FOREIGN KEY (id_blocker)
			REFERENCES users(id)
			ON DELETE CASCADE
			;");

		$DB_REQ->execute();
	}

	$DB_REQ = $container->db->query("
		SELECT COUNT(*) AS count
		FROM information_schema.tables
		WHERE table_name = 'notifications'
			AND TABLE_SCHEMA='matcha'
		;");

	$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);

	if (intval($result['count']) == 0) {

		$DB_REQ = $DB_PDO->prepare("
			CREATE TABLE notifications (
				`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`id_owner` int(11) NOT NULL,
				`id_sender` int(11) NOT NULL,
				`unread` tinyint(1) NOT NULL,
				`type` varchar(255) NOT NULL,
				`id_reference` int(11) NOT NULL,
				`date_notif` datetime NOT NULL
			);");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
			ALTER TABLE `notifications`
			ADD FOREIGN KEY (id_owner)
			REFERENCES users(id)
			ON DELETE CASCADE
			;");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
			ALTER TABLE `notifications`
			ADD FOREIGN KEY (id_sender)
			REFERENCES users(id)
			ON DELETE CASCADE
			;");

		$DB_REQ->execute();
	}

		$DB_REQ = $container->db->query("
		SELECT COUNT(*) AS count
		FROM information_schema.tables
		WHERE table_name = 'chat'
			AND TABLE_SCHEMA='matcha'
		;");

		$result = $DB_REQ->fetch(PDO::FETCH_ASSOC);


	if (intval($result['count']) == 0) {

		$DB_REQ = $DB_PDO->prepare("
			CREATE TABLE chat (
				`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`id_poster` int(11) NOT NULL,
				`id_receptor` int(11) NOT NULL,
				`message` text,
				`date_message` datetime NOT NULL
			);");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
			ALTER TABLE `chat`
			ADD FOREIGN KEY (id_poster)
			REFERENCES users(id)
			ON DELETE CASCADE
			;");

		$DB_REQ->execute();

		$DB_REQ = $DB_PDO->prepare("
			ALTER TABLE `chat`
			ADD FOREIGN KEY (id_receptor)
			REFERENCES users(id)
			ON DELETE CASCADE
			;");

		$DB_REQ->execute();

	}
	echo "Database matcha has been created!";

}

catch (Exception $e) {
	die("Connection failed: " . $e->getMessage());
}
