<?php
require_once "P3_lib.php";
require_once "Page.class.php";

session_start();
function __autoload($className) {
		require_once $className . '.class.php';
	}

			
		//get a singleton instance of the database class
		$db = Database::getInstance();
		$table_names = $db->getValidTableNames("cms_%");
if ($_POST && !empty($_POST['user']) && !empty($_POST['password'])) 
{
	$_SESSION['user'] = $_POST['user'];
	//$_SESSION['rights'] = $userValidated;
	$_SESSION['pass'] = $_POST['password'];
	$userValidated = validateUser( $_SESSION['user'], $_SESSION['pass'], $db, "cms_user" );
	$_SESSION['rights'] = $userValidated;	
		
	//echo "Rights are " . $_SESSION['rights'];
}
if(!isset($_SESSION['user']))
{
	header("Location:login.php");
}else
{
	
		
		//echo Page::header("Database Class Usage");




	
	//echo $_SESSION['user'];
	$v_expire = NULL; 
	$v_path="/~zto7115/"; // use your own account
	$v_domain="nova.it.rit.edu";
	$v_secure=false;

	//echo "The user is: " . $_SESSION["user"] . "<---";
	setcookie ("user", $_SESSION['user'], $v_expire, $v_path,$v_domain, $v_secure);
	//setcookie ("password", $_POST["password"], $v_expire, $v_path,$v_domain, $v_secure);
	$cookie = $_COOKIE["user"];



	//if(($_POST['user'] == $adminName && $_POST['password'] == $adminPassword) || ($_POST['user'] == $editorName && $_POST['password'] == $editorPassword))
	if($_SESSION['rights'] == 1 || $_SESSION['rights'] == 2 || $_SESSION['rights'] == 3) //IF USER IS VALIDATED
	{
		
		
		
		// initiate session
		
		// check that form has been submitted and that name is not empty
		//echo $_SESSION['rights'];
		if( $_SESSION['rights'] == 1) //IF USER HAS ADMIN RIGHTS
		{
			$dis = populate( $db, $table_names, 1 );
			//echo "You have admin rights";
		}elseif( $_SESSION['rights'] == 2 ) //IF USER HAS EDITOR RIGHTS
		{
			$dis = populate( $db, $table_names, 2 );
			//all editor rights in here
			//echo "You have editor rights";

		}elseif( $_SESSION['rights'] == 3 ) //IF USER HAS ADVERTISER RIGHTS
		{
			$dis = populate( $db, $table_names, 3 );
			//all advertiser rights in here
			//echo "You have advertiser rights";

		}
	
	}
	else
	{
		unset($_SESSION['user']);
		 
		session_destroy();
		header("Location:login.php");
	
	}
	echo Page::header("Admin Page");
	echo Page::navigation();
	echo $dis;
}	
	
		/*
		require_once 'Page.class.php';
		require_once 'P3_lib.php';

		echo Page::header("Admin Page");
		
		//put code in here to check access and logged in or not
		
			//uses singleton class - only one instance of the class is allowed
			$db = Database::getInstance();

			echo Page::navigation();

			//get valid table names
			$table_names = $db->getValidTableNames("cms_%");
			
			//check to see if any tables need to be removed for editor? Use provided functions in lib to accomplish?
			
			//select table to edit here
			*/
			/*****
			  *   2 types of tables:
			  *        1) simple tables that can just use input boxes (cms_user, cms_banners, cms_edition, cms_ads/news_which_edition)
			  *        2) more complex tables that have text areas and don't fit in one row of a table easily
			  *        
			  *	 Some of the tables have foreign keys in them, so will produce errors if constraints aren't met
			  *
			 */
			 
			 //perform requested action(s).  Try to write code that uses functions that will work for similar tables without
			 //duplicating code.  Use functions provided in database class to help with this
		
		//echo Page::footer();
		
?>