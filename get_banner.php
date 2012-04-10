<?php

		require_once "P3_lib.php";
		session_start();	
		function __autoload($className) 
		{
			require_once $className . '.class.php';
		}
		$db = Database::getInstance();
		
		echo Page::navigation();
		echo Page::header("Banners");
		
		echo getBanner($db, 'cms_banner');

		echo Page::footer();
			
		
?>