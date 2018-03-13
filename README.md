# Oddbrew_MailViewer
### A context-aware transactional mail pre-viewer for Magento.
Preview and debug your emails in the back_office with all the context loaded. Removes the need for a mailcatcher in local environment, and the constraint to pass orders to visualize final emails.

## Features
* Preview all main transactional mails directly from the back-office, rendered exactly as they are to the client : 
    * Orders
    * Invoices
    * Shipments
    * Creditmemos
    * ~~Comments/Updates~~ (considered for a future version)
* Support for guest orders
* Support for custom transactional mail templates
* Template/Block hints directly in preview window

## Requirements (feedback forMagento versions will be appreciated !)
* PHP >= 5.4
* Magento CE
    * 1.9 => OK
    * 1.8 => ?
    * 1.7 => ?
    * 1.6 => ?
* Magento EE
    * 1.14 => ?
    * 1.13 => ?
    * 1.12 => ?
    * 1.11 => ?

## Overview

* Order preview

![New Order Mail Preview](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/doc/images/overview_order.PNG "New Order Mail Preview")

* Invoice preview

![New Invoice Mail Preview](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/doc/images/overview_invoice.PNG "New Invoice Mail Preview")

* Shipment preview

![New Shipment Mail Preview](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/doc/images/overview_shipment.PNG "New Shipment Mail Preview")

* Creditmemo preview

![New Creditmemo Mail Preview](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/doc/images/overview_creditmemo.PNG "New Creditmemo Mail Preview")

* Base Magento preview functionality for custom templates VS MailViewer preview

![Base Magento Preview](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/doc/images/magento_order_preview.PNG " Magento Preview") | ![MailViewer Preview](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/doc/images/mailviewer_order_preview.png "MailViewer Preview")
:-----------------------------------------------------------------------------------------------------------------------------------------:|:--------------------------------------------------------------------------------------------------------------------------------------------:
**Magento Base Preview** | **MailViewer Preview**

* Debug mode

![Debug Mode](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/doc/images/mailviewer_debug_mode.PNG "Debug Mode For MailViewer")

## Suggestions, bug report, comments

[>>>This way !<<<](https://github.com/OddBrew/Oddbrew_MailViewer/issues)

## License

["New"BSD-3](https://github.com/OddBrew/Oddbrew_MailViewer/blob/master/LICENSE)

## Support

If this extension helped you, feel free to support me here : [Donate](http://paypal.me/AlexandreFayette)

