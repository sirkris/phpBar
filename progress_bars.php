<html>

<head>
<title>Test</title>
</head>

<body bgcolor="white" text="black" link="green" vlink="darkgreen" alink="blue">

<?php

for ( $i = 0; $i <= 100; $i++ )
{
	print "<img src=\"make.phpbar.php?percent=$i\" /><br /><b>$i%</b><br /><br />";
}

?>
</body>

</html>
