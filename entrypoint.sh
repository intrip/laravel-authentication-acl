#!/bin/bash

service mysql start

cd /app/
composer install

tail -F /dev/null

