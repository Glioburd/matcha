<?php

function console_log( $data ){
	echo '<script>';
	echo 'console.log('. json_encode( $data ) .')';
	echo '</script>';
}

function debug ( $data ) {
	echo '<pre>';
	var_dump($data);
	echo '</pre>';	
}

function recursive_array_diff($a1, $a2) { 
	$r = array(); 
	foreach ($a1 as $k => $v) {
		if (array_key_exists($k, $a2)) { 
			if (is_array($v)) { 
				$rad = recursive_array_diff($v, $a2[$k]); 
				if (count($rad)) { $r[$k] = $rad; } 
			} else { 
				if ($v != $a2[$k]) { 
					$r[$k] = $v; 
				}
			}
		} else { 
			$r[$k] = $v; 
		} 
	} 
	return $r; 
}