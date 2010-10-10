<?php

require_once 'PHPUnit/Framework.php';
require_once 'PHPXurrency/lib/PHPXurrency.php';
require_once 'PHPXurrency/test/TestHelper.php';

class PHPXurrency_Test extends PHPUnit_Framework_TestCase
{
    private $_xurrency;

    public function setUp()
    {
        $this->_xurrency = new PHPXurrency();
    }

    public function testUnitaryConversion()
    {
        TestHelper::setAPIResponse('eur', 'usd', 1, 1.5, '2010-10-04 00:00:00');

        $this->assertEquals(1.5, $this->_xurrency->getRate('eur', 'usd'));
    }

    public function testSimpleConversion()
    {
        TestHelper::setAPIResponse('eur', 'usd', 1, 1.5, '2010-10-04 00:00:00');

        $this->assertEquals(3, $this->_xurrency->getRate('eur', 'usd', 2));
    }

    public function testNegativeConversion()
    {
        TestHelper::setAPIResponse('eur', 'usd', 1, 1.5, '2010-10-04 00:00:00');

        $this->assertEquals(1.5, $this->_xurrency->getRate('eur', 'usd', -1));
    }

    public function testUpdatedDate()
    {
        TestHelper::setAPIResponse('eur', 'usd', 1, 1.5, '2010-10-04 00:00:00');

        $this->assertEquals('2010-10-04 00:00:00', $this->_xurrency->getUpdatedAt('eur', 'usd'));
    }

    public function testKeyIsSet()
    {
        $xurrency = new PHPXurrency('my_fake_api_key');

        $this->assertEquals('my_fake_api_key', $xurrency->getAPIKey());
    }

    /**
     * @expectedException XurrencyAPILimitReachedException
     */
    public function testLimitReached()
    {
        TestHelper::setAPIResponse('eur', 'usd', 1, 1.5, '2010-10-04 00:00:00', array('fail_with' => 'Limit Reached (10 requests per day). Please adquire a license key', 'code' => 3));

        $rate = $this->_xurrency->getRate('eur', 'usd', 1);
    }

    /**
     * @expectedException XurrencyAPIInvalidCurrencies
     */
    public function testInvalidCurrencies()
    {
        TestHelper::setAPIResponse('xxx', 'yyy', 1, 1, '2010-10-04 00:00:00', array('fail_with' => 'Currencies are not valid', 'code' => 2));

        $rate = $this->_xurrency->getRate('yyy', 'xxx', 1);
    }

    /**
     * @expectedException XurrencyAPIInvalidKey
     */
    public function testInvalidKey()
    {
        TestHelper::setAPIResponse('eur', 'usd', 1, 1, '2010-10-04 00:00:00', array('fail_with' => 'The api key is not valid', 'code' => 4));

        $rate = $this->_xurrency->getRate('eur', 'usd');
    }
}