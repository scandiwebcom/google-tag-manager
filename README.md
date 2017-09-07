# Scandi_Gtm

This module is used to inject Google Tag Manager script and dataLayer collection

## Installation

Add Scandiweb_Core if it is not added already by running
```
composer config repositories.scandiweb-module-core git git@github.com:scandiwebcom/Scandiweb-Assets-Core.git
composer require scandiweb/module-core:~0.1.2
```

Add the exrension by running

```
composer config repositories.scandi-google-tag-manager git git@github.com:scandiwebcom/google-tag-manager.git
composer require scandiwebcom/google-tag-manager:1.0.7
php -f bin/magento setup:upgrade
```
## Configuration

Go to the Stores -> Configurations -> Scandiweb -> Google Tag Manager from the admin panel.

1. Ensure that module is enabled, switch to 'Yes', if required;

2. Add container ID from the Google Tag Manager account;

3. Ensure that script is storing in the head, switch to 'Yes' if required;

4. Ensure you have jQuery selector which will be able to get all the products items on the category page 
('ol.product-items > li.item.product-item' by default);

5. Configure your steps of the checkout, leave blank if one step checkout is used.

6. Clean cache and refresh the main page, inspect the page and search for 'Google Tag Manager'. 
It should be injected on the page;

7. Then open the browser console and type dataLayer (variable is case-sensitive). If object is returned - 
then you may open a bottle of wine and use it.