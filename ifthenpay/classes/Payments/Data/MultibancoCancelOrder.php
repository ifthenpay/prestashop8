<?php

/**
 * 2007-2024 Ifthenpay Lda
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
 * @copyright 2007-2024 Ifthenpay Lda
 * @author    Ifthenpay Lda <ifthenpay@ifthenpay.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PrestaShop\Module\Ifthenpay\Payments\Data;

use PrestaShop\Module\Ifthenpay\Factory\Models\IfthenpayModelFactory;
use PrestaShop\Module\Ifthenpay\Factory\Prestashop\PrestashopModelFactory;


if (!defined('_PS_VERSION_')) {
    exit;
}

class MultibancoCancelOrder
{
    /**
     * cancels multibanco order if no payment has been received within the payment deadline
     *
     * @return void
     */
    public function cancelOrder()
    {
		if (
			\Configuration::get('IFTHENPAY_MULTIBANCO_CANCEL_ORDER_AFTER_TIMEOUT') &&
			(
				(
					\Configuration::get('IFTHENPAY_MULTIBANCO_VALIDADE') &&
					\Configuration::get('IFTHENPAY_MULTIBANCO_VALIDADE') != '' &&
					\Configuration::get('IFTHENPAY_MULTIBANCO_VALIDADE') != null &&
					\Configuration::get('IFTHENPAY_MULTIBANCO_VALIDADE') != '999999'
				) ||
				\Configuration::get('IFTHENPAY_MULTIBANCO_VALIDADE') == '0'
			)
		) {
			$multibancoOrders = IfthenpayModelFactory::build('multibanco')->getAllPendingOrdersWithDeadline();

			$timezone = \Configuration::get('PS_TIMEZONE');
			if (!$timezone) {
				$timezone = 'Europe/Lisbon';
			}
			date_default_timezone_set($timezone);

			foreach ($multibancoOrders as $multibancoOrder) {

				if (isset($multibancoOrder['validade']) && $multibancoOrder['validade'] != '' && $multibancoOrder['validade'] != null) {

					$deadlineDate = \DateTime::createFromFormat('d-m-Y', $multibancoOrder['validade']);
					$deadlineDate->setTime(23, 59); //set time to 23h:59m
					$deadlineStr = strtotime($deadlineDate->format('Y-m-d H:i:s'));

					$currentDateStr = strtotime(date('Y-m-d H:i:s'));


					if ($deadlineStr < $currentDateStr) {
						$new_history = PrestashopModelFactory::buildOrderHistory();
						$new_history->id_order = (int) $multibancoOrder['id_order'];
						$new_history->changeIdOrderState((int) \Configuration::get('PS_OS_CANCELED'), (int) $multibancoOrder['id_order']);
						$new_history->addWithemail(true);
					}
				}
			}
		}
    }
}
