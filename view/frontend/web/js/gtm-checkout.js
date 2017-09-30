/**
 * @category Scandi
 * @package Scandi\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

require(['jquery'],

    function ($) {

        /**
         * Runs the logic, if dataLayer object is defined
         */
        $(document).ready(function () {
            if (isDataLayer()) {
                collectCheckoutPush();
                bindInputs();
                $(window).bind('hashchange', function (e) {
                    collectCheckoutPush();
                    bindInputs();
                });
                $(checkoutWrappers[0]).bind("DOMSubtreeModified",function(){
                    alert('changed');
                });
            }
        });

        /**
         * etect checkout step, based on the array
         * @returns {*}
         */
        function detectCheckoutStep() {
            var steps = getSteps(), step;
            if (!steps) {
                return false;
            }
            var anchor = document.location.hash;
            if (anchor === '' || (steps.length === 1 && steps[0] === '')) {
                step = 1;
            }
            else if (steps.includes(anchor)) {
                step = steps.indexOf(anchor) + 1;
            }
            return step.toString();
        }

        /**
         * Check if dataLayer was initialised
         * @returns {boolean}
         */
        function isDataLayer() {
            return typeof(dataLayer) !== 'undefined';
        }

        /**
         * Get global with steps from the backend.
         * Generated in Scandi\Gtm\Helper\Collectors\Checkout->getCheckoutSteps;
         * @returns {*}
         */
        function getSteps() {
            if (typeof(checkoutLayerSteps) !== 'undefined') {
                return checkoutLayerSteps;
            }
            else {
                return false;
            }
        }

        /**
         * Collect push
         * @param option
         */
        function collectCheckoutPush(option) {
            if (typeof (option) !== 'undefined') {
                dataLayer.push({
                    'event': 'checkout',
                    'ecommerce': {
                        'checkout': {
                            'actionField': {
                                'step': detectCheckoutStep(),
                                'option': option
                            },
                            'products': getCart()
                        }
                    }
                });
            } else {
                dataLayer.push({
                    'event': 'checkout',
                    'ecommerce': {
                        'checkout': {
                            'actionField': {
                                'step': detectCheckoutStep(),
                            },
                            'products': getCart()
                        }
                    }
                });
            }
        }

        /**
         * Find cart data pushed on page render
         * @returns {*}
         */
        function getCart() {
            // Generated in Scandi\Gtm\Helper\Collectors\Checkout->getCart();
            if (typeof(cartData) !== 'undefined') {
                return cartData;
            }
            return '';
        }

        /**
         * Bind radio buttons with push
         * @returns {null}
         */
        function bindInputs() {
            var inputs = getInputsByStep();
            if (inputs.length === 0) {
                setTimeout(function(){
                    bindInputs()
                }, 1000);
                return null;
            }
            setTimeout(function(){
                jQuery(inputs).change(function(){collectCheckoutPush($(this).attr('value'))})
            }, 1000);
        }

        /**
         * Detect visible input, by checking which step is displayed
         * @returns {*}
         */
        function getInputsByStep() {
            var wrappers = getCheckoutWrappers(), index = detectCheckoutStep() - 1;
            if (wrappers.length <= 0) {
                return null;
            }
            if (wrappers[index] === 'undefined') {
                return null;
            }
            var selector = wrappers[index] + ' input[type=radio]';
            return $(selector);
        }

        /**
         * Returns global object of checkoutWrapper if declared
         * Generated in Scandi\Gtm\Helper\Collectors\Checkout->getCheckoutWrappers();
         * @returns {*}
         */
        function getCheckoutWrappers() {
            if (typeof checkoutWrappers !== 'undefined') {
                return checkoutWrappers;
            }
            return '';
        }
    });
