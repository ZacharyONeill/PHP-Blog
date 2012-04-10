<?php


//return duplicate values in an array as an array with the values that appear >  1 time
//if an associative array, will return the key for the last element that has the same value.
function array_not_unique( $a = array() )
{
  return array_diff_key( $a , array_unique( $a ) );
}
function find_na($var)
{
    // returns whether the value passed in is = "na"
    return($var == "na");
}
//is an array an associative array or not
//an alternative: return  array_values($arr) === $arr; //true if yes

function isIndexedArray($array){
    return  ctype_digit( implode('', array_keys($array) ) );
}
//remove an item from an array
function remove_item_by_value($array, $val = '', $preserve_keys = true) {
    if (empty($array) || !is_array($array)) return false;
    if (!in_array($val, $array)) return $array;
 
    foreach($array as $key => $value) {
        if ($value == $val) unset($array[$key]);
    }
 
    return ($preserve_keys === true) ? $array : array_values($array);
}
//allows some tags
function sanitize($val) {
	$val = trim($val);
	$val = strip_tags($val, "<h1><h2><h3><p><img><a><strong><em><ol><ul><li>");
	//$val = htmlentities($val);
	$val = stripslashes($val);
	return $val;
}
function getStartInfo($db) {
	$str = "<h2>Tables:</h2>\n<ul>\n";
	$tableArray = $db->getValidTableNames("demo_%");
	foreach ($tableArray as $table) {
		$numRecs = $db->getNumRecords($table);
		$pk = implode(" , ", $db->getPrimaryKey($table));
		$str.="<li>$table has $numRecs rows. The primary key(s) is '$pk'</li>\n";
	}
	$str.="</ul>\n";
	
	return $str;
}
function createColumnNames( $db, $tableName) 
{
	$column = $db->getColNames( $tableName );
	
	$sub = "<ul>";
	foreach($column as $col) {
		$sub .= "<li>$col</li>";
	}
	$sub .= "</ul>";
	
	return $sub;

}
function createColumnInfo( $db, $tableName) 
{
	$column = $db->getColInfo( $tableName );
	
	$sub = "<table border='1'><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
	foreach($column as $k=>$col) {
		$type = $col['Type'];
		$null = $col['Null'];
		$key = $col['Key'];
		$default = $col['Default'];
		$extra = $col['Extra'];
		
		$sub .= "<tr><td> $k </td><td> $type </td><td> $null </td><td> $key </td><td> $default </td><td> $extra </td></tr>";
		
	}
	$sub .= "</table>";
	
	return $sub;

}

function createQuery( $db, $tableName, $numDisplay, $page, $tableName2 ) 
{
	if( $tableName2 == "" ) 
	{
		$qu = "Select city, county, state, zip from $tableName order by city asc limit ?, ?";
	}else
	{
		
		$qu = "Select city, county, statename, zip from $tableName left join $tableName2 On state=abbrev order by city asc limit ?, ?";
	}
	
	$offset = ($page-1) * $numDisplay;
	$numRecords = $db->getNumRecords($tablename);
	$sub = "";
	if($numRecords/$numDisplay <= $page) 
	{
		$vars = array($offset, $numDisplay);
		$types = array("i", "i");
		
		$err = $db->doQuery($qu, $vars, $types);
		
		if($err == "" ) 
		{
			$rows = $db->fetch_all_array();
			$sub .= "<h1>Page $page</h1>";
			if( $tableName2 == "" ) 
			{
				$sub .= "<table border='1'><tr><th>City</th><th>County</th><th>State</th><th>Zip</th></tr>";
			}else
			{
				
				$sub .= "<table border='1'><tr><th>City</th><th>County</th><th>State Name</th><th>Zip</th></tr>";
			}
					
			
			
			foreach($rows as $row) 
			{
				$city = $row['city'];
				$county = $row['county'];
				if( $tableName2 == "" ) 
				{
					
					$state = $row['state'];
				}else
				{
					
					$state = $row['statename'];
				}
				
				$zip = $row['zip'];
				
				$sub .= "<tr><td> $city </td><td> $county </td><td> $state </td><td> $zip </td></tr>";
			}
			
			$sub .= "</table>";
			return $sub;
		}
		else 
		{
			return $err;
		}
	}
}

