### Nemiro.Data.PHP

**Nemiro.Data.PHP** это небольшой набор вспомогательных классов для работы с базами данных **MySql** и **PostgreSQL**.

Работа с базами данных осуществляется всего пятью простыми методами: **ExecuteNonQuery**, **ExecuteScalar**, **GetData**, **GetTable** and **GetRow**.

Классы позволяются автоматически управлять соединениями и создавать параметризированные запросы, что делает работу с базами данных безопасной.

Исходный код **Nemiro.Data.PHP** предоставляется на условиях лицензии **Apache License Version 2.0**.

### Возможности

* Единый интерфейс работы с различными поставщиками данных;
* Автоматическое управление соединениями с базами данных;
* Параметризированные запросы;
* Клиент для MySql;
* Клиент для PostgreSQL.

### Системные требования

* PHP 5 >= 5.3;
* MySQL >= 5.6;
* PostgreSQL >= 7.4.

**Примечание:** Работа с ранними версиями просто не проверялась, но в теории возможна.

### Поддержка

Дальнейшая поддержка и развитие проекта не планируется. Переходите на **.NET** ;-)

### Как работать с этим проектом?

Проект создан в **[Visual Studio 2013](https://www.visualstudio.com/)** с использованием **[PHP Tools for Visual Studio](https://visualstudiogallery.msdn.microsoft.com/6eb51f05-ef01-4513-ac83-4c5f50c95fb5)**.

Для использования классов в собственных проекта,
рекомендуется поместить файлы решения в папку **\Nemiro\Data** 
(соответствует имени пространства имен).

### Как использовать классы?

#### Конфигурация

По умолчанию, параметры соединения с базами данных берутся из следующих констант:

```PHP
// Константы для MySql
// имя базы данных
define('MYSQL_DB_NAME', '%your database name here%');
// имя пользователя бд
define('MYSQL_DB_USER', '%your database usernamename here%');
// пароль пользователя бд
define('MYSQL_DB_PASSWORD', '%your database password here%');
// имя хоста сервера бд, в большинстве своем - localhost
define('MYSQL_DB_HOST', 'localhost');
// номер порта, по которому доступен сервер бд, в большинстве своем - 3306
define('MYSQL_DB_PORT', 3306);
// режим соединения с бд
define('MYSQL_DB_MODE', 2);

// Константы для PostgreSQL
define('PGSQL_DB_NAME', '%your database name here%');
define('PGSQL_DB_USER', '%your database usernamename here%');
define('PGSQL_DB_PASSWORD', '%your database password here%');
define('PGSQL_DB_HOST', 'localhost');
define('PGSQL_DB_PORT', 5432);
define('PGSQL_DB_MODE', 1);
```

Константа **DB_MODE** может иметь одну из следующих значений:

* 0 - ручной режим - программист вручную закрывает соединение с БД методом **Disconnect**;
* 1 - автоматический режим - соединение автоматически открывается и закрывается при каждом запросе к базе данных;
* 2 - умный (рекомендуется) - соединение закрывается в момент завершения жизненного цикла экземпляра класса-клиента.

Любые параметры можно переопределить отдельно, при инициализации конкретного экземпляра класса-клиента.

#### Необходимые файлы

Для работы с клиентами БД следует включить следующие файлы:

```PHP
require_once './Nemiro/Data/Import.php';
```

или

```PHP
// клиент для MySql
require_once './Nemiro/Data/MySql.php';
// клиент для PgSql
require_once './Nemiro/Data/PgSql.php';
```

Обратите внимание, нужно указывать тот путь, по которому у вас расположены файлы классов библиотеки **Nemiro.Data.PHP**.

#### Импорт пространств имен и классов

Для удобства работы, рекомендуется импортировать необходимые классы:

```PHP
// MySql - клиент для MySql
use Nemiro\Data\MySql as MySql;
// PgSql - клиент для PostgreSQL
use Nemiro\Data\PgSql as PgSql;
// DBCommand - формирователь запросов
use Nemiro\Data\DBCommand as DBCommand;
```

#### Как обрабатывать ошибки?

Каждый метод может выбросит исключение.

Для обработки ошибок следует использовать блоки `try { } catch { }`.


#### Примеры использования

В следующем примере выполняется запрос на выборку всех записей из таблицы `[messages]`.

Запрос выполняется методом **GetTable**, который возвращает массив строк.

Каждая строка, из результатов запроса, перебирается в цикле и выводится на экран.

```PHP
// создаем клиент MySql
$client = new MySql();

// создаем команду
$client->Command = new DBCommand('SELECT * FROM messages');

// выполняем запрос и получаем таблицу
$table = $client->GetTable();

// выводим полученные данные на экран
echo '<pre>';
foreach($table as $row)
{
	print_r($row);
}
echo '</pre>';
```

В следующем примере показано использование параметризированного запроса.

Запрос выполняется методом **ExecuteScalar**, который, 
для инструкции `INSERT INTO`, возвращает идентификатор добавленной записи 
(если идентификатор существует и является числовым счетчиком).

```PHP
// создаем клиент MySql
$client = new MySql();

// создаем команду
$client->Command = new DBCommand
(
	'INSERT INTO users (username, date_created) '.
	'VALUES (@username, @date_created)'
);

// @username и @date_created - это параметры запроса, 
// добавляем их в команду и указываем нужные значения
$client->Command->Parameters->Add('@date_created')->SetValue(date('Y-m-d H-i-s'));
$client->Command->Parameters->Add('@username')->SetValue('anyname');

// выполняем запрос
$newId = $client->ExecuteScalar();

// выводим результат на экран
echo 'ID = '.$newId;
```

В следующем примере показано использованием метода **GetData** 
для выполнения нескольких запросов одновременно.

```PHP
// создаем клиент MySql
$client = new MySql();

// создаем команду 1
$firtCommand = new DBCommand('SELECT * FROM users WHERE is_blocked = 0');
// создаем команду 2
$secondCommand = new DBCommand
(
	'SELECT * FROM messages WHERE id_users IN '.
	'(SELECT id_users FROM users WHERE is_blocked = 0) AND '.
	'subject LIKE @search_subject'
);
$secondCommand->Parameters->Add('@search_subject', '%hello%');
// создаем команду 3
$thirdCommand = 'SELECT * FROM files';

// и т.д...

// добавляем команды
$client->Command = array($firtCommand, $secondCommand, $thirdCommand);

// выполняем запрос
$data = $client->GetData();

// выводим список полученных таблиц (всего три)

echo '<pre>';
foreach ($data as $table)
{
	print_r($table);
}
echo '</pre>';
```

В следующем примере показана неправильный запрос, попытка выполнить который приведет к возникновению исключения, которое будет обработано.

```PHP
try
{
	$client->Command = 'SELECT * FROOOM [table]';
	$client->ExecuteNonQuery();
} 
catch (Exception $ex)
{
	echo 'Ошибка: '.$ex->getMessage();
}
```