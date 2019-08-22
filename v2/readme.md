# Biltorvet dealer tools version 2

*This is an attempt on rewriting the original Biltorvet dealer tools plugin codebase, 
for easier maintenance and further development of features.
The idea of the new codebase is to utilize modern OOP principles managed by the Composer package manager.*

**Enviroment requirements**
- PHP 7.2 (7.3 Recommended)
- Apache2 (nginx should work)
- Xdebug 2.7.1
- Composer
- CLI (cmdr for windows is advisable)

**Running**

Create a .env file with the correct api endpoint. (see .example.env)

Activate the plugin in Wordpress and everything should work.
Remember to run `$ composer update`, to update dependencies.

**Testing**

*Most of the code is tested and further development SHOULD have a min of 80% code coverage*

- Run all tests with a pretty output in console

`$ phpunit --testdox`

- Create code coverage report (path argument defined MUST exist)

`$ phpunit --coverage-html tests/reports`

**Coding standards**

PSR-2 guidelines MUST be respected when editing and or adding new code. 

Automatic testing and fixing of these guidelines can be run with the following commands:

Dry run. Gives a list of syntax issues.

`$ composer run phpcs`

Tries to fix syntax issues automatically. (Do not fix everything, and code must be tested after using this command.)

`$ composer run phpcs-fix` 

