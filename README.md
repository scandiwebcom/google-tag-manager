# Scandi_Gtm

This module is used to inject Google Tag Manager script and dataLayer collection

## Installation

Add Scandiweb_Core if it is not added already by running
```
composer config repositories.scandiweb-module-core git git@github.com:scandiwebcom/Scandiweb-Assets-Core.git
composer require scandiweb/module-core:~0.1.2
```

Add the extension by running

```
composer config repositories.scandi-google-tag-manager git git@github.com:scandiwebcom/google-tag-manager.git
composer require scandiwebcom/google-tag-manager:1.0.22
php -f bin/magento setup:upgrade
```
## Configuration

Go to the Stores -> Configurations -> Scandiweb -> Google Tag Manager from the admin panel.

1. Ensure that module is enabled, switch to 'Yes', if required;

2. Add script and noscript snippets into appropriate fields;

3. Ensure that script is storing in the head, switch to 'Yes' if required;

4. Ensure you have jQuery selector which will be able to get all the products items on the category page 
('ol.product-items > li.item.product-item' by default);

5. Configure your steps of the checkout, leave blank if one step checkout is used;

6. Leave field "Pagesize Limit for impressions" blank for now;

7. Clean cache and refresh the main page, inspect the page and search for 'Google Tag Manager'. 
It should be injected on the page;

8. Then open the browser console and type dataLayer (variable is case-sensitive) you should see object of dataLayer;

9. Go to the category page and check if amount of impressions are pushed correctly. If there is a difference from the 
expected amount - go to the configurations and enter maximum amount of products user can see on one category page in the
"Pagesize Limit for impressions" -> Drop caches;

    9.1. If you have amount switchers user can see on the category or search pages, and amount is pushed incorrectly,
    please contact aleksejt@scandiweb.com in order to report the problem.
    
10. Configure color, size variables to be named, as required;

11. Enter the brand, to be pushed with ALL of the projects (dynamic change will be added later);

12. If you want checkout options to be pushed, please enter ids to be used in jQuery selectors to select all radios.
