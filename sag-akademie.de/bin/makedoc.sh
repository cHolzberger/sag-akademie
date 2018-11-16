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
PATH_PROJECT=`dirname $0`
PATH_PROJECT=`dirname $PATH_PROJECT`
PATH_PHPDOC=$PATH_PROJECT/applications/phpdoc/phpdoc
PATH_DOCS=$PWD/resources/documentation
php  "$PATH_PHPDOC" -- -d "$PATH_PROJECT" -t "$PATH_DOCS" -c SAG-Akademie


# vim: set expandtab :
