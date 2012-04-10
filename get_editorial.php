<?php

		require_once "P3_lib.php";
		session_start();
		

		function __autoload($className) 
		{
			require_once $className . '.class.php';
		}
		//get a singleton instance of the database class
		$db = Database::getInstance();
		echo Page::navigation();
		
		echo Page::header("Editorial");
		echo getCurrentEditorial($db, "cms_editorial");
		echo Page::footer();
			
		
?>