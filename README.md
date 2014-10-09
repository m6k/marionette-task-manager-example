
See it in action http://task-manager.m6k.cz

## Setup on local computer

Dependencies:

- php 5.4 or newer, probably would run on 5.3 as well
- [composer](https://getcomposer.org/)
- create conf/local.json file with {"environment": "devel"}
  - this file defines what environment config should be used
  - it would contain any passwords which must not be versioned in git
- redis - running on 127.0.0.1 without any authorization
- mysql - create database taskmanager and init it with sql/structure.sql
  - fill username/password to conf/local.ini (example in conf/config.json)
- to switch between storage imlementation use "taskStorageService":"redisTaskStorage" or "mysqlTaskStorage"
  in conf/local.json
- use decent browser, I believe first thing to break will be IE 8 and trailing commas in object literals,
  this can be fixed by using CoffeeScript (and it would get me plus point..)


- get composer dependencies (all commands below must run from project root):

	`composer install --dev`

- to run local server

	`php -S 127.0.0.1:8080 -t www`

- to run tests (it needs both mysql and redis, and it clears all data before run!)

	`vendor/bin/phpunit`


## What would be my next steps if I had more time?

1. Maybe at least <em>some</em> styles?
1. Describe basic architectural decisions.
1. hhvm. I would love to play with it for a while
1. pushState so that urls does not use fragments
1. coffeescript
1. code sniffer / linter for both php and js, minify js