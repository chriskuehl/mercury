<?php
function startsWith($haystack, $needle) {
    return !strncmp($haystack, $needle, strlen($needle));
}

function isCorrectPassword($email, $password) {
	$stream = imap_open("{mail.mercury.techxonline.net:993/imap/ssl}INBOX", $email, $password, OP_READONLY, 0);
	return $stream ? true : false;
}

// have they submitted?
$error = null;
$worked = false;

if (isset($_POST['change'])) {
	// verify their information
	$domain = $_POST['domain'];
	$name = $_POST['name'];
	$oldPassword = $_POST['oldPassword'];
	$newPassword = $_POST['newPassword'];
	$verifyNewPassword = $_POST['verifyNewPassword'];
	
	
	if (strpos($domain, "..") !== FALSE || strpos($domain, "/") !== FALSE || strpos($domain, " ") !== FALSE || strlen($domain) < 3) { // make sure domain is clean
		$error = "Use a valid domain name!";
	} else if (strpos($name, "..") !== FALSE || strpos($name, "/") !== FALSE || strpos($name, " ") !== FALSE || strlen($name) < 3) { // make sure name is clean
		$error = "Use a valid name!";
	} else if (! is_dir("/home/email/email/mail/" . $domain . "/")) { // make sure the domain exists
		$error = "I don't see that domain name!";
	} else if ($newPassword != $verifyNewPassword) { // new passwords match?
		$error = "Those passwords didn't match!";
	} else if (strlen($newPassword) < 4 || strlen($newPassword) > 250) { // new passwords valid?
		$error = "Your new password must be between 4 and 250 characters in length.";
	} else if (strlen($name) < 1) { // entered name?
		$error = "Enter your current email address.";
	} else if (! isCorrectPassword($name . "@" . $domain, $oldPassword)) { // old password is right?
		$error = "The password you entered doesn't match the current one.";
	} else {
		$worked = true;
		file_put_contents("/home/email/email/mail/$domain/$name/shadow", "$name:{plain}$newPassword");
	}
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Mercury Mail: Password Change</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow" />

    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      
      td {
	      vertical-align: middle !important;
      }
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="/">Mercury Mail</a>
        </div>
      </div>
    </div>

    <div class="container">
    
	<ul class="breadcrumb">
	  <li><a href="/">Mercury Mail</a> <span class="divider">/</span></li>
	  <li><a href="/docs/">Help</a> <span class="divider">/</span></li>
	  <li class="active">Password Change</li>
	</ul>
      <h1>Password Change</h1>
      
      <?php
      if ($error != null) {
      ?>
      <div class="alert alert-error">
	<?php echo $error; ?>
	</div>
      <?php
      } else if ($worked) {
      	?>
      	<div class="alert alert-success">
		Your password was changed!
	</div>
      	<?php
      }
      ?>
      
      <p>Enter the information below to change your password. You will need to update your password on any email clients you use (e.g. phones, Thunderbird, ...).</p>
      
      <form class="form-horizontal" method="POST">
	  <div class="control-group">
	    <label class="control-label" for="inputEmail">Email</label>
	    <div class="controls">
	      <input type="text" id="inputEmail" style="width: 120px;" name="name" placeholder="chris">
	      @
	      <select name="domain">
	      	<?php
	      	foreach (scandir("/home/email/email/mail") as $domain) {
	      		if (startsWith($domain, ".")) {
	      			continue;
	      		}
	      		
	      		echo "<option value=\"$domain\">$domain</option>"; // potential injection attack...
	      	}
	      	?>
	      </select>
	    </div>
	  </div>
	  <div class="control-group">
	    <label class="control-label" for="oldPassword">Old Password</label>
	    <div class="controls">
	      <input type="password" name="oldPassword" id="oldPassword" placeholder="">
	    </div>
	  </div>
	  
	  <div class="control-group">
	    <label class="control-label" for="newPassword">New Password</label>
	    <div class="controls">
	      <input type="password" name="newPassword" id="newPassword" placeholder="">
	    </div>
	  </div>
	  
	  <div class="control-group">
	    <label class="control-label" for="verifyNewPassword">Verify New Password</label>
	    <div class="controls">
	      <input type="password" name="verifyNewPassword" id="verifyNewPassword" placeholder="">
	    </div>
	  </div>
	  <div class="control-group">
	    <div class="controls">
	      <button type="submit" name="change" class="btn">Change Password</button>
	    </div>
	  </div>
	</form>
    </div> <!-- /container -->
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>