function insertRow($db, $tableName, $vars, $types) //inserts rows
{
	
	$qu = "insert into $tableName values(";
	for($i = 0; $i < count($vars); $i++) 
	{
		$qu .= "?";
		if($i != count($vars)-1) 
		{
			$qu .= ",";
		}
	}
	$qu .= ")";
	
	$err = $db->doQuery($qu, $vars, $types);
	
	if($err == "") {
		return "<h2>Record Added Successfully.</h2>";
	}else {
		return $err;
	}

}

function updateRow($db, $tableName, $vars, $types) //updates rows
{
	$qu = "update $tableName set ";
	
	$columnNames = $db->getColNames($tableName);
	
	for($i = 0; $i < count($columnNames); $i++) 
	{
			$qu .= "$columnNames[$i] = ? ";
			if($i != count($columnNames)-1) 
			{
				$qu .= ",";
			}
		
	}
	$qu .= "where $columnNames[0] = $vars[0]";
	
	$err = $db->doQuery($qu, $vars, $types);
	
	if($err == "") 
	{
		return "<h2>Record Updated Successfully.</h2>";
	}else 
	{
		return $err;
	}
}

function deleteRow($db, $tableName, $vars, $types) //deletes rows
{
	$columnNames = $db->getColNames($tableName);
	$qu = "Delete from $tableName where $columnNames[0] = ?";
	
	
	$err = $db->doQuery($qu, $vars, $types);
	
	if($err == "") 
	{
		return "<h2>Record Deleted Successfully.</h2>";
	}else 
	{
		return $err;
	}
}
function validateUser( $userName, $password, $db, $tableName ) //this function looks up the currently logged in user and returns their permissions
{
	$qu = "Select password, access from $tableName where username = ?";
	$vars = array($userName);
	$types = array("s");
	$err = $db->doQuery($qu, $vars, $types);
	$pass = $db->fetch_all_array();

	if( $err == "" )
	{
		if( sha1($password) == $pass[0]['password'] ) 
		{
			return $pass[0]['access'];
		}else
		{
			return 0;
		}
	
	}else
	{
		return 0;
	}	
}
function loadXML( $db ) //this function is for the load class. it takes in a database and also allows the user to designate a file to load.
{
	$count = 0;
	if(isset($_POST['fileName']) && isset($_POST['table']))
	{
		$xmlfile = $_POST['fileName'];
		$tableName = $_POST['table'];
		$dom = new DomDocument();
		$dom->load($xmlfile);
		if($xmlfile == 'news.xml') //if file is news...
		{
			$drop1 = 'post';
			$drop2 = 'subject';
			$drop3 = 'content';
			$drop4 = 'date';
			
		}else if($xmlfile == 'editorial.xml') //if file is editorial...
		{
			
			$drop1 = 'editorial';
			$drop2 = 'yes';
			$drop3 = 'content';
			$drop4 = 'no';	
			
		}elseif($xmlfile == 'banners.xml') //if file is banners...
		{
			$drop1 = 'banner';
			$drop2 = 'name';
			$drop3 = 'weight';
			$drop4 = 'count';
		}
		
		$colNames = $db->getColNames($tableName);	
		if(isset($_POST['submit'])) //if submit is clicked
		{
			$trunk =("TRUNCATE TABLE $tableName"); //this erases all current data in the table
			$err = $db->doQuery($trunk);
			if($err == "")
			{
			
			}
			else
			{
				
				$status .= "Truncate not done.";
			}
			
			
			$qu = "insert into $tableName ($colNames[0], $colNames[1], $colNames[2],$colNames[3]) values ('',?,?,?)";
			$elements = $dom->getElementsByTagName($drop1);
			$elemCount = $elements->length;
			for($i = 0; $i <= $elemCount-1; $i++)
			{
				$element = $elements->item($i);
				if($xmlfile != 'editorial.xml')
				{

					$col1 = $element->getAttribute($_POST[$colNames[1]]);
					$col3 = $element->getAttribute($_POST[$colNames[3]]);
					if($xmlfile=='news.xml')
					{
						$col2 = $element->getElementsByTagName($_POST[$colNames[2]])->item(0)->nodeValue;
						$col3 = strtotime($col3);
					}else
					{
						$col2 = $element->getAttribute($_POST[$colNames[2]]);
					}

					$types= array("s","s","i");
				}else
				{
					$col1 = $elements->item(0)->nodeValue;
					$col2 = $_POST[$colNames[2]];

					if($col2 == 'yes')
					{
						$col2 = 1;
						
					}elseif($col2 == 'no')
					{
						$col2 = 0;
					}
					$col3 = strtotime('now'); //to convert into time...
					$types = array("s","i","i");
				}
				
				$vars = array($col1,$col2,$col3);
				$err = $db->doQuery($qu,$vars,$types);

				if($err == "")
				{
					$status .= "row $i added<br/>";
				}
				else
				{
					$status .= "Error. Row not Added.";
				}	
			}
		}
	}
	if(isset($_POST['submitTable'])) //if submit table button is clicked
	{
		$tableName = $_POST['table'];
		$file = $_POST['fileName'];
		$sub .= "<form action='load.php' method='POST'>";
		$sub .= "<h1>Column Names for: $tableName</h1>";
		$sub .= "<ul>";

		foreach($colNames as $column)
		{
			$list = "<select name=$column>
				<option value='$drop2'>$drop2</option>
				<option value='$drop3'>$drop3</option>
				<option value='$drop4'>$drop4</option>
				</select>";
			if($column != "id" && $column != "archivaldate")
			{
				$sub .= "<li>$column</li>".$list;
			}else
			{
				$sub .= "<li>$column</li>";
			}
		}
		$sub .= "</ul>
			<input type='hidden' name='table' value=$tableName />
			<input type='hidden' name='fileName' value=$file />
			<p><input type='submit' name='submit' value='Get Table Info'/></p>
			</form>";
	}else
	{
		$sub .= "<form action='load.php' method='POST'>";
		$list = "<select name='table'>
		<option value='cms_news'>cms_news</option>
		<option value='cms_banner'>cms_banner</option>
		<option value='cms_editorial'>cms_editorial</option>
		</select>";
		$sub .= "<p>Table: " . $list . " File: <input type='text' name='fileName' size='40' value='' />
				<p><input type='submit' name='submitTable' value='Load'/></p>";
		$sub .= "</form>";
	}
	$sub .= $status;
	return $sub;
}
function getCurrentEditorial( $db, $tableName ) //this returns the editorial content marked current, if more than 1 marked current, returns the first one.
{
	$qu = "Select content from $tableName where current = ?";
	$current = 1;
	$vars = array($current);
	$types = array("i");
	$err = $db->doQuery($qu, $vars, $types);
	$theContent = $db->fetch_all_array();
	$sub = $theContent[0]['content'];
	if( $err == "" )
	{
		
		return $sub;
	
	}else
	{
		
		return "None Found";
	}
	
	
	
}
function getNews($db, $tableName, $thePage = 1, $theCount = 5) //this creates the display of the news.xml file in the browser based on page and count. Default page is 1 and default count is 5
{

	
	$qu = "select * from $tableName order by pubdate asc limit ?,?";
	if($tableName == 'cms_ads')
	{
		$qu = "select * from $tableName where approved = 1 order by pubdate asc limit ?,?";
		$theTable = "cms_ads_which_edition";
		$theID = "ads_id";
	}else
	{
		$theTable = "cms_news_which_edition";
		$theID = "news_id";
	}
	if(isset($_GET['count'])&& is_numeric($_GET['count']))
	{
		$theCount = $_GET['count'];
	}
	$theRecords = $db->getNumRecords($tableName);
	$numPages = $theRecords/$theCount;
	$newPages = ceil($numPages);
	if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] <= $newPages)
	{
		$thePage = $_GET['page'];

	}

	$offset = ($thePage-1) * $theCount;
	
	if($thePage <= $newPages)
	{
		
		$vars = array($offset, $theCount);
		$types = array("i","i");
		
		$err = $db->doQuery($qu,$vars,$types);
		if($err == "")
		{
			
			$res = $db->fetch_all_array();
		
			$dom = new DomDocument();
			$root = $dom->appendChild($dom->createElement("root"));
			foreach($res as $r)
			{

				$id = $r['id'];
				$theEdition = "select edition_id from $theTable where $theID = $id";
				$theRecords = $db->doQuery($theEdition);
				$content = $r['content'];

				if($tableName == "cms_ads")
				{
					$name = $r['title'];

				}else
				{
					$name = $r['subject'];

				}
				$theDate = $r['pubdate'];
				$theDate = date("D, M Y, G:i:s T", $theDate);
				//all of the XML creating
				$root->appendChild($dom->createAttribute("PageNumber"))->appendChild($dom->createTextNode($thePage));
				$root->appendChild($dom->createAttribute("TotalPages"))->appendChild($dom->createTextNode($newPages));
				$root->appendChild($dom->createAttribute("NumberPerPage"))->appendChild($dom->createTextNode($theCount));
				$tag = $root->appendChild($dom->createElement("post"));

				$tag->appendChild($dom->createAttribute("title"))->appendChild($dom->createTextNode($name));
				$tag->appendChild($dom->createAttribute("time"))->appendChild($dom->createTextNode($theDate));

				$tag->appendChild($dom->createElement("content",$content));
				$theRecords = $db->doQuery($theEdition);
				
				if($theRecords == "")
				{
					
					$theEdition = $db->fetch_all_array();

					if(count($theEdition) == 0)
					{

						$tag->appendChild($dom->createElement("edition"));
					}else
					{
						$editXML = $tag->appendChild($dom->createElement("edition"));
						foreach($theEdition as $e)
						{
							$edID = $e['edition_id'];
							$edquery = "select editionname from cms_edition where id = $edID";
							
							$quer = $db->doQuery($edquery);
							if($quer == "")
							{
								$arr = $db->fetch_all_array();

								if(count($arr)==0)
								{
									
									
									$editXML->appendChild($dom->createElement("Name",'All'));
								}else
								{
									
									foreach($arr as $a)
									{
										$editXML->appendChild($dom->createElement("Name",$a['editionname']));
									}
								}
							}
						}
					}
				}else
				{
					echo "mysqli error:";
				}
				
			}
			$dom->formatOutput = true;
		}else
		{
			echo "Error";
		}
		
		return $dom->saveXML();
	}
} 



