# Практика 3
# Часть A. Nginx
## 1. Установка Nginx
### 01-nginx-status
![01-nginx-status](Screenshots/01-nginx-status.png)
## 2. Страница по IP
### 02-browser-ip
![02-browser-ip](Screenshots/02-browser-ip.png)
## 3. curl
### 03-curl
![04-putty](Screenshots/03-curl.png)
## 4. Директория и права
### 04-permissions
![04-permissions](Screenshots/04-permissions.png)
## 5. Конфигурация Nginx

listen 80 default_server;
listen [::]:80 default_server;

root /var/www/html;

server_name _;

index index.html index.htm index.nginx-debian.html;

# Часть B. DNS
## 6. DNS-зона
### 05-dns-zone
![05-dns-zone](Screenshots/05-dns-zone.png)
## 7. A-запись
### 06-a-record
![06-a-record](Screenshots/06-a-record.png)
## 8. ping
### 07-ping
![07-ping](Screenshots/07-ping.png)
## 9. dig
### 08-dig
![08-dig](Screenshots/08-dig.png)
10. dig +trace
### 09-dig-trace
![09-dig-trace](Screenshots/09-dig-trace.png)
##11. Сайт по домену
### 10-browser-domain
![10-browser-domain](Screenshots/10-browser-domain.png)
