# AutoIT Dealer Tools

**For versions 2.x.x PHP 7.1+ is required!**
<br><br>

Adds Vehicle search, Vehicle list and Vehicle Detail Page.

**Features**

* Integration to AutoDesktop
* Car search
* Show car-data
* Send leads to AutoDesktop


**Credits**

Developed by [AutoIT A/S](https://biltorvet.as)


## Other Notes
### License
To be able to shown your own cars from AutoDesktop you must contact AutoIT Support, 
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

Create a wordpress landingpages for the following:
* A page for the car search and result list
* A page for the car detail
* A page for the form to book testdrive 
* A page for the form to send purchase request

Head over to the settings page of the plugin:  

![Old settings](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-biltorvet-settings-old.png)

* Fill out the api-key that has been provided by AutoIT A/S.
* Choose you primary color (This will be used in multiple places)
* Configure the rest of the settings if needed.  

![Old settings](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-biltorvet-settings-old-view.png)

Head back to the plugin menu and update the plugin to the latest version (If no updates are shown try installing WP_Control and run the plugin update cron event).
When updated to the newest version, the plugin settings are now shown in the dashboard menu and not as a submenu to settings:
<br><br>
**NOTE: Versions 2.x.x requires PHP 7.1+**
<br><br>

![New settings](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-autoit-settings-new.png)

__Save the settings in all three tabs (if not the plugin might throw errors).__  

![New settings](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-autoit-settings-view.png)

## Shortcodes
You are now ready to place the shorcodes on the landingpages  

A shortcode is a piece of syntax that can be inserted anywhere in the WordPress text editor, and will then be processed into some kind of functionality. You can imagine shortcodes as building blocks you can move around. Shortcodes have optionally attributes, that change their output.  
A shortcode is delimited by square brackets, for example __[bdt_cta type="TestDrive"]__  
<br><br>
__Examples of building the searchpage / resultlist and vehicledetailspage are shown at the end of this section__<br><br>

### Global shortcodes  
These shortcodes should work anywhere in WordPress.  
<br><br>
__bdt_vehicletotalcount__  
shows a total count of vehicles for the current dealer.  

__bdt_vehicle_search__  
Print out the vehicle search filter.  

__bdt_vehicle_search_results__  
Print out the search results - list of cars.  

__bdt_vehicle_search_frontpage__  
Specialized search for use on the frontpage (or any other landingpage that is not the main search and result page)  

__bdt_vehicle_card__  
Print out a vehicle "card" - a box with short informations about the vehicle. This is equal to one search result item from the vehicle search results.  
Attributes:
* vehicle - specifies the vehicle ID. If omitted, it will look for bdt_vehicle_id URL parameter.  


__bdt_featuredvehicles__ 
Shows a list of vehicles that had been marked as "In focus" in AutoDesktop. This allows sold cars to show until removed from the index.  

__bdt_get_vehicles__  
Creates a list of cars from a specific make  
Required attribute:  
* __make__  

Optional attributes:  
* __model__  
* __propellant__  


Example 1: __[bdt_get_vehicles make="Audi"]__ - lists all cars with the make "Audi".  

Example 2: __[bdt_get_vehicles make="Audi" model="A5"]__ - lists all cars with the make "Audi" and model "A5".  

Example 3: __[bdt_get_vehicles make="Audi" model="A5" propellant="diesel"]__ - lists all cars with the make "Audi", model "A5" and uses diesel as propellant.

__bdt_get_vehicles_by_status_code__  
Creates a list of cars from status codes set in AutoDesktop (In order to use this shortcodes the dealer has to list cars using AutoDesktop. BilInfo cars are currently not supported as it's specific fields set in AutoDesktop).  
<br>Required attribute:  
* __status__

<br>The following status codes are currently supported:  
* __Sold__ - Shows all cars with the label "Solgt".  
* __New__ - Shows all cars with the label "Nyhed".  
* __Leasing__ - Shows all cars with the label "Leasing".  
* __Warehousesale__ - Shows all cars with the label "Lagersalg".  
* __Flexleasing__ - Shows all cars with the label "Flexleasing".  
* __Export__ - Shows all cars with the label "Eksport".  
* __Upcoming__ - Shows all cars with the label "På vej ind".  
* __Rental__ - Shows all cars with the label "Udlejning".  
* __Commision__ - Shows all cars with the label "Kommision".  
* __Wholesale__ - Shows all cars with the label "Kun engros".  

Example 1: __[bdt_get_vehicles_by_status_code status="Sold"]__ - lists all cars marked as "Solgt".  

__bdt_get_vehicles_by_type__  
Creates a list of cars from their type.  
<br>Required attribute:  
* __type__

<br>The following types are currently supported:  
* __Car__ - Shows all vehicles of the type "Car".
* __Van__ - Shows all vehicles of the type "Van".
* __Motorcycle__ - Shows all vehicles of the type "Motorcycle".
* __Truck__ - Shows all vehicles of the type "Truck".  


Example 1: __[bdt_get_vehicles_by_type type="Car"]__ - lists all vehicles with the type "Personbil".<br><br>

### Vehicle detail shortcodes  
These will work only on a vehicle detail template page.  

__bdt_cta__  
This will generate a CTA button, with a link to the contact page or booking page with parameters that facilitate the leads functionality. It can be opened and content inserted will become wrapped in a link element.  

Attributes:
* __color__ - hexadeximal color with a hash, or a color name that colors the CTA text and icon. If omitted, the CTA takes on the primary color.  

Attributes:  
* __type__ - one of the five following types:
  * TestDrive - book test drive
  * PhoneCall - call me back
  * Purchase - buy the car
  * Email - send email
  * Contact - general contact  
  
Example 1: __[bdt_cta type="TestDrive"]__  
Example 2: __[bdt_cta type="TestDrive"]__ This content will be shown instead of the icon and predefind text.[/bdt_cta]  


__bdt_prop__  
Fetch a property of a car - currently needs to be the danish caption. These are directly matched from the database, so this list may be
incomplete/obsolete.  

Attributes:  

* __p__ - see the list below.  
* __nona="-"__ - text to show when no match was found. If omitted, returns "Ikke angivet".  
* __raw="true"__ - returns the unformated value. Useful in cases where the value needs to be further processed or used.  

Example: __[bdt_prop p="0-100"]__ 

Properties: 
* Company,  
* BodyType,
* Mileage,
* ModelYear,
* Price,
* XVat - Without VAT(MOMS) Ja/Nej,
* Acceleration,
* FirstRegistrationDate,
* AirbagCount,
* CylinderCount,
* DoorCount,
* GearCount,
* GearType,
* SeatCount,
* AnnualOwnerTax,
* PropellantType,
* Width,
* Color,
* Height,
* Kmx1l - kilometers per liter,
* Whx1km,
* Length,
* DeliveryCost,
* MaxTorque,
* MaxHorsepower,
* EngineSize,
* RegistrationNumber,
* VIN,
* LastChangedDate,
* TopSpeed,
* TotalWeight,
* TankCapacity,
* AllowedTrailerWeightWithBrakes,
* AllowedTrailerWeightWithoutBrakes,
* DealersReferenceNumber,
* EquipmentItem - A general type for all equipment. Equipment is not mapped, i.e. you cannot fetch a particular equipment item.
* LeasingDeal,
* LeasingBusiness,
* LeasingFirstPayment,
* LeasingFirstPaymentVAT,
* LeasingRunTime,
* LeasingMonthlyPayment,
* LeasingMonthlyPaymentVAT,
* LeasingRemainingValue,
* FinancingAnnualLoanFeesInPercents,
* FinancingAnnualDebitorInterest,
* FinancingFixedInterest,
* FinancingMonthlyPrice,
* FinancingLoanTransfer,
* FinancingRunTime,
* FinancingInterestRate,
* FinancingTotalCreditFees,
* FinancingTotalFeesToPay,
* FinancingTotalSetupFees,
* FinancingDownpayment,
* FinancingDownpaymentInPercent  

<br>__bdt_specifications__  
  
Print out a table of the specifications.  

__[bdt_prop p="description"]__  
Print out the car description.  

__bdt_equipment__  
Print out a table of equipment  

__bdt_recommendedvehicles__ 
Print out three recommended unsold vehicles, related to the current vehicle.  

__bdt_slideshow__  
Print out a slideshow of current vehicle's images.  

__bdt_vehicle_labels__  
Print out a list of labels asociated with the given car. Labels come in predefined colors, that can be overwritten with CSS in your child theme:
* .bdt .badge-primary - used as default label color,
* .bdt .badge-danger - used for sold label,
* .bdt .badge-success - used for new label,
* .bdt .badge-warning - used for reserved label,
* .bdt .badge-info - used for leasing label  
* .bdt .badge-orange - used for "I fokus" label  
* .bdt .badge-secondary - used for "Lagersalg" label  
* .bdt .badge-dark - used for "Uden afgift" label  
* .bdt .badge-purple - used for "Udlejning" label  

Attributes:  
* __allowed="new,sold"__ - comma separated list of labels that are allowed to be shown. Use this if there are too many labels available, and you don't necessarily want to show them all. If ommited, all labels will be returned.  

__bdt_vehicle_search_backtoresults__  
Prints out a "smart" back button, that returns you to the vehicle search results if available, or car search if not.  

__bdt_vehicle_price__  
Shows intelligently the "most attractive" price of a vehicle - i.e. financing or leasing if available, or the cash price as fallback.  

__bdt_sharethis__  
Shows icons for Facebook-sharing, sharing by mail and printing.<br><br>

### Setup your forms to send leads to AutoDesktop  
To be able to send leads to AutoDesktop you need to create forms on the pages that you have specified on the settingspage of the AutoIt plugin.  

![forms](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-adt-leads-1.png)  

To be able to send leads when the forms get submitted, you need to set the ID’ to the specific inputs fields.
Se example below to see an example with the Contact Form 7 plugin.  

![contactform7](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-adt-leads-2-contactform7.png)  

To show a car-card next to the form, as showed below, use this shortcode: [bdt_vehicle_card]  

![contactform](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-adt-leads-3.png)  

__NB The vehicle-card will only be visible, when a client comes directly from a vehicle detailspage.__<br><br>

### Widgets  
__[bdt_widget]__
Required attribute:  
* __type__  

Supported widget-types:  
* Consent - Samtykke widget
* AutoDesktopLeads - Currently only Book Prøvetur is supported
* Santander - Santander widget. WORKS ONLY IN VEHICLE DETAIL PAGE.  


The widgets should work without any additional parameters, as the usual clientID parameter is resolved automatically by association to the companyId associated with the current API key. Widgets can be used anywhere in your WordPRess installation, except for Santander widget that can only be used on a vehicle detail page, because it requires some inputs about a vehicle to be displayed.  

The [bdt_widget] shortcode can be opened in order to insert some content and put a link on it. In such case, the widget won'd be embedded; but when the link is clicked, a new window opens with a widget inside.
In case that your desired widget type doesn't show up, make sure that it had been activated for your company - contact Biltorvet A/S support to get a widget activated for your API key. Additionaly, you may want to reuse a specific widget ID instead of relying on automatic resolve. For that, use externalid attribute, for example [bdt_widget externalid="XXXXXX-XXXX-XXXX-XXXXXX"].  

__Widget attributes__  
All widgets have following common attributes, that you can use to tweak their appearance for your particullar instance:  

* color - sets the primary theme color for the given widget.
* logourl - sets the URL for the logo to be displayed inside.
* fontcolor - sets the primary theme color for the font inside.
* fontfamily - sets the google font to be loaded for the whole widget. (not implemented yet)  

Example 1: [bdt_widget type="Santander" color="#ff0000"]<br><br>

![widget](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-widget.png)<br><br>



__Widget type specific attributes__  
Some widgets allow you to specific attributes.  

__Santander:__  
* hideVehiclePrice="true" - hides the vehicle price in the widget, which is useful in the vehicle detail page, where there's probably price in some other place already.  
* brandingid - some dealers have a specific branding.  

__AutoDesktopLeads:__
* title - Main title of the widget, before action is selected. If action is preselected with actiontype parameter (see below), this step will be skipped.
* color - Main color of the widget.
* fontcolor - Main font color of the widget.
* logourl - logo to be used.,
* vehicletype - preselect the vehicle type
* vehicletypehide - true or false
* selectedvehicletype - Personbil or Varebil
* actiontype - TestDrive, OfferNewCar or Contact
* make - set the preselected make
* makehide - true or false
* selectedmake - make to be preselected.
* allowedmakes - a JSON object with a list of makes that are allowed. It filters out all other makes, if this attribute is specified. Example: ["Ford","VW","Aston Martin"]
* model - set the preselected model
* modelhide - true or false
* variant - set the preselected variant
* varianthide - true or false
* openingtimes - an array of days with opening times ranges. Nees to be put in with single quotes - it's a JSON object. Example value: {"0
":[null,null],"1":[{"b":900,"e":1200},{"b":1230,"e":1700}],"2":[{"b":900,"e":1200},{"b":1230,"e":1700}],"3":[{"b":900,"e":1200},{"b":1230,"e":17
00}],"4":[{"b":900,"e":1200},{"b":1230,"e":1700}],"5":[{"b":900,"e":1200},{"b":1230,"e":1700}],"6":[{"b":1000,"e":1700},null]}
* filterpersonalmodels - a JSON object of personal models to be filtered out. If omitted, all personal models will appear. Example: ["500C"
,"Cherokee"]
* filterbusinessmodels - a JSON object of business models to be filtered out. If omitted, all business models will appear. Example: ["500L
"]  


__Consent:__
* consentcategory - samtykke category,
* requiredconsenttype - required samtykke channel: BySMS, ByEmail or Both,
* name - pre-filled name of the person giving samtykke,
* address - pre-filled address of the person giving samtykke,
* postalcode - pre-filled postal code of the person giving samtykke,
* city - pre-filled city of the person giving samtykke,
* email - pre-filled email of the person giving samtykke,
* mobilephone - pre-filled mobile phone of the person giving samtykke<br><br>


### Building the searchpage / resultlist page and vehicle detailspage  
How the search, result and detailspage can be build.

<br>__Car search / resultlist__  
![search page](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-search-and-result-page-old-1.png)  

<br>__Vehicle detailspage__  
![detailspage](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-details-old-1.png)  

![detailspage](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-details-old-2.png)  

![detailspage](https://www.autoit.dk/Media/autoit-dealer-tools/bdt-details-old-3.png)<br><br>


### Searchpage and resultlist  
When using the plugin it's important to notice that the searchpage and resultlist won't work optiomal unless the landingpage used for the shortcodes is placed in the root of the domain.  
<br>
Example 1: __demo1.biltorvet.as/brugte-biler__ - Everything will work justfine.  
Example 2: __demo1.biltorvet.as/out-new-homepage/brugte-biler__ - Filtering options, pagination and such won't work as it should.  
<br>
Be sure to remember this when creating a website using this plugin.  
<br>
__Share links with predefined searchfilters__  
The URL are build to make it easy to share pages with predefined searchfilters. The url build like this:  
__/1/make/model__   
<br>

The first paramater is the pagination number, the second parameter is the make of the vehicle and the third parameter is the model of the vehicle.  
<br>
Example 1: __/1/audi/__ - Will show the searchpage with all the Audi's for sale  
Example 2: __/1/audi/a5__ - Will show the searchpage with all the Audi A5's for sale
