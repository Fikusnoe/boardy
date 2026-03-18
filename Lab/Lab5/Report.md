# Практика 5
# Часть A. HTTPS для основного сайта
## 1. Установка certbot
### 01-certbot-installed
![01-certbot-installed](Screenshots/01-certbot-installed.png)
## 2. Установка certbot
### 02-certbot-success
![02-certbot-success](Screenshots/02-certbot-success.png)\
## 3. Проверка в браузере
### 03-browser-lock
![03-browser-lock](Screenshots/03-browser-lock.png)
### 04-certificate-info
![04-certificate-info](Screenshots/04-certificate-info.png)
## 4. Редирект
### 05-redirect
![05-redirect](Screenshots/05-redirect.png)
## 5. Конфиг после certbot
### 06-nginx-ssl-config
![06-nginx-ssl-config](Screenshots/06-nginx-ssl-config.png)

# Часть B. HTTPS для API-сервиса
## 6. Сертификат для api-поддомена
### 07-api-certbot
![07-api-certbot](Screenshots/07-api-certbot.png)
## 7. Проверка обоих доменов
### 08-both-https
![08-both-https](Screenshots/08-both-https.png)

# Часть C. Разбор TLS
## 8. TLS handshake
### 09-tls-handshake
![09-tls-handshake](Screenshots/09-tls-handshake.png)
## 9. Цепочка доверия
### 10-chain
![10-chain](Screenshots/10-chain.png)\
Цепочка: fgsfds.ai-info.ru > Let's Encrypt > Internet Security Research Group<br>
Сертификат сайта браузер получает первым, проверяет его, с помощью промежуточного CA.<br>
Затем проверяет сертификат промежуточного СА с помощью корневого CA.<br>
Корневой сертификат уже есть в хранилище доверенных корневых CA браузера/ОС.<br>
Если все подписи верны и сертификаты не истекли, цепочка доверия прошла и проверка успешна.<br>

## 10. Сравнение сертификатов
### 11-compare-certs
![11-compare-certs](Screenshots/11-compare-certs.png)\
Общее - издатель.<br>
Отличаются - домены.<br>

# Часть C. HSTS, кэширование, gzip
## 11. HSTS
### 12-hsts
![12-hsts](Screenshots/12-hsts.png)\
Что такое HSTS и от чего защищает?<br>
HSTS это защитная механика, которая заставляет браузер всегда открывать сайт по https.<br>
Она защищает от подмены протокола (http на https), перехвата cookie файлов.<br>
### 12. 13-cache-gzip
![13-cache-gzip](Screenshots/13-cache-gzip.png)\
## 13. Автообновление
### 14-renew
![14-renew](Screenshots/14-renew.png)
### 15-pull-request
![15-pull-request](Screenshots/15-pull-request.png)
