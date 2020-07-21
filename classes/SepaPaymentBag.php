<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   isotope_payment_sepa
 * @author    Michael Gruschwitz <info@grusch-it.de>
 * @license   LGPL
 * @copyright Michael Gruschwitz 2015-2017
 */

namespace Gruschit;

use Contao\StringUtil;
use Serializable;

/**
 * SEPA Payment Data Bag.
 *
 * Holds the bank account data for the SEPA payment module.
 *
 * @package    isotope_payment_sepa
 * @author     Michael Gruschwitz <info@grusch-it.de>
 * @copyright  Michael Gruschwitz 2015-2017
 * @see        http://stackoverflow.com/questions/20983339/validate-iban-php#20983340
 */
class SepaPaymentBag implements Serializable
{

	/**
	 * @var array
	 */
	private $arrData = array();

	/**
	 * @param array $arrData
	 */
	public function __construct(array $arrData = array())
	{
		$this->arrData = $arrData;
	}

	/**
	 * Load payment bag from a serialized string.
	 *
	 * @param string $strSerialized
	 * @return static
	 */
	public static function load($strSerialized)
	{
		$static = new static;
		$static->unserialize($strSerialized);

		return $static;
	}

	/**
	 * Save a value of form field to the bag.
	 *
	 * @param string $strKey The name of the form field
	 * @param string $strValue The value to be saved
	 */
	public function put($strKey, $strValue)
	{
		foreach (SepaCheckoutForm::getFieldConfigurations() as $strName => $arrField)
		{
			// unknown form field
			if ($strKey != $strName)
			{
				continue;
			}

			// do not save submit button values
			if (isset($arrField['inputType']) && $arrField['inputType'] == 'submit')
			{
				continue;
			}

			$this->arrData[$strKey] = $strValue;
		}
	}

	/**
	 * Retrieve all values.
	 *
	 * @return array
	 */
	public function all()
	{
		$arrData = array();
		foreach (SepaCheckoutForm::getFieldConfigurations() as $strName => $arrField)
		{
			// do not return submit button values
			if (isset($arrField['inputType']) && $arrField['inputType'] == 'submit')
			{
				continue;
			}

			$arrData[$strName] = $this->get($strName);
		}

		return $arrData;
	}

	/**
	 * Retrieve a value.
	 *
	 * @param string $strKey The form fields name
	 * @return mixed|null
	 */
	public function get($strKey)
	{
		if ( ! isset($this->arrData[$strKey]))
		{
			return null;
		}

		foreach (SepaCheckoutForm::getFieldConfigurations() as $strName => $arrField)
		{
			// unknown form field
			if ($strKey != $strName)
			{
				continue;
			}

			return $this->arrData[$strKey];
		}

		return null;
	}

	/**
	 * Remove a value from the session.
	 *
	 * @param string $strKey
	 */
	public function remove($strKey)
	{
		if (isset($this->arrData[$strKey]))
		{
			unset($this->arrData[$strKey]);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function serialize()
	{
		return serialize($this->arrData);
	}

	/**
	 * @inheritdoc
	 */
	public function unserialize($serialized)
	{
		$this->arrData = StringUtil::deserialize($serialized, true);
	}

}
