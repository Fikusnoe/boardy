# Практика 14: Финальная микросервисная архитектура
# Часть A. Passport как OAuth 2.1 сервер
## 1. Установка и SPA-клиент
### 01-passport-install.png
![01-passport-install.png](Screenshots/01-passport-install.png)
### 02-spa-client.png
![02-spa-client.png](Screenshots/02-spa-client.png)\
Почему публичный клиент без secret?<br>
Любой пользователь может посмотреть код публичного клиента и найти там client secret, это небезопасно.<br>
Чем PKCE заменяет client_secret и от какой атаки защищает?<br>
PKCE создаёт одноразовый секрет прямо в браузере на момент авторизации. Защищает от перехвата кода авторизации.<br>
## 2. TTL и refresh
![03-token-ttl.png](Screenshots/03-token-ttl.png)\
почему access короткий, а refresh длинный?<br>
access короткий, потому что он часто используется, при утечке токена он быстро протухает, refresh токен нужен чтобы обновить access токен, он используется реже и хранится в более безопасной среде HttpOnly Cookie.<br>
Что произойдёт если access будет 24 часа?<br>
Человек, укравший access сможет делать запросы от лица пользователя всё время действия токена, 24 часа - очень долго<br>
## 3. Проверка выдачи через curl
### 04-pkce-curl.png
![04-pkce-curl.png](Screenshots/04-pkce-curl.png)\
Какие шаги OAuth flow прошёл этот curl-запрос?<br>
1) Фронтенд создаёт code_verifier и code_challenge<br>
2) На /oauth/authorize передаётся code_challenge<br>
3) Пользователь входит в аккаунт<br>
4) Сервер редиректит на страницу с code<br>
5) На /oauth/token передаётся code и code_verifier<br>
6) Сервер проверяет эти строки и отдаёт токен, в случае совпадения.<br>

# Часть Б. Создание boardy_api
## 4. Создание boardy_api
### 05-databases.png
![05-databases.png](Screenshots/05-databases.png)
### 06-comments-schema.png
![06-comments-schema.png](Screenshots/06-comments-schema.png)\
почему access короткий, а refresh длинный?<br>
access короткий, потому что он часто используется, при утечке токена он быстро протухает, refresh токен нужен чтобы обновить access токен, он используется реже и хранится в более безопасной среде HttpOnly Cookie.<br> 
Что произойдёт если access будет 24 часа?<br>
Человек, укравший access сможет делать запросы от лица пользователя всё время действия токена, 24 часа - очень долго<br>
## 5. FastAPI подключён к новой БД
![07-fastapi-db.png](Screenshots/07-fastapi-db.png)

# Часть В. FastAPI: RS256 + полный CRUD
## 6. RS256 проверка
### 08-rs256-success.png
![08-rs256-success.png](Screenshots/08-rs256-success.png)
### 09-rs256-fail.png
![09-rs256-fail.png](Screenshots/09-rs256-fail.png)\
Почему RS256 безопаснее HS256 для распределённых систем?<br>
HS256  - это симметричный алгоритм. Для создания токена и для его проверки используется один и тот же секретный ключ.<br>
RS256 - это асимметричный алгоритм. Используется пара ключей: приватный (для подписи) и публичный (для проверки).<br>
Подпись токенов у RS256 будет на одном сервисе, где хранится приватный ключ, у остальных сервисов - публичные ключи, в отличии от HS256, где у всех сервисов приватный ключ и при утечке нужно будет менять его на всех сервисах.<br>
## 7. Полный CRUD с author_name
### 10-crud-all.png
![10-crud-all.png](Screenshots/10-crud-all.png)\
Почему author_name передаётся в payload запроса, а не извлекается из токена?<br>
Потому что токен - данные, которые индентифицируют пользователя в системе. С помощью него система удостоверяется в правах доступа пользователя.<br>
Payload - данные, которые применяются в какой-то операции. Имя в сервисе можно поменять, а подделать токен - нет.<br> 
Пользователь отправляет на сервер запрос, подтверждает свою личность за счёт токена, передаёт остальные данные о запросе в Payload.<br>
Что было бы если зашить в JWT custom claim?<br>
Помимо того, что токен сам по себе довольно большой, еще он создается на какой-то промежуток времени. Если пользователь поменяет имя, а токен не обновится, то имя пользователя в постах будет старое, пока не обновится токен.<br>
## 8. Owner check
### 11-owner-check.png
![11-owner-check.png](Screenshots/11-owner-check.png)\
Где в коде проверяется владелец?<br>
Владелец проверяется в файле /opt/boardy-api/routers/comments.py внутри эндпоинтов @router.putи @router.delete. Проверяется ID авторов.<br>
Что произойдёт если убрать эту проверку?<br>
Если убрать проверку на права редактирования, то любой авторизированный пользователь сможет сделать что угодно с любым комментарием.<br>
## 9. CORS
### 12-cors-config.png
![12-cors-config.png](Screenshots/12-cors-config.png)\
Почему allow_origins=['*'] + credentials=true браузер блокирует?<br>
Чтобы работал механизм безопасности от CSRF атак.<br>
Что произошло бы с куками если бы пропустил?<br>
Любой сайт смог бы получить куки пользователя с любого другого сайта.<br>

