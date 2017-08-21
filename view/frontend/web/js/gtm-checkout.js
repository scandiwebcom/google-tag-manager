/**
 * @category Scandi
 * @package Scandi\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

require(['jquery'],

    function ($) {

        $(document).ready(function () {
            if (isDataLayer()) {
                collectCheckoutPush();
                $(window).bind('hashchange', function (e) {
                    collectCheckoutPush();
                });
            }
        });

        // Detect checkout step, based on the array
        function detectCheckoutStep() {
            steps = getSteps();
            if (!steps) {
                return false;
            }
            var anchor = document.location.hash;
            if (anchor === '' || (steps.length === 1 && steps[0] === '')) {
                var step = 1;
            }
            else if (steps.includes(anchor)) {
                var step = steps.indexOf(anchor) + 1;
            }
            return step;
        }

        // Check if dataLayer was initialised
        function isDataLayer() {
            return typeof dataLayer;
        }

        // Get global with steps from the backend.
        // Generated in Scandi\Gtm\Helper\Collectors\Checkout->getCheckoutSteps;
        function getSteps() {
            if (typeof checkoutLayerSteps !== 'undefined') {
                return checkoutLayerSteps;
            }
            else {
                return false;
            }
        }

        // Collect push
        function collectCheckoutPush() {
            dataLayer.push({
                'event': 'checkout',
                'ecommerce': {
                    'checkout': {
                        'actionField': {
                            'step': detectCheckoutStep()
                        },
                        'products': getCart()
                    }
                }
            })
        }

        // Find cart data pushed on page render
        function getCart() {
            for (var i = 0, len = dataLayer.length - 1; i < len; i++) {
                if (dataLayer[i]["ecommerce"]["cart"]) {
                    var cart = dataLayer[i]["ecommerce"]["cart"];
                    // clean cart from ecommerce, as it would be pushed into the checkout data
                    dataLayer[i]["ecommerce"]["cart"] = [];
                    return cart;
                }
            }
            return false;
        }
    });
