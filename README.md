# AutoIT Dealer Tools

Adds Vehicle search, Vehicle list and Vehicle Detail Page.

**Features**

* Integration to AutoDesktop
* Search in the dealers cars
* Show car-data
* Send leads to AutoDesktop


**Credits**

Developed by [AutoIT A/S](https://biltorvet.as)


## Other Notes
### License
Good news, this plugin is free for everyone! But to be able to shown your own cars from AutoDesktop you must contact AutoIT Support, so we can create your personal API Key.


### Contact & Feedback
Please let us know if you like the plugin or you hate it or whatever... Please fork it, add an issue for ideas and bugs.

## 3rd party connections
This plugin is internally using our proprietary JSON/REST API http://api-v1.biltorvet.as - this API connects to AutoIT A/S services, and proxies other 3rd party services as necessary to reduce the complexity of implementation. This server is located in Denmark and is thereby liable to the Danish law. This API operates with personal data. You can find our privacy policy at https://www.AutoIT.as/media/1389/privatlivspolitik-AutoIT-as.pdf

## Changelog
See readme.txt (which is used for the WordPress plugin repository), to see the latest changes.

# AutoIT dealer tools version 2

*This is an attempt on rewriting the original AutoIT dealer tools plugin codebase, 
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

