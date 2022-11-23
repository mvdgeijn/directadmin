<?php

/*
 * DirectAdmin API Client
 * (c) bHosted.nl B.V.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Objects;

use Mvdgeijn\DirectAdmin\Context\UserContext;
use Mvdgeijn\DirectAdmin\Objects\Users\User;

/**
 * DnsMx.
 *
 * @author Marc van de Geijn <marc@bhosted.nl>
 */
class DnsMx extends BaseObject
{
    private string $internal;

    /**
     * @param $name
     * @param UserContext $context
     * @param $data
     */
    public function __construct($name, UserContext $context, $data)
    {
        parent::__construct($name, $context);

        $this->internal = $data['internal'];
    }

    /**
     * @return string
     */
    public function isInternal(): bool
    {
        return $this->internal == "yes";
    }

    /**
     * @param string $internal
     * @return DnsMx
     */
    public function setInternal(bool $internal): DnsMx
    {
        $this->internal = $internal ? "yes" : "no";

        return $this;
    }
}
