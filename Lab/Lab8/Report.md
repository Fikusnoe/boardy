# Практика 8
# Часть A. MySQL — установка и настройка
## 1. Установка MySQL
### 01-mysql-status.png
![01-mysql-status.png](Screenshots/01-mysql-status.png)
## 2. База данных и пользователь
### 02-db-charset.png
![02-db-charset.png](Screenshots/02-db-charset.png)\
почему utf8mb4, а не utf8?<br>
utf8 -  недостаточно, в ней всего 3 байта, не поддерживаются эмодзи. В utf8mb4 поддерживаются все языки, эмодзи, именно это является стандартом.<br>
Что такое collation и зачем unicode_ci?<br>
Collation - способ сравнения, unicode_ci - нужен для точного сравнения по Unicode правилам.<br>
## 3. phpMyAdmin
### 03-phpmyadmin.png
![03-phpmyadmin.png](Screenshots/03-phpmyadmin.png)

# Часть B. Таблицы и связи
## 4. Три таблицы
### 04-tables-cli.png
![04-tables-cli.png](Screenshots/04-tables-cli.png)
### 05-tables-pma.png
![05-tables-pma.png](Screenshots/05-tables-pma.png)\
что такое FOREIGN KEY и ON DELETE CASCADE?<br>
FOREIGN KEY — это связь между двумя таблицами, которая обеспечивает целостность данных. Она гарантирует, что значение в одной таблице существует в другой таблице.<br>
ON DELETE CASCADE — это правило, которое говорит: "Когда удаляешь запись из главной таблицы — автоматически удали все связанные записи из дочерней"<br>
Зачем?<br>
Для связи между таблицами и чтобы не оставалось старых данных, которые должны быть обновлены вместе с родительскими данными.<br>
Какой движок используется и почему?<br>
InnoDB по причине ACID-совместимости и обработки конкурентного доступа.<br>
## 5. SQL-скрипт
### 06-schema-sql.png
![06-schema-sql.png](Screenshots/06-schema-sql.png)

# Часть C. SQL — базовые операции
## 6. INSERT
### 07-data-cli.png
![07-data-cli.png](Screenshots/07-data-cli.png)
### 08-data-pma.png
![08-data-pma.png](Screenshots/08-data-pma.png)
## 7. SELECT + JOIN
### 09-join.png 
![09-join.png ](Screenshots/09-join.png )\
зачем JOIN?<br>
JOIN позволяет сопоставить строки из одной таблицы со строками другой таблицы по заданному правилу сопоставления, получив на выходе новую таблицу.<br>
Как получить имя автора без него?<br>
Можно написать подзапрос в скобках.<br>
## 8. Foreign Key — защита целостности
### 10-fk-error.png
![10-fk-error.png](10-fk-error.png)
## 9. CASCADE
### 11-cascade.png
![11-cascade.png](Screenshots/11-cascade.png)
## 10. SQL-инъекция
### 12-injection.png
![12-injection.png](Screenshots/12-injection.png)\
как работает SQL-инъекция?<br>
SQL-инъекция - это атака, при которой пользователь внедряет SQL-код в приложение через код или запрос.<br>
Как prepared statement защищает?<br>
Prepared Statement разделяет SQL-код и данные. Данные никогда не интерпретируются как SQL-команды.<br>
## 11. db.php
### 13-db-php.png
![13-db-php.png](Screenshots/13-db-php.png)
## 12. submit.php через MySQL
### 14-submit.png
![14-submit.png](Screenshots/14-submit.png)\
### 15-submit-pma.png
![15-submit-pma.png](Screenshots/15-submit-pma.png)
## 13. messages.php через MySQL
### 16-messages.png
![16-messages.png](Screenshots/16-messages.png)

# Часть E. FastAPI + MySQL
### 17-api-messages.png
![17-api-messages.png](Screenshots/17-api-messages.png)
### 18-api-users.png
![18-api-users.png](Screenshots/18-api-users.png)\
почему aiomysql, а не обычный mysql-connector?<br>
aiomysql асинхронен, mysql-connector - нет.<br>
Что будет с event loop при синхронном драйвере?<br>
Event Loop заблокируется.<br>
