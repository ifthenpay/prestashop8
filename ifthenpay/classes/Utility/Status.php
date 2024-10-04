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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Status {

	private static $statusPending = "a7b9e3f042d8c16a";
    private static $statusSuccess = "6dfcbb0428e4f89c";
    private static $statusError = "101737ba0aa2e7c5";
    private static $statusCancel = "d4d26126c0f39bf2";

    public static function getTokenStatus($token)
    {
        switch ($token) {
			case self::$statusPending:
                return 'pending';
            case self::$statusSuccess:
                return 'success';
            case self::$statusCancel:
                return 'cancel';
            case self::$statusError:
                return 'error';
            default:
                return '';
        }
    }



	/**
     * Get the value of statusPending
     */
    public static function getStatusPending()
    {
        return self::$statusPending;
    }



    /**
     * Get the value of statusSuccess
     */
    public static function getStatusSuccess()
    {
        return self::$statusSuccess;
    }

    /**
     * Get the value of statusError
     */
    public static function getStatusError()
    {
        return self::$statusError;
    }

    /**
     * Get the value of statusCancel
     */
    public static function getStatusCancel()
    {
        return self::$statusCancel;
    }
}
