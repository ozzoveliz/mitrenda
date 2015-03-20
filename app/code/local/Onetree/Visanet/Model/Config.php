<?php


class Onetree_Visanet_Model_Config
{

    private $_vi = "7561742e6d697472";
    private $_vposCryptoPub;
    private $_vposSignPub;
    private $_comCryptoPri;
    private $_comSignPri;

    public function __construct()
    {
        $this->_vposCryptoPub = "-----BEGIN PUBLIC KEY-----\n" .
            "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC0t0Cnbne8gQoeGK4nG6O3zfwh\n" .
            "q8u9Wp5zHjyVYbvx2zudSOlBnJ5qU74BcTGypbn6W7jjvSNE7AmncOAVh4RxuRXO\n" .
            "+bINFIyQ7/ErH/v1YpDFk8knC/NuvFpfHqhJ/5j2I8y+WmyF0MZmGtm074nUGv4d\n" .
            "qlbUMT9aYUQ+RzMO7QIDAQAB\n" .
            "-----END PUBLIC KEY-----";


        $this->_vposSignPub = "-----BEGIN PUBLIC KEY-----\n" .
            "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCtvXnikeSS+H/Qs/51iL3ZYPfz\n" .
            "KW94WUAz7IdZIOIcuG1zLIR3kUNUc/vdSmW120dwkIleB6pl4cVT5nDewBFJCzTS\n" .
            "W6jGaWaryzl7xS3ZToKTHpVeQr3avN7H+Om9TfsccY7gBV3IOIauTg9xIpDjIg52\n" .
            "fUcfyPq+Bhw0cWkDUQIDAQAB\n" .
            "-----END PUBLIC KEY-----";


        $this->_comCryptoPri = "-----BEGIN RSA PRIVATE KEY-----\n" .
            "MIICXAIBAAKBgQDJ/2QRETUMxmG7KEqWIbeUAcVGls+DxAm3O79GebATmRnvzlPe\n" .
            "+evW1z2kMDwdfNabqx+DBdmzk6dqMoaTvXfq/trdetdj4b9zMzW+m40qX0IZ6QeV\n" .
            "pcIenFGcz5rV7yKUqNvpe2j9YeYturIDJD9Ndh9pWY5DZV9NiAZG7x0RdQIDAQAB\n" .
            "AoGAAnAhrg8OG9xdG5wt4kf/5jprPiHd2VJJZ7vB4EDhvirP5FQSMRPFI++vH8cx\n" .
            "Qo3aLUDQfY1ZhlBW6fI4K5pS0y7Hv72PEDIUrk48Usw/s7bwFKi79nostIrhe0Ca\n" .
            "M9likIgKsfmfktu14lrFuozG6tiim8FB8BwAeDVqAfIJ95UCQQDtPXH0nBxjCD56\n" .
            "POHc1wGumyP5lNKUUdVRAKjT15ViKuTcbrDpZTxBEmlsXze+D53QGfCEXYBcjkE4\n" .
            "lN+q9Ee3AkEA2fiEqzCtuApBsfjZ+HtEo8XWR0cAg/LOHvg7eyFJGUs2j4JAjuye\n" .
            "uvay8MOkEy7WTn29fO+XXMafmsgF1dN4MwJBAOJ2pTE7VF20oO9O7sxA8fobXlwB\n" .
            "FINBGsAYQRD32kG7cHx7raUeXkY/rSMmQa/MeHkOSeoQ11eC8/9vHZOzXEECQB0R\n" .
            "i3/ZChHVLFWzvj3pEopUw/LVgTgXlW2D5UrScZEx+qDA2iM1YyXD6LE7JhJ42JO3\n" .
            "KSIUQbdZtcdKjZyqiwkCQFcbUmsxti00xvxdKwD0K/Q3Z+t9P3Jp2mujrLUiN2ah\n" .
            "vqCjs4p8Qc3I3eq0s+gBq+QOtQvMdF9GMT/QVXp5b9k=\n" .
            "-----END RSA PRIVATE KEY-----";

        $this->_comSignPri = "-----BEGIN RSA PRIVATE KEY-----\n" .
            "MIICWwIBAAKBgQDH3iWFC2otapws5MdC/UJy8x96vFlzc581k3FzSOIrzqUQSYPm\n" .
            "7xO4pj1ZLpF/XcNT1Sjvq0mFIzXc82RO65EaEz7YaNLzMBund6/3R1PAsz4GhkAB\n" .
            "08y3IhooUOmokTIFkdCsZ2RYSwcE3I6WIgVUhVyGfSu/yLRkDGo1nlBPTQIDAQAB\n" .
            "AoGASf3dmgF9z052ftA7THuBzBhhgQiVfP5C5IyvJWTIBju8M+ljYW0EugkhEREC\n" .
            "YEqP80somoGo+Bbu69tXOThQQNVBqQSZCBLLuXzhKI7JbWWFT7wJjFkJcVialqeG\n" .
            "tLkv0NzxU/cnfh6/AxN8dG1IeaUMr3QOsNPosNK85QIPhykCQQDutFxdhzy7m5yB\n" .
            "DrJz+aFRVzOf2A7KIKGsUB42ebzbhm95QU9vIqiyp8dje27dDITEgCzCFQjqaUky\n" .
            "WqOSItsPAkEA1llq0kZopnlfDWSqxkoaf2VoQKZeK9x4CBNb5apaKeZQuPYqzYzH\n" .
            "JMIRMZU7ovR5w4mO7G0JhbVO8J90Agrf4wJABZGtqfzyvMnHn/cw0KXdTqGDMdJ/\n" .
            "ndWfD/8ahVjXwCNlzGVqrgQX/XsOmtKRZpTZMCBxauHEwHtOWt9ke49WnwJAU8f/\n" .
            "iNEcdnVaQdHnLjoowULRzPM6O8qg4AcxFEPRmi77vk/5yv4LmMKCFe9OsBL+xP8v\n" .
            "bhnwbuK/SC4LGgFGIQJAals3x+ylWEldMXgvkB2OaY/iqqWTwu7nDtEFgBL83c86\n" .
            "Klm6S6WxGdA3xaAtLwBjo00fq3Xgbkz0tpvfVZIefA==\n" .
            "-----END RSA PRIVATE KEY-----";

    }

    public function getVi()
    {
        return $this->_vi;
    }

    public function getVposCryptoPub()
    {
        return $this->_vposCryptoPub;
    }

    public function getVposSignPub()
    {
        return $this->_vposSignPub;
    }

    public function getComCryptoPri()
    {
        return $this->_comCryptoPri;
    }

    public function getComSignPri()
    {
        return $this->_comSignPri;
    }

    public function getIdCommerce()
    {
        return Mage::getStoreConfig('payment/visanet/IDCOMMERCE');
    }

    public function getIdAcquirer()
    {
        return Mage::getStoreConfig('payment/visanet/IDACQUIRER');
    }
}
