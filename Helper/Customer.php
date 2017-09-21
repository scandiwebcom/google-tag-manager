<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper;

/**
 * Class Customer
 * @package Scandi\Gtm\Helper
 */
class Customer
{

    /**
     * @param $session
     * @return string
     */
    public function getCustomerId($session)
    {
        $customer = $session->getCustomer();
        if ($customer->getId()) {
            return $customer->getId();
        } else {
            return 'NOT LOGGED IN';
        }
    }
}
