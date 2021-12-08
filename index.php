<html>
 <head>
  <link rel="stylesheet" href="style.css">
  <title>MySpamQL - Control Panel</title>
 </head>
 <body>
  <div id="UserForm">
   <h1 id="widget-title">MySpamQL</h1>
    <pre>Version 1.0.1</pre>
<?php
if(isset($_POST["submit"])){
// Select our input files
$usernames= 'usernames.txt';
$first_names = 'first_names.txt';
$last_names = 'last_names.txt';
$dom = 'domains.txt';
// Set Up Config
$DATABASE_HOST = htmlspecialchars($_POST["remotehost"]);
$DATABASE_USER = htmlspecialchars($_POST["dbusername"]);
$DATABASE_PASS = htmlspecialchars($_POST["dbuserpass"]);
$DATABASE_NAME = htmlspecialchars($_POST["dbname"]);
$USER_COUNT = htmlspecialchars($_POST["usercount"]);

//TESTING ONLY
echo $DATABASE_HOST."<br>";
echo $DATABASE_USER."<br>";
echo base64_encode($DATABASE_PASS)."<br>";
echo $DATABASE_NAME."<br>";
echo $USER_COUNT."<br>";
//

// Create connection
    $conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $DATABASE_NAME";
if ($conn->query($sql) === TRUE) {
  $conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $sql1 = "CREATE TABLE IF NOT EXISTS Users (ID int(11) AUTO_INCREMENT,
                      USER varchar(255) NOT NULL,
                      FIRST_NAME varchar(255) NOT NULL,
                      LAST_NAME varchar(255) NOT NULL,
                      EMAIL varchar(255) NOT NULL,
                      PASSWORD varchar(255) NOT NULL,
                      WEBSITEURL varchar(255) NOT NULL,
                      PERMISSION_LEVEL int,
                      PRIMARY KEY  (ID))";
  if($conn->query($sql1) === TRUE) {
    echo "<p><b>Database and User Table Online.</b></p>";
  }else{
    echo "<p><b>Database and User Table Offline.</b></p>" . $conn->error;
  }
} else {
  echo "<p><b>Error Creating Database: " . $conn->error;
}




try {
  $conn = new PDO("mysql:host=$DATABASE_HOST;dbname=$DATABASE_NAME", $DATABASE_USER, $DATABASE_PASS);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->beginTransaction();

// UNLIMITED SPAM!
// for ($x = 1; $x <= 10; $x--) {
// But For Now....
// Just 10 New Spam Accounts 9 + 1 (tweaked)
for ($x = 0; $x <= $USER_COUNT -1; $x++) {

// Generate User Name
$username = file($usernames);
$line = $username[array_rand($username)];
$username = $line;

// Generate First Name
$firstname = file($first_names);
$line = $firstname[array_rand($firstname)];
$firstname = $line;

// Generate Last Name
$lastname = file($last_names);
$line = $lastname[array_rand($lastname)];
$lastname = $line;

// Generate Email Address
$com = file($dom);
$line = $com[array_rand($com)];
$com = $line;
$com = preg_replace('/\s+/', '', $com);
$efirstname = preg_replace('/\s+/', '', $firstname);
$elastname = preg_replace('/\s+/', '', $lastname);
$email = $efirstname."@".$efirstname.$elastname.$com;
$email = preg_replace('/\s+/', '', $email);
$email = strtolower($email);
// Finished With Email

// Generate Random Password String
$pass = openssl_random_pseudo_bytes(16);
$password= bin2hex($pass);

// Generate Random Website
$websiteurl = 'http://www.'.$efirstname.$elastname.$com;
$websiteurl = preg_replace('/\s+/', '', $websiteurl);

// Enter Data To Database
  $conn->exec("INSERT INTO Users (USER, FIRST_NAME, LAST_NAME, EMAIL, PASSWORD, WEBSITEURL)
  VALUES ('$username', '$firstname', '$lastname', '$email', '$password', '$websiteurl')");
}
  $conn->commit();
  echo " <p><b>$x Users Created.</b></p>";
} catch(PDOException $e) {
  // roll back the transaction if something failed
  $conn->rollback();
  echo "<p><b>Error: " . $e->getMessage();
}
$conn = null;
//header('Refresh: 2; url=form.php');


echo '<input type="submit" onClick="window.location.href=window.location.href" value="Back"></input></p>';
} else{ ?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method ="post">
 <p>Remote Host IP: <input style="margin-left:5px;" type="text" name="remotehost"></p>
 <p>DB User Name: <input style="margin-left:13px;" type="text" name="dbusername"></p>
 <p>DB User Pass: <input type="password" style="margin-left:19px;" type="text" name="dbuserpass"></p>
 <p>DB Name: <input style="margin-left:48px;" type="text" name="dbname"></p>
 <p>User Count: <input style="margin-left:35px;" type="text" name="usercount"></p>
 <p><input type="submit" value="Create Users" name=submit></p>
</form>
 <?php } ?>
  </div>
 </body>
</html>
