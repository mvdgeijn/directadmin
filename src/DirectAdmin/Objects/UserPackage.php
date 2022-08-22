<?php
/*
 * Copyright (c) 2022 by bHosted.nl B.V.  - All rights reserved
 */

namespace Mvdgeijn\DirectAdmin\Objects;

use Mvdgeijn\DirectAdmin\Context\ResellerContext;

/**
 * Database.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class UserPackage extends BaseObject
{
    private bool $aftp;

    private int $bandwidth;

    private bool $catchall;

    private int $cgi;

    private bool $cron;

    private bool $dnscontrol;

    private string $domainptr;

    private string $ftp;

    private string $inode;

    private string $language;

    private bool $login_keys;

    private string $mysql;

    private string $nemailf;

    private string $nemailml;

    private string $nemailr;

    private string $nemails;

    private string $nsubdomains;

    private bool $php;

    private int $quota;

    private string $skin;

    private bool $spam;

    private bool $ssh;

    private bool $ssl;

    private bool $suspend_at_limit;

    private bool $sysinfo;

    private string $vdomains;

    /**
     * Package constructor.
     *
     * @param string $name Name of the database
     * @param ResellerContext $context Context within which the object is valid
     */
    public function __construct(string $name, ResellerContext $context, string $content )
    {
        parent::__construct($name, $context);

        parse_str( $content, $params );

        $this->aftp = $params['aftp'] == "ON";
        $this->bandwidth = (int) $params['bandwidth'];
        $this->catchall = $params['catchall'] == "ON";
        $this->cgi = $params['cgi'] == "ON";
        $this->cron = $params['cron'] == "ON";
        $this->dnscontrol = $params['dnscontrol'] == "ON";
        $this->domainptr = $params['domainptr'];
        $this->ftp = $params['ftp'];
        $this->inode = $params['inode'];
        $this->language = $params['language'];
        $this->login_keys = $params['login_keys'] == "ON";
        $this->mysql = $params['mysql'];
        $this->nemailf = $params['nemailf'];
        $this->nemailml = $params['nemailml'];
        $this->nemailr = $params['nemailr'];
        $this->nemails = $params['nemails'];
        $this->nsubdomains = $params['nsubdomains'];
        $this->php = $params['php'] == "ON";
        $this->quota = (int) $params['quota'];
        $this->skin = $params['skin'];
        $this->spam = $params['spam'] == "ON";
        $this->ssh = $params['ssh'] == "ON";
        $this->ssl = $params['ssl'] == "ON";
        $this->suspend_at_limit = $params['suspend_at_limit'] == "ON";
        $this->sysinfo = $params['sysinfo'] == "ON";
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
     * @return UserPackage
     */
    public function setAftp(bool $aftp): UserPackage
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
     * @return UserPackage
     */
    public function setBandwidth(int $bandwidth): UserPackage
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
     * @return UserPackage
     */
    public function setCatchall(bool $catchall): UserPackage
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
     * @return UserPackage
     */
    public function setCgi(int $cgi): UserPackage
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
     * @return UserPackage
     */
    public function setCron(bool $cron): UserPackage
    {
        $this->cron = $cron;
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
     * @return UserPackage
     */
    public function setDnscontrol(bool $dnscontrol): UserPackage
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
     * @return UserPackage
     */
    public function setDomainptr(string $domainptr): UserPackage
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
     * @return UserPackage
     */
    public function setFtp(string $ftp): UserPackage
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
     * @return UserPackage
     */
    public function setInode(string $inode): UserPackage
    {
        $this->inode = $inode;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return UserPackage
     */
    public function setLanguage(string $language): UserPackage
    {
        $this->language = $language;
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
     * @return UserPackage
     */
    public function setLoginKeys(bool $login_keys): UserPackage
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
     * @return UserPackage
     */
    public function setMysql(string $mysql): UserPackage
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
     * @return UserPackage
     */
    public function setNemailf(string $nemailf): UserPackage
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
     * @return UserPackage
     */
    public function setNemailml(string $nemailml): UserPackage
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
     * @return UserPackage
     */
    public function setNemailr(string $nemailr): UserPackage
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
     * @return UserPackage
     */
    public function setNemails(string $nemails): UserPackage
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
     * @return UserPackage
     */
    public function setNsubdomains(string $nsubdomains): UserPackage
    {
        $this->nsubdomains = $nsubdomains;
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
     * @return UserPackage
     */
    public function setPhp(bool $php): UserPackage
    {
        $this->php = $php;
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
     * @return UserPackage
     */
    public function setQuota(int $quota): UserPackage
    {
        $this->quota = $quota;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkin(): string
    {
        return $this->skin;
    }

    /**
     * @param string $skin
     * @return UserPackage
     */
    public function setSkin(string $skin): UserPackage
    {
        $this->skin = $skin;
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
     * @return UserPackage
     */
    public function setSpam(bool $spam): UserPackage
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
     * @return UserPackage
     */
    public function setSsh(bool $ssh): UserPackage
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
     * @return UserPackage
     */
    public function setSsl(bool $ssl): UserPackage
    {
        $this->ssl = $ssl;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuspendAtLimit(): bool
    {
        return $this->suspend_at_limit;
    }

    /**
     * @param bool $suspend_at_limit
     * @return UserPackage
     */
    public function setSuspendAtLimit(bool $suspend_at_limit): UserPackage
    {
        $this->suspend_at_limit = $suspend_at_limit;
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
     * @return UserPackage
     */
    public function setSysinfo(bool $sysinfo): UserPackage
    {
        $this->sysinfo = $sysinfo;
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
     * @return UserPackage
     */
    public function setVdomains(string $vdomains): UserPackage
    {
        $this->vdomains = $vdomains;
        return $this;
    }

}
