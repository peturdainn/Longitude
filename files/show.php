<!DOCTYPE html>
<html>
  <head>
    <title>Longitude</title>
	<meta http-equiv="refresh" content="300">
    <meta name="viewport" content="initial-scale=1.0, width=device-width" />
    <meta charset="utf-8">
    <!-- OpenFreeMap and MapLibre stuff -->
    <link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet" />    
    <!-- --- -->    
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
        .marker {
            display: block;
            border: 0px;
            cursor: pointer;
            padding: 0;
        }
   </style>    
  </head>
  <body>
<script>
<?php

date_default_timezone_set('Europe/Brussels');

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
$today = date('Y-m-d');

echo "var cnt = ".$count.";\n";

if(isset($_GET['zoom']))
{
    $zoom = stripslashes($_GET['zoom']);
	echo "var zoomset = true\n";
 	echo "var zoomlevel = ".$zoom."\n";
}
else
{
	echo "var zoomset = false\n";
	echo "var zoomlevel = 15\n";
}

// for bounding box calculation
$lat_min = 0;
$lat_max = 0;
$lon_min = 0;
$lon_max = 0;

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
		
	    if(0 == $i)
	    {
	        $lat_min = $lat[$i];
	        $lat_max = $lat[$i];
	        $lon_min = $lon[$i];
	        $lon_max = $lon[$i];
	    }
	    else
	    {
	        if($lat[$i] < $lat_min) { $lat_min = $lat[$i]; }	    
	        if($lat[$i] > $lat_max) { $lat_max = $lat[$i]; }	    
	        if($lon[$i] < $lon_min) { $lon_min = $lon[$i]; }	    
	        if($lon[$i] > $lon_max) { $lon_max = $lon[$i]; }	    
	    }
			    
	    $pic[$i] = "./data/".$who[$i]."/".$who[$i].".png";

	    if($handle = fopen($pic[$i], "r"))
	    {
		    fclose($handle);
	    }
	    else
	    {
		    $pic[$i] = "longitude.png";
        }		

	    // stuff those arrays
	    echo "who.push(\"".$who[$i]."\");\n";
	    echo "lat.push(".$lat[$i].");\n";
	    echo "lon.push(".$lon[$i].");\n";
	    echo "pic.push(\"".$pic[$i]."\");\n";
	}
}
$latavg = ($lat_min + $lat_max) / 2;
$lonavg = ($lon_min + $lon_max) / 2;
echo "latavg = ".$latavg.";\n";
echo "lonavg = ".$lonavg.";\n";
echo "var bbox = [[".$lon_min.", ".$lat_min."], [".$lon_max.", ".$lat_max."]];\n";
?>

</script>

<?php
echo "<hr><center><table>\n";
for ($i = 0; $i < $count; $i++)
{
	echo "<tr><td><A HREF=\"show_history.php?who=".$who[$i]."&showday=".$today."\">".$who[$i]."</A></td><td>@</td><td>".$locdate[$i]."</td><td>".$loctime[$i]."</td><td>(".$speed[$i].")</td></tr>\n";
}
echo "</table></center><hr>\n";
?>
    <div id="mapdiv" style="width: 100%; height: 90%; background: grey" />      
    <script type="module">
        import * as maplibregl from 'https://unpkg.com/maplibre-gl@^6.0.0/dist/maplibre-gl.mjs';
		
        const map = new maplibregl.Map({
            style: 'https://tiles.openfreemap.org/styles/liberty',
            center: [lonavg, latavg],
            zoom: zoomlevel,
            container: 'mapdiv',
            attributionControl: false,
        });
        
        map.addControl(new maplibregl.NavigationControl({
            visualizePitch: true,
            visualizeRoll: true,
            showZoom: true,
            showCompass: true
        }));

        map.addControl(new maplibregl.ScaleControl({
            maxWidth: 200,
            unit: 'metric'
        }));        

        if(cnt > 1)
        {
            map.fitBounds(bbox, { padding: 100, animate: false, maxZoom:15});
        };

        // show the requested users
        let i = 0;
        for(i = 0; i < who.length; i++)
        {
          const el = document.createElement('div');
          el.className = 'marker';
          el.style.backgroundImage = 'url(' + pic[i] + ')';
          el.style.width = '50px';
          el.style.height = '50px';
          let alertstring = who[i];
          el.addEventListener('click', () => {
             window.alert(alertstring);
          });          
          // add marker to map
          new maplibregl.Marker({element: el})
                .setLngLat([ lon[i], lat[i] ])
                .addTo(map);        
        };

    </script>
  </body>
</html>

