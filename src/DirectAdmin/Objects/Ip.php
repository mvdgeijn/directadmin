<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Objects;

use Mvdgeijn\DirectAdmin\Context\AdminContext;
use Mvdgeijn\DirectAdmin\Context\UserContext;
use Mvdgeijn\DirectAdmin\Objects\Users\Reseller;

/**
 * Database.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class Ip extends BaseObject
{
    private string $gateway;

    private bool $global;

    private string $netmask;

    private string $reseller;

    private string $linked_ip;

    private string $ns;

    private string $status;

    private string $value;

    /**
     * Database constructor.
     *
     * @param string $name Name of the database
     * @param Reseller $owner Database owner
     * @param UserContext $context Context within which the object is valid
     */
    public function __construct($name, AdminContext $context, string $content)
    {
        parent::__construct($name, $context);

        parse_str( $content, $params );

        $this->gateway = $params['gateway'];
        $this->global = $params['global'] == "yes";
        $this->netmask = $params['netmask'];
        $this->ns = $params['ns'];
        $this->reseller = $params['reseller'];
        $this->status = $params['status'];
        $this->value = $params['value'];

        if( isset($params['linked_ips']) )
            $this->linked_ip = $params['linked_ips'];
    }

    /**
     * @return Reseller
     */
    public function getIp()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getGateway(): string
    {
        return $this->gateway;
    }

    /**
     * @param string $gateway
     * @return Ip
     */
    public function setGateway(string $gateway): Ip
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGlobal(): bool
    {
        return $this->global;
    }

    /**
     * @param bool $global
     * @return Ip
     */
    public function setGlobal(bool $global): Ip
    {
        $this->global = $global;
        return $this;
    }

    /**
     * @return string
     */
    public function getNetmask(): string
    {
        return $this->netmask;
    }

    /**
     * @param string $netmask
     * @return Ip
     */
    public function setNetmask(string $netmask): Ip
    {
        $this->netmask = $netmask;
        return $this;
    }

    /**
     * @return string
     */
    public function getNs(): string
    {
        return $this->ns;
    }

    /**
     * @param string $ns
     * @return Ip
     */
    public function setNs(string $ns): Ip
    {
        $this->ns = $ns;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Ip
     */
    public function setStatus(string $status): Ip
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Ip
     */
    public function setValue(string $value): Ip
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getReseller(): string
    {
        return $this->reseller;
    }

    /**
     * @param string $reseller
     * @return Ip
     */
    public function setReseller(string $reseller): Ip
    {
        $this->reseller = $reseller;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkedIp(): string
    {
        return $this->linked_ip;
    }

    /**
     * @param string $linked_ip
     * @return Ip
     */
    public function setLinkedIp(string $linked_ip): Ip
    {
        $this->linked_ip = $linked_ip;

        return $this;
    }
}
