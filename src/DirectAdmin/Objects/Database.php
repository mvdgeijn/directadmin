<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Objects;

use Mvdgeijn\DirectAdmin\Context\UserContext;
use Mvdgeijn\DirectAdmin\DirectAdminException;
use Mvdgeijn\DirectAdmin\Objects\Users\User;

/**
 * Database.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class Database extends BaseObject
{
    const CACHE_ACCESS_HOSTS   = 'access_hosts';
    const CACHE_DATABASE_USERS = 'database_users';

    /** @var User */
    private $owner;

    /** @var string */
    private $databaseName;

    /**
     * Database constructor.
     *
     * @param string $name Name of the database
     * @param User $owner Database owner
     * @param UserContext $context Context within which the object is valid
     */
    public function __construct($name, User $owner, UserContext $context)
    {
        parent::__construct($name, $context);
        $this->owner = $owner;
        $this->databaseName = $this->owner->getUsername() . '_' . $this->getName();
    }

    /**
     * Creates a new database under the specified user.
     *
     * @param User $user Owner of the database
     * @param string $name Database name, without <user>_ prefix
     * @param string $username Username to access the database with, without <user>_ prefix
     * @param string|null $password Password, or null if database user already exists
     * @return Database Newly created database
     */
    public static function create(User $user, $name, $username, $password)
    {
        $options = [
            'action' => 'create',
            'name' => $name,
        ];
        if (!empty($password)) {
            $options += ['user' => $username, 'passwd' => $password, 'passwd2' => $password];
        } else {
            $options += ['userlist' => $username];
        }
        $user->getContext()->invokeApiPost('DATABASES', $options);
        return new self($name, $user, $user->getContext());
    }

    /**
     * Deletes this database from the user.
     */
    public function delete()
    {
        $this->getContext()->invokeApiPost('DATABASES', [
            'action' => 'delete',
            'select0' => $this->getDatabaseName(),
        ]);
        $this->getContext()->getContextUser()->clearCache();
    }

    /**
     * @return Database\AccessHost[]
     */
    public function getAccessHosts()
    {
        return $this->getCache(self::CACHE_ACCESS_HOSTS, function () {
            $accessHosts = $this->getContext()->invokeApiGet('DATABASES', [
                'action' => 'accesshosts',
                'db' => $this->getDatabaseName(),
            ]);

            return array_map(function ($name) {
                return new Database\AccessHost($name, $this);
            }, $accessHosts);
        });
    }

    /**
     * @return Database\Users[]
     */
    public function getUsers()
    {
        return $this->getCache(self::CACHE_DATABASE_USERS, function () {
            $users = $this->getContext()->invokeApiGet('DATABASES', [
                'action' => 'users',
                'db' => $this->getDatabaseName(),
            ]);

            return array_map(function ($name) {
                return new Database\Users($name, $this);
            }, $users);
        });
    }

    public function getQuota()
    {
        $quotas = $this->getContext()->getContextUser()->getDatabaseQuotas();

        if( isset( $quotas[$this->getName()] ) ) {
            return $quotas[$this->getName()];
        } else {
            throw new DirectAdminException('Username incorrect on database ' . $this->getDatabaseName() );
        }
    }

    /**
     * @param string $name
     * @return Database\AccessHost
     */
    public function createAccessHost($name)
    {
        $accessHost = Database\AccessHost::create($this, $name);
        $this->getContext()->getContextUser()->clearCache();
        return $accessHost;
    }

    /**
     * @return string Name of the database
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getDatabaseShortName()
    {
        return $this->getName();
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
