#!/bin/bash
FNAME=$(basename $1)
./gen-single.py "$1" > "/etc/apache2/sites-auto/$FNAME"
