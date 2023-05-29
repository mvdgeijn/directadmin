<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Objects;

use DateTime;
use Mvdgeijn\DirectAdmin\DirectAdmin;
use Mvdgeijn\DirectAdmin\Context\UserContext;
use Mvdgeijn\DirectAdmin\Objects\Users\User;

/**
 * Database.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class LoginKey extends BaseObject
{
    const CACHE_ACCESS_HOSTS = 'access_hosts';

    /** @var User */
    private $owner;

    /** @var string */
    private $keyname;

    /** @var string */
    private $keyvalue;

    /**
     * LoginKey constructor.
     *
     * @param string $keyname Name of the login key
     * @param User $owner Login key owner
     * @param UserContext $context Context within which the object is valid
     */
    public function __construct($keyname, $keyvalue, User $owner, UserContext $context)
    {
        parent::__construct($keyname, $context);
        $this->owner = $owner;
        $this->keyname = $keyname;
        $this->keyvalue = $keyvalue;
    }

    /**
     * Creates a new login key under the specified user.
     *
     * @param User $user Owner of the login key
     * @param string $token
     * @return LoginKey Newly created login key
     */
    public static function create(User $user)
    {
        $date = new DateTime();
        $date->modify('+120 minutes');
        $keyvalue = hash('sha256', microtime() . $user->getName());
        $keyname = 'Key' . $date->getTimestamp();

        $options = [
            'action' => 'create',
            'keyname' => $keyname,
            'key' => $keyvalue,
            'key2' => $keyvalue,
            'hour' => $date->format('G'),
            'minute' => $date->format('i'),
            'month' => $date->format('n'),
            'day' => $date->format('j'),
            'year' => $date->format('Y'),
            'max_uses' => 0,
            'ips' => '',
            'passwd' => $user->getContext()->getPassword(),
            'clear_key' => 'yes',
            'allow_htm' => 'yes',
            'select_allow0' => 'ALL_USER',
            'select_allow1' => 'CMD_LOGIN',
            'select_allow2' => 'CMD_LOGOUT',
            'select_allow3' => 'CMD_PLUGINS',      // nodig voor Installatron
            'select_allow4' => 'CMD_API_DATABASES', // nodig voor Installatron (aanmaken db's voor bv. Wordpress)
        ];
        
        if( $user->getType() == DirectAdmin::ACCOUNT_TYPE_RESELLER ) {
            $options['select_allow5'] = 'ALL_RESELLER';
        }
        
        $user->getContext()->invokeApiPost('LOGIN_KEYS', $options);

        return new self($keyname, $keyvalue, $user, $user->getContext());
    }

    public function getKeyName(): string
    {
        return $this->keyname;
    }

    public function getKeyValue(): string
    {
        return $this->keyvalue;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }
}
