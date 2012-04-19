#!/bin/bash
if [ -d "web/photos"  ]; then
  cd web/photos
else
  exit 1;
fi;

for name in `ls `;
  do mv $name ` echo $name|awk -F. '{print $1".jpg"}'`  ;
done;

for name in `ls`; 
  do 
    convert  -thumbnail 140 $name thumb.$name;
  done;
