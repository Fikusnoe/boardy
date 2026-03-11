# Практика 3
# Часть A. Виртуальный хост основного сайта
## 1. Директория проекта
### 01-directory
![01-directory](Screenshots/01-directory.png)
## 2. Конфиг виртуального хоста
### 02-browser-ip
![02-vhost-config](Screenshots/02-vhost-config.png)\
server_name - название домена.<br>
root - папка откуда берутся файлы.<br>
access_log - лог для подключения.<br>
error_log - лог для ошибок.<br>
try_files — ищет файл, не нашёл - 404 ошибка.<br>
error_page - страница ошибки 404.<br>

# Часть B. Страницы проекта
## 3. Лендинг
### 03-landing
![03-landing](Screenshots/03-landing.png)
## 4. Форма обратной связи
### 04-permissions
![04-forms](Screenshots/04-form.png)
## 5. Стили и 404
![05-404](Screenshots/05-404.png)

# Часть C. Второй виртуальный хост — API
## 6. DNS-запись для поддомена
### 06-dns-api
![06-dns-api](Screenshots/06-dns-api.png)
## 7. Проверка DNS
### 07-dig-api
![07-dig-api](Screenshots/07-dig-api.png)
## 8. Конфиг и заглушка API
### 08-api-config
![08-api-config](Screenshots/08-api-config.png)
### 09-api-browser
![09-api-browser](Screenshots/09-api-browser.png)

# Часть D. Исследование HTTP
## 9. GET-запрос через curl -v
### 10-curl-v.png
![10-curl-v](Screenshots/10-curl-v.png)
## 10. Виртуальные хосты в действии
### 11-vhosts
![11-vhosts](Screenshots/11-vhosts.png)\
На одном сервере находится несколько доменов, по имени Host Nginx понимает к какому домену обращается пользователь.<br>
Третий запрос был перенаправлен на хост по умолчанию (с лендингом).
## 11. POST-запрос
![12-post-405](Screenshots/12-post-405.png)\
405 ошибка из-за того, что метод еще не реализован.
### 12-post-405
Get возвращает и заголовки и тело ответа, а Head только заголовки. Head нужен чтобы быстро проверить ответ, не подгружая тело ответа.
# Часть E. Логи
## 13. Раздельные логи
### 13-logs
![13-logs](Screenshots/13-logs.png)
## 14. Фильтрация логов
### 14-log-stats
![14-log-stats](Screenshots/14-log-stats.png)
