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

}
