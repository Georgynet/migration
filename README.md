# Механизм миграций для БД

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Georgynet/migration/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Georgynet/migration/?branch=master)
[![Code Climate](https://codeclimate.com/github/Georgynet/migration/badges/gpa.svg)](https://codeclimate.com/github/Georgynet/migration)
[![Build Status](https://travis-ci.org/Georgynet/migration.svg?branch=master)](https://travis-ci.org/Georgynet/migration)
[![Code Coverage](https://scrutinizer-ci.com/g/Georgynet/migration/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Georgynet/migration/?branch=master)

Простой механизм реализующий просмотр списка доступных миграций. Применять и откатывать миграции можно пошагово.

Для использования необходимо создать экземпляр класса ```MigrationApp```, передать ему консольное приложение, подключение к БД и конфиг.

Пример конфига:

```php
return [
    'migration_path' => dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'migrations',
    'migration_table_name' => 'migration_table'
];
```

После этого инициализировать механизм миграций консольной командой:

```
php ./bin/console migrate:init
```

В БД появится таблица, имя которой задаётся через конфиг в поле ```migration_table_name```. Таблица хранит историю применения миграций.

Для создания новой миграции используется команда (с необязательным аргументом, задающим имя миграции):

```
php ./bin/console migrate:new [name]
```

После этого в директории, заданной через конфиг в поле ```migration_path``` появится класс с двумя методами ```up``` и ```down```, которые должны возвращать текст запросов, выполняемых для применения и отмены миграции, соответственно.

Список доступных миграций можно посмотреть, воспользовавшись коммандой:

```
php ./bin/console migrate:list
```

Для применения миграции используется команда, с необязательным аргументом name, принимающим количество применяемых миграций или имя миграции до которой сделать фиксацию (имя миграции так же включается в фиксацию):

```
php ./bin/console migrate:up [name]
```

Повышение происходит до максимально доступной версии миграции.

Для отмены используется команда, с необязательным аргументом, принимающим количество откатываемых миграций или имя миграции, до которой происходит откат (миграция, имя которой передано, так же откатывается):

```
php ./bin/console migrate:down [name]
```
