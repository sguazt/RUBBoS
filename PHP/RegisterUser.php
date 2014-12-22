<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "RegisterUser.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();
    
    $firstname = getSessionPostGetParam('firstname');
    if (!isset($firstname))
	{
      printError($scriptName, $startTime, "Register user", "You must provide a first name!");
      exit();
    }
      
    $lastname = getSessionPostGetParam('lastname');
    if (!isset($lastname))
	{
      printError($scriptName, $startTime, "Register user", "You must provide a last name!");
      exit();
    }
      
    $nickname = getSessionPostGetParam('nickname');
    if (!isset($nickname))
	{
      printError($scriptName, $startTime, "Register user", "You must provide a nick name!");
      exit();
    }

    $email = getSessionPostGetParam('email');
    if (!isset($email))
	{
      printError($scriptName, $startTime, "Register user", "You must provide an email address!");
      exit();
    }

    $password = getSessionPostGetParam('password');
    if (!isset($password))
	{
      printError($scriptName, $startTime, "Register user", "You must provide a password!");
      exit();
    }

    getDatabaseLink($link);

    // Check if the nick name already exists
    $nicknameResult = mysql_query("SELECT * FROM users WHERE nickname=\"$nickname\"", $link);
	if (!$nicknameResult)
	{
		error_log("[".__FILE__."] Nickname query 'SELECT * FROM users WHERE nickname=\"$nickname\"' failed: " . mysql_error($link));
		die("ERROR: Nickname query failed for nickname '$nickname': " . mysql_error($link));
	}
    if (mysql_num_rows($nicknameResult) > 0)
    {
      printError($scriptName, $startTime, "Register user", "The nickname you have choosen is already taken by someone else. Please choose a new nickname!");
      mysql_free_result($nicknameResult);
      exit();
    }
    mysql_free_result($nicknameResult);

    // Add user to database
    $now = date("Y:m:d H:i:s");
    $result = mysql_query("INSERT INTO users VALUES (NULL, \"$firstname\", \"$lastname\", \"$nickname\", \"$password\", \"$email\", 0, 0, '$now')", $link);
	if (!$result)
	{
		error_log("[".__FILE__."] Failed to insert new user in database 'INSERT INTO users VALUES (NULL, \"$firstname\", \"$lastname\", \"$nickname\", \"$password\", \"$email\", 0, 0, '$now')': " . mysql_error($link));
		die("ERROR: Failed to insert new user in database for nickname '$nickname': " . mysql_error($link));
	}

    $result = mysql_query("SELECT * FROM users WHERE nickname=\"$nickname\"", $link);
	if (!$result)
	{
		error_log("[".__FILE__."] Query 'SELECT * FROM users WHERE nickname=\"$nickname\"' failed: " . mysql_error($link));
		die("ERROR: Query user failed for nickname '$nickname': " . mysql_error($link));
	}
    $row = mysql_fetch_array($result);

    printHTMLheader("RUBBoS: Welcome to $nickname");

    print("<h2>Your registration has been processed successfully</h2><br>\n");
    print("<h3>Welcome $nickname</h3>\n");
    print("RUBBoS has stored the following information about you:<br>\n");
    print("First Name : ".$row["firstname"]."<br>\n");
    print("Last Name  : ".$row["lastname"]."<br>\n");
    print("Nick Name  : ".$row["nickname"]."<br>\n");
    print("Email      : ".$row["email"]."<br>\n");
    print("Password   : ".$row["password"]."<br>\n");
    print("<br>The following information has been automatically generated by RUBBoS:<br>\n");
    print("User id       :".$row["id"]."<br>\n");
    print("Creation date :".$row["creation_date"]."<br>\n");
    print("Rating        :".$row["rating"]."<br>\n");
    print("Access        :".$row["access"]."<br>\n");
    
    mysql_free_result($result);
    mysql_close($link);
    
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
