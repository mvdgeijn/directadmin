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

}