# Часть Г. React PKCE flow
## 10. PKCE утилиты
### 13-pkce-utils.png
![13-pkce-utils.png](Screenshots/13-pkce-utils.png)\
Почему code_challenge передаётся в /authorize, а code_verifier — в /token?<br>
/authorize - небезопасная зона, данные оттуда могут перехватить, поэтому отправляется code_challenge - хэш code_verifier. Даже если кто-то перехватит  code_challenge, получить verifier обратно он не сможет, потому что хеширование - односторонняя функция.<br>
Что если перепутать?<br>
Во-первых система запутается, потому что challenge это Хеш verifier, расшифровка verifier не даст challenge, во-вторых это нарушает идею PKCE.<br>
## 11. Login flow
### 14-login-redirect.png
![14-login-redirect.png](Screenshots/14-login-redirect.png)
### 15-login-callback.png
![15-login-callback.png](Screenshots/15-login-callback.png)
## 12. Обмен code на токены
### 16-token-exchange.png
![16-token-exchange.png](Screenshots/16-token-exchange.png)\
Что произойдёт если убрать проверку state?<br>
Сервис не будет проверять достоверность ссылки, откуда вернулся пользователь (из callback, например). Так, пользователю могут подсунуть вредоносную ссылку, из-за которой он может зайти не в свой, а в чужой аккаунт.<br>
Какая атака возможна?<br>
CSRF<br>
## 13. Refresh token в HttpOnly cookie
### 17-refresh-cookie.png
![17-refresh-cookie.png](Screenshots/17-refresh-cookie.png)\
Что случится если refresh положить в localStorage и сайт получит XSS?<br>
В таком случае пользователь, похитивший refresh, получит полный контроль над украденным аккаунтом на полное время ддлительности refresh (может быть очень долго)<br>
## 14. Silent refresh
### 18-silent-refresh.png
![18-silent-refresh.png](Screenshots/18-silent-refresh.png)

# Часть Д. Redis Pub/Sub
## 15. Redis установлен
### 19-redis-ping.png
![19-redis-ping.png](Screenshots/19-redis-ping.png)
## 16. Laravel publish new_post
### 20-laravel-publish.png
![20-laravel-publish.png](Screenshots/20-laravel-publish.png)\
Чем Redis::publish архитектурно лучше Http::post() к FastAPI?<br>
Redis создаёт каналы Sub и Pub. Сервисы остаются независимы друг от друга, они никак друг с другом не взаимодействуют, кроме этих каналов. Кроме того, чтобы сервисы получали сообщения от Pub, сервисам достаточно<br> подписаться на этот канал, а не создавать от главного сервиса для нескольких других сервисов несколько запросов.<br>
## 17. FastAPI subscriber на new_post
### 21-subscriber-running.png
![21-subscriber-running.png](Screenshots/21-subscriber-running.png)
![22-broadcast-flow.png](Screenshots/22-broadcast-flow.png)
## 18. User observer и user.renamed
### 23-user-renamed.png
![23-user-renamed.png](Screenshots/23-user-renamed.png)
Почему UserObserver вызывается автоматически?<br> 
После обновления User, идёт обращение к AppServiceProvider, в нём указан класс UserObserver, у которого есть метод updated.<br> 
Где это магия Laravel?<br>
Из бд диспетчер событий смотрит в  AppServiceProvider и потом работа с UserObserver.php<br>
## 19. Денормализация имени
### 24-denorm-before.png
![24-denorm-before.png](Screenshots/24-denorm-before.png)
### 24-denorm-before.png
![25-denorm-after.png](Screenshots/25-denorm-after.png)

# Часть Е. Финальные проверки
## 20. Два браузера: посты в реалтайме
### 26-two-browsers-post.png
![26-two-browsers-post.png](Screenshots/26-two-browsers-post.png)
## 21. Два браузера: комментарии в реалтайме
### 27-two-browsers-comment.png
![27-two-browsers-comment.png](Screenshots/27-two-browsers-comment.png)
## 22. Никаких прямых HTTP-вызовов
### 28-no-http-callback.png
![28-no-http-callback.png](Screenshots/28-no-http-callback.png)
### 29-nginx-no-internal.png
![29-nginx-no-internal.png](Screenshots/29-nginx-no-internal.png)

