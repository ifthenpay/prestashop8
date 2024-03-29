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

namespace PrestaShop\Module\Ifthenpay\Factory;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\Ifthenpay\Admin\IfthenpayAdminOrder;
use PrestaShop\Module\Ifthenpay\Admin\IfthenpayAdminResend;
use PrestaShop\Module\Ifthenpay\Admin\IfthenpayAdminUpdate;
use PrestaShop\Module\Ifthenpay\Admin\IfthenpayAdminRemember;
use PrestaShop\Module\Ifthenpay\Admin\IfthenpayAdminRefund;
use PrestaShop\Module\Ifthenpay\Admin\IfthenpayOrderDetail;
use PrestaShop\Module\Ifthenpay\Payments\Data\IfthenpayPaymentReturn;

class IfthenpayStrategyFactory
{
    public static function build($type, $order, $ifthenpayModule, $message = '')
    {
        switch ($type) {
            case 'ifthenpayPaymentReturn':
                return new IfthenpayPaymentReturn($order, $ifthenpayModule);
            case 'ifthenpayAdminOrder':
                return new IfthenpayAdminOrder($order, $ifthenpayModule, $message);
            case 'ifthenpayAdminRemember':
                return new IfthenpayAdminRemember($order, $ifthenpayModule);
            case 'ifthenpayAdminResend':
                return new IfthenpayAdminResend($order, $ifthenpayModule);
            case 'ifthenpayAdminUpdate':
                return new IfthenpayAdminUpdate($order, $ifthenpayModule);
            case 'ifthenpayAdminRefund':
                return new IfthenpayAdminRefund($order, $ifthenpayModule);
            case 'ifthenpayOrderDetail':
                return new IfthenpayOrderDetail($order, $ifthenpayModule);
            default:
                throw new \Exception("Unknown Strategy class");
        }
    }
}
