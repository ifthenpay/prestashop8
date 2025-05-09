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

namespace PrestaShop\Module\Ifthenpay\Utility;

use PrestaShop\Module\Ifthenpay\Factory\Prestashop\PrestashopModelFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Utility
{
    private static $name = 'ifthenpay';

    public static function redirectIfthenpayConfigPage()
    {
        $token = \Tools::getAdminTokenLite('AdminModules');
        \Tools::redirectAdmin('index.php?controller=AdminModules&configure=' . self::$name . '&tab_module=payments_gateways&module_name=' . self::$name . '&token=' . $token);
    }

    public static function redirectAdminOrder($order)
    {
        if (version_compare(_PS_VERSION_, '1.7.5', '<')) {
            $token = \Tools::getAdminTokenLite('AdminOrders');
            \Tools::redirectAdmin(\Context::getContext()->link->getAdminLink('AdminOrders') . '&vieworder=&id_order=' . $order->id . '&token=' . $token);
        } else {
            \Tools::redirectAdmin(
                \Context::getContext()->link->getAdminLink('AdminOrders', true, [], [
                    'vieworder' => 1,
                    'id_order' => (int) $order->id,
                ])
            );
        }
    }

    public static function checkPaymentMethodDefined()
    {
        if (!isset($_GET['paymentMethod'])) {
            self::redirectIfthenpayConfigPage();
        }
    }

    public static function setPrestashopCookie($cookieName, $cookieValue)
    {
        \Context::getContext()->cookie->__set($cookieName, $cookieValue);
        \Context::getContext()->cookie->write();
    }

    public static function unsetPrestashopCookie($cookieName)
    {
        \Context::getContext()->cookie->__unset($cookieName);
        \Context::getContext()->cookie->write();
    }

    /**
     * Get formated price
     *@param Order $order
     *@return string
     */
    public static function getFormatedPrice($order)
    {
        $price = $order->getOrdersTotalPaid();
        return \Context::getContext()
                ->currentLocale->formatPrice($price, \Context::getContext()->currency->iso_code);
    }

    public static function getMailTranslationString($paymentType, $type = '')
    {
        switch ($type) {
            case 'refund':
                return \Context::getContext()->language->iso_code === 'pt' ? 'Confirmação de Reembolso ' . ucfirst($paymentType) : 'Refund Confirmation for ' . ucfirst($paymentType);
            case 'details':
                return \Context::getContext()->language->iso_code === 'pt' ? 'Dados de pagamento ' . ucfirst($paymentType) : 'Payment details for ' . ucfirst($paymentType);
            default:
                return \Context::getContext()->language->iso_code === 'pt' ? 'Pagamento em falta...' : 'Payment missing...';
        }
    }

    public static function convertPriceToEuros($order)
    {
        $actualCurrency = PrestashopModelFactory::buildCurrency((string) $order->id_currency);
        $amount = $order->getOrdersTotalPaid();

        // Convert amount to EUR if currency is different
        if ($actualCurrency->iso_code !== 'EUR') {
            return \Tools::convertPriceFull(
                $order->getOrdersTotalPaid(),
                $actualCurrency,
                PrestashopModelFactory::buildCurrency((string) \Currency::getIdByIsoCode('EUR'))
            );
        }
        return $amount;
    }

    public static function getClassName($class)
    {
        return substr(strrchr(get_class($class), '\\'), 1);
    }

    public static function numberToPagination($rows, $page)
    {
        $range = 50;
        $pages = ceil($rows / $range);

        $html = '';
        if ($pages) {
            $htmlList = '';

            for ($i = 1; $i <= $pages; $i++) {

                $isActive = $i == $page ? 'active' : '';

                if (
                    $i == 1 ||
                    $i == $pages ||
                    ($i > $page - 3 && $i <= $page + 2)
                ) {
                    $htmlList .= '
                    <li class="page-item">
                        <button type="button" class="btn btn-outline-primary btn_paginator ' . $isActive . '">' . $i . '</button>
                    </li>
                    ';
                }

                if (($i == $pages - 1 && $pages > $page + 2) ||
                    ($i == 2 && $page > 3)
                ) {
                    $htmlList .= '
                    <li class="page-item if_dots">
                    ...
                    </li>';
                }
            }

            $html = '
            <nav aria-label="Log navigation" class="pagination_container">
                <ul class="if_paginator">
                    ' . $htmlList . '
                </ul>
            </nav>
            ';
        }

        return $html;
    }

    /**
     * get a new array with the keys specified in the $keys array
     * @param array $inputArray
     * @param array $keys
     * @return array
     */
    public static function extractArrayWithKeys($inputArray, $keys): array
    {
        $outputArray = array();
        foreach ($keys as $key) {
            if (array_key_exists($key, $inputArray)) {
                $outputArray[$key] = $inputArray[$key];
            }
        }
        return $outputArray;
    }


    /**
     * Convert assoc array data to string
     *@param Order $order
     *@return string
     */
    public static function dataToString($data)
    {
        $strData = '{';
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $strData .= $key . ' => ' . $value . ', ';
            }
        }

        $strData .= '}';
        return $strData;
    }

    public static function GenerateSecurityCode() {
        $rnd = rand(10000, 99999);
        $code = strval($rnd);
        return $code;
    }


	/**
	 * masks a string by replacing the characters according to the binary mask given in $mask
	 * if mask string is smaller then the string to mask ($text), then it replaces the remainder with the mask character
	 */
	public static function maskString(string $text, string $mask, string $maskCharacter = 'X')
	{
		$maskedText = '';

		for ($i = 0; $i < strlen($text); $i++) {
			if(!isset($mask[$i])){
				$maskedText .= $maskCharacter;
			}else{
				$maskedText .= ($mask[$i] == '0') ? $maskCharacter : $text[$i];
			}
		}

		return $maskedText;
	}
}
