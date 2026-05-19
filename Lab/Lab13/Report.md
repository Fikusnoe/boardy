# Практика 12: Laravel: переезд на фреймворк (MVC + Breeze + Socialite)
# Часть A. FastAPI: WebSocket
## 1. ConnectionManager
### 01-ws-connected.png
![01-ws-connected.png](Screenshots/01-ws-connected.png)\
Почему self.active — список в памяти процесса, а не в базе данных?<br>
Cписок в памяти, потому что WebSocket соединения живут в RAM, так как нужна динамика, а запросы к БД - медленные и тяжеловесные.<br>
Что произойдёт если Uvicorn перезапустится?<br>
Все Websocket соединения оборвутся, список очистится<br>
## 2. /internal/broadcast
### 02-broadcast.png
![02-broadcast.png](Screenshots/02-broadcast.png)\
Почему /internal/broadcast не требует JWT-авторизации?<br>
Потому что этот эндпоинт не предназначен для внешних запросов. Он создан для внутреннего общения между Laravel и FastAPI.<br>
Какой риск остаётся и как его закрывает Nginx?<br>
Если кто-то узнает про существование /internal/broadcast/, он сможет отправлять поддельные сообщения в WebSocket, и все пользователи увидят их.<br>
Nginx просто закрывает эту проблему: в конфигурации запрещены запросы ото всех, кроме localhost (127.0.0.1)<br>
## 3. Два клиента
### 03-two-clients.png
![03-two-clients.png](Screenshots/03-two-clients(1).png)
![03-two-clients.png](Screenshots/03-two-clients(2).png)\
Что произойдёт если один из клиентов отключился, а broadcast уже начался?<br>
Отключившийся пользователь не получит сообщение, остальные - получат.<br>
Где в коде это обрабатывается?<br>
В ws.py метод broadcast ведёт учёт мёртвых Вебсокетов и удаляет их.<br>

# Часть B. OAuth через GitHub
## 4. Nginx-конфиг
### 04-laravel-log.png
![04-laravel-log.png](Screenshots/04-laravel-log.png)\
Зачем timeout(2)?<br>
Таймаут нужен, чтобы пользователь не застрял на странице создания поста.<br>
Что случится если FastAPI недоступен и timeout не указан?<br>
Если FastAPI недоступен и нет таймаута, пользователь будет висеть на странице, пока FastAPI не ответит или пока не выдаст ошибку.<br>
## 5. Проверка callback
### 05-callback.png
![05-callback.png](Screenshots/05-callback.png)\
Почему HTTP-callback называют костылём?<br>
HTTP-callback — это ручной способ связать два сервиса, который переносит логику доставки на уровень приложения вместо использования брокеров сообщений.<br>
Опишите проблему этой архитектуры.<br>
1) Для получения поста нужно дождаться ответа от FastAPI. Если FastAPI тормозит - сообщение придёт с задержкой.<br>
2) Если есть несколько воркеров Uvicorn и разные ConnectionManager, часть клиентов не получат события<br>
3) Отсутствие гарантий доставки: при отсутствии доступа к FastAPI или сети, пользователи могут не получить информацию о новом посте.<br>


# Часть C. WebSocket в Blade
## 6. WebSocket в Blade
### 06-devtools-ws.png
![06-devtools-ws.png](Screenshots/06-devtools-ws.png)\
Почему на локалке нужно ws://, а на проде wss://?<br>
ws:// подходит для локалки потому что весь трафик внутри хоста и разработка на ws:// легче и быстрее. Для прода важна безопасность трафика поэтому используем wss://<br>
Что произойдёт если использовать wss:// без TLS?<br>
Браузер требует wss:// для https, без wss:// браузер выкинет ошибку.<br>
## 7. Два браузера
### 07-two-browsers.png
![07-two-browsers.png](Screenshots/07-two-browsers.png)
### 08-devtools-frame.png
![08-devtools-frame.png](Screenshots/08-devtools-frame.png)
## 8. XSS
### 09-show-tables.png
![09-show-tables.png](Screenshots/09-show-tables.png)\
Что делает функция escapeHtml()?<br>
Экранирует символы для безопасного отображения текста.<br>
Что случится если вставить данные напрямую в innerHTML без экранирования?<br>
Без экранирования любой может написать скрипт и при загрузке такого скрипта в посте, скрипт выполнится, чем могут воспользоваться хакеры.<br>

## 9. Переподключение
### 10-reconnect.png
![10-reconnect.png](Screenshots/10-reconnect.png
## 10. Маршруты
### 11-nginx-ws.png
![11-nginx-ws.png](Screenshots/11-nginx-ws.png)\
Что сломается если убрать proxy_http_version 1.1?<br>
WebSocket не установится. Nginx по умолчанию использует HTTP/1.0, который не поддерживает протокол Upgrade. Браузер получит ошибку.<br>
Если убрать proxy_set_header Upgrade?<br>
FastAPI не поймёт, что нужно переключиться на WebSocket. Заголовок Upgrade говорит серверу сменить протокол с HTTP на WebSocket. Без него соединение останется обычным HTTP и сразу закроется.<br>
Если убрать proxy_read_timeout?<br>
Соединение будет обрываться через 60 секунд по умолчанию. WebSocket предназначен для долгоживущих соединений, поэтому нужно увеличивать таймаут<br>
## 11. Лента постов
### 12-internal-denied.png
![12-internal-denied.png](Screenshots/12-internal-denied.png)\
Почему /internal/broadcast опасен без ограничения доступа?<br>
Потому что без ограничения доступа любой человек в сети сможет получить доступ к функциям WebSocket на сервере.<br>
Кто мог бы его вызвать?<br>
Все, кто знают об этой уязвимости.<br>
