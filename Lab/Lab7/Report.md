# Практика 7
# Часть A. PHP-FPM
## 1. Установка PHP-FPM
### 01-php-version.png
![01-php-version.png](Screenshots/01-php-version.png)
## 2. Форма и сообщения на PHP
### 02-php-form.png
![02-php-form.png](Screenshots/02-php-form.png)
### 03-php-messages.png
![03-php-messages.png](Screenshots/03-php-messages.png)
## 3. Конфиг Nginx для PHP
### 04-nginx-php.png
![04-nginx-php.png](Screenshots/04-nginx-php.png)\
чем fastcgi_pass отличается от CGI через fcgiwrap?<br>
fastcgi_pass — это директива, указывающая на FastCGI-сервер, а fcgiwrap — это адаптер, который позволяет запускать старые CGI-скрипты через этот современный протокол.<br>
Почему PHP-FPM быстрее?<br>
PHP-FPM - высокооптимизированный FastCGI-сервер для PHP, который изначально создан для высокой производительности.<br>
## 4. Shared nothing
### 05-shared-nothing.png
![05-shared-nothing.png](Screenshots/05-shared-nothing.png)\
Почему счётчик не растёт? <br>
Переменные не живут между запросами.<br>
Что такое shared nothing?<br>
Это архитектурный подход, при котором каждый воркер системы работает независимо и не разделяет с другими компонентами никаких ресурсов.
## 5. Блокировка воркеров
### 06-php-slow.png
![06-php-slow.png](Screenshots/06-php-slow.png)

# Часть B. FastAPI
## 6. Установка и приложение
### 07-api-status.png
![07-api-status.png](Screenshots/07-api-status.png)
### 08-api-messages.png
![08-api-messages.png](Screenshots/08-api-messages.png)
## 7. Живой процесс (счётчик)
### 09-counter.png
![09-counter.png](Screenshots/09-counter.png)\
почему здесь счётчик растёт, а в PHP не рос?<br>
Uvicorn не уничтожает состояние после запроса.
## 8. Async: 10 запросов за 2 секунды
### 10-async-slow.png
![10-async-slow.png](Screenshots/10-async-slow.png)\
Почему 10 запросов по 2 секунды заняли ~2, а не 20 секунд?<br>
Запросы выполняются асинхронно, поток не блокируется и выполняет ещё задачи.
## 9. Блокирующий код убивает event loop
### 11-blocking.png
![11-blocking.png](Screenshots/11-blocking.png)\
Чем /api/slow отличается от /api/slow-blocking? Почему время разное?<br>
slow обрабатывает запросы асинхронно, один за другим без блокировки, а slow-blocking блокирует поток на всё время выполнения каждого отдельного запроса
## 10. Swagger
### 12-swagger.png
![12-swagger.png](Screenshots/12-swagger.png)
## 11. systemd-сервис
### 13-systemd.png
![13-systemd.png](Screenshots/13-systemd.png)
## 12. Nginx proxy_pass
### 14-nginx-api.png
![14-nginx-api.png](Screenshots/14-nginx-api.png)\
чем proxy_pass отличается от fastcgi_pass?<br>
proxy_pass использует HTTP протокол, обратный прокси с другим HTTP-сервером для работы с приложением, когда как fastcgi_pass использует FastCGI протокол для общения с приложением.<br>
Почему для PHP одно, для Python другое?<br>
PHP изначально был модулем Apache, FastCGI был способом общения с другими серверами. Для Python стал стандартом WSGI, который позволял серверу и приложению общаться через HTTP с помощью прокси-сервера.

# Часть C. Сравнение
## 13. Два формата
### 15-compare.png (HTML)
![15-compare.png](Screenshots/15-compare.png)
### 15-compare(1).png (JSON)
![15-compare(1).png](Screenshots/15-compare(1).png)\
HTML vs JSON<br>
Одни данные, два формата. Чем отличаются? Для кого каждый?<br>
HTML - визуальная разметка страницы для человека в браузере. JSON - структурированные данные для программы.
## 14. Процессы
### 16-processes.png (HTML)
![16-processes.png](Screenshots/16-processes.png)

### 17-pull-request.png
![17-pull-request.png](Screenshots/17-pull-request.png)
