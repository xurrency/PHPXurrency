<?php

/**
 * @package   PHPXurrency
 * @author    Alfonso Jimenez <yo a.t alfonsojimenez.com>
 * @link      http://xurrency.com
 * @copyright Copyright 2010 Xurrency
 * @license   New BSD
 */

class PHPXurrency
{
    /**
     * API License key
     * @var string
     */
    private $_key;

    /**
     *
     * Internal cache
     * @var array
     */
    private $_cache;

    /**
     *
     * Public constructor
     *
     * @param string $key API Key
     */
    public function __construct($key = null)
    {
        $this->_key   = $key;
        $this->_cache = array();
    }

    /**
     *
     * Returns an exchange rate
     *
     * @param string $base   Base currency
     * @param string $target Target currency
     * @param double $amount Amount
     * @return double
     */
    public function getRate($base, $target, $amount = 1)
    {
        $rate = $this->_makeRequest($base, $target);

        return abs($rate->value*$amount);
    }

    /**
     *
     * Returns last time when the data was updated
     *
     * @param string $base   Base currency
     * @param string $target Target currency
     * @return string
     */
    public function getUpdatedAt($base, $target)
    {
        $rate = $this->_makeRequest($base, $target);

        return $rate->updated_at;
    }

    /**
     *
     * Returns the API Key
     *
     * @return string
     */
    public function getAPIKey()
    {
        return $this->_key;
    }

    private function _makeRequest($base, $target)
    {
        if (!$this->_isCached($base.$target)) {
            $api_uri = 'http://xurrency.com/api/'.$base.'/'.$target.'/1';

            if (!empty($this->_key)) {
                $api_uri .= '?key='.$this->_key;
            }

            $content = file_get_contents($api_uri);

            $request = json_decode($content);

            if ($request->status === 'fail') {
                if ($request->code == 3) {
                    throw new XurrencyAPILimitReachedException($request->message);
                } elseif ($request->code == 2) {
                    throw new XurrencyAPIInvalidCurrencies($request->message);
                } elseif ($request->code == 4 || $request->code == 5) {
                    throw new XurrencyAPIInvalidKey($request->message);
                } else {
                    throw new Exception($request->message);
                }
            } else {
                $result = $request->result;

                $this->_setCache($base.$target, $result);
            }
        } else {
            $result = $this->_getCache($base.$target);
        }

        return $result;
    }

    private function _setCache($key, $value)
    {
        $this->_cache[$key] = $value;
    }

    private function _getCache($key)
    {
        return $this->_cache[$key];
    }

    private function _isCached($key)
    {
        return isset($this->_cache[$key]);
    }

    private function _flushCache()
    {
        $this->_cache = array();
    }
}

class XurrencyAPILimitReachedException extends Exception
{
     public function __construct($message)
     {
        parent::__construct($message);
    }
}

class XurrencyAPIInvalidCurrencies extends Exception
{
     public function __construct($message)
     {
        parent::__construct($message);
    }
}

class XurrencyAPIInvalidKey extends Exception
{
     public function __construct($message)
     {
        parent::__construct($message);
    }
}