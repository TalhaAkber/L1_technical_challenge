#!/bin/bash

# Schema Update
php bin/console doctrine:schema:update --force

# Start the Symfony server
service cron start && symfony server:start --no-tls