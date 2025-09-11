<?php

namespace PrestaShop\Module\Ifthenpay\Exceptions;

use Exception;
use Throwable;

class AlreadyPaidException extends Exception
{

	protected $errorCode;



	public function __construct($message, $errorCode = 0, Throwable|null $previous = null)
	{
		$this->errorCode = $errorCode;

		parent::__construct($message, 0, $previous);
	}
}
