# Longitude - a tiny locationtracker 

## What?
Location data storage and visualization.
A method to pass location data from a mobile app like PhoneTrack to your webhost, store the last known location and keep a log.
A method to visualize the location of one or more tracked people, using Here maps.
No database, just PHP and plain text files for storage. Super simple, readable.
Use Basic auth to shield this because this project doesn't deal with authentication.
Shows traffic data because this thing was created so my wife could see where I was and if I got stuck in traffic on the way home from work. Yes.

## Why?
Long ago when Google stopped Latitude, I was pissed about that and a few hours later this replacement was born.
Since then it hasn´t seen much love except for the migration from Google maps to TomTom and then Here maps.
Basically whatever easy free maps provider I could find.
I must say Here maps is super fast in operation and easy to work with.

## Getting started
You will need to sign up for a (free) developer key at Here maps and enter it in the map.php file.
It's free for personal / low volume usage and so far I have never hit its limit. YMMV
This project can handle as many users as you like, and even show multiple together on the map.
Make a folder for each user and edit the .PNG of the marker to be whatever avatar or pin you want.

Put the files in a folder with a .htaccess file for auth.
From the mobile app (example here: PhoneTrack), specify the URL as https://your.server/folder/set.php?lat=%LAT&long=%LON&speed=%SPD&who=yourname
On your website, for visualization use a link like https://your.server/folder/show.php?who=username1,username2
If you want, specify 1 or many names.

Below the folder with the PHP files, add the included ./data folder. In it should be one folder for each user, name being equal to the user name. In there, have a <username>.dat and <username>.png - the first will hold the last position, the other is the marker on the map (do customize it!). I've included a sample user called 'Atomium' :)
