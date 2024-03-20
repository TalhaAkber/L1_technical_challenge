#!/bin/bash

# Schema Update
php bin/console doctrine:schema:update --force --no-interaction

# Creating Test Database
php bin/console --env=test doctrine:database:create --if-not-exists

# Creating Schema in Test Database
php bin/console --env=test doctrine:schema:update --force --no-interaction

# Seeding with Fixture Data
php bin/console --env=test doctrine:fixtures:load --no-interaction

# Start the Symfony server
service cron start && symfony server:start --no-tls