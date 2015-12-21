Owncloud-nagios
===============
Nagios monitoring of owncloud on linux

This plugin was written to monitor statistics from Owncloud in nagios.
first-release:
	- Quota limit / user and space utilisation

howto:

- Copy the du.php somewhere on your owncloud server
- edit crontab as user www-data or low-access user, and add " * /15  *  *  *  *  curl  -s http://192.168.0.10/owncloud/du.php?user=username > /tmp/quota_username"
- copy the file check_owncloud_quota to your <nagios>/libexec dir (where all your other scripts for nagios probably reside).
- edit <nagios>/etc/objects/commands.cfg and add the commands specified in the provided commands.cfg
- edit <nagios>/etc/objects/owncloud-server-file.cfg and add a definition for each user, as illustrated in the provided user.cfg
  -	this may also be scripted with a bash-for loop (as i have done considering the many users i have on owncloud)



Tested on OpenSuSe 13.2 with Nagios Core 4.0.8 and OwnCloud 8.2

