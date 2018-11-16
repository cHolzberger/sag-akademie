#!/bin/bash
# $Id: makedoc.sh,v 1.2 2007-12-10 01:11:19 ashnazg Exp $ 

#/**
#  * makedoc - PHPDocumentor script to save your settings
#  * 
#  * Put this file inside your PHP project homedir, edit its variables and run whenever you wants to
#  * re/make your project documentation.
#  * 
#  * The version of this file is the version of PHPDocumentor it is compatible.
#  * 
#  * It simples run phpdoc with the parameters you set in this file.
#  * NOTE: Do not add spaces after bash variables.
#  *
#  * @copyright         makedoc.sh is part of PHPDocumentor project {@link http://freshmeat.net/projects/phpdocu/} and its LGPL
#  * @author            Roberto Berto <darkelder (inside) users (dot) sourceforge (dot) net>
#  * @version           Release-1.1.0
#  */


##############################
# should be edited
##############################

#/**
#  * title of generated documentation, default is 'Generated Documentation'
#  * 
#  * @var               string TITLE
#  */
TITLE="Your Project Documentation"

#/** 
#  * name to use for the default package. If not specified, uses 'default'
#  *
#  * @var               string PACKAGES
#  */
PACKAGES="SAGEngine"

#/** 
#  * name of a directory(s) to parse directory1,directory2
#  * $PWD is the directory where makedoc.sh 
#  *
#  * @var               string PATH_PROJECT
#  */
PATH_PROJECT=`dirname $0`
PATH_PROJECT=`dirname $PATH_PROJECT`
#/**
#  * path of PHPDoc executable
#  *
#  * @var               string PATH_PHPDOC
#  */
PATH_PHPDOC=$PATH_PROJECT/applications/phpdoc/

#/**
#  * where documentation will be put
#  *
#  * @var               string PATH_DOCS
#  */
PATH_DOCS=$PWD/resources/dokumentation

#/**
#  * what outputformat to use (html/pdf)
#  *
#  * @var               string OUTPUTFORMAT
#  */
OUTPUTFORMAT=HTML

#/** 
#  * converter to be used
#  *
#  * @var               string CONVERTER
#  */
CONVERTER=Smarty

#/**
#  * template to use
#  *
#  * @var               string TEMPLATE
#  */
TEMPLATE=default

#/**
#  * parse elements marked as private
#  *
#  * @var               bool (on/off)           PRIVATE
#  */
PRIVATE=off

# make documentation
"$PATH_PHPDOC" -d "$PATH_PROJECT" -t "$PATH_DOCS" -ti "$TITLE" -dn $PACKAGES \
-o $OUTPUTFORMAT:$CONVERTER:$TEMPLATE -pp $PRIVATE


# vim: set expandtab :
