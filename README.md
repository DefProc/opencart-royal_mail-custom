# OpenCart royal_mail custom shipping module

Replaces the existing royal_mail module for [OpenCart](http://www.opencart.com). 

Includes the options from [Royal Mail (with Handling)](http://www.opencart.com/index.php?route=extension/extension/info&extension_id=1654) module:

* Option to add a fixed handling charge to all services.
* Tariffs updated from 5th April 2011.
* Special Delivery Service.

Added:

* Exclude international delivery options for domestic delivery.
	* includes Channel Islands in domestic delivery rates
* Exclude domestic delivery options for international delivery.
* Updated service costs to include up to 2kg for Airmail.
* Services are excluded if the goods cost exceeds the insurance rate.
* Updated compensation costs for Airsure & International Signed.
* Added shipping times for each service.
* Consolidated Special Delivery service into one option - service choice is calculated from cart value.
* Fixed the Airsure/Surface Mail selection bug in OpenCart 1.4.9.4 (already fixed in 1.4.9.5).

Additional Changes (in the add-logic branch):

* Ignore Surface Mail quote if more expensive than airmail.
* Ignore Standard Parcels quote if more expensive than first class.
* Ignore International Signed quote if an Airsure quote exists.

## Installation

Copy the `admin` and `catalog` directories into an OpenCart installation to replace the files.

Go to OpenCart administration > Extensions > Shipping > "Royal Mail (custom)", click [Install].

Then edit the settings.

