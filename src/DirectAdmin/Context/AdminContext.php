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
use Mvdgeijn\DirectAdmin\Objects\Ip;
use Mvdgeijn\DirectAdmin\Objects\ResellerPackage;
use Mvdgeijn\DirectAdmin\Objects\Users\Admin;
use Mvdgeijn\DirectAdmin\Objects\Users\Reseller;
use Mvdgeijn\DirectAdmin\Objects\Users\User;

/**
 * Context for administrator functions.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class AdminContext extends ResellerContext
{
    /**
     * Creates a new Admin level account.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @return Admin The newly created Admin
     */
    public function createAdmin($username, $password, $email)
    {
        return $this->createAccount($username, $password, $email, [], 'ACCOUNT_ADMIN', Admin::class);
    }

    /**
     * Creates a new Reseller level account.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $domain
     * @param string|array $package Either a package name or an array of options for custom
     * @param string $ip shared, sharedreseller or assign. Defaults to 'shared'
     * @return Reseller
     * @url http://www.directadmin.com/api.html#create for options to use.
     */
    public function createReseller($username, $password, $email, $domain, $package = [], $ip = 'shared')
    {
        $options = array_merge(
            ['ip' => $ip, 'domain' => $domain, 'serverip' => 'ON', 'dns' => 'OFF'],
            is_array($package) ? $package : ['package' => $package]
        );
        return $this->createAccount($username, $password, $email, $options, 'ACCOUNT_RESELLER', Reseller::class);
    }

    public function checkIfUserExists( $user ): bool
    {
        $result = $this->invokeApiGet('USER_EXISTS', ['user' => $user]);

        return( $result['error'] == "0" && $result['exists'] != "0" );
    }

    /**
     * Returns a list of known admins on the server.
     *
     * @return Admin[]
     */
    public function getAdmins()
    {
        return BaseObject::toObjectArray($this->invokeApiGet('SHOW_ADMINS'), Admin::class, $this);
    }

    /**
     * Returns a full list of all accounts of any type on the server.
     *
     * @return User[]
     */
    public function getAllAccounts()
    {
        $accounts = array_merge($this->getAllUsers(), $this->getResellers(), $this->getAdmins());
        ksort($accounts);
        return $accounts;
    }

    /**
     * Returns a full list of all users on the server, so no resellers or admins.
     *
     * @return User[]
     */
    public function getAllUsers()
    {
        return BaseObject::toObjectArray($this->invokeApiGet('SHOW_ALL_USERS'), User::class, $this);
    }

    /**
     * Returns a specific reseller by name, or NULL if there is no reseller by this name.
     *
     * @param string $username
     * @return null|Reseller
     */
    public function getReseller($username)
    {
        $resellers = $this->getResellers();
        return $resellers[$username] ?? null;
    }

    /**
     * Returns the list of known resellers.
     *
     * @return Reseller[]
     */
    public function getResellers()
    {
        return BaseObject::toObjectArray($this->invokeApiGet('SHOW_RESELLERS'), Reseller::class, $this);
    }

    /**
     * Returns the list of reseller packages
     *
     * @return ResellerPackage[]
     */
    public function getResellerPackages()
    {
        return BaseObject::toRichObjectArray($this->invokeApiGet('PACKAGES_RESELLER', ['full' => 'yes']), ResellerPackage::class, $this);
    }

    /**
     * Returns the reseller package
     *
     * @return ResellerPackage
     */
    public function getResellerPackage( string $package )
    {
        $packages = $this->getResellerPackages();
        return $packages[$package] ?? null;
    }

   /**
     * Change the reseller package
     * 
     * @param string $reseller
     * @param string $package
     * @return array
     */
    public function setResellerPackage( string $reseller, string $package )
    {
        return $this->invokeApiGet('MODIFY_RESELLER',['action' => 'package', 'user' => $reseller, 'package' => $package ]);
    }
    
    /**
     * Returns the list with all IPs
     *
     * @return array
     */
    public function getIPs()
    {
        return BaseObject::toRichObjectArray($this->invokeApiGet('IP_MANAGER'), Ip::class, $this );
    }

    /**
     * @param string $ip
     * @param string $netmask
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addIP( string $ip, string $netmask ): bool
    {
        try {
            $result = $this->invokeApiPost('IP_MANAGER', ['action' => 'add', 'ip' => $ip, 'netmask' => $netmask]);

            $result = $result['error'] == "0";
        } catch( DirectAdminException|GuzzleException $e ) {
            error_log( $e->getMessage() );
            $result = false;
        }

        return $result;
    }

    /**
     * Delete the free and unassigned IP('s)
     *
     * @param string|array $ips
     * @return bool
     */
    public function deleteIPs(string|array $ips ): bool
    {
        try {
            $params = [
                'action' => 'select',
                'delete' => "1"
            ];

            if( is_string( $ips ) ) {
                $params["select0"] = $ips;
            }
            else
            {
                $i = 0;
                foreach ($ips as $ip) {
                    $params["select$i"] = $ip;
                    $i++;
                }
            }

            $result = $this->invokeApiPost(
                'IP_MANAGER',
                $params);

            $result = $result['error'] == "0";
        } catch( DirectAdminException|GuzzleException $e ) {
            error_log( $e->getMessage() );
            $result = false;
        }

        return $result;
    }

    /**
     * Link the subIP to the mainIP
     *
     * @param string $mainIp
     * @param string $subIp
     * @param string $dns
     * @param bool $apache
     * @param bool $apply
     * @param bool $background
     * @return bool
     */
    public function linkIP(string $mainIp, string $subIp, string $dns = "yes", bool $apache = true, bool $apply = true, bool $background = true ): bool
    {
        try {
            $params = [
                'action' => 'add',
                'link' => 'add',
                'ip' => $mainIp,
                'ip_to_link' => $subIp,
                'dns' => $dns ? "yes" : "no",
                'apache' => $apache ? "yes" : "no",
                'apply' => $apply ? "yes" : "no",
                'background' => $background ? "yes" : "no"
            ];

            $result = $this->invokeApiPost(
                'IP_MANAGER_DETAILS',
                $params);

            $result = $result['error'] == "0";
        } catch( DirectAdminException|GuzzleException $e ) {
            error_log( $e->getMessage() );
            $result = false;
        }

        return $result;
    }

    /**
     * Unlink IP(s) from the main IP
     *
     * @param string $mainIp
     * @param string|array $subIps
     * @param bool $flushDns
     * @param bool $rewriteApache
     * @return bool
     */
    public function unlinkIP(string $mainIp, string|array $subIps, bool $flushDns = true, bool $rewriteApache = true ): bool
    {
        try {
            $params = [
                'action' => 'select',
                'remove' => 'Un_Link',
                'ip' => $mainIp,
                'flush_dns' => $flushDns ? "yes" : "no",
                'rewrite_apache' => $rewriteApache ? "yes" : "no"
            ];

            if( is_string( $subIps ) ) {
                $params["select0"] = $subIps;
            }
            else
            {
                $i = 0;
                foreach ($subIps as $ip) {
                    $params["select$i"] = $ip;
                    $i++;
                }
            }

            $result = $this->invokeApiPost(
                'IP_MANAGER_DETAILS',
                $params);

            $result = $result['error'] == "0";
        } catch( DirectAdminException|GuzzleException $e ) {
            error_log( $e->getMessage() );
            $result = false;
        }

        return $result;
    }


    /**
     * Assign IP('s) to reseller
     *
     * @param string $reseller
     * @param string|array $ips
     * @return bool
     */
    public function assignIP(string $reseller, string|array $ips ): bool
    {
        try {
            $params = [
                'action' => 'select',
                'assign' => "1",
                'reseller' => $reseller,
            ];

            if( is_string( $ips ) ) {
                $params["select0"] = $ips;
            }
            else
            {
                $i = 0;
                foreach ($ips as $ip) {
                    $params["select$i"] = $ip;
                    $i++;
                }
            }

            $result = $this->invokeApiPost(
                'IP_MANAGER',
                $params);

            $result = $result['error'] == "0";
        } catch( DirectAdminException|GuzzleException $e ) {
            error_log( $e->getMessage() );
            $result = false;
        }

        return $result;
    }

    /**
     * Returns a new AdminContext acting as the specified admin.
     *
     * @param string $username
     * @param bool $validate Whether to check the admin exists and is an admin
     * @return AdminContext
     */
    public function impersonateAdmin($username, $validate = false)
    {
        return new self($this->getConnection()->loginAs($username), $validate);
    }

    /**
     * Returns a new ResellerContext acting as the specified reseller.
     *
     * @param string $username
     * @param bool $validate Whether to check the reseller exists and is a reseller
     * @return ResellerContext
     */
    public function impersonateReseller($username, $validate = false)
    {
        return new ResellerContext($this->getConnection()->loginAs($username), $validate);
    }
}
