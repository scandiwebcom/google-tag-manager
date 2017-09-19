/**
 * @category Scandi
 * @package Scandi\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/*jshint browser:true jquery:true*/
/*global confirm:true*/
require([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'jquery/ui',
    'mage/decorate',
    'mage/collapsible',
    'mage/cookies',
    'Magento_Checkout/js/sidebar'

], function ($, authenticationPopup, customerData, alert, confirm) {

    $.widget('scandi.sidebar', $.mage.sidebar, {

        /**
         * Update content after item remove
         *
         * @param elem
         * @param response
         * @private
         */
        _removeItemAfter: function (elem, response) {
            if (typeof(dataLayer) !== "undefined" && response['eventPush']) {
                dataLayer.push(response['eventPush']);
            }
        },

        /**
         *
         * @private
         */
        _calcHeight: function () {
            var self = this,
                height = 0,
                counter = this.options.minicart.maxItemsVisible,
                target = $(this.options.minicart.list),
                targetWrapper = target.parents('.ui-widget'),
                headerHeight = $('[data-block="minicart"]').height(),
                windowHeight = $(window).height(),
                margin = 20,
                outerHeight;
            target.parent().removeClass('scroll');
            self.scrollHeight = 0;
            target.children().each(function () {
                if ($(this).find('.options').length > 0) {
                    $(this).collapsible();
                }
                outerHeight = $(this).outerHeight();
                if (counter-- > 0) {
                    height += outerHeight;
                }
                self.scrollHeight += outerHeight;
            });
            target.parent().height(height);
            if (!$.mage.cookies.get('user_allowed_save_cookie')) {
                headerHeight += $('.message.cookie').height();
            }
            // Adjust height if minicart contents are too big
            if (windowHeight - headerHeight - (target.height() + (targetWrapper.height() - height)) < 0) {
                height = windowHeight - headerHeight - (targetWrapper.height() - height) - margin;
                target.parent().height(height).addClass('scroll');
            } else {
                target.parent().height('auto').removeClass('scroll');
            }
        }

    });

    return $.scandi.sidebar;
});
