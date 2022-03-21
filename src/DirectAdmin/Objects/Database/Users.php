<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omines\DirectAdmin\Objects\Database;

use Omines\DirectAdmin\Objects\BaseObject;
use Omines\DirectAdmin\Objects\Database;

/**
 * AccessHost.
 */
class Users extends BaseObject
{
    /** @var Database $database */
    protected $database;

    /**
     * @param string   $host
     */
    public function __construct($user, Database $database)
    {
        parent::__construct($user, $database->getContext());
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
