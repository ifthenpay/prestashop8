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

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\Ifthenpay\Utility\Utility;
use PrestaShop\Module\Ifthenpay\Log\IfthenpayLogProcess;
use PrestaShop\Module\Ifthenpay\Factory\IfthenpayStrategyFactory;
use PrestaShop\Module\Ifthenpay\Factory\Prestashop\PrestashopModelFactory;

class UpdateController extends ModuleAdminController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        try {
            $order = PrestashopModelFactory::buildOrder(Tools::getValue('orderId'));
            IfthenpayStrategyFactory::build('ifthenpayAdminUpdate', $order, $this->module)->execute();
            IfthenpayLogProcess::addLog('Payment Data updated with success', IfthenpayLogProcess::INFO, $order->id);
            Utility::setPrestashopCookie('success', $this->module->l('Payment Data updated with success!', pathinfo(__FILE__)['filename']));
        } catch (Exception $th) {
            IfthenpayLogProcess::addLog('Error updating payment data - ' . $th->getMessage(), IfthenpayLogProcess::ERROR, $order->id);
            Utility::setPrestashopCookie('error', $this->module->l('Error updating payment data!', pathinfo(__FILE__)['filename']));
        }
        Utility::redirectAdminOrder($order);
    }
}
