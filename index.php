<?php
	require_once('sophieasciify.php');

	$SophieAsciify = new SophieAsciify();
	try {
		$SophieAsciify->serveColor('./img/01.jpg');
	} catch(Exception $e) {
		echo $e;
	}

	try {
		$SophieAsciify->serveColor('./img/02.jpg');
	} catch(Exception $e) {
		echo $e;
	}

	try {
		$SophieAsciify->serveGrayscale('./img/03.jpg');
	} catch(Exception $e) {
		echo $e;
	}

	try {
		$SophieAsciify->serveColor('./img/04.jpg');
	} catch(Exception $e) {
		echo $e;
	}

	try {
		$SophieAsciify->serveGrayscale('./img/05.jpg');
	} catch(Exception $e) {
		echo $e;
	}

	try {
		$SophieAsciify->serveGrayscale('./img/06.jpg');
	} catch(Exception $e) {
		echo $e;
	}
?>