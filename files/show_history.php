<!DOCTYPE html>
<html>
  <head>
    <title>Longitude History</title>
	<meta http-equiv="refresh" content="300">
    <meta name="viewport" content="initial-scale=1.0, width=device-width" />
    <meta charset="utf-8">
    <!-- OpenFreeMap and MapLibre stuff -->
    <script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>
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
// this php code greps the history and generates geojson and some javascript variables
error_reporting('E_ALL');
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Brussels');

// get arguments
$wholist = stripslashes($_GET['who']);
$who = explode(",", $wholist);
$showday = date('Y-m-d');
$historycount = 0;

if(isset($_GET['showday']))
{
    // date specified (default = today)
    $showday = preg_replace("/[^0-9\-]/", "", $_GET['showday']);
}

// for bounding box calculation
$lat_min = 0;
$lat_max = 0;
$lon_min = 0;
$lon_max = 0;

// for start/end markers
$lat_start = 0;
$lat_end = 0;
$lon_start = 0;
$lon_end = 0;


# Build GeoJSON feature collection array
$geojson_lines = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);
$geojson_points = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);

$filedate = substr($showday, 0, 4).substr($showday, 5, 2);
$filename = "./data/".$who[0]."/log/".$filedate.".log";
$grepdate = substr($showday, 0, 4).".".substr($showday, 5, 2).".".substr($showday, 8, 2);
$grepcmd = "grep ".$grepdate." ".$filename;
$posstringlist = null;
$retval = null;
exec($grepcmd, $posstringlist, $retval);

$points = array();
$firstrun = true;
foreach($posstringlist as $posstring)
{
    $parts = explode("\t", $posstring, 5);
    $p_date  = $parts[0];
    $p_time  = $parts[1];
    $p_lat   = $parts[2];
    $p_lon   = $parts[3];
    $p_speed = $parts[4];

    // store points
    $feature = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            'coordinates' => array(
                $p_lon,
                $p_lat
                )
            ),
        'properties' => array(
            'name' => $p_time,
            'description' => $p_time."<br>(".$p_speed." kmh)",
        )
    );
    
    // store feature info for points
    array_push($geojson_points['features'], $feature);    	
   
    // store point in line array
    $point = array(
                $p_lon,
                $p_lat,
             );
    array_push($points, $point);                 
    
    // get our bounding box
    if($firstrun)
    {
        // first run
        $firstrun = false;
        $lat_min = $p_lat;
        $lat_max = $p_lat;
        $lon_min = $p_lon;
        $lon_max = $p_lon;        
        $lat_start = $p_lat;
        $lon_start = $p_lon;
    }
    else
    {
        if($p_lat < $lat_min) { $lat_min = $p_lat; }	    
        if($p_lat > $lat_max) { $lat_max = $p_lat; }	    
        if($p_lon < $lon_min) { $lon_min = $p_lon; }	    
        if($p_lon > $lon_max) { $lon_max = $p_lon; }	 
    }
    $lat_end = $p_lat;
    $lon_end = $p_lon;
    
}

// Add line feature to feature collection array
$feature = array(
    'type' => 'Feature',
    'properties' => "{ 'color': '#33C9EB' }",
    'geometry' => array(
        'type' => 'LineString',
        'coordinates' => $points,
        ),
    );

array_push($geojson_lines['features'], $feature);    	

$latavg = ($lat_min + $lat_max) / 2;
$lonavg = ($lon_min + $lon_max) / 2;
$historycount = count($points);

// create javascript variables
if(0 < $historycount)
{
    echo "var geojson_points = ".json_encode($geojson_points, JSON_NUMERIC_CHECK).";\n";
    echo "var geojson_lines = ".json_encode($geojson_lines, JSON_NUMERIC_CHECK).";\n";
}
else
{
    # no history!
}

echo "var latavg = ".$latavg.";\n";
echo "var lonavg = ".$lonavg.";\n";
echo "var lat_start = ".$lat_start.";\n";
echo "var lon_start = ".$lon_start.";\n";
echo "var lat_end = ".$lat_end.";\n";
echo "var lon_end = ".$lon_end.";\n";
echo "var bbox = [[".$lon_min.", ".$lat_min."], [".$lon_max.", ".$lat_max."]];\n";	
?>
</script>

<?php
// small extra bit of php to insert name and date into header... sorry
echo "<hr><center><table><tr><td>";
echo "<A HREF=\"show.php?who=".$who[0]."\">".$who[0]."</A>";
echo "</td><td><form>";
echo "<input type=\"date\" name=\"showday\" value=".$showday." required />";
echo "<input type=\"hidden\" name=\"who\" value=".$who[0]." />";
echo "<button>GO</button></form>";
echo "</td></tr></table></center><hr>\n";
?>
    <div id="mapdiv" style="width: 100%; height: 90%; background: grey" />      
    <script  type="text/javascript" charset="UTF-8" >

        const map = new maplibregl.Map({
            style: 'https://tiles.openfreemap.org/styles/liberty',
            center: [lonavg, latavg],
            zoom: 10,
            container: 'mapdiv',
            attributionControl: false,
        })  ;      
        
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

        map.fitBounds(bbox, { padding: 50, animate: false, maxZoom:15});

        //console.log(geojson_lines);
        //console.log(geojson_points);
        
        map.on('load', () => {
        
            map.addSource('history_lines', {
              "type":'geojson',
              "data": geojson_lines
            });
            map.addSource('history_points', {
              "type":'geojson',
              "data": geojson_points
            });
            map.addLayer({
              'id': 'myhistory_lines',
              'type': 'line',
              'source': 'history_lines',
              'paint': {
                    'line-color': '#3388EB',
                    'line-width': 5
              }
            });            
            map.addLayer({
              'id': 'myhistory_points',
              'type': 'circle',
              'source': 'history_points',
              'paint': {
                    'circle-radius': 4,
                    'circle-color': "#fff",
                    'circle-stroke-color': "#aaa",
                    'circle-stroke-width': 1,
                  }
            });    
    
            new maplibregl.Marker()
                .setLngLat([ lon_start, lat_start ])
                .addTo(map);             
            new maplibregl.Marker()
                .setLngLat([ lon_end, lat_end ])
                .addTo(map);             
        });
        
        const popup = new maplibregl.Popup({
            closeButton: false,
            closeOnClick: false
        });
                     
        let currentFeatureCoordinates = undefined;   
        map.on('mousemove', 'myhistory_points', (e) => {
            const featureCoordinates = e.features[0].geometry.coordinates.toString();
            if (currentFeatureCoordinates !== featureCoordinates) {
                currentFeatureCoordinates = featureCoordinates;

                // Change the cursor style as a UI indicator.
                map.getCanvas().style.cursor = 'pointer';

                const coordinates = e.features[0].geometry.coordinates.slice();
                const description = e.features[0].properties.description;

                // Ensure that if the map is zoomed out such that multiple
                // copies of the feature are visible, the popup appears
                // over the copy being pointed to.
                while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                }

                // Populate the popup and set its coordinates
                // based on the feature found.
                popup.setLngLat(coordinates).setHTML(description).addTo(map);
            }
        });

        map.on('mouseleave', 'myhistory_points', () => {
            currentFeatureCoordinates = undefined;
            popup.remove();
            map.getCanvas().style.cursor = '';
        });

    </script>
  </body>
</html>

