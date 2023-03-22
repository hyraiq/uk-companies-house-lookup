hyraiq/uk-companies-house-lookup
================

## Contributing

All contributions are welcome! You'll need [docker](https://docs.docker.com/engine/install/) installed in order to
run tests and CI processes locally. These will also be run against your pull request with any failures added as
GitHub annotations in the Files view.

```shell
# First build the required docker container
$ docker compose build

# Then you can install composer dependencies
$ docker compose run php ./composer.phar install

# Now you can run tests and other tools
$ docker compose run php make (fix|psalm|phpstan|phpunit)
```

In order for you PR to be accepted, it will need to be covered by tests and be accepted by:

- [php-cs-fixer](https://github.com/FriendsOfPhp/PHP-CS-Fixer)
- [psalm](https://github.com/vimeo/psalm/)
- [phpstan](https://github.com/phpstan/phpstan)
