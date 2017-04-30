# Gyudon

Mastodon client for CLI.

## Setup

Require [composer](https://getcomposer.org).  
Clone or download repository, then run `composer install`.

```
$ git clone https://github.com/TomckySan/Gyudon.git
$ cd ./Gyudon
$ composer install
```

Add the path to environment variable.

## Authorization

Login to mastodon instance, and approve this app.

```
$ gyudon auth
```

Input mastodon instance url, email and password for login.

## Usage

### `timeline`

Retrieving recent posts from timeline:

```
$ gyudon timeline
```
You can specify the number of posts to get.

```
$ gyudon timeline 10
```

## TODO

* `toot` command

## License

MIT
