#########################################################################
# File Name: pack.sh
# Author: sunlei
# mail: ericsun@eptco.com
# Created Time: Sun 12 Jun 2016 01:14:53 AM EDT
#########################################################################
#!/bin/bash

VERSION=1.0.0
OUTPUT=./output
MODULENAME=muac
PACKAGE=${MODULENAME}_${VERSION}.${BUILD_NUMBER}.tar.gz

echo $PACKAGE

if [ -d "${OUTPUT}" ]; then
    rm -rf ${OUTPUT}
fi

mkdir -p ${OUTPUT}

tar czvf ${OUTPUT}/${PACKAGE} components/ config/ controllers/ models/ runtime/ views/ web/ --exclude=.svn --exclude=.idea
if [ $? -ne 0 ]; then exit -1; fi