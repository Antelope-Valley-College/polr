#!/usr/bin/env bash

###
#  Development deploy script
#  Mike Wilmes, Antelope Valley College
#  Feb 3, 2023
#  This script uses docker to deploy two disposable containers for Polr development.
#  The first is a MySQL server with a defined database and user for Polr.
#  The second is an Apache/PHP server that gets built up and has Polr deployed from the
#    AVC repo. The branch is selectable for testing purposes. 
###

# $1 development path- location of the Apache document root on the local file system
#      Defaults to the 'dev' folder in the local directory.
# $2 branch (optional)- branch to use to deploy from

DEV_PATH=$1
BRANCH=$2

if [ -z "$DEV_PATH"]; then
  DEV_PATH=`pwd`/dev
fi

echo "DevelopmentPath: $DEV_PATH"
echo "Branch: $BRANCH"

function generate_password {
  # $1 = length of password
  cat /dev/urandom | tr -dc '[:graph:]' | fold -w ${1:-$1} | head -n 1
}

echo "Destroying old environment"
docker stop polr-web
docker rm polr-web
docker stop polr-mysql
docker rm polr-mysql

rm -rf $DEV_PATH 2>/dev/null
mkdir -p $DEV_PATH

echo "Generating passwords"
while true; do
  MYSQL_ROOT=`generate_password 32`
  echo $MYSQL_ROOT| egrep "#|'">/dev/null || break
done
while true; do
  MYSQL_POLR=`generate_password 32`
  echo $MYSQL_POLR| egrep "#|'">/dev/null || break
done
MYSQL_ENV=$DEV_PATH/mysql_env.txt

set -x

cat > $MYSQL_ENV <<EOF
MYSQL_ROOT_PASSWORD=$MYSQL_ROOT
MYSQL_DATABASE=polr
MYSQL_USER=polr
MYSQL_PASSWORD=$MYSQL_POLR
MYSQL_HOST=`hostname`
EOF

echo "Launching new containers"
docker run --name polr-mysql --env-file=$MYSQL_ENV -d -p 3306:3306 mysql
docker run --name polr-web -v "${DEV_PATH}:/var/www/html" -d -p 80:80 php:8.1-apache

set +x

echo "Running deploy script"
cp deploy.sh $DEV_PATH
docker exec polr-web ./deploy.sh $BRANCH

echo "Launch http://`hostname` to get started."
echo ""
echo "MySQL root password: $MYSQL_ROOT"
echo ""
echo "Polr password: $MYSQL_POLR"