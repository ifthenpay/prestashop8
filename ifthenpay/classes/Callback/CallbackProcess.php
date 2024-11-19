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

namespace PrestaShop\Module\Ifthenpay\Callback;

use PrestaShop\Module\Ifthenpay\Log\IfthenpayLogProcess;
use PrestaShop\Module\Ifthenpay\Factory\Callback\CallbackFactory;
use PrestaShop\Module\Ifthenpay\Factory\Models\IfthenpayModelFactory;
use PrestaShop\Module\Ifthenpay\Factory\Prestashop\PrestashopModelFactory;
use PrestaShop\Module\Ifthenpay\Factory\Database\DatabaseFactory;

if (!defined('_PS_VERSION_')) {
	exit;
}

class CallbackProcess
{
	protected $paymentMethod;
	protected $paymentData;
	protected $order;
	protected $request;


	/**
	 * Set the value of paymentMethod
	 *
	 * @return  self
	 */
	public function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;

		return $this;
	}

	/**
	 * Set the value of paymentData
	 *
	 * @return  self
	 */
	protected function setPaymentData()
	{
		$this->paymentData = CallbackFactory::buildCalllbackData($this->request)->execute();
	}

	/**
	 * Set the value of order
	 *
	 * @return  self
	 */
	protected function setOrder()
	{
		$this->order = PrestashopModelFactory::buildOrder($this->paymentData['order_id']);
	}



	protected function executePaymentNotFound()
	{

		if (isset($_GET['test']) && $_GET['test'] === 'true') {
			http_response_code(200);

			$ifthenpayModule = \Module::getInstanceByName('ifthenpay');

			$response = [
				'status' => 'warning',
				'message' => $ifthenpayModule->l('Order not found.', pathinfo(__FILE__)['filename'])
			];

			die(json_encode($response));
		}

		IfthenpayLogProcess::addLog('Callback Payment not found - ' . print_r($_GET, 1), IfthenpayLogProcess::ERROR, $this->order->id);
		http_response_code(404);
		die('Pagamento não encontrado');
	}

	protected function changeIfthenpayPaymentStatus($status)
	{
		$ifthenpayModel = IfthenpayModelFactory::build($this->paymentMethod, $this->paymentData['id_ifthenpay_' . $this->paymentMethod]);

		//WORKAROUND: odd behaviour from prestashop model object, it loses the transaction_id of the order for Ccard, so there is a need to set it in the next two lines
		if ($this->paymentMethod == 'ccard' && isset($this->paymentData['transaction_id'])) {
			$ifthenpayModel->transaction_id = $this->paymentData['transaction_id'];
		}
		if ($this->paymentMethod == 'cofidispay' && isset($this->paymentData['transaction_id'])) {
			$ifthenpayModel->transaction_id = $this->paymentData['transaction_id'];
		}
		$ifthenpayModel->status = $status;
		$ifthenpayModel->update();
	}

	protected function changePrestashopOrderStatus($statusId)
	{
		// if has split orders
		$splitOrders = $this->getSplitOrders($this->order->reference, $this->order->id_cart);
		if (count($splitOrders) > 1) {
			// update status for split orders
			foreach ($splitOrders as $order) {

				if (!isset($order['id_order'])) {
					IfthenpayLogProcess::addLog('Error processing callback for split orders - prestashop db order property id_order is not set.', IfthenpayLogProcess::ERROR, $this->order->id);
					http_response_code(400);
				}

				$new_history = PrestashopModelFactory::buildOrderHistory();
				$new_history->id_order = (int) $order['id_order'];
				$new_history->changeIdOrderState((int) $statusId, (int) $order['id_order']);
				$new_history->addWithemail(true);

				IfthenpayLogProcess::addLog('Split Order status change with success to paid (after receiving callback)', IfthenpayLogProcess::INFO, $order['id_order']);
			}
		} else {
			$new_history = PrestashopModelFactory::buildOrderHistory();
			$new_history->id_order = (int) $this->order->id;
			$new_history->changeIdOrderState((int) $statusId, (int) $this->order->id);
			$new_history->addWithemail(true);

			IfthenpayLogProcess::addLog('Order status change with success to paid (after receiving callback)', IfthenpayLogProcess::INFO, $this->order->id);
		}
	}



	protected function updateOrderPayment()
	{
		// if has split orders
		$splitOrders = $this->getSplitOrders($this->order->reference, $this->order->id_cart);
		if (count($splitOrders) > 1) {
			// update status for split orders
			foreach ($splitOrders as $order) {

				if (!isset($order['id_order'])) {
					IfthenpayLogProcess::addLog('Error processing callback for split orders - prestashop db order property id_order is not set.', IfthenpayLogProcess::ERROR, $this->order->id);
					http_response_code(400);
				}

				$orderObj = PrestashopModelFactory::buildOrder($order['id_order']);

				$this->updateOrderPaymentByOrder($orderObj);
			}
		} else {
			$this->updateOrderPaymentByOrder($this->order);
		}
	}



	/**
	 * Set the value of request
	 *
	 * @return  self
	 */
	public function setRequest($request)
	{
		$this->request = $request;

		return $this;
	}



	/**
	 * Get orders that have been created from one that had products tied to different shipping methods and thus were created separately
	 * Used to process single callback and update order status for both
	 */
	protected function getSplitOrders($reference, $cartId)
	{
		$query = DatabaseFactory::buildDbQuery();
		$query->select('*');
		$query->from('orders');
		$query->where('`reference` = \'' . pSQL($reference) . '\' AND `id_cart` = ' . (int) $cartId);

		$orders = \Db::getInstance()->executeS($query);

		return $orders;
	}



	/**
	 * updates transacion_id in order_payment if present, and also updates the payment method
	 */
	protected function updateOrderPaymentByOrder($order)
	{
		$orderPayment = $order->getOrderPaymentCollection();
		if (count($orderPayment) > 0) {

			$transactionId = $this->paymentData['transaction_id'] ?? '';

			if ($transactionId != '') {
				$orderPayment[0]->transaction_id = $transactionId;
			}
			$orderPayment[0]->payment_method = $this->paymentMethod;


			if ($this->paymentMethod == 'ccard') {

				if (isset($this->request['brand']) && $this->request['brand'] != '') {
					$orderPayment[0]->card_brand = $this->request['brand'];
				}

				if (isset($this->request['pan']) && $this->request['pan'] != '') {
					$orderPayment[0]->card_number = $this->request['pan'];
				}
			}

			$orderPayment[0]->save();
		}
	}
}
