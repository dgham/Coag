@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../wapmorgan/mp3info/bin/mp3scan
php "%BIN_TARGET%" %*
