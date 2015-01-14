# Requirements - Task Manager

This was the task assignment:

> You should create web application. This application should be simple task manager. Consisting of:
>
> * Create Task
> * Edit Task
> * Report Time
> * Close Task
> * List Tasks
>
> Your solution have to be JS based client. That communicates with PHP based backend. Over JSON based API (REST or REST
> like). This backend should write and read data from/to at least two persistent data storing layers based on
> configuration. Those layers have to have same access interface. One of those persistent layers have to be SQL database.
> Second persistent layer you are free to chose (not SQL database). Using OOP on LAMP architecture. No need for data
> validation only sanitize to avoid security risks.
>
> You shouldn’t spent more than 8 hours on this test. Send us code in git repository. Including Readme file with deploy
> instructions and any data files necessary for deployment.

# Result

The hard part was the eight hour limit - and I literally did not have more time anyway. I did not have enough hands-on
experience with starting project with any "big" framework like Symfony at the time, so I decided to use
[Slim](http://www.slimframework.com/) instead, and then I made more questionable choices along the way just to speed
things up - e.g. I wrote [my own DI
container](https://github.com/m6k/marionette-task-manager-example/blob/master/src/Container.php), while it would be
better to use existing one, e.g. [Pimple](http://pimple.sensiolabs.org/).

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
1. pushState so that urls does not use fragments
1. code sniffer / linter for both php and js, minify js