<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace JosClient\Kernel\Traits;


/**
 * Trait ResponseCastable.
 *
 * @author overtrue <i@overtrue.me>
 */
trait SignCorrelation
{
    /**
     * 计算签名
     */
    protected function generateSign($options)
    {
        $appSecret = $this->app['config']['app_secret'];
        $accessToken = $this->app['config']['access_token'];
        $method = $options['method'];
        unset($options['method']);
        $params = [
            'access_token' => $accessToken,
            'app_key' => $this->app['config']['app_key'],
            'timestamp' => $this->getCurrentTimeFormatted(),
            'v' => '2.0',
            'method' => $method,
            '360buy_param_json' => empty($options) ? "{}" : json_encode($options)
        ];
        ksort($params);
        $stringToBeSigned = $appSecret;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $appSecret;
        $params['sign'] = strtoupper(md5($stringToBeSigned));
        return $params;
    }


    private function getCurrentTimeFormatted()
    {
        return  date("Y-m-d H:i:s") . '.000' . $this->getStandardOffsetUTC(date_default_timezone_get());
    }

    private function getStandardOffsetUTC($timezone)
    {
        if ($timezone == 'UTC') {
            return '+0000';
        } else {
            $timezone = new \DateTimeZone($timezone);
            $transitions = array_slice($timezone->getTransitions(), -3, null, true);

            foreach (array_reverse($transitions, true) as $transition) {
                if ($transition['isdst'] == 1) {
                    continue;
                }

                return sprintf('%+03d%02u', $transition['offset'] / 3600, abs($transition['offset']) % 3600 / 60);
            }

            return false;
        }
    }
}
