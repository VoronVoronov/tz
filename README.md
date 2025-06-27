## Запуск с Docker

1. Клонируйте репозиторий
```bash
git clone git@github.com:VoronVoronov/tz.git
cd tz
```

2. Скопируйте .env.example в .env и настройте переменные окружения
```bash
cp .env.example .env
```

3. Запустите контейнеры
```bash
docker compose up -d
```

Все зависимости будут установлены автоматически, миграции выполнены, и фронтенд собран при первом запуске контейнеров.

## Команды

Создание пользователя:
```bash
docker compose exec php php artisan user:create dastan dastan@zholdas.icu admin
```

Управление балансом:
```bash
# Пополнение
docker compose exec php php artisan balance:manage dastan@zholdas.icu 100 "Add Balance"

# Списание
docker compose exec php php artisan balance:manage dastan@zholdas.icu 10 "Add Balance" --action=withdraw
```

## Сервисы

- Frontend: http://localhost
- База данных: PostgreSQL (порт 5432)
- Redis для очередей: порт 6379
- Queue Worker для обработки транзакций

## Структура Docker

- `nginx` - веб-сервер
- `php` - PHP-FPM для бэкенда
- `frontend` - Node.js для сборки фронтенда
- `postgres` - база данных
- `redis` - для очередей
- `queue` - обработчик очередей Laravel