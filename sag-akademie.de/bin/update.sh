cd ..

rm resources/cache/site-*
rm resources/cache/gears-*
svn update
svn update pdf
svn update img
svn update templates
svn update lib
svn update resources/flex
svn update resources/scripts
svn update resources/taglib
svn update resources/fonts
svn update documentation
svn update _flex
svn update services
svn update template/pages

chmod -R a+rw ./
rm resources/cache/site-*

#wget -o /dev/null "http://sag-akademie.de/admin/?clearApc=true"
#wget -o /dev/null "http://sag-akademie.de/admin/?clearMemcached=true"