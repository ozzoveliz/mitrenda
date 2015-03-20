<?php


class Onetree_Visanet_Model_Configtesting {
    
    private $_vi = "6d697472656e6461";
    
    
    private $_vposCryptoPub;
    
    
    private $_vposSignPub;
    
    
    private $_comCryptoPri;

    private $_comSignPri;
/*    private $_comSignPri = "-----BEGIN RSA PRIVATE KEY-----\n
MIICXwIBAAKBgQDILiD7K0e+/dHYefrQ4Zni4PVUPP+SrHKBzqLJ+iME+Sl8OC14\n
7aN9JIBGmBDHd8t66fmn1ryPni9Q4KGnhqZGqyIZx75nLP76t4lZHT9Wv4DnPOmx\n
Q2yeZ8cn6JfSfaHD/yXVkBDfL0clTPLGBqWrsqhdijm03o8jofM2S+LOzwIDAQAB\n
AoGBAJWE1v+kQS+oOHBgE/anO6EZ9ESaVy7//Xt1k2QXoMabMOBmuZhEr8POWr3f\n
4VCL6wGA+AfkPe1r8t2PG0+dJF38UcWcLhkrtXm7/Ycmyxj8h42QMxMUgkI929JM\n
ew9X/4abmGxlUl28unInclUEd4KXex6ug4ukj2dz/7qMgzcZAkEA4ylAp9LqZZSF\n
PY2c8/SvxClkwt1oBseF45jxQErVG1xQgx32BA8hM0R/aVoHUio7Kguvw35gCLDl\n
B4L4n5fJ9QJBAOGX/n3EynerckCnXlFJhhgTidzGysd7UnNXKqdU1H4NX/jGbF/G\n
a83aFOlMu9hsXKvUmW9CHxFwi76olUJUZzMCQQCJ7bVtcVqTnS+d5UuksTC8Keod\n
i/QrEaERRf9Oa2GkQFQ+mMWVR16AU5oNbPW/BRdxezEYwbYr8MTP3814keC1AkEA\n
kKOmLLcW5UFMYL2ukEmmqxsj4iSm8N1V0NPLajvOff9PUC7QX1vV1McFb0ueiLV5\n
eUY3Fgl75++T+asW/88j1wJBAMZtZoYs4Br1PW+liF0laJPX0ZbNyxLdDbGAPscX\n
pza6CBX8/HWu/K/wbrRkjxoSoEBybjuibQrmrR32FEB7Gac=\n
-----END RSA PRIVATE KEY-----";
*/
    
public function __construct() {

/*$this->_vposCryptoPub = "-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC9HsZ2aukH2lVGXDzamcnRIISZ\n".
"37rP39AXRy8YM3hYMdwHeU+xUJk1Q7VaX6HXQtgW15oW4Z0g5QxqfpVDqS0aeCGI\n".
"hkogegT3mcuYs51XwgG2s6FmHGGjLah+cLekbkFdxBoy9Tn21DYUn0wkaheyVzQ9\n".
"2IoTgmLucfagLaLYGQIDAQAB\n".
"-----END PUBLIC KEY-----";*/

$this->_vposCryptoPub ="-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDTJt+hUZiShEKFfs7DShsXCkoq\n".
"TEjv0SFkTM04qHyHFU90Da8Ep1F0gI2SFpCkLmQtsXKOrLrQTF0100dL/gDQlLt0\n".
"Ut8kM/PRLEM5thMPqtPq6G1GTjqmcsPzUUL18+tYwN3xFi4XBog4Hdv0ml1SRkVO\n".
"DRr1jPeilfsiFwiO8wIDAQAB\n".
"-----END PUBLIC KEY-----";

        
/*$this->_vposSignPub = "-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC++f8lbSK56AjqulRF6uIWTihO\n".
"rD2zJ5w1U3NRCuCU6RBiDz+eqii6ZodEU3uYe3iCxiuryYlMLJQTUokrhYTtfe9S\n".
"YK6CTu7nXniUp3z7E7cjWsdinY8i4udW/pqmojdyyx9//H0/iHE6EpdCU8emRDDB\n".
"FgbU0SGzwwW3r1fnOQIDAQAB\n".
"-----END PUBLIC KEY-----";*/

$this->_vposSignPub = "-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvJS8zLPeePN+fbJeIvp/jjvLW\n".
"Aedyx8UcfS1eM/a+Vv2yHTxCLy79dEIygDVE6CTKbP1eqwsxRg2Z/dI+/e14WDRs\n".
"g0QzDdjVFIuXLKJ0zIgDw6kQd1ovbqpdTn4wnnvwUCNpBASitdjpTcNTKONfXMtH\n".
"pIs4aIDXarTYJGWlyQIDAQAB\n".
"-----END PUBLIC KEY-----";


/*Private key cifrado*/
$this->_comCryptoPri = "-----BEGIN RSA PRIVATE KEY-----\n".
"MIICXgIBAAKBgQDYYKjWoDPEVBwvbLyPgzKwgKJaefqbkUwdGdvlcZX3S6j3gK9l\n".
"tYMZzLdpLGX7TDnYMYZJWr62bCSewGUvgnHyDBWwbvrAwhZl02ecGuE17QWJRZ0Z\n".
"qfQ5U9cqzd8aVgxvSiNHsZ7ocJ0gn3AZPOIuxsRxCLRK+7TYu4Sr2DBSXQIDAQAB\n".
"AoGBAJi4IHLbv63ib8JJTQ093JkXMYAKeAc1FjOz/LCE6wQQMsH94c43vsCG7S0e\n".
"WRx1QJ6G1fEQS2sWCQLDaKePujuAaM/hP5UV104mSUEbXs9HRWOiU+kUf65vkWfH\n".
"P5HfxaRciv8KqBWWbFkzwqG9o5B+cmNqwMj1LQGx4mSssWYBAkEA7nY6SDasVqW7\n".
"99UyucTM+xMnq5MNjC5oBWRLT2V8OiN0Gd+NjYBh1SrlAgNDTKVV7XgMVhEtYkqT\n".
"+nHlM5N1KQJBAOhKoN2gRS5cL6vNWHDI76+CCeTSo9h6RIPYfRD8W0HEpw2lEZkO\n".
"JIBWyMOdELwNTkGBsMdVs6jg43Fxk9HqxhUCQQCCO295IOLeL4WIxJb/fcu1E7EG\n".
"szki5fVJfIzkv7goyFQQDVdxgDvt/48ymjWXFnTnTDjwiFEQ7UoD7VAP6uiBAkAq\n".
"xxuK1OoImJd61w0TcB2bv6HczuG3iwS1FxgpGaAgo3t0KtLr0cvtCo5cX79mMjkY\n".
"HS8V9nVog0m/aYAkZ0zlAkEA3SrOSSOaJd8B6xx+5NDY3zrzElkDqOiNawSH5moq\n".
"YF1u+plidQ6uVPDktl2LjCfbR62BR7jJedZjSEQOe3rCsw==\n".
"-----END RSA PRIVATE KEY-----";

/*Private key fimra*/
$this->_comSignPri = "-----BEGIN RSA PRIVATE KEY-----\n".
"MIICWwIBAAKBgQCbMrzojzX9gZ2KxUHAJt+wLnLT3do4JB/V6k4UUjEAN+j2NLWj\n".
"NdQ/S+WreNhAQRTbRZQ3HnR7192ldWNogCN9v+DsiS2B+6ORQEXx8lPUdHdbEwwR\n".
"sNqk+dpqkDbMuoM0zfWfOySfYWhRMqu6mSb/qr5r9d7ZmvGfd4NSI5XLnwIDAQAB\n".
"AoGAb7yH5ae15156Xn7GZ+GVCvNmGMORaPoZMZoPvPGv9chLIXexjmAi/69VG69V\n".
"gm1Y5sWhgE2c78zq90Uc3Q/kfHWyk94W+ybWrqsFQNRjDTq46L7MMX1DbtHirGk+\n".
"Eg38/A9lByQblMVuOw7Ge2+4dsJVFiLbxmRkJE+TlYv7cYECQQDI2zPwT8WqYBml\n".
"N4gGzvbiNYi7Gh5bq8BpsFHSB0iNR4anHVBY+94y1bgBMwqfTZqJ6DxH0KYfgYtn\n".
"orGwjzMNAkEAxc6LoFwUWorLXgIb//wh5vMRJsjNLBiEb8bL2isHGy6aueTF75mO\n".
"6footHU/Fytk+548OV+qJjiPZcaOZTe+WwJAeDjUVsO4wajx0LVzYvfgSDfY1nzk\n".
"rOQZsGkMIFWhtIaab06ZeBmRvwWzNGyTsBRoKqFp62ZU+Mi2Y1q7Vr3vxQJAetbw\n".
"2O416aCF+OSAdPEkMKNERHyxBbBYFn4zPFI6QIQprEXJMil/mggzXRXuzzmwpZ23\n".
"sr2ZEdFQJBkxduQuPwJAReFbfth4LPF7cJKjhfQXjJpUsOOd7CCz7W1THeflZBlZ\n".
"6Ah5FPgPfxLJX0eK6kiQnYuymujkcwb+8k+I3TLK+w==\n".
"-----END RSA PRIVATE KEY-----";
    }
    
    public function getVi() {
        return $this->_vi;
    }
    
    public function getVposCryptoPub() {
        return $this->_vposCryptoPub;
    }
    
    public function getVposSignPub() {
        return $this->_vposSignPub;
    }
    
    public function getComCryptoPri() {
        return $this->_comCryptoPri;
    }
    
    public function getComSignPri() {
        return $this->_comSignPri;
    }

    public function getIdCommerce(){
        return Mage::getStoreConfig('payment/visanet/TESTIDCOMMERCE');
    }

    public function getIdAcquirer(){
        return Mage::getStoreConfig('payment/visanet/TESTIDACQUIRER');
    }
}