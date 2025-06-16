<?php

namespace ProductBundle\Exception;

/**
 * 广告法极限词异常
 */
class AdvertisementLimitException extends \Exception
{
    /**
     * @var string[] 触及的极限词
     */
    public $words = [];
}
