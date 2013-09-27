#!/bin/bash
rm /etc/apache2/sites-auto/*
find */* -maxdepth 1 -type f -exec ./update-single.sh {} \;
# service apache2 reload
