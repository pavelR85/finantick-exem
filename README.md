

## Finantick Exem

1. Mini CRM application.

## Installation

1. Unzip archive
2. Create empty database and set your data connection for your database in .env file
3. Open terminal go to unpack directory and run commands:
- composer required
- php bin/console doctrine:schema:update --force
- php bin/console doctrine:fixtures:load 
- php bin/console app:fetch-binance-data
- symfony server:start
4. Open your browser /