function populate($db, $tableArray, $rights) //this is a function that dynamically creates, adds, edits, deletes, etc from the database. Parameters are a database name, tablename, and the rights of the user.
{
	$sub2 = '';
	
	if(isset($_POST['whichTable']))  //to see if the whichTable button is set
	{
			$theTable = $_POST['chosenTable'];
			$sub2 .= '<h2>'.$theTable.'</h2>';
			$sub2 .= getTableName($db, $theTable);		
	}
	
	if(isset($_POST['submitAdd']))  //checks to see if the submitAdd button was pressed, and adds accordingly
	{	
		$tabName = $_POST['tab'];
		$columnNames = $db->getColNames($tabName);
		$vars = array();
		$types = array();
		
		for($i=0;$i<count($columnNames);$i++) 
		{
			if($columnNames[$i] == 'password') 
			{
				$vars[$i] = sha1($_POST[$columnNames[$i]]);
			}else 
			{
				$vars[$i] = $_POST[$columnNames[$i]];
			}
		}

		for($i = 0; $i < count($vars); $i++) 
		{
			if(is_numeric($vars[$i])) 
			{
				$types[$i] = 'i';
			}else {
				$types[$i] = 's';
			}
		}
		
		$sub2 .= insertRow($db, $tabName, $vars, $types);
		
	}else if(isset($_POST['submitEdit'])) //is edit pressed? if so...
	{
		$tabName = $_POST['tab'];
		$columnNames = $db->getColNames($tabName);
		$vars = array();
		$types = array();
		
		for($i=0; $i < count($columnNames); $i++) 
		{
			if($columnNames[$i] == 'password') 
			{
				$vars[$i] = sha1($_POST[$columnNames[$i]]);
			}else 
			{
				$vars[$i] = $_POST[$columnNames[$i]];
			}
		
		}
	
		for($i=0; $i < count($vars); $i++) 
		{
			if(is_numeric($vars[$i])) 
			{
				$types[$i] = 'i';
			}else {
				$types[$i] = 's';
			}
		}
		
		$sub2 .= updateRow($db, $tabName, $vars, $types);
	
	}
	
	if(isset($_POST['edit']))  //this checks to see which row edit was pressed on
	{
		$tabName = $_POST['tabname'];
		$columnNames = $db->getColNames($tabName);
		$query = "select * from $tabName where $columnNames[0] = ?";
		
		$vars = array(intval($_POST[$columnNames[0]]));
		$types = array('i');
		$err = $db->doQuery($query, $vars, $types);
		
		
		
		if($err == '') 
		{
			
			$fetch = $db->fetch_all_array();
			$arr = array();
			$sub2 .= '<form action="admin.php" method="POST">';
			
			foreach($fetch as $fet) {
			
				for($i=0;$i<count($fet);$i++) 
				{
					$arr[$i] = $fet[$columnNames[$i]];
					if($columnNames[$i] == 'content') 
					{
						$sub2 .= '<h3>' . $columnNames[$i] . ':</h3><br/><textarea name="' . $columnNames[$i] . '" rows="40" cols="50">' . $arr[$i] . '</textarea>';
					}else if($columnNames[$i] == 'approved') 
					{
						
						if($arr[$i] == 1) 
						{
							$sub2 .= '<h3>' . $columnNames[$i] . ': </h3> Yes <input type="radio" name="' . $columnNames[$i] . '" value="1" checked="checked" />
										No <input type="radio" name="'.$columnNames[$i].'" value="0" />';
						}else 
						{
							$sub2 .= '<h3>' . $columnNames[$i] . ': </h3> Yes <input type="radio" name="' . $columnNames[$i] . '" value="1" />
										No <input type="radio" name="' . $columnNames[$i] . '" value="0" checked="checked"/>';
						}
					}else 
					{
						$sub2 .= '<h3>' . $columnNames[$i] . ': </h3><input type="text" name="' . $columnNames[$i] . '" value="' . $arr[$i] . '"/>';
					}
				}
			}
			$sub2 .= '<input type="hidden" name="tab" value="' . $tabName . '"/>
					<br/><input type="submit" name="submitEdit" value="Edit" />';
			$sub2 .= '</form>';
			
		}else {
			return $err;
		}
	}else if(isset($_POST['delete'])) //if delete is hit on a row...
	{
		
		$tabName = $_POST['tabname'];
		$columnNames = $db->getColNames($tabName);
		
		$vars = array($_POST[$columnNames[0]]);
		if(is_numeric($_POST[$columnNames[0]])) 
		{
			$types = array('i');
		}else 
		{
			$types = array('s');
		}
		
		$sub2 .= deleteRow($db, $tabName, $vars, $types);
	}
	
	if(isset($_POST['add'])) //when add is hit...
	{
		
		$tabName = $_POST['theTableName'];
		$columnNames = $db->getColNames($tabName);
		
		$sub2 .= '<form action="admin.php" method="POST">';
		
		for($i=0;$i<count($columnNames);$i++) 
		{
			
			if($columnNames[$i] == 'content') 
			{
				$sub2 .= '<h3>'.$columnNames[$i].':</h3><br/><textarea name="'.$columnNames[$i].'" rows="40" cols="50"></textarea>';
			}else if($columnNames[$i] == 'approved') 
			{
				$sub2 .= '<h3>'.$columnNames[$i].': </h3> Yes <input type="radio" name="'.$columnNames[$i].'" value="1" checked="checked" />';
				$sub2 .= ' No <input type="radio" name="'.$columnNames[$i].'" value="0" />';
			}else 
			{
				$sub2 .= '<h3>'.$columnNames[$i].': </h3><input type="text" name="'.$columnNames[$i].'" value="'.$arr[$i].'"/>';
			}
			
		}
		$sub2 .= '<input type="hidden" name="tab" value="'.$tabName.'"/>
					<br/><input type="submit" name="submitAdd" value="Submit" />';
		$sub2 .= '</form>';
		
		
	}
	
	$sub = '<form action="admin.php" method="POST">
			<p>Choose Table: </p><select name="chosenTable">';
	foreach($tableArray as $tablename) 
	{
		if($rights == 1) 
		{
			$sub .= '<option value="' . $tablename . '">' . $tablename . '</option>';
		}else 
		{
			if($tablename == 'cms_ads' || $tablename == 'cms_banner' || $tablename == 'cms_news' || $tablename == 'cms_editorial') 
			{
				$sub .= '<option value="' . $tablename . '">' . $tablename . '</option>';
			} 
		}
	}
	
	$sub .= '</select> <input type="submit" name="whichTable" value="Submit" />';
	$sub .= '</form>';
	$sub .= $sub2;
	
	
	return $sub;
}

