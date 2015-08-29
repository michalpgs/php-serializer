<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/29/15
 * Time: 12:42 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Serializer;

use NilPortugues\Serializer\Strategy\JsonStrategy;

class JsonSerializer extends Serializer
{
    public function __construct()
    {
        parent::__construct(new JsonStrategy());
    }
}
