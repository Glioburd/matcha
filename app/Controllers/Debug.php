<?php

namespace App\Controllers;

class Debug {

	static public function debugUser($container, $user) {
		if ($container->debug) {
		echo "<pre><h2>USER:</h2>";
		debug($user);
		}
	}

	static public function debugUsers($container, $user, $userprofile) {
		if ($container->debug) {
			echo "<pre><h2>USERPROFILE:" . $userprofile->login() . "</h2></pre>";
			debug($userprofile);
			echo "<pre><h2>USER:" . $user->login() . "</h2></pre>";
			debug($user);
			echo "<pre>Difference entre user et userprofile :<br>";
			print_r(recursive_array_diff((array)$user, (array)$userprofile));
			debug($_SESSION['debug']);
			echo "</pre>";
		}
	}
}