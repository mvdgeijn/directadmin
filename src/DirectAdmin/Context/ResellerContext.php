<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Context;

use GuzzleHttp\Exception\GuzzleException;
use Mvdgeijn\DirectAdmin\DirectAdminException;
use Mvdgeijn\DirectAdmin\Objects\BaseObject;
use Mvdgeijn\DirectAdmin\Objects\UserPackage;
use Mvdgeijn\DirectAdmin\Objects\Users\User;

/**
 * Context for reseller functions.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class ResellerContext extends UserContext
{
    /**
     * Creates a new user on the server.
     *
     * @param string $username Login for the new user
     * @param string $password Password for the new user
     * @param string $email Email for the new user
     * @param string $domain Default domain for the new user
     * @param string $ip IP for the user
     * @param string|array $package Either a package name or an array of options for custom
     * @return User Newly created user
     * @url http://www.directadmin.com/api.html#create for options to use.
     */
    public function createUser($username, $password, $email, $domain, $ip, $package = [])
    {
        $options = array_merge(
            ['ip' => $ip, 'domain' => $domain],
            is_array($package) ? $package : ['package' => $package]
        );
        return $this->createAccount($username, $password, $email, $options, 'ACCOUNT_USER', User::class);
    }

    /**
     * Internal helper function for creating new accounts.
     *
     * @param string $username Login for the new user
     * @param string $password Password for the new user
     * @param string $email Email for the new user
     * @param array $options List of DA account options to apply
     * @param string $endpoint API endpoint to invoke
     * @param string $returnType Class name that should wrap the resulting account
     * @return object An instance of the type specified in $returnType
     * @throws GuzzleException
     */
    protected function createAccount($username, $password, $email, $options, $endpoint, $returnType)
    {
        $this->invokeApiPost($endpoint, array_merge($options, [
            'action' => 'create',
            'add' => 'Submit',
            'email' => $email,
            'passwd' => $password,
            'passwd2' => $password,
            'username' => $username,
        ]));
        return new $returnType($username, $this);
    }

    /**
     * Deletes a single account.
     *
     * @param string $username Account to delete
     */
    public function deleteAccount($username)
    {
        $this->deleteAccounts([$username]);
    }

    /**
     * Deletes multiple accounts.
     *
     * @param string[] $usernames Accounts to delete
     */
    public function deleteAccounts(array $usernames)
    {
        $options = ['confirmed' => 'Confirm', 'delete' => 'yes'];
        foreach (array_values($usernames) as $idx => $username) {
            $options["select{$idx}"] = $username;
        }
        $this->invokeApiPost('SELECT_USERS', $options);
    }

    /**
     * Suspends a single account.
     *
     * @param string $username Account to suspend
     * @param string $reason (none|abuse|billing|inactive|other|spam|user_bandwidth|user_quota)
     * @throws GuzzleException
     */
    public function suspendAccount(string $username, string $reason = 'none'): void
    {
        $this->suspendAccounts( [$username], true, $reason );
    }
    
    /**
     * Unsuspends a single account.
     *
     * @param string $username Account to delete
     */
    public function unsuspendAccount($username)
    {
        $this->suspendAccounts([$username], false);
    }

    /**
     * Suspends (or unsuspends) multiple accounts.
     *
     * @param string[] $usernames Accounts to (un)suspend
     * @param bool $suspend (true - suspend, false - unsuspend)
     * @param string $reason (none|abuse|billing|inactive|other|spam|user_bandwidth|user_quota)
     * @throws GuzzleException
     */
    public function suspendAccounts( array $usernames, bool $suspend = true, string $reason = 'none' ): void
    {
        $options = [
            'reason' => $reason,
            $suspend ? 'dosuspend' : 'dounsuspend' => 'yes',
        ];
        
        foreach (array_values($usernames) as $idx => $username) {
            $options['select' . $idx] = $username;
        }

        $this->invokeApiPost('SELECT_USERS', $options);
    }
    
    /**
     * Unsuspends multiple accounts.
     *
     * @param string[] $usernames Accounts to delete
     */
    public function unsuspendAccounts(array $usernames)
    {
        $this->suspendAccounts($usernames, false);
    }

    /**
     * Returns all IPs available to this reseller.
     *
     * @return array List of IPs as strings
     */
    public function getIPs()
    {
        return $this->invokeApiGet('SHOW_RESELLER_IPS');
    }

    /**
     * Returns a single user by name.
     *
     * @param string $username
     * @return User|null
     */
    public function getUser($username)
    {
        $resellers = $this->getUsers();
        return isset($resellers[$username]) ? $resellers[$username] : null;
    }

    /**
     * Returns all users for this reseller.
     *
     * @return User[] Associative array of users
     */
    public function getUsers()
    {
        return BaseObject::toObjectArray($this->invokeApiGet('SHOW_USERS'), User::class, $this);
    }

    /**
     * Set the password for the user. It can be set if the system, ftp and/or database passwords have to be reset
     * all at once to the same password. Default is all passwords are set to the new password
     *
     * @param string $user
     * @param string $password
     * @param bool $system
     * @param bool $ftp
     * @param bool $database
     * @return array
     * @throws GuzzleException
     */
    public function setUserPassword( string $user, string $password, bool $system = true, bool $ftp = true, bool $database = true ): array
    {
        return $this->invokeApiPost('USER_PASSWD', [
            'username' => $user,
            'passwd' => $password,
            'passwd2' => $password,
            'system' => $system ? 'yes' : 'no',
            'ftp' => $ftp ? 'yes' : 'no',
            'database' => $database ? 'yes' : 'no'
        ]);
    }
    
    /**
     * Get the user that belongs to the domain
     *
     * @param $domain
     * @return array|null
     */
    public function getDomainOwner( $domain )
    {
        try {
            return $this->invokeApiGet('DOMAIN_OWNERS', ['domain' => $domain ] );
        } catch( DirectAdminException $e ) {
            return null;
        }
    }

    /**
     * Get the users for all domains
     *
     * @param $domain
     * @return array
     */
    public function getDomainOwners( )
    {
        return $this->invokeApiGet('DOMAIN_OWNERS' );
    }

    /**
     * get reseller statistics / active package settings
     *
     * @return array Associative array of reseller statistics / active package settings
     */
    public function getStatistics()
    {
        return $this->invokeApiGet('RESELLER_STATS');
    }

    /**
     * get reseller usage (disk, bandwidth, etc.)
     *
     * @return array Associative array with reseller usage
     */
    public function getUsage()
    {
        return $this->invokeApiGet('RESELLER_STATS', ['type' => 'usage']);
    }

    /**
     * Impersonates a user, allowing the reseller/admin to act on their behalf.
     *
     * @param string $username Login of the account to impersonate
     * @param bool $validate Whether to check the user exists and is a user
     * @return UserContext
     */
    public function impersonateUser($username, $validate = false)
    {
        return new UserContext($this->getConnection()->loginAs($username), $validate);
    }

    /**
     * Returns the list of user packages for this reseller
     *
     * @return UserPackage[]
     */
    public function getUserPackages()
    {
        return BaseObject::toRichObjectArray($this->invokeApiGet('PACKAGES_USER', ['full' => 'yes']), UserPackage::class, $this);
    }

    /**
     * Returns the requested user package for this reseller
     *
     * @return UserPackage
     */
    public function getUserPackage( string $package )
    {
        $packages = $this->getUserPackages();
        return $packages[$package] ?? null;
    }

    /**
     * Change the user package
     *
     * @param string $user
     * @param string $package
     * @return void
     */
    public function setUserPackage( string $user, string $package )
    {
        return $this->invokeApiGet('MODIFY_USER',['action' => 'package', 'user' => $user, 'package' => $package ]);
    }

    /**
     * Set the IP type (shared or not)
     *
     * @param string $ip
     * @param bool   $share
     * @return array
     */
    public function setIpConfig( string $ip, bool $share = true )
    {
        return $this->invokeApiPost('IP_CONFIG',['action' => 'select', 'select0' => $ip, 'share' => $share ? "yes" : "no" ]);
    }    
}
