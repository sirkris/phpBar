<html>

<head>
<title>Test</title>
</head>

<body bgcolor="white" text="black" link="green" vlink="darkgreen" alink="blue">

<?php

if ( !isset( $_SESSION ) )
{
	session_start();
}

for ( $i = 0; $i <= 100; $i++ )
{
	print "<img src=\"make.phpbar.php?percent=$i&timings=1" . ($i == 0 ? "&init=1" : NULL) . "\" /><br /><b>$i%</b><br /><br />";
}

/* Wait until all timings have been collected.  --Kris */
$timeout = 25;
do
{
	sleep( 1 );
	$timeout--;
	if ( $timeout == 0 )
	{
		$timeout = -1;
		break;
	}
} while ( !isset( $_SESSION["phpbar_timings"] ) || count( $_SESSION["phpbar_timings"] ) < 101 );

if ( $timeout == -1 )
{
	print "<b>ERROR!  Timed-out waiting for timings (expected == 101, actual == " . count( $_SESSION["phpbar_timings"] ) . ", irony == 1)</b>";
	//var_dump( $_SESSION["phpbar_timings"] );
}
else
{
	print "<b>Timings:</b><br /><br />";
	
	$total = 0;
	foreach ( $_SESSION["phpbar_timings"] as $skey => $timings )
	{
		print "<b>$skey</b>:&nbsp; " . ($timings["end"] - $timings["start"]) . " sec<br />";
		
		$total += ($timings["end"] - $timings["start"]);
	}
	
	print "<br /><b>Total:</b>&nbsp; $total sec<br />\r\n";
}

?>
</body>

</html>
