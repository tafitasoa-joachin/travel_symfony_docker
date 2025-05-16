#!/bin/bash
set -e

# Wait for MySQL to be available
echo "Waiting for MySQL to be available..."
sleep 5

# Setup Symfony environment
echo "Setting up Symfony environment..."
if [ ! -f .env.local ]; then
    cp .env .env.local
fi

# Use Clever Cloud MySQL database
echo "DATABASE_URL=mysql://ue8t5vjaz1rhvrkj:sMaDfFPkUjKaO4RdAndk@bztk5ekzudeux7v5tznc-mysql.services.clever-cloud.com:3306/bztk5ekzudeux7v5tznc" > .env.local

# Skip Symfony commands that require the runtime
echo "Skipping Symfony commands to avoid runtime errors..."

# Start Apache
echo "Starting Apache..."
exec apache2-foreground