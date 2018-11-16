PROJECTDIR=$( dirname "$0" ) 
cd $PROJECTDIR/../

VERSION="V$(date +%Y.%m.%d)"
echo $VERSION > version.txt

hg add update/*
hg commit -m "Beta $VERSION"
hg tag -f beta

hg push
echo "Waiting 10 seconds..."
sleep 10 

wget http://beta.sag-akademie.de/_update.php -O-
