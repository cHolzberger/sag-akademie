PROJECTDIR=$( dirname "$0" ) 
cd $PROJECTDIR/../

VERSION="V$(date +%Y.%m.%d)"

echo $VERSION > version.txt

hg add update/*
hg commit -m "Release $VERSION"
hg tag -f stable
hg tag -f beta
hg tag -f $VERSION

hg push
echo "Waiting 10 seconds..."
sleep 10 

wget http://sag-akademie.de/_update.php -O-
wget http://beta.sag-akademie.de/_update.php -O-
