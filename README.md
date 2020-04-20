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
Good news, this plugin is free for everyone! But to be able to shown your own cars from AutoDesktop you must contact AutoIT Support, 
so we can create your personal API Key.


### Contact & Feedback
Please let us know if you like the plugin or you hate it or whatever... Please fork it, add an issue for ideas and bugs.

## 3rd party connections
This plugin is internally using our proprietary JSON/REST API http://api-v1.biltorvet.as - 
this API connects to AutoIT A/S services, and proxies other 3rd party services as necessary to reduce the complexity of implementation. 
This server is located in Denmark and is thereby liable to the Danish law. This API operates with personal data. 
You can find our privacy policy at https://www.biltorvet.as/media/1389/privatlivspolitik-biltorvet-as.pdf

## Changelog
See the release tab in this repository to see the latest changes.

# Installing and using the plugin
Last version of the plugin using the Wordpress SVN was 1.0.22. The following versions are updated using Github. It's very important to follow the installation guide closely in order to make the plugin work.

Find the plugin in the wordpress repository - Search for Biltorvet Dealer Tools last (v. 1.0.22)
Install and activate the plugin

Create wordpress landingpages for the following:
* A page for the car search and result list
* A page for the car detail
* A page for the form to book testdrive 
* A page for the form to send purchase request

Head over to the settings page of the plugin:

* Fill out the api-key that has been provided by AutoIT A/S.
* Choose you primary color (This will be used on multiple places)
* Configure the rest of the settings if needed.

Head back to the plugin menu and update the plugin to the latest version (If no updates are shown try installing WP_Control and run the plugin update cron event).
When installed the plugin settings are now shown in the dashboard menu and not as a submenu to settings:

Save settings in all three tabs (if not the plugin might throw errors).

## Shortcodes
You are now ready to place the shorcodes on the landingpages







