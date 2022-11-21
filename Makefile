.PHONY: tests
tests: php vendor/phpunit/phpunit/phpunit -c phpunit.xml

# ./vendor/bin/phpunit -c phpunit.xml