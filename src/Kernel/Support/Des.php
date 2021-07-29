<?php

namespace JosClient\Kernel\Support;

/**
 * openssl 实现的 DES 加密类，支持各种 PHP 版本
 */
class Des
{
    /**
     * @var string $method 加解密方法，可通过 openssl_get_cipher_methods() 获得
     */
    protected $method;

    /**
     * @var string $output 输出格式 无、base64、hex
     */
    protected $output;

    /**
     * @var string $options
     */
    protected $options;

    // output 的类型
    const OUTPUT_NULL = '';
    const OUTPUT_BASE64 = 'base64';
    const OUTPUT_HEX = 'hex';

    static private $instance;

    /**
     * DES constructor.
     * @param string $method
     *      ECB DES-ECB、DES-EDE3 （为 ECB 模式时，$iv 为空即可）
     *      CBC DES-CBC、DES-EDE3-CBC、DESX-CBC
     *      CFB DES-CFB8、DES-EDE3-CFB8
     *      CTR
     *      OFB
     *
     * @param string $output
     *      base64、hex
     * @param int $options
     */
    protected function __construct($method = 'DES-ECB', $output = '', $options = OPENSSL_RAW_DATA | OPENSSL_NO_PADDING)
    {
        $this->method = $method;
        $this->output = $output;
        $this->options = $options;
    }

    /**
     * @param string $method
     * @param $output
     * @param int $options
     * @return Des
     */
     public static function getInstance($method = 'DES-CBC', $output = self::OUTPUT_HEX, $options = OPENSSL_RAW_DATA | OPENSSL_NO_PADDING){
        if(!self::$instance)
        {
            self::$instance = new self($method, $output, $options);
        }

        return self::$instance;
    }

    /**
     * 加密信息
     * @param $str
     * @param $key
     * @param $iv
     * @return false|string
     */
    public static function encrypt($str, $key, $iv = '')
    {
        $instance = self::getInstance();
        $str = $instance->pkcsPadding($str, 8);

        $sign = @openssl_encrypt($str, $instance->method, $key, $instance->options, $iv ?: $key);

        if ($instance->output == self::OUTPUT_BASE64) {
            $sign = base64_encode($sign);
        } else if ($instance->output == self::OUTPUT_HEX) {
            $sign = bin2hex($sign);
        }

        return $sign;
    }

    /**
     * 解密
     *
     * @param $encrypted
     * @param string $key
     * @return string
     */
    public static function decrypt($encrypted, $key, $iv = '')
    {
        $instance = self::getInstance();
        if ($instance->output == self::OUTPUT_BASE64) {
            $encrypted = base64_decode($encrypted);
        } else if ($instance->output == self::OUTPUT_HEX) {
            $encrypted = hex2bin($encrypted);
        }

        $sign = @openssl_decrypt($encrypted, $instance->method, $key, $instance->options, $iv ?: $key);
        $sign = $instance->unPkcsPadding($sign);
        $sign = rtrim($sign);
        return $sign;
    }

    /**
     * 填充
     *
     * @param $str
     * @param $blocksize
     * @return string
     */
    private function pkcsPadding($str, $blocksize)
    {
        $pad = $blocksize - (strlen($str) % $blocksize);
        return $str . str_repeat(chr($pad), $pad);
    }

    /**
     * 去填充
     *
     * @param $str
     * @return string
     */
    private function unPkcsPadding($str)
    {
        $pad = ord($str{strlen($str) - 1});
        if ($pad > strlen($str)) {
            return false;
        }
        return substr($str, 0, -1 * $pad);
    }

}
