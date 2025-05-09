<?php

/**
 * 2007-2022 Ifthenpay Lda
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
 * @copyright 2007-2022 Ifthenpay Lda
 * @author    Ifthenpay Lda <ifthenpay@ifthenpay.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PrestaShop\Module\Ifthenpay\Payments\Data;

use PrestaShop\Module\Ifthenpay\Factory\Models\IfthenpayModelFactory;
use PrestaShop\Module\Ifthenpay\Factory\Prestashop\PrestashopModelFactory;

if (!defined('_PS_VERSION_')) {
	exit;
}

class PixCancelOrder
{
	/**
	 * cancels Pix order if no payment has been received 30 minutes after order confirmation "date_add"
	 *
	 * @return void
	 */
	public function cancelOrder()
	{
		if (\Configuration::get('IFTHENPAY_PIX_CANCEL_ORDER_AFTER_TIMEOUT')) {
			$pixOrders = IfthenpayModelFactory::build('pix')->getAllPendingOrders();

			$timezone = \Configuration::get('PS_TIMEZONE');
			if (!$timezone) {
				$timezone = 'Europe/Lisbon';
			}
			date_default_timezone_set($timezone);

			foreach ($pixOrders as $pixOrder) {
				$minutes_to_add = 30;
				$time = new \DateTime($pixOrder['date_add']);
				$time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
				$deadlineStr = strtotime($time->format('Y-m-d H:i:s'));

				$currentDateStr = strtotime(date('Y-m-d H:i:s'));


				if ($deadlineStr < $currentDateStr) {
					$new_history = PrestashopModelFactory::buildOrderHistory();
					$new_history->id_order = (int) $pixOrder['id_order'];
					$new_history->changeIdOrderState((int) \Configuration::get('PS_OS_CANCELED'), (int) $pixOrder['id_order']);
					$new_history->addWithemail(true);
				}
			}
		}
	}
}
