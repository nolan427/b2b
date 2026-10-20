#!/bin/bash
# Force fresh composer install
rm -f composer.lock
composer install --no-dev --optimize-autoloader
