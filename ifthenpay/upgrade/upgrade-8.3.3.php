<?php

/**
 * File: /upgrade/upgrade-8.3.3.php
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
 * adds new methods order statuses and
 * forces callback reactivation
 *
 * @param [type] $module
 * @return void
 */
function upgrade_module_8_3_3($module)
{
	// Process Module upgrade to 8.3.3
	IfthenpayLogProcess::addLog('Running module upgrade to version 8.3.3', IfthenpayLogProcess::INFO, 0);

	return alterLogMessageColumnDataType();
}




function alterLogMessageColumnDataType()
{
	if (!checkColumnsExistence(_DB_PREFIX_ . 'ifthenpay_log', 'message')) {

		try {
			// log if possible
			IfthenpayLogProcess::addLog('Error running upgrade script from 8.3.3. Possible cause: could not find the ifthenpay_log table or ifthenpay_log.message column', IfthenpayLogProcess::INFO, 0);
		} catch (\Throwable $th) {
			// silence this error
		}
		return false;
	}

	$alterQuery = 'ALTER TABLE `' . pSQL(_DB_PREFIX_ . 'ifthenpay_log') . '`
                   MODIFY COLUMN `' . pSQL('message') . '` TEXT;';

	$result = Db::getInstance()->execute($alterQuery);

	IfthenpayLogProcess::addLog('Ran upgrade script from 8.3.3 module version (alterColumnDataType()) for table ' . _DB_PREFIX_ . 'ifthenpay_log' . ' with result code = ' . $result, IfthenpayLogProcess::INFO, 0);

	return $result == 1 ? true : false;
}




function checkColumnsExistence($tableName, $oldColumnName)
{
	$db = \Db::getInstance();
	$dbNameQuery = 'SELECT DATABASE()';
	$dbName = $db->getValue($dbNameQuery);

	$query = 'SELECT COUNT(*) AS column_exists
    FROM information_schema.columns
    WHERE table_name = \'' . pSQL($tableName) . '\'
    AND table_schema = \'' . pSQL($dbName) . '\'
    AND column_name = \'' . pSQL($oldColumnName) . '\'';

	$count = $db->getValue($query);

	return $count > 0 ? 1 : 0;
}
