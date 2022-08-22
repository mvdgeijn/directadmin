<?php
/*
 * Copyright (c) 2022 by bHosted.nl B.V.  - All rights reserved
 */

namespace Mvdgeijn\DirectAdmin\Objects;

use Mvdgeijn\DirectAdmin\Context\AdminContext;

/**
 * Database.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class ResellerPackage extends BaseObject
{
    private bool $aftp;

    private int $bandwidth;

    private bool $catchall;

    private int $cgi;

    private bool $cron;

    private bool $dns;

    private bool $dnscontrol;

    private string $domainptr;

    private string $ftp;

    private string $inode;

    private string $ips;

    private bool $login_keys;

    private string $mysql;

    private string $nemailf;

    private string $nemailml;

    private string $nemailr;

    private string $nemails;

    private string $nsubdomains;

    private string $nusers;

    private bool $oversell;

    private bool $php;

    private bool $serverip;

    private bool $spam;

    private bool $ssh;

    private bool $ssl;

    private bool $sysinfo;

    private bool $userssh;

    private string $vdomains;

    private int $quota;

    /**
     * Package constructor.
     *
     * @param string $name Name of the database
     * @param AdminContext $context Context within which the object is valid
     */
    public function __construct(string $name, AdminContext $context, string $content )
    {
        parent::__construct($name, $context);

        parse_str( $content, $params );

        $this->aftp = $params['aftp'] == "ON";
        $this->bandwidth = (int) $params['bandwidth'];
        $this->catchall = $params['catchall'] == "ON";
        $this->cgi = $params['cgi'] == "ON";
        $this->cron = $params['cron'] == "ON";
        $this->dns = $params['dns'] == "ON";
        $this->dnscontrol = $params['dnscontrol'] == "ON";
        $this->domainptr = $params['domainptr'];
        $this->ftp = $params['ftp'];
        $this->inode = $params['inode'];
        $this->ips = $params['ips'];
        $this->login_keys = $params['login_keys'] == "ON";
        $this->mysql = $params['mysql'];
        $this->nemailf = $params['nemailf'];
        $this->nemailml = $params['nemailml'];
        $this->nemailr = $params['nemailr'];
        $this->nemails = $params['nemails'];
        $this->nsubdomains = $params['nsubdomains'];
        $this->nusers = $params['nusers'];
        $this->oversell = $params['oversell'] == "ON";
        $this->php = $params['php'] == "ON";
        $this->quota = (int) $params['quota'];
        $this->serverip = $params['serverip'] == "ON";
        $this->spam = $params['spam'] == "ON";
        $this->ssh = $params['ssh'] == "ON";
        $this->ssl = $params['ssl'] == "ON";
        $this->sysinfo = $params['sysinfo'] == "ON";
        $this->userssh = $params['userssh'] == "ON";
        $this->vdomains = $params['vdomains'];
    }

    /**
     * @return bool
     */
    public function isAftp(): bool
    {
        return $this->aftp;
    }

    /**
     * @param bool $aftp
     * @return ResellerPackage
     */
    public function setAftp(bool $aftp): ResellerPackage
    {
        $this->aftp = $aftp;
        return $this;
    }

    /**
     * @return int
     */
    public function getBandwidth(): int
    {
        return $this->bandwidth;
    }

    /**
     * @param int $bandwidth
     * @return ResellerPackage
     */
    public function setBandwidth(int $bandwidth): ResellerPackage
    {
        $this->bandwidth = $bandwidth;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCatchall(): bool
    {
        return $this->catchall;
    }

    /**
     * @param bool $catchall
     * @return ResellerPackage
     */
    public function setCatchall(bool $catchall): ResellerPackage
    {
        $this->catchall = $catchall;
        return $this;
    }

    /**
     * @return int
     */
    public function getCgi(): int
    {
        return $this->cgi;
    }

    /**
     * @param int $cgi
     * @return ResellerPackage
     */
    public function setCgi(int $cgi): ResellerPackage
    {
        $this->cgi = $cgi;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCron(): bool
    {
        return $this->cron;
    }

    /**
     * @param bool $cron
     * @return ResellerPackage
     */
    public function setCron(bool $cron): ResellerPackage
    {
        $this->cron = $cron;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDns(): bool
    {
        return $this->dns;
    }

    /**
     * @param bool $dns
     * @return ResellerPackage
     */
    public function setDns(bool $dns): ResellerPackage
    {
        $this->dns = $dns;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDnscontrol(): bool
    {
        return $this->dnscontrol;
    }

    /**
     * @param bool $dnscontrol
     * @return ResellerPackage
     */
    public function setDnscontrol(bool $dnscontrol): ResellerPackage
    {
        $this->dnscontrol = $dnscontrol;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomainptr(): string
    {
        return $this->domainptr;
    }

    /**
     * @param string $domainptr
     * @return ResellerPackage
     */
    public function setDomainptr(string $domainptr): ResellerPackage
    {
        $this->domainptr = $domainptr;
        return $this;
    }

    /**
     * @return string
     */
    public function getFtp(): string
    {
        return $this->ftp;
    }

    /**
     * @param string $ftp
     * @return ResellerPackage
     */
    public function setFtp(string $ftp): ResellerPackage
    {
        $this->ftp = $ftp;
        return $this;
    }

    /**
     * @return string
     */
    public function getInode(): string
    {
        return $this->inode;
    }

    /**
     * @param string $inode
     * @return ResellerPackage
     */
    public function setInode(string $inode): ResellerPackage
    {
        $this->inode = $inode;
        return $this;
    }

    /**
     * @return string
     */
    public function getIps(): string
    {
        return $this->ips;
    }

    /**
     * @param string $ips
     * @return ResellerPackage
     */
    public function setIps(string $ips): ResellerPackage
    {
        $this->ips = $ips;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLoginKeys(): bool
    {
        return $this->login_keys;
    }

    /**
     * @param bool $login_keys
     * @return ResellerPackage
     */
    public function setLoginKeys(bool $login_keys): ResellerPackage
    {
        $this->login_keys = $login_keys;
        return $this;
    }

    /**
     * @return string
     */
    public function getMysql(): string
    {
        return $this->mysql;
    }

    /**
     * @param string $mysql
     * @return ResellerPackage
     */
    public function setMysql(string $mysql): ResellerPackage
    {
        $this->mysql = $mysql;
        return $this;
    }

    /**
     * @return string
     */
    public function getNemailf(): string
    {
        return $this->nemailf;
    }

    /**
     * @param string $nemailf
     * @return ResellerPackage
     */
    public function setNemailf(string $nemailf): ResellerPackage
    {
        $this->nemailf = $nemailf;
        return $this;
    }

    /**
     * @return string
     */
    public function getNemailml(): string
    {
        return $this->nemailml;
    }

    /**
     * @param string $nemailml
     * @return ResellerPackage
     */
    public function setNemailml(string $nemailml): ResellerPackage
    {
        $this->nemailml = $nemailml;
        return $this;
    }

    /**
     * @return string
     */
    public function getNemailr(): string
    {
        return $this->nemailr;
    }

    /**
     * @param string $nemailr
     * @return ResellerPackage
     */
    public function setNemailr(string $nemailr): ResellerPackage
    {
        $this->nemailr = $nemailr;
        return $this;
    }

    /**
     * @return string
     */
    public function getNemails(): string
    {
        return $this->nemails;
    }

    /**
     * @param string $nemails
     * @return ResellerPackage
     */
    public function setNemails(string $nemails): ResellerPackage
    {
        $this->nemails = $nemails;
        return $this;
    }

    /**
     * @return string
     */
    public function getNsubdomains(): string
    {
        return $this->nsubdomains;
    }

    /**
     * @param string $nsubdomains
     * @return ResellerPackage
     */
    public function setNsubdomains(string $nsubdomains): ResellerPackage
    {
        $this->nsubdomains = $nsubdomains;
        return $this;
    }

    /**
     * @return string
     */
    public function getNusers(): string
    {
        return $this->nusers;
    }

    /**
     * @param string $nusers
     * @return ResellerPackage
     */
    public function setNusers(string $nusers): ResellerPackage
    {
        $this->nusers = $nusers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOversell(): bool
    {
        return $this->oversell;
    }

    /**
     * @param bool $oversell
     * @return ResellerPackage
     */
    public function setOversell(bool $oversell): ResellerPackage
    {
        $this->oversell = $oversell;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPhp(): bool
    {
        return $this->php;
    }

    /**
     * @param bool $php
     * @return ResellerPackage
     */
    public function setPhp(bool $php): ResellerPackage
    {
        $this->php = $php;
        return $this;
    }

    /**
     * @return bool
     */
    public function isServerip(): bool
    {
        return $this->serverip;
    }

    /**
     * @param bool $serverip
     * @return ResellerPackage
     */
    public function setServerip(bool $serverip): ResellerPackage
    {
        $this->serverip = $serverip;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSpam(): bool
    {
        return $this->spam;
    }

    /**
     * @param bool $spam
     * @return ResellerPackage
     */
    public function setSpam(bool $spam): ResellerPackage
    {
        $this->spam = $spam;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSsh(): bool
    {
        return $this->ssh;
    }

    /**
     * @param bool $ssh
     * @return ResellerPackage
     */
    public function setSsh(bool $ssh): ResellerPackage
    {
        $this->ssh = $ssh;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSsl(): bool
    {
        return $this->ssl;
    }

    /**
     * @param bool $ssl
     * @return ResellerPackage
     */
    public function setSsl(bool $ssl): ResellerPackage
    {
        $this->ssl = $ssl;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSysinfo(): bool
    {
        return $this->sysinfo;
    }

    /**
     * @param bool $sysinfo
     * @return ResellerPackage
     */
    public function setSysinfo(bool $sysinfo): ResellerPackage
    {
        $this->sysinfo = $sysinfo;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUserssh(): bool
    {
        return $this->userssh;
    }

    /**
     * @param bool $userssh
     * @return ResellerPackage
     */
    public function setUserssh(bool $userssh): ResellerPackage
    {
        $this->userssh = $userssh;
        return $this;
    }

    /**
     * @return string
     */
    public function getVdomains(): string
    {
        return $this->vdomains;
    }

    /**
     * @param string $vdomains
     * @return ResellerPackage
     */
    public function setVdomains(string $vdomains): ResellerPackage
    {
        $this->vdomains = $vdomains;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuota(): int
    {
        return $this->quota;
    }

    /**
     * @param int $quota
     * @return ResellerPackage
     */
    public function setQuota(int $quota): ResellerPackage
    {
        $this->quota = $quota;
        return $this;
    }

}
