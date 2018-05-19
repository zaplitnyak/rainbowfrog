#!/usr/bin/env bash


FILES=./ansi/*.ansi
while true; do
  for f in $FILES
  do
    echo -e "\e[2J$1";
    cat $f
    sleep 0.06
  done
done
echo -e '\n'
