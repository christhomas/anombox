# Anombox

An incredibly simple anonymous dropbox implemented as a Lumen REST api using static credentials and PHP sessions for managing login status

I needed a simple REST api where I could post files without any login information, but allow the download of the files to be controlled. It's
for a highly specific use case. You probably shouldn't use this software. But maybe it interests somebody

# Credentials

Execute `composer install` and then edit the `ACCESS_CONTROL` environment variable
