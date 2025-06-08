<!DOCTYPE html>
<html>
  <head>
    <title>Longitude - a tiny Latitude replacement by peturdainn</title>
	<meta http-equiv="refresh" content="300">
    <meta name="viewport" content="initial-scale=1.0, width=device-width" />
    <meta charset="utf-8">
    <link rel='stylesheet' type='text/css' href='tomtom/map.css'/>
    <script src='tomtom/tomtom.min.js'></script>
<script>
<?php

// this file (c) 2025 peturdainn
// MIT licensed
// latest version at https://github.com/peturdainn/Longitude

// some preparation: init vars and create empty arrays
echo "var who = [];\n";
echo "var lat = [];\n";
echo "var lon = [];\n";
echo "var pic = [];\n";

// now let's fill them...
error_reporting('E_ALL');
$wholist = stripslashes($_GET['who']);
$who = explode(",", $wholist);
$count = count($who);
$zoom = stripslashes($_GET['zoom']);

echo "var cnt = ".$count.";\n";
if(isset($_GET['zoom']))
	echo "var zoomlevel = ".$zoom."\n";
else
	echo "var zoomlevel = 12\n";

for ($i = 0; $i < $count; $i++)
{
	// file and its name are a local var
	$filename = "./data/".$who[$i]."/".$who[$i].".dat";
	if($handle = fopen($filename, "r"))
	{
		$locdate[$i] = stream_get_line($handle, 100, "\n");
		$loctime[$i] = stream_get_line($handle, 100, "\n");
		$lat[$i] = stream_get_line($handle, 100, "\n");
		$lon[$i] = stream_get_line($handle, 100, "\n");
		$speed[$i] = stream_get_line($handle, 100, "\n");
		fclose($handle);
		if(0 == strlen($speed[$i]))
		{
			$speed[$i] = "-";
		}
		else
		{
			$speed[$i] = $speed[$i]." km/h";
		}
	}

	$pic[$i] = "./data/".$who[$i]."/".$who[$i].".png";

	if($handle = fopen($pic[$i], "r"))
		fclose($handle);
	else
		$pic[$i] = "longitude.png";

	// stuff those arrays
	echo "who.push(\"".$who[$i]."\");\n";
	echo "lat.push(".$lat[$i].");\n";
	echo "lon.push(".$lon[$i].");\n";
	echo "pic.push(\"".$pic[$i]."\");\n";
}

?>
    </script>
    <style type="text/css">
        body {
            overflow:hidden;
        }
        table, tr, td {
            border: 0px;
            border-spacing: 10px 0px;
            padding: 0px;
        }
    </style>
  </head>
  <body>

<?php
echo "<hr><center><table>\n";
for ($i = 0; $i < $count; $i++)
{
	echo "<tr><td><A HREF=\"data/".$who[$i]."/log/\">".$who[$i]."</A></td><td>@</td><td>".$locdate[$i]."</td><td>".$loctime[$i]."</td><td>(".$speed[$i].")</td></tr>\n";
}
echo "</table></center><hr>\n";
?>
    <div id="mapdiv" style="width: 100%; height: 90%; background: grey" />      
    <script  type="text/javascript" charset="UTF-8" >
        var latavg = lat[0];
        var lonavg = lon[0];
        for(i = 1; i < who.length; i++)
        {
            latavg = (latavg + lat[i]) / 2;
            lonavg = (lonavg + lon[i]) / 2;
        }

        
        tomtom.setProductInfo('longitude', '3.1');
        var mapvar = tomtom.L.map('mapdiv', {
            key: 'your-key-here',
            source: 'raster',
            basePath: '/map/tomtom',
            zoom: zoomlevel,
            center: [latavg, lonavg],
            traffic: true,
            trafficFlow: false
        });
        for(i = 0; i < who.length; i++)
        {
            var marker = tomtom.L.marker([lat[i],lon[i]], {
                iconURL: pic[i],
                title: who[i]
            });
            //marker.bindpopup('who[i]');
            mapvar.addLayer(marker);
        }
    </script>
  </body>
</html>

