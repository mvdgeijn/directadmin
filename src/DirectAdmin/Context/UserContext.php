<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Context;

use Mvdgeijn\DirectAdmin\DirectAdmin;
use Mvdgeijn\DirectAdmin\DirectAdminException;
use Mvdgeijn\DirectAdmin\Objects\Domain;
use Mvdgeijn\DirectAdmin\Objects\Users\User;
use Mvdgeijn\DirectAdmin\Objects\FileManager;

/**
 * Context for user functions.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class UserContext extends BaseContext
{
    /** @var User */
    private $user;

    /**
     * Constructs the object.
     *
     * @param DirectAdmin $connection A prepared connection
     * @param bool $validate Whether to check if the connection matches the context
     */
    public function __construct(DirectAdmin $connection, $validate = false)
    {
        parent::__construct($connection);
        if ($validate) {
            $classMap = [
                DirectAdmin::ACCOUNT_TYPE_ADMIN => AdminContext::class,
                DirectAdmin::ACCOUNT_TYPE_RESELLER => ResellerContext::class,
                DirectAdmin::ACCOUNT_TYPE_USER => self::class,
            ];
            if ($classMap[$this->getType()] != get_class($this)) {
                /* @codeCoverageIgnoreStart */
                throw new DirectAdminException('Validation mismatch on context construction');
                /* @codeCoverageIgnoreEnd */
            }
        }
    }

    /**
     * Returns the type of the account (user/reseller/admin).
     *
     * @return string One of the DirectAdmin::ACCOUNT_TYPE_ constants describing the type of underlying account
     */
    public function getType()
    {
        return $this->getContextUser()->getType();
    }

    /**
     * Returns the actual user object behind the context.
     *
     * @return User The user object behind the context
     */
    public function getContextUser()
    {
        if (!isset($this->user)) {
            $this->user = User::fromConfig($this->invokeApiGet('SHOW_USER_CONFIG'), $this);
        }

        return $this->user;
    }

    /**
     * @return array
     */
    public function getUsage()
    {
        return $this->getContextUser()->getUsage();
    }

    /**
     * @param $domainName
     * @return array|null
     */
    public function getDnsMx( $domainName )
    {
        return $this->getContextUser()->getDnsMx( $domainName );
    }

    /**
     * Returns a domain managed by the current user.
     *
     * @param string $domainName The requested domain name
     * @return Domain|null The domain if found, or NULL if it does not exist
     */
    public function getDomain( $domainName )
    {
        return $this->getContextUser()->getDomain($domainName);
    }

    /**
     * Returns a full list of the domains managed by the current user.
     *
     * @return Domain[]
     */
    public function getDomains()
    {
        return $this->getContextUser()->getDomains();
    }

    /**
     * @return FileManager
     */
    public function getFileManager( )
    {
        return new FileManager( $this );
    }
    
    /**
     * Returns the username of the current context.
     *
     * @return string Username for the current context
     */
    public function getUsername()
    {
        return $this->getConnection()->getUsername();
    }
}
