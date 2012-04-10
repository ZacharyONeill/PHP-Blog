<?php


	header("Content-Type: text/xml");
	require_once "P3_lib.php";

	function __autoload($className) {
	require_once $className . '.class.php';
	}


	//get a singleton instance of the database class
	$db = Database::getInstance();


	echo getNews($db,'cms_news');


?>