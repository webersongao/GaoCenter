<?php

// +----------------------------------------------------------------------
// | pay-php-sdk
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/pay-php-sdk
// +----------------------------------------------------------------------

return [
    // 微信支付参数
    'wechat' => [
        // 沙箱模式
        'debug'      => get_setting('pay_config')['wechat']['debug'],

        // 应用ID
        'app_id'     => get_setting('pay_config')['wechat']['app_id'],
        // 'app_id'     => 'wx78029370fbbd5443',
        // 微信支付商户号
        'mch_id'     =>get_setting('pay_config')['wechat']['mch_id'],
        // 'mch_id'     => '1245760202',
        
        /*1377957102
         // 子商户公众账号ID
         'sub_appid'  => '子商户公众账号ID，需要的时候填写',
         // 子商户号
         'sub_mch_id' => '子商户号，需要的时候填写',
        */
        // 微信支付密钥7eGl96TjCCxYGDUjfz12pQ7A48uIid4O
        'mch_key'    =>get_setting('pay_config')['wechat']['mch_key'],
        // 'mch_key'    => '7eGl96TjCCxYGDUjfz12pQ7A48uIid4O',
        // 微信证书 cert 文件
        'ssl_cer'    => '',
        // 微信证书 key 文件
        'ssl_key'    => '',
        // 缓存目录配置
        'cache_path' => '',
        // 支付成功通知地址
        'notify_url' => '',
        // 网页支付回跳地址
        'return_url' => '',
    ],
    // 支付宝支付参数
    'alipay' => [
        // 沙箱模式
        'debug'       => get_setting('pay_config')['alipay']['debug'],
        // 应用ID
        'app_id'      =>  get_setting('pay_config')['alipay']['app_id'],
        // 'app_id'      => '2016082000301127',
        // 支付宝公钥(1行填写)
        'public_key'  =>get_setting('pay_config')['alipay']['public_key'],
        // 'public_key'  => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1QjVHRThAnP400AW79nYsDwYV3I2tmyG/hmocekNjbKIAfikx075kBmaXbVRZAq2TA3JY+wAxxdR9Y2jvcE72n4ugYD0Yyp5JjMhLWnyoMX5UC1HIgSz5QV86WGNckAt7imLqd1EHctU7FzcyXevqnxZ85jcUB4tYpYq/hOnXp/xhVB4OTvntVFzulwmujx+P3S4ZrOWUMJMhXR8hUNgkp4rlvecotBFbHdQD/osfECv7RlwMJ5H0+azUwR1WPtNvHBk/r+RYQu2nPJKUOQzhw/f1At2zuUM0/93ryCbfaFUnAAyPZ1lp3x4Zkd0wnGeRojvKEkc0kBn4MQPX+14gQIDAQAB',
        // 支付宝私钥(1行填写)
        'private_key' =>get_setting('pay_config')['alipay']['private_key'],
        // 'private_key' => 'MIIEowIBAAKCAQEArY0Z01JMNXKgbryrlIa+xqUwdbCcnuGj/5/3tgoa0+9MnO46kP76DUSW6hfbUlmKrHG5TrVPbKRSurbJUEFYnCbjXfkp+iNw3EbL4aNcaNGqRnp5XGkqOpexDYvlZI5WvSAnDS7sDSvp21ZN5CXSuOVer20OoQEyLLPt4y2QqnnEIFL83fXtsb0mGBO5WIrHiqZzU5uwPwQopJEBhUxsEycF8R+uhrp9MtU4+A7BArwfRhuPlL7tS5x7pM8Hrc6R71T0GF/s4KWv2XFk5Q124TCX7iJq1pGZG1UCLKluVPOkYgCJtPrtkcbBpSkWl+JhApFGXa7zapzTvm4zMfQ6KwIDAQABAoIBAF2ak8pBQe810sfHJLen8S9MmxVu2xpqUryld8IMMyoJkAW4C4h2rUEItGiv00/YEY4ujCaibaMIAcLIoU2S4QrfgpQFthk+kjmMSZx6MnYqVy9KETjBU9BkTk8jG+cfSjMZK1bV7uSvwaLWKo6Zw8yLkYUGSfSWo4wuw2hbBAKdZJQGI+MCLTu1u+MXgo/4LhL7XGMnuNinWFBGnd/889ngTxQNXIF9y7HfNAAA/N2p8NZotdcyFiUT3VdcvosmPmIkX2OzunGtRmlO6ydacpoSwxyjejTUpjZJMRxDDAGFHjekuOaReasugu8Z3Agx4LG8l4sPigtVcqq7SY+mKJECgYEA2704SjnTfVz/tkkeI45NtYj/akaP2Jw9W9fbk+vMzyPEdt31y6usYjI34UqjfI6ohJ+MSCh7KDHDgfn7PFryjQS3Qd2zR2Fm2UghuNEtkanQcr8H23xH6qA9ZJD5zvw10JBr6XdE4hng8A0C14J5Qav99oWODpZ0W0MGitBCReMCgYEAyjCxAG57VM2cKRRiA8rmRNVAv0LFK5D1a4sIzW9bEZG7NuUgkXrwHO5qPY89W7Yoi7T0KjqpV+A7yeTtBxi9dZXAGBn1kekTG39RiZKORARBaCUkHSpdiGwL88/nNrttOk0wPb+aMEFh3CCxgtnkf7d4dGn/q/2cSP/BHWCLrRkCgYEAmN2SA1EnJ9dCrXVAWkvtE5Uy2qQr/ezzYqlQQB+SY6fmTSssi3vqeIWnCjv6b/Rul5TG6ov+4X99Gzbk6J/8jM3zDwdEaSwBeLcNfp1GrkcMlEcBGFflT/wZuZSBtNUQOv+9krU+XmzSZy0mBPbnlCAqlQ1kPhG88KA4NOmcsTECgYAFA2oQIa0rMCH0Hs5DW8+T21nMpEIxT1nWfc8NEPrIF731oX7KPKshfIPj3N5fnMeqlyUKuwOh6yxwWB3MdD+WX80wi8w7/vR7VQ/XgmvGofhhNbKMipVhIZS2SexovgL6VBmjHlIbajOb+q+MGA0DYbA56rrtL8+lO7o1GUS9WQKBgCHSJFslip+KFoAmEHVfjM0ApZin1XaUV2B8kw1XJnb1Hac6FvMWBbXq0hc/pM6KHtKaFXGd+QD9K/QFq5ruJaxF8P8qFlXt/6ebgV2KvYlo2q30rbKHzRgh0HTuOC3p2H6ayXR9lPu4F32arIlMm2EwvA2V4fBkOHZsF9mDzFT1',
        // 缓存目录配置
        'cache_path'  => '',
        // 支付成功通知地址
        // 'notify_url'  =>  base_url().'/pay/notify/',
        'notify_url'  =>  '',
        // 网页支付回跳地址
        'return_url'  => '',
    ],
];