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

namespace PrestaShop\Module\Ifthenpay\Request;

if (!defined('_PS_VERSION_')) {
	exit;
}

use PrestaShop\Module\Ifthenpay\Factory\Request\RequestFactory;
use PrestaShop\Module\Ifthenpay\Callback\CallbackStrategy;
use \Symfony\Contracts\HttpClient\ResponseInterface;

class WebService
{
	private $client;
	private ResponseInterface $response;
	private $curl;

	public function __construct($headers = [])
	{
		$this->client = RequestFactory::buildClient();

		$this->curl = curl_init();
		curl_setopt_array($this->curl, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_HTTPHEADER => $headers,
		]);
	}

	public function getResponseCode()
	{
		return $this->response->getStatusCode();
	}


	public function getResponse()
	{
		return $this->response->getContent();
	}

	public function getResponseJson()
	{
		return $this->response->toArray();
	}

	public function postRequest($url, $data, $json = false)
	{
		try {
			$this->response = $this->client->request(
				'POST',
				$url,
				$json ? ['json' => $data] : [
					'body' => $data,
					'headers' => [
						'Content-Type' => 'application/x-www-form-urlencoded',
					],
				]
			);
			return $this;
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	public function getRequest($url, $data = [])
	{
		try {
			$this->response = $this->client->request('GET', $url, ['query' => $data]);
			return $this;
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	public function getRequest_callback($url, $data = [])
	{
		curl_setopt_array($this->curl, [
			CURLOPT_POST => false,
			CURLOPT_URL => $url
		]);
		$this->response = curl_exec($this->curl);

		if ($this->response === false) {
			throw new \Exception(curl_error($this->curl));
		}

		return $this->response;
	}
}
