<#
Development deploy script
Mike Wilmes, Antelope Valley College
Feb 3, 2023
This script uses docker to deploy two disposable containers for Polr development.
The first is a MySQL server with a defined database and user for Polr.
The second is an Apache/PHP server that gets built up and has Polr deployed from the
  AVC repo. The branch is selectable for testing purposes. 

To enable use in environments that prohibit unsigned scripts, this script uses a 
config.json for parameters:

DevelopmentPath- location of the Apache document root on the local file system. 
  Defaults to the 'dev' folder in the local directory.
Branch (optional)- branch to use to deploy from

To execute:
type build_dev.ps1|powershell -command -

#>
$config = get-content '.\config.json' -ErrorAction SilentlyContinue|ConvertFrom-Json

$DevelopmentPath = if ($config.DevelopmentPath) {$config.DevelopmentPath} else {Join-Path (Get-Location) 'dev'}
$Branch = if ($config.Branch) {$config.Branch} else {''}

write-host "DevelopmentPath: $DevelopmentPath"
write-host "Branch: $Branch"

. docker stop polr-web
. docker rm polr-web
. docker stop polr-mysql
. docker rm polr-mysql

New-Item -Path $DevelopmentPath -ItemType Directory -ErrorAction SilentlyContinue
Get-ChildItem -Path $DevelopmentPath | Remove-Item -Force -Recurse

Add-Type -AssemblyName 'System.Web'
do { 
  $mysql_root = [System.Web.Security.Membership]::GeneratePassword(32, 8) 
} while ($mysql_root -match "#|'")
do { 
  $mysql_polr = [System.Web.Security.Membership]::GeneratePassword(32, 8) 
} while ($mysql_polr -match "#|'")
$mysql_env = Join-Path $DevelopmentPath 'mysql_env.txt'

@"
MYSQL_ROOT_PASSWORD=$mysql_root
MYSQL_DATABASE=polr
MYSQL_USER=polr
MYSQL_PASSWORD=$mysql_polr
MYSQL_HOST=${env:COMPUTERNAME}
"@ | Set-Content($mysql_env)

. docker run --name polr-mysql --env-file=$mysql_env -d -p 3306:3306 mysql
. docker run --name polr-web -v "${DevelopmentPath}:/var/www/html" -d -p 80:80 php:8.1-apache

Copy-Item 'deploy.sh' $DevelopmentPath
. docker exec polr-web ./deploy.sh $Branch

start-process http://${env:COMPUTERNAME}

write-host MySQL root password: $mysql_root
write-host ""
write-host Polr password: $mysql_polr