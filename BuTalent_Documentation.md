[![wakatime](https://wakatime.com/badge/user/d7f8cf89-fee2-46da-89df-70b82216f2c2/project/bbf41023-2501-4c28-8db0-d3b1a10cf616.svg)](https://wakatime.com/badge/user/d7f8cf89-fee2-46da-89df-70b82216f2c2/project/bbf41023-2501-4c28-8db0-d3b1a10cf616)

# BuTalent Documentation

## Laravel Project Setup

### Create Laravel Project
```shell
docker-compose run --rm composer create-project --prefer-dist laravel/laravel .
```

### Install Dependencies
```shell
docker-compose run --rm composer install
```

### Build Container
```shell
docker-compose up -d --build nginx  --force-recreate
# other build problem
# DOCKER_BUILDKIT=0 docker-compose up --build nginx
```

### Folder Permission Fix
```shell
docker compose exec -it -u root php chown -R www-data:www-data .
```

### Create Storage Link
```bash
docker compose run --rm artisan  storage:link
```

### Migrate Database
```bash
docker compose run --rm artisan  migrate
```

### Clear Cache
```shell
docker compose run --rm artisan optimize:clear
```

### JWT Renew Secret Key
```shell
docker compose run --rm artisan jwt:secret
```

### Clear DB and Seed
```shell
docker compose run --rm artisan migrate:fresh --seed
```

### Only Seed
```shell
docker compose run --rm artisan db:seed
```

### Specific Seeder
```shell
docker compose run --rm artisan db:seed --class=UserSeeder
```

### Swagger Documentation
```shell
docker compose run --rm  artisan  l5-swagger:generate --all
```

### Node Project Setup
```shell
docker compose run --rm npm create vite@latest . -- --template react-ts
```

## Backend - ESDS Command Documentation

### Initial Setup
#### Composer
```bash
docker compose run --rm composer install
docker compose run --rm composer update
```

#### Environment Setup
```bash
cp .env.example .env
# Update your .env file accordingly
```

### Docker Build and Configuration
```bash
docker compose up -d --build nginx --force-recreate
# Or use:
# DOCKER_BUILDKIT=0 docker compose up --build nginx
```

### Set Proper Permissions
```bash
docker compose exec -it -u root php chown -R www-data:www-data storage storage/app storage/framework storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache && chmod -R 777 storage storage/app storage/framework storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
```

### Storage Setup
```bash
docker compose run -u root --rm artisan storage:link
```

### Database Setup
```bash
docker compose run --rm artisan migrate
docker compose run --rm artisan db:seed
docker compose exec pgsql bash -c "cd /docker-entrypoint-initdb.d && ./restore.sh"
```

### API Documentation
```bash
docker compose run --rm artisan l5-swagger:generate --all
```

### Route Commands
```bash
docker compose run --rm artisan route:list
docker compose run --rm artisan route:list --compact
docker compose run --rm artisan route:list --method=GET
docker compose run --rm artisan route:list --name=user
docker compose run --rm artisan route:list --show=middleware
docker compose run --rm artisan route:list --json
docker compose run --rm artisan route:list --path=api
```

### Maintenance Commands
```bash
docker compose run --rm artisan optimize:clear
```

### Stop Services
```bash
docker compose down
```

## Services Overview

- **nginx**, **php**, **composer**, **artisan**, **phpunit**, **pgsql**, **pgadmin**, **redis**

## Troubleshooting

```bash
docker compose ps
docker compose logs [service_name]
```

## Docker Prune All
```shell
docker system df
docker stop $(docker ps -aq)
docker volume rm -f $(docker volume ls -q)
docker rm -vf $(docker ps -aq)
docker rmi -f $(docker images -aq)
docker network rm $(docker network ls -q)
docker system prune -a --volumes -f
docker system df
```