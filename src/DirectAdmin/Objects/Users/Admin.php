<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Objects\Users;

use Mvdgeijn\DirectAdmin\Context\AdminContext;
use Mvdgeijn\DirectAdmin\Context\UserContext;
use Mvdgeijn\DirectAdmin\Context\BaseContext;
use Mvdgeijn\DirectAdmin\DirectAdminException;

/**
 * Admin.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class Admin extends Reseller
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name, UserContext $context, $config = null)
    {
        parent::__construct($name, $context, $config);
    }

    /**
     * @return AdminContext
     */
    public function impersonate(): BaseContext
    {
        /** @var AdminContext $context */
        if (!($context = $this->getContext()) instanceof AdminContext) {
            throw new DirectAdminException('You need to be an admin to impersonate another admin');
        }
        return $context->impersonateAdmin($this->getUsername());
    }
}
