# Laravel Cloud Setup

This project already reads database settings from environment variables. No code change is required to switch between local MySQL and Laravel Cloud MySQL.

## Database

The application uses the standard Laravel MySQL connection in [config/database.php](c:\laragon\www\perfectlum\config\database.php):

- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

For Laravel Cloud, use:

- `DB_CONNECTION=mysql`
- `DB_DATABASE=testpl`

If you attach the managed database to the environment, Laravel Cloud can inject these values automatically.

## Recommended environment values

Start from [.env.cloud.example](c:\laragon\www\perfectlum\.env.cloud.example) and fill in the real secrets in Laravel Cloud.

## SQL import

1. Create the MySQL database in Laravel Cloud.
2. Enable the database public endpoint temporarily.
3. Import the SQL dump from your local machine:

```bash
mysql -h YOUR_DB_HOST -P 3306 -u YOUR_DB_USERNAME -p testpl < your_dump.sql
```

4. Disable the public endpoint again if you no longer need it.

## Important notes

- Do not copy your local `.env` to Laravel Cloud.
- Do not run migrations blindly on production-like data, because this project has migration history drift.
- `/api/sync` does not depend on the primary domain. It only needs the correct reachable base URL and valid remote credentials.
