# Практика 14: Docker и Docker Compose
# Часть A. Dockerfile для Laravel
## 1. PHP-FPM с расширениями
### 01-laravel-build.png
![01-laravel-build.png](Screenshots/01-laravel-build.png)\
Зачем PHP-FPM в Docker, а не Apache+PHP?<br>
PHP-FPM позволяет разделить веб-сервер (Nginx) и обработчик PHP на независимые контейнеры, что соответствует принципу микросервисной архитектуры.<br> 
Какое архитектурное преимущество?<br>
Это дает лучшую производительность при обработке статики, гибкое масштабирование и возможность обновлять компоненты по отдельности.<br>
## 2. Кеширование composer-зависимостей
### 02-composer-layer.png
![02-composer-layer.png](Screenshots/02-composer-layer.png)\
Что произойдёт если COPY всего проекта сделать ДО composer install?<br>
Если composer.json не менялся - composer install не выполнится при следующей сборке. Если сделать COPY всего проекта до composer install, то при любом изменении даже одной строчки в коде Docker будет заново пересобирать этот слой.<br>
Механизм кеширования слоёв Docker:<br>
Docker строит образ как стопку слоев. Каждый слой создается инструкцией в Dockerfile (FROM, RUN, COPY).<br>
1) Кэш по контенту: Docker вычисляет хеш-сумму (checksum) для каждого слоя. Если инструкция и файлы, которые она использует, не изменились, Docker берет готовый слой из кэша.<br>
2) Как только один слой меняется, все последующие слои пересобираются заново, так как их базовый слой изменился.<br>
## 3. .dockerignore
### 03-dockerignore.png
![03-dockerignore.png](Screenshots/03-dockerignore.png)\
Что произойдёт если не исключить .env из образа?<br>
Если не исключить .env из образа, все секреты (пароли от БД, API-ключи, приватные ключи шифрования) окажутся «зашиты» внутрь Docker-образа.<br>
Какая угроза безопасности?<br>
Любой человек может посмотреть и скачать .env, получить полный доступ к внутрянке приложения.<br>

# Часть Б. Dockerfile для FastAPI
## 4. requirements.txt
### 04-requirements.png
![04-requirements.png](Screenshots/04-requirements.png)\
Почему версии фиксируем, а не пишем 'latest'?<br>
Фиксация версий гарантирует воспроизводимость: у каждого разработчика будет одни и те же версии библиотек, что даст одинаковые условия для разработки.<br>
Что произойдёт через год без фиксации?<br>
Без фиксации через год библиотеки могут серьёзно изменится, что может привести к поломке работоспособности кода.<br>
## 5. Сборка образа
### 05-fastapi-build.png
![05-fastapi-build.png](Screenshots/05-fastapi-build.png)
## 6. CMD с правильным host
### 06-uvicorn-cmd.png
![06-uvicorn-cmd.png](Screenshots/06-uvicorn-cmd.png)\
Почему --host 0.0.0.0, а не 127.0.0.1?<br>
127.0.0.1 - это локалхост, порт внутри самого же контейнера. Он означает "принимать соединения только изнутри этого же контейнера".<br>
0.0.0.0 означает: «слушать на всех сетевых интерфейсах контейнера». Это позволяет другим контейнерам в той же Docker-сети обращаться к твоему приложению по его внутреннему IP-адресу или имени сервиса.<br>
Что сломается с 127.0.0.1?<br>
Если Nginx захочет обратиться к FastAPI, он будет стучаться в 127.0.0.1, то есть в свой же контейнер. В итоге получится 502 ошибка.<br>

# Часть В. Конфиг Nginx
## 7. docker/nginx/default.conf
### 07-nginx-conf.png
![07-nginx-conf.png](Screenshots/07-nginx-conf.png)\
почему laravel:9000, а не 127.0.0.1:9000?<br>
127.0.0.1:9000 не сработает, потому что в мире Docker каждый контейнер - это изолированная среда со своим собственным localhost. Для контейнера Nginx адрес 127.0.0.1 указывает на него самого, а не на соседний контейнер с Laravel.<br>
Как Docker резолвит имена контейнеров?<br>
Это работает благодаря встроенному DNS-серверу, который автоматически запускается в каждой пользовательской сети:<br>
Service Discovery: описанные сервисы в docker-compose.yml, Docker регистрирует во внутреннем DNS.<br>
Обращение по имени: Когда Nginx внутри своего контейнера видит запрос к http://laravel:9000, он отправляет DNS-запрос встроенному серверу Docker'а: «Какой IP у контейнера с именем laravel?».<br>
Маршрутизация: Docker возвращает внутренний IP-адрес контейнера Laravel (например, 172.18.0.3), и Nginx успешно передает ему данные через FastCGI.<br>
## 8. WebSocket location
### 08-ws-config.png
![08-ws-config.png](Screenshots/08-ws-config.png)\
Где в коде проверяется владелец?<br>
Владелец проверяется в файле /opt/boardy-api/routers/comments.py внутри эндпоинтов @router.putи @router.delete. Проверяется ID авторов.<br>
Что произойдёт если убрать эту проверку?<br>
Если убрать проверку на права редактирования, то любой авторизированный пользователь сможет сделать что угодно с любым комментарием.<br>

