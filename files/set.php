<html><body>
<?php
//error_reporting('E_ALL');

$response = 200;

// only try to store if minimum required items are present
if(isset($_REQUEST['lat']) && isset($_REQUEST['long']) && isset($_REQUEST['who']))
{
	// extra's
	if(isset($_REQUEST['speed']))
	{
		$kmh = round((($_REQUEST['speed']) * 3600) / 1000);
	}
	else
	{
		$kmh = -1;
	}

	date_default_timezone_set('Europe/Brussels');

	$who = stripslashes($_REQUEST['who']); // try to filter crap
	$filename = "./data/".$who."/".$who.".dat";
	$logfile  = "./data/".$who."/log/".date('Ym').".log";
	$handle = @fopen($filename, "w");
	if($handle != FALSE)
	{
        // store location
		fwrite($handle, date('Y.m.d'));
		fwrite($handle, "\n");
		fwrite($handle, date('H:i:s'));
		fwrite($handle, "\n");
		fwrite($handle, $_REQUEST['lat']);
		fwrite($handle, "\n");
		fwrite($handle, $_REQUEST['long']);
		fwrite($handle, "\n");
		if($kmh >= 0)
		{
			fwrite($handle, $kmh);
			fwrite($handle, "\n");
		}
		fclose($handle);
	}	
	else
	{
    	$response = 400;
    }
	// now do logging
	$handle2 = @fopen($logfile, "a");
	if($handle != FALSE)
	{
		fwrite($handle2, "\n");
		fwrite($handle2, date('Y.m.d'));
		fwrite($handle2, "\t");
		fwrite($handle2, date('H:i:s'));
		fwrite($handle2, "\t");
		fwrite($handle2, $_REQUEST['lat']);
		fwrite($handle2, "\t");
		fwrite($handle2, $_REQUEST['long']);
		if($kmh >= 0)
		{
			fwrite($handle2, "\t");
			fwrite($handle2, $kmh);
		}
	   	fclose($handle2);
	}
	else
	{
    	$response = 400;
    }
    if($response == 200)
    {
    	// all good
    	$_REQUEST['speed'] = 0;
	    echo "OK";
	}
	else
	{
		echo "FAIL 1";
	}
}
else
{
	$response = 400;
	echo "FAIL 2";
}
?>
</body></html>
<?php
	http_response_code($response);
?>