function getTableName($db, $tableName) //returns the names of the tables. Takes in a database and a tablename
{
	$columnNames = $db->getColNames($tableName);
	$sub = '<form action="admin.php" method="POST"><table border="1"><tr>';
	
	foreach($columnNames as $column) 
	{
		$sub .= '<th>'.$column.'</th>';
		
	}
	$sub .= '</tr>';
	$qu = "select * from $tableName order by $columnNames[0] asc";
	$err = $db->doQuery($qu);
	
	if($err == '') 
	{
		$fetch = $db->fetch_all_array();
		$arr = array();
		foreach($fetch as $fet) 
		{
			$sub .= '<tr><form action="admin.php" method="POST">';
			for($i = 0; $i < count($fet); $i++) 
			{
				$arr[$i] = $fet[$columnNames[$i]];
				if( $columnNames[$i] == 'pubdate' || $columnNames[$i] == 'archivaldate')
				{
					$arr[$i] = date("F j, Y, g:i a", $arr[$i]);
				}
				$sub .= '<td><input type="text" name="'.$columnNames[$i].'" value="'.$arr[$i].'" readonly="readonly"/></td>';
			}
			$sub .= '<input type="hidden" name="tabname" value="'.$tableName.'" />
					<td><input type="submit" name="edit[]" value="Edit" /></td>
					<td><input type="submit" name="delete[]" value="Delete" /></td>
					</form></tr>';
		}
	}else 
	{
		return $err;
	}

	$sub .= '</table><br/>';
	$sub .= '<input type="hidden" name="theTableName" value="' . $tableName . '" />';
	$sub .= '<input type="submit" name="add" value="Add a New Record" />';
	$sub .= '</form>';
	
	return $sub;
}
function getBanner($db, $tableName) 
{
    $query = "select filename, count FROM $tableName order by count asc";
   
    $err = $db->doQuery($query);
    $fetch = $db->fetch_all_array();
    $sub = $fetch[0]['filename'];
    $count = intval($fetch[0]['count']) + 1;
    $qu = "update $tableName set count = $count where filename = '$sub'";
    $db->doQuery($qu);

    if( $err == "" ) {
        return $sub;
    }else {
        return $err;
	}
}
?>