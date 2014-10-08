
## Setup on local computer

Dependencies:

- php 5.6
- [composer](https://getcomposer.org/)
- redis - running on 127.0.0.1 without any authorization
- mysql
- decent browser, I think first thing to break will be IE 8 and trailing commas in object literals,
  this can be fixed by using CoffeeScript (and it would get me plus point..)

<pre>
$ cd task-manager
$ composer install
$ php -S 127.0.0.1:8080 -t www
</pre>

## Todo

1. time tracking
1. storage tests
1. mysql storage
1. deploy to vps, publish git repo read only over http?

-- release ---

1. Maybe at least <em>some</em> styles?
1. List closed tasks.
1. Concurrency. Do not even think about opening this app in two browsers ;)
1. Describe basic architectural decisions.
1. hhvm. I would love to play with it for a while
1. react-js for js views instead of marionette views
1. pushState so that urls does not use fragments
1. coffeescript
1. code sniffer / linter for both php and js