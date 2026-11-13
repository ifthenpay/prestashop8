<?php

/**
 * File: /upgrade/upgrade-8.3.6.php
 * @author    Ifthenpay Lda <ifthenpay@ifthenpay.com>
 *  @copyright 2007-2024 Ifthenpay Lda
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\Module\Ifthenpay\Log\IfthenpayLogProcess;

if (!defined('_PS_VERSION_')) {
	exit;
}

/**
 * Registers the hooks that older installations may be missing.
 * Installations that predate the refund feature (April 2023) were upgraded in place
 * and never had actionProductCancel registered, so refunds made in the backoffice
 * never reached the module.
 *
 * @param Ifthenpay $module
 * @return bool
 */
function upgrade_module_8_3_6($module)
{
	IfthenpayLogProcess::addLog('Running module upgrade to version 8.3.6', IfthenpayLogProcess::INFO, 0);

	$hooks = ['actionProductCancel'];

	foreach ($hooks as $hook) {
		// registerHook is idempotent per shop, but only register when missing to keep the log accurate
		if ($module->isRegisteredInHook($hook)) {
			continue;
		}

		if (!$module->registerHook($hook)) {
			IfthenpayLogProcess::addLog('Fail to run upgrade script 8.3.6: could not register hook ' . $hook, IfthenpayLogProcess::ERROR, 0);
			return false;
		}

		IfthenpayLogProcess::addLog('Upgrade script 8.3.6: registered missing hook ' . $hook, IfthenpayLogProcess::INFO, 0);
	}

	return true;
}
