#!/bin/bash
SCRIPT=`realpath $0`
SCRIPTPATH=`dirname $SCRIPT`
echo $SCRIPTPATH
cd $SCRIPTPATH

nodejs $SCRIPTPATH/node_modules/gulp/bin/gulp.js
