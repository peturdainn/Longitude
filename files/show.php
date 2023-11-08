<!DOCTYPE html>
<html>
  <head>
    <title>Longitude - a tiny Latitude replacement by peturdainn</title>
	<meta http-equiv="refresh" content="300">
    <meta name="viewport" content="initial-scale=1.0, width=device-width" />
    <meta charset="utf-8">
    <!-- Here maps stuff -->
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
    <!-- --- -->

<script>
<?php

// this file (c) 2023 peturdainn
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
        html, body {
            overflow:hidden;
            height: 100%;
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

        function centermap(map)
        {
          map.setCenter({lat:latavg, lng:lonavg});
          map.setZoom(zoomlevel);
        }
        function enableTrafficInfo(map) 
        {
            // Show traffic tiles
            map.addLayer(defaultLayers.vector.normal.traffic);
            // Enable traffic incidents layer
            //map.addLayer(defaultLayers.incidents);
        }

        var platform = new H.service.Platform({
          apikey: 'your api key here',
        });

        var defaultLayers = platform.createDefaultLayers();

        // create world map view
        var map = new H.Map(document.getElementById('mapdiv'),
          defaultLayers.vector.normal.map, {
          center: {lat:latavg, lng:lonavg},
          zoom: zoomlevel, 
          pixelRatio: window.devicePixelRatio || 1
        });

        // make the map interactive
        // MapEvents enables the event system
        // Behavior implements default interactions for pan/zoom (also on mobile touch environments)
        var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

        // Create the default UI components
        var ui = H.ui.UI.createDefault(map, defaultLayers);

        // Now use the map as required: center & enable traffic layer
        centermap(map);
        enableTrafficInfo(map);

        // show the requested users
        for(i = 0; i < who.length; i++)
        {
            var myIcon = new H.map.Icon(pic[i]);
            var myMarker = new H.map.Marker({ lat:lat[i], lng:lon[i] } , { icon: myIcon });
            map.addObject(myMarker);            
        }

    </script>
  </body>
</html>

