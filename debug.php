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