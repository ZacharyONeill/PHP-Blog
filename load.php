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
		if ($_SESSION['rights'] == 1) 
		{
			echo Page::header("Load");
			echo loadXML($db);
			echo Page::footer();
			
		}else
		{
			echo "You are not logged in as an Admin.";
			
		}
?>