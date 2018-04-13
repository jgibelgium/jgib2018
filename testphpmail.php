<html>
	<head>
		<title>Testing PHP mail from wampserver</title>
	</head>
	<body>
		<?php
		
		$message="Hello";
		// $to = "ron68be@gmail.com"; //ok
		$to = "ict@janegoodall.be"; //ok
		$subject="test mail";
		$result = mail($to,$message,$message);
		
		if ($result == TRUE)
		{
			echo "De mail is succesvol aanvaard.";
		}
		
		?>
		
	</body>
	
	
</html>