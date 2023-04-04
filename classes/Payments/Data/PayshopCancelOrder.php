<?php

/**
 * 2007-2023 Ifthenpay Lda
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @copyright 2007-2023 Ifthenpay Lda
 * @author    Ifthenpay Lda <ifthenpay@ifthenpay.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PrestaShop\Module\Ifthenpay\Payments\Data;

use PrestaShop\Module\Ifthenpay\Factory\Models\IfthenpayModelFactory;
use PrestaShop\Module\Ifthenpay\Factory\Prestashop\PrestashopModelFactory;


if (!defined('_PS_VERSION_')) {
    exit;
}

class PayshopCancelOrder
{
    /**
     * cancels payshop order if no payment has been received 30 minutes after order confirmation "date_add"
     *
     * @return void
     */
    public function cancelOrder()
    {

        if (
            \Configuration::get('IFTHENPAY_PAYSHOP_CANCEL_ORDER_AFTER_TIMEOUT')
            && (\Configuration::get('IFTHENPAY_PAYSHOP_VALIDADE') && \Configuration::get('IFTHENPAY_PAYSHOP_VALIDADE') != '' && \Configuration::get('IFTHENPAY_PAYSHOP_VALIDADE') != null)
        ) {
            $payshopOrders = IfthenpayModelFactory::build('payshop')->getAllPendingOrdersWithDeadline();

            $timezone = \Configuration::get('PS_TIMEZONE');
            if (!$timezone) {
                $timezone = 'Europe/Lisbon';
            }
            date_default_timezone_set($timezone);

            foreach ($payshopOrders as $payshopOrder) {

                if (isset($payshopOrder['validade']) && $payshopOrder['validade'] != '' && $payshopOrder['validade'] != null) {

                    $deadlineDate = (\DateTime::createFromFormat('Ymd', $payshopOrder['validade']))->format('Y-m-d');
                    $today = (new \DateTime(date("Y-m-d")))->format('Y-m-d');

                    if ($deadlineDate < $today) {
                        $new_history = PrestashopModelFactory::buildOrderHistory();
                        $new_history->id_order = (int) $payshopOrder['id_order'];
                        $new_history->changeIdOrderState((int) \Configuration::get('PS_OS_CANCELED'), (int) $payshopOrder['id_order']);
                        $new_history->addWithemail(true);
                    }
                }
            }
        }
    }
}