# Часть Г. docker-compose.yml
## 9. Пять сервисов
### 09-compose-services.png
![09-compose-services.png](Screenshots/09-compose-services.png)\
## 10. Volumes
### 10-volumes.png
![10-volumes.png](Screenshots/10-volumes.png)\
Что произойдёт с данными MySQL если убрать mysql_data volume и сделать docker compose down?<br> 
Если убрать mysql_data volume, то при  docker compose down данные удаляться, а при новом up MySQL запустится полностью пустым, с чистого листа.<br>
Volume (mysql_data) нужен для того, чтобы данные жили вне жизненного цикла контейнера.<br>
Чем именованный volume отличается от bind-mount?<br>
Именованный volume управляется самим Docker и хранит данные в системной директории. Bind mount напрямую связывает конкретную папку на хосте с контейнером, что удобно для разработки кода, но менее производительно для хранения данных.<br>
## 11. Healthcheck для MySQL и Redis
### 11-healthcheck.png
![11-healthcheck.png](Screenshots/11-healthcheck.png)\
Почему depends_on без healthcheck недостаточно?<br>
depends_on гарантирует лишь порядок запуска контейнеров, но не их готовность к работе.<br>
Какая race condition возникает?<br>
MySQL может стартовать позже Laravel, но внутри себя он будет еще долго инициализировать таблицы и файлы, поэтому при попытке подключения Laravel получит ошибку подключения.<br>
## 12. init.sql для двух БД
### 12-init-sql.png
![12-init-sql.png](Screenshots/13-databases-created.png)
### 12-init-sql.png
![13-databases-created.png](Screenshots/13-databases-created.png)\
Почему init.sql выполняется только при первом запуске?<br>
Образ MySQL настроен так, что он запускает скрипты, только если папка с данными /var/lib/mysql - пуста.<br>
Что произойдёт если изменить файл после первого запуска?<br>
Обновлённые файлы будут проигнорированы после первого запуска.<br>
## 13. Два .env файла
### 14-env-compose.png
![14-env-compose.png](Screenshots/14-env-compose.png)
### 15-env-laravel.png
![15-env-laravel.png](Screenshots/15-env-laravel.png)\
Зачем два разных .env?<br>
Это два разных файла для двух разных программ. Корневой нужен для Docker Compose, чтобы читать переменные и подставлять их в docker-compose.yml.<br>
У Laravel .env используется для своих переменных, например путь и пароль к БД.<br>
Почему DB_HOST=mysql, а не 127.0.0.1?<br>
127.0.0.1 - локалхост, а в Докере каждый сервис - отдельное приложение. Если указать 127.0.0.1, то запросы будут идти не в другой контейнер, а внутри одного.<br>

# Часть Д. Запуск и проверка
## 14. docker compose up
### 16-compose-up.png
![16-compose-up.png](Screenshots/16-compose-up.png)
## 15. Миграции в контейнере
### 17-migrate.png
![17-migrate.png](Screenshots/17-migrate.png)
### 18-passport-install.png
![18-passport-install.png](Screenshots/18-passport-install.png)\
Чем docker compose exec отличается от docker compose run?<br>
compose exec выполняет команду в уже запущенном контейнере, когда как docker compose run запускает отдельный контейнер и удаляет его после завершения команды.<br>
## 16. Приложение работает
### 19-app-running.png
![19-app-running.png](Screenshots/19-app-running.png)\
### 20-comment-works.png
![20-comment-works.png](Screenshots/20-comment-works.png)\
## 17. Реалтайм работает
### 21-realtime-posts.png
![21-realtime-posts.png](Screenshots/21-realtime-posts.png)
### 22-realtime-comments.png
![22-realtime-comments.png](Screenshots/22-realtime-comments.png)
## 18. Данные переживают перезапуск
### 23-persist.png
![23-persist.pngg](Screenshots/23-persist.png)\
Что произойдёт с данными при docker compose down -v?<br>
Без флага - останавливаются и удаляются контейнеры и образы, но сохраняются данные. При флаге -v так же удаляются абсолютно все данные.<br>
В чём опасность флага -v?<br>
Риск потерять все данные, невозможность восстановления, потери состояний сервисов.<br>
## 19. Централизованные логи
### 24-logs.png
![24-logs.png](Screenshots/24-logs.png)\
Какие плюсы централизованных логов Docker по сравнению с tail -f /var/log/* на хосте?<br>
1) Логи сразу отсортированы по времени и синхронизированны, можно сразу посмотреть как действия на одном сервисе отразились на другом.<br>
2) Логи в докере не привязаны к самому контейнеру, он может быть удалён, но логи останутся.<br>
3) Доступ к логам докер можно выдать без прав root или sudo<br>
## 20. Чистая машина
### 25-fresh-install.png
![25-fresh-install.png](Screenshots/25-fresh-install.png)
Какая команда нужна на новой машине от клона репозитория до рабочего приложения?<br>
1) Клонирование:<br>
git clone "ссылка на репозиторий"<br>
Создаем файл переменных для Docker Compose:<br>
cp .env.example .env<br>
Создаем файл переменных для Laravel:<br>
cp boardy-laravel/.env.example boardy-laravel/.env<br>

2) Сборка и запуск:<br>
docker compose build<br>
docker compose up -d<br>

3) Инициализация приложения:<br>
PHP:<br>
docker compose exec laravel composer install --no-interaction<br>
docker compose exec laravel php artisan key:generate<br>
Миграции для БД:<br>
docker compose exec laravel php artisan migrate --force<br>
Настройка OAuth:<br>
docker compose exec laravel php artisan passport:install --no-interaction<br>
docker compose exec laravel php artisan passport:client --public \<br>
    --name="Boardy SPA" \<br>
    --redirect_uri="http://localhost/oauth/callback"<br>
    
4) Проверка:<br>
docker compose ps<br>
