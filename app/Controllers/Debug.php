<?php

namespace App\Controllers;

class Debug {

	function debug_to_console( $data ) {
		ob_start();
		if ( is_array( $data ) )
			$output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
		else
			$output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

		echo $output;
		ob_end_flush();
	}

	static public function debugUser($container, $user) {
		if ($container->debug) {
		echo "<pre><h2>USER:</h2>";
		debug($user);
		}
	}

	static public function debugUsers($container, $user, $userprofile) {
		if ($container->debug) {
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
