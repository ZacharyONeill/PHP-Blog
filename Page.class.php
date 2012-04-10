<?php

class Page {
   
   static function header($title='untitled', $stylesheet='dummy.css'){
   	return <<<END
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>$title</title>
	<link type="text/css" rel="stylesheet" href="$stylesheet" />
</head>
<body>
END;
   }
   
	static function footer() {
		return <<<END
</body>
</html>
END;
   }
   
   static function navigation(){
   	return "<div class='nav'><a href='admin.php'>admin</a> | <a href='login.php'>login</a> | <a href='logout.php'>logout</a> | <a href='load.php'>load.php</a> | <a href='get_news.php?page=1&amp;count=5'>get_news.php </a> | <a href='get_ads.php?page=1&amp;count=5'>get_ads.php</a> | <a href='get_banner.php' >get_banner.php</a> | <a href='get_editorial.php' >get_editorial.php</a> | <a href='../B/index.php'>Go to Client App</a></div>\n";
   }

 } // end class Page
?>