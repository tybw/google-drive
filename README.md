# Manage changes in Google Drive

Sample code to manage changes in Google Drive. Currently it is only 
list and watching file changes are supported.

### Installation
```
composer install
```

### Run
```
php -e bin/console
```
#### Output
```
Symfony 4.1.13 (kernel: src, env: dev, debug: true)

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -e, --env=ENV         The Environment name. [default: "dev"]
      --no-debug        Switches off debug mode.
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands for the "app" namespace:
  app:google:drive:changes        List file changes in Google Drive
  app:google:drive:changes:watch  Watch file changes in Google Drive
  app:google:drive:list           List files in Google Drive
  app:google:oauth                Carry out Google OAuth operations
```
