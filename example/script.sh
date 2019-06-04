#!/bin/sh

gpsbabel -i nmea -f "gps08.csv" -o gpx,gpxver=1.1 -F "gps08.gpx"
