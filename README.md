
# Task Manager Test Assignment

## Setup on local computer

This setup script is for Mac, adjust accordingly to your OS.
Dependencies:

- php 5.6
- composer - https://getcomposer.org/
- redis - running on 127.0.0.1 without any authorization
- mysql

```
$ cd task-manager
$ composer install
$ php -S 127.0.0.1:8080 -t www
$
```


## Todo

1. show readme
1. time tracking
1. storage tests
1. mysql storage

-- release ---

1. Maybe at least <em>some</em> styles?
1. List closed tasks.
1. Concurrency. Do not even think about opening this app in two browsers ;)
1. Describe basic architectural decisions.
1. hhvm. I would love to play with it for a while
1. react-js for js views instead of marionette views
1. pushState so that urls does not use fragments
1. code sniffer / linter for both php and js


Coffeescript is not on the list, I don't mind it, but I do not see that much benefit to use it over plain JavaScript.