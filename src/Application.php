<?php

/*
 * This file is part of the zzbzh/jd-client.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace JosClient;

use JosClient\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property \JosClient\Jos\Client             $jos
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Jos\ServiceProvider::class,
    ];
}
