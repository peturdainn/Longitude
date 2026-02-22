# Longitude toolset

## general setup
- protect the tools using Basic Auth! These tools do not care about authentication and access protection!
- create a 'data' folder and beneath that a <username> folder where <username> is the name you will pass to the tools (case sensitive!)
- in the user folder, create
    - <username>.png to be used as marker pin
    - 'log' folder for location history (no log folder means no history saved)
- after reporting the location a first time, the user folder will contain a <username>.dat with the last location
- the log folder, when it exists, will be fileld with YYYYMM.log files, one file per month

## set.php
Use this to report a location. Form:
`https://your.server/folder/set.php?lat=%LAT&long=%LON&speed=%SPD&who=username`
There are some mobile apps available that can make this call as 'custom server', like phonetrack
Only accepts one name!
Speed is optional
Date and time will be supplied on the server

## show.php
Use this to view the last reported location of one or more users. Form:
`https://your.server/folder/show.php?who=username1,username2&zoom=10`
If more than one username is supplied, the map will be zoomed and centered so that all users are visible
Zoom level is optional, if supplied the above autozoom is not done
All supplied usernames will be listed on top with date, time and speed of last update
The username is a clickable link that loads show_history.php (see below)

## show_history.php
Shows all location logs of one user on a given day (only one user accepted)
The first and last locations are markers, the rest are lines connecting white dots of locations
Click a white dot for timestamp and speed
The map is always zoomed and centered so that all history is visible
The username is a clickable link that loads show.php (see above)
Click the date-picker to view another date
