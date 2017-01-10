<?php

namespace App\Controllers;

class Debug {

	private function filter(&$value) {
		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

	static public function debugUser($container, $user) {
		if ($container->debug) {
		echo "<pre><h2>USER:</h2>";
		debug($user);
		}
	}

	static public function debugUsers($container, $user, $userprofile) {
		if ($container->debug) {
			array_walk_recursive($user, $this, "filter");
			array_walk_recursive($userprofile, $this, "filter");
			echo "<pre><h2>USERPROFILE:" . htmlspecialchars($userprofile->login()) . "</h2></pre>";
			debug($userprofile);
			echo "<pre><h2>USER:" . htmlspecialchars($user->login()) . "</h2></pre>";
			debug($user);
			echo "<pre>Difference entre user et userprofile :<br>";
			print_r(recursive_array_diff((array)$user, (array)$userprofile));
			echo "</pre>";
		}
	}

	static public function debugNotifs($container, $notifs) {
		if ($container->debug) {
		echo "<h2>NOTIFS:</h2>";
		debug($notifs);
		}
	}
}
