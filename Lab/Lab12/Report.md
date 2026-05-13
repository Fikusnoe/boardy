# Практика 12: Laravel: переезд на фреймворк (MVC + Breeze + Socialite)
# Часть A. Установка и переключение домена
## 1. Composer и PHP-расширения
### 01-composer-php.png
![01-composer-php.png](Screenshots/01-composer-php.png)
## 2. Переезд папок
### 02-folders.png
![02-folders.png](Screenshots/02-folders.png)
### 03-laravel-version.png
![03-laravel-version.png](Screenshots/03-laravel-version.png)
## 3. Структура Laravel
Назначения папок:<br>
app/ - ядро приложения с контроллерами, моделями и бизнес-логикой.<br>
routes/ - хранилище всех маршрутов.<br>
resources/views/ - шаблоны HTML.<br>
database/ - инструменты для управление базой данных и её структурой.<br>
public/ - корневая директория, содержащая точку входа и публичные ресурсы.<br>

Почему document_root nginx должен указывать на public/, а не на /var/www/boardy/?<br>
Указание на public/ гарантирует, что index.php будет единственной точкой входа.<br>
Что плохого случится, если указать на корень?<br>
Если указать на корень, любой пользователь может напрямую обратится к файлам, в том числе служебным, что приведёт к утечке чувствительной информации, по типу паролей, секретов и т.п.<br>
## 4. Nginx-конфиг
### 04-nginx-config.png
![04-nginx-config.png](Screenshots/04-nginx-config.png)
### 05-comment-created.png
![05-laravel-welcome.png](Screenshots/05-laravel-welcome.png)\
Что делает try_files $uri $uri/ /index.php?$query_string?<br>
Это обработка запроса пользователя nginx'ом: сначала проверяется наличие файла с именем $uri, затем проверяется папка с таким именем $uri/ , затем запрос отдаётся фреймворку через /index.php?$query_string на index.php<br>
Что произойдёт без этой строки при заходе на /posts/3?<br>
Без этой проверки nginx вернёт 404 Not found<br>

# Часть B. OAuth через GitHub
## 5. Создание БД boardy_main
### 06-databases.png
![06-databases.png](Screenshots/06-databases.png)\
Зачем мы создаём новую БД, а не подгоняем старую под Laravel?<br>
У старой БД схема под чистый PHP, подгонять под Laravel будет дороже, чем создавать с нуля.<br>
Что в схеме старой БД мешает?<br>
password_hash вместо password, username вместо name<br>
## 6. Подключение Laravel к БД
### 07-tinker-pdo.png
![07-tinker-pdo.png](Screenshots/07-tinker-pdo.png)
## 7. Миграции posts и comments
### 08-migrate-status.png
![08-migrate-status.png](Screenshots/08-migrate-status.png)
### 09-show-tables.png
![09-show-tables.png](Screenshots/09-show-tables.png)
## 8. OAuth App на GitHub
### 10-model-relations.png
![10-model-relations.png](Screenshots/10-model-relations.png
## 9. Столбец github_id
### 11-seed-counts.png
![11-seed-counts.png](Screenshots/11-seed-counts.png)

# Часть C. Анализ
## 10. Маршруты
### 12-route-list.png 
![12-route-list.png ](Screenshots/12-route-list.png )
## 11. Лента постов
### 13-oauth-logged.png
![13-posts-index.png](Screenshots/13-posts-index.png)
## 12. Страница поста с комментариями
### 14-post-show.png
![14-post-show.png](Screenshots/14-post-show.png)\
## 13. Создание поста
### 15-post-create.png
![15-post-create.png](Screenshots/15-post-create.png)
### 16-post-after-create.png
![16-post-after-create.png](Screenshots/16-post-after-create.png)
## 14. Policy и редактирование
### 17-edit-own.png
![17-edit-own.png](Screenshots/17-edit-own.png)
### 18-edit-foreign-403.png
![18-edit-foreign-403.png](Screenshots/18-edit-foreign-403.png)\
Cравните Policy с тем, как авторизация была реализована в Lab10–11 (на чистом PHP).<br>
Сколько строк кода ушло на тот же эффект?<br>
На весь crud + Policy ушло около 30 строк во всём проекте, когда как только повторение проверки авторизации и session_start() в файлах обычного PHP в легаси проекте занимало более 30.<br>
## 15. Удаление поста
### 19-post-deleted.png
![19-post-deleted.png](Screenshots/19-post-deleted.png)
## 16. Комментарий через Blade
### 20-comment-created.png
![20-comment-created.png](Screenshots/20-comment-created.png)

# Часть D. Breeze + Socialite
## 17. Установка Breeze
### 21-register.png
![21-register.png](Screenshots/21-register.png)
### 22-login.png
![22-login.png](Screenshots/22-login.png)
## 18. Регистрация и вход
### 23-after-register.png
![23-after-register.png](Screenshots/23-after-register.png)
## 19. GitHub OAuth-приложение
### 24-github-app.png
![24-github-app.png](Screenshots/24-github-app.pngg)
## 20. Socialite
### 25-login-with-github.png
![25-login-with-github.png](Screenshots/25-login-with-github.png)
## 21. Полный OAuth flow
### 25-login-with-github.png
![25-login-with-github.png](Screenshots/26-github-authorize.png)
### 25-login-with-github.png
![25-login-with-github.png](Screenshots/27-after-github-login.png)
### 25-login-with-github.png
![25-login-with-github.png](Screenshots/28-mysql-github-id.png)\
Сравните количество строк кода Lab11 (ручной OAuth на чистом PHP) и Lab12 (Socialite). Что сократилось и за счёт чего?<br>
В Lab11 вручную реализован весь OAuth-поток. На это ушло около 70 строк кода. В Lab12 пакет Socialite скрыл все эти шаги: достаточно двух методов - redirect() и user() — и проверка state, HTTP-запросы, остальное внутри Socialite. В результате код сократился до 15 строк.<br>

# Часть E. Архитектурные вопросы
## 22. Что осталось от прошлых практик
У вас на VPS лежат /var/www/boardy-legacy/ (старый PHP) и БД boardy. Зачем мы их не удалили? <br>
Чтобы сравнить ручную реализацию функций веб-приложения и реализацию с фреймворком.<br>
Что произойдёт, если попробовать открыть https://fgsfds.ai-info.ru/login.php (старый PHP-логин)?<br>
Ничего, сейчас nginx смотрит только в public - точку входа в приложение, в архитектуре Laravel такого файла нет.<br>
## 23. FastAPI и React
FastAPI продолжает работать на api.fgsfds.ai-info.ru, а React-файлы лежат в Lab9–11. Но в Laravel-проекте мы их не используем. Почему сейчас не используем — что мешает интегрировать?<br>
FastAPI не умеет проверять пользователей как Laravel, вместе с этим не работает React.<br>
Где они нам пригодятся в Lab13?<br>
Перепишем FastAPI под BFF: валидация Bearer-токенов от Passport (RS256), проксирование запросов в Laravel.<br>
Вернём React на страницу поста — комменты будут через FastAPI с Bearer<br>
## 24. Реалтайм
Сейчас комментарии появляются только после F5. Какое архитектурное решение нам нужно, чтобы один пользователь видел новый комментарий другого без перезагрузки?<br>
WebSocket, который поддерживает постоянные соединения и отправляет события всем подключённым клиентам.<br>
Какие два сервера-кандидата для этого решения и почему именно они?<br>
Laravel Reverb и FastAPI + WebSocket.<br>
Laravel - потому что основной сайт на нём, нативно. FastApi, потому что он уже работает с комментариями и можно вынести логику отдельно от основного сайта.<br>
