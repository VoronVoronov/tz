## Тестовое задание

## Технологии

- Laravel 11
- Vue 3
- MySQL 8.0
- Redis (для очередей)
- Docker
- Nginx

## Установка и запуск

1. Склонируйте репозиторий
```bash
git clone git@github.com:VoronVoronov/tz.git
```

2. Скопируйте .env.example в .env
```bash
cp .env.example .env
```

3. Запустите контейнеры
```bash
docker compose up -d --build
```

При первом запуске автоматически:
- Установятся все зависимости PHP и Node.js
- Создадутся таблицы в базе данных
- Настроится Laravel Passport для API аутентификации
- Соберется фронтенд
- Запустится очередь для обработки операций с балансом

## Сервисы

- Frontend: http://localhost:5173
- Backend API: http://localhost:80
- MySQL: localhost:3306
- Redis: localhost:6379

## Команды

Создание пользователя:
```bash
docker compose exec php php artisan user:create dastan dastan@zholdas.icu admin
```

Управление балансом (через очередь):
```bash
# Пополнение
docker compose exec php php artisan balance:manage dastan@zholdas.icu 100 "Add Balance"

# Списание
docker compose exec php php artisan balance:manage dastan@zholdas.icu 10 "Add Balance" --action=withdraw
```

## Структура проекта

- `app/` - Код приложения
  - `Services/` - Бизнес-логика
  - `Repositories/` - Работа с данными
  - `Jobs/` - Задачи для очереди
- `docker/` - Конфигурация Docker
  - `php/` - PHP и Laravel
  - `node/` - Node.js и Vue
  - `nginx/` - Веб-сервер
- `resources/js/` - Vue компоненты

## Сервисы Docker

- `nginx` - Nginx веб-сервер (порт 80)
  - Проксирует запросы к PHP-FPM
  - Обслуживает статические файлы

- `php` - PHP-FPM для бэкенда
  - PHP 8.2 с необходимыми расширениями
  - Composer для управления зависимостями
  - Laravel приложение

- `frontend` - Node.js для фронтенда (порт 5173)
  - Node.js 20 для сборки Vue.js
  - Hot-reload в режиме разработки
  - Vite для сборки

- `mysql` - MySQL 8.0 (порт 3306)
  - Хранение данных в volume mysql_data
  - Пользователи, балансы, транзакции
  - Таблицы для Laravel Passport

- `redis` - Redis для очередей (порт 6379)
  - Хранение данных в volume redis_data
  - Обработка асинхронных операций

- `queue` - Laravel Queue Worker
  - Обработка операций с балансом
  - Использует Redis как драйвер очередей
  - Автоматический перезапуск при ошибках
- `queue` - обработчик очередей Laravel