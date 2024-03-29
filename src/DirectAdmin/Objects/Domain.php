<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin\Objects;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Query;
use Mvdgeijn\DirectAdmin\Context\UserContext;
use Mvdgeijn\DirectAdmin\DirectAdminException;
use Mvdgeijn\DirectAdmin\Objects\Domains\Subdomain;
use Mvdgeijn\DirectAdmin\Objects\Email\Forwarder;
use Mvdgeijn\DirectAdmin\Objects\Email\Mailbox;
use Mvdgeijn\DirectAdmin\Objects\Users\User;
use Mvdgeijn\DirectAdmin\Utility\Conversion;

/**
 * Encapsulates a domain and its derived objects, like aliases, pointers and mailboxes.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class Domain extends BaseObject
{
    const CACHE_FORWARDERS = 'forwarders';
    const CACHE_MAILBOXES = 'mailboxes';
    const CACHE_SUBDOMAINS = 'subdomains';

    const CATCHALL_BLACKHOLE = ':blackhole:';
    const CATCHALL_FAIL = ':fail:';

    /** @var string */
    private $domainName;

    /** @var User */
    private $owner;

    /** @var string[] */
    private $aliases;

    /** @var string[] */
    private $pointers;

    /** @var float */
    private $bandwidthUsed;

    /** @var float|null */
    private $bandwidthLimit;

    /** @var float */
    private $diskUsage;

    /** @var string */
    private $ssl;

    /** @var string */
    private $php;

    /** @var string */
    private $suspended;

    /** @var string */
    private $localMail;

    /**
     * Construct the object.
     *
     * @param string $name The domain name
     * @param UserContext $context The owning user context
     * @param string|array $config The basic config string as returned by CMD_API_ADDITIONAL_DOMAINS
     */
    public function __construct($name, UserContext $context, $config)
    {
        parent::__construct($name, $context);

        $this->setConfig($context, is_array($config ) ? $config : Query::parse($config ));
    }

    /**
     * Creates a new domain under the specified user.
     *
     * @param User $user Owner of the domain
     * @param string $domainName Domain name to create
     * @param float|null $bandwidthLimit Bandwidth limit in MB, or NULL to share with account
     * @param float|null $diskLimit Disk limit in MB, or NULL to share with account
     * @param bool|null $ssl Whether SSL is to be enabled, or NULL to fallback to account default
     * @param bool|null $php Whether PHP is to be enabled, or NULL to fallback to account default
     * @param bool|null $cgi Whether CGI is to be enabled, or NULL to fallback to account default
     * @return Domain The newly created domain
     * @throws GuzzleException
     */
    public static function create(User $user, $domainName, $bandwidthLimit = null, $diskLimit = null, $ssl = null, $php = null, $cgi = null): Domain
    {
        $options = [
            'action' => 'create',
            'domain' => $domainName,
            (isset($bandwidthLimit) ? 'bandwidth' : 'ubandwidth') => $bandwidthLimit,
            (isset($diskLimit) ? 'quota' : 'uquota') => $diskLimit,
            'ssl' => Conversion::onOff($ssl, $user->hasSSL()),
            'php' => Conversion::onOff($php, $user->hasPHP()),
            'cgi' => Conversion::onOff($cgi, $user->hasCGI()),
        ];
        $user->getContext()->invokeApiPost('DOMAIN', $options);
        $config = $user->getContext()->invokeApiGet('ADDITIONAL_DOMAINS');
        return new self($domainName, $user->getContext(), $config[$domainName]);
    }

    /**
     * Creates a new email forwarder.
     *
     * @param string $prefix Part of the email address before the @
     * @param string|string[] $recipients One or more recipients
     * @return Forwarder The newly created forwarder
     */
    public function createForwarder($prefix, $recipients)
    {
        return Forwarder::create($this, $prefix, $recipients);
    }

    /**
     * Creates a new mailbox.
     *
     * @param string $prefix Prefix for the account
     * @param string $password Password for the account
     * @param int|null $quota Quota in megabytes, or zero/null for unlimited
     * @param int|null $sendLimit Send limit, or 0 for unlimited, or null for system default
     * @return Mailbox The newly created mailbox
     */
    public function createMailbox($prefix, $password, $quota = null, $sendLimit = null)
    {
        return Mailbox::create($this, $prefix, $password, $quota, $sendLimit);
    }

    /**
     * Creates a pointer or alias.
     *
     * @param string $domain
     * @param bool $alias
     */
    public function createPointer($domain, $alias = false)
    {
        $parameters = [
            'domain' => $this->domainName,
            'from' => $domain,
            'action' => 'add',
        ];
        if ($alias) {
            $parameters['alias'] = 'yes';
            $list = &$this->aliases;
        } else {
            $list = &$this->pointers;
        }
        $this->getContext()->invokeApiPost('DOMAIN_POINTER', $parameters);
        $list[] = $domain;
        $list = array_unique($list);
    }

    /**
     * Upload the private and public key
     *
     * @param string $sslPrivateAndPublicPem
     * @return array
     * @throws GuzzleException
     */
    public function uploadSslPrivatePublic(string $private, string $public )
    {
        $parameters = [
            'domain' => $this->domainName,
            'type' => 'paste',
            'action' => 'save',
            'certificate' => $private . "\n" . $public
        ];

        $response = $this->getContext()->invokeApiPost('SSL', $parameters );

        return $response;
    }

    /**
     * Upload the cacert bundle. Can be null to deactive cacert
     *
     * @param string|null $cacert
     * @return array
     * @throws GuzzleException
     */
    public function uploadSslCaCert(?string $cacert = null)
    {
        $parameters = [
            'domain' => $this->domainName,
            'type' => 'cacert',
            'action' => 'save'
        ];

        if( $cacert != null ) {
            $parameters['active'] = 'yes';
            $parameters['cacert'] = $cacert;
        } else {
            $parameters['active'] = 'no';
            $parameters['cacert'] = '';
        }

        $response = $this->getContext()->invokeApiPost('SSL', $parameters );

        return $response;
    }

    /**
     * Creates a new subdomain.
     *
     * @param string $prefix Prefix to add before the domain name
     * @return Subdomain The newly created subdomain
     */
    public function createSubdomain($prefix)
    {
        return Subdomain::create($this, $prefix);
    }

    /**
     * Deletes this domain from the user.
     */
    public function delete()
    {
        $this->getContext()->invokeApiPost('DOMAIN', [
            'delete' => true,
            'confirmed' => true,
            'select0' => $this->domainName,
        ]);
        $this->owner->clearCache();
    }

    /**
     * @return string[] List of aliases for this domain
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @return float Bandwidth used in megabytes
     */
    public function getBandwidthUsed()
    {
        return $this->bandwidthUsed;
    }

    /**
     * @return float|null Bandwidth quotum in megabytes, or NULL for unlimited
     */
    public function getBandwidthLimit()
    {
        return $this->bandwidthLimit;
    }

    /**
     * @return string|null Currently configured catch-all configuration
     */
    public function getCatchall()
    {
        $value = $this->getContext()->invokeApiGet('EMAIL_CATCH_ALL', ['domain' => $this->domainName]);
        return isset($value['value']) ? $value['value'] : null;
    }

    /**
     * @return float Disk usage in megabytes
     */
    public function getDiskUsage()
    {
        return $this->diskUsage;
    }

    /**
     * @return string The real domain name
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * @return string SSL ON or OFF
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * @return string PHP ON or OFF
     */
    public function getPhp()
    {
        return $this->php;
    }

    /**
     * @return string Local mail yes or no
     */
    public function getLocalMail()
    {
        return $this->localMail;
    }

    /**
     * @return string Suspended yer or no
     */
    public function getSuspended()
    {
        return $this->suspended;
    }

    /**
     * Returns unified sorted list of main domain name, aliases and pointers.
     *
     * @return string[]
     */
    public function getDomainNames()
    {
        return $this->getCache('domainNames', function () {
            $list = array_merge($this->aliases, $this->pointers, [$this->getDomainName()]);
            sort($list);
            return $list;
        });
    }

    /**
     * @return Forwarder[] Associative array of forwarders
     */
    public function getForwarders()
    {
        return $this->getCache(self::CACHE_FORWARDERS, function () {
            $forwarders = $this->getContext()->invokeApiGet('EMAIL_FORWARDERS', [
                'domain' => $this->getDomainName(),
            ]);
            return DomainObject::toDomainObjectArray($forwarders, Forwarder::class, $this);
        });
    }

    /**
     * @return Mailbox[] Associative array of mailboxes
     */
    public function getMailboxes()
    {
        return $this->getCache(self::CACHE_MAILBOXES, function () {
            $boxes = $this->getContext()->invokeApiGet('POP', [
                'domain' => $this->getDomainName(),
                'action' => 'full_list',
            ]);
            return DomainObject::toDomainObjectArray($boxes, Mailbox::class, $this);
        });
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string[] List of domain pointers for this domain
     */
    public function getPointers()
    {
        return $this->pointers;
    }

    /**
     * @return Subdomain[] Associative array of subdomains
     */
    public function getSubdomains()
    {
        return $this->getCache(self::CACHE_SUBDOMAINS, function () {
            $subs = $this->getContext()->invokeApiGet('SUBDOMAINS', ['domain' => $this->getDomainName()]);
            $subs = array_combine($subs, $subs);
            return DomainObject::toDomainObjectArray($subs, Subdomain::class, $this);
        });
    }

    /**
     * Invokes a POST command on a domain object.
     *
     * @param string $command Command to invoke
     * @param string $action Action to execute
     * @param array $parameters Additional options for the command
     * @param bool $clearCache Whether to clear the domain cache on success
     * @return array Response from the API
     */
    public function invokePost($command, $action, $parameters = [], $clearCache = true)
    {
        $response = $this->getContext()->invokeApiPost($command, array_merge([
            'action' => $action,
            'domain' => $this->domainName,
        ], $parameters));
        if ($clearCache) {
            $this->clearCache();
        }
        return $response;
    }

    /**
     * @param string $newValue New address for the catch-all, or one of the CATCHALL_ constants
     */
    public function setCatchall($newValue)
    {
        $parameters = array_merge(['domain' => $this->domainName, 'update' => 'Update'],
            (empty($newValue) || $newValue[0] == ':') ? ['catch' => $newValue] : ['catch' => 'address', 'value' => $newValue]);
        $this->getContext()->invokeApiPost('EMAIL_CATCH_ALL', $parameters);
    }

    /**
     * Allows Domain object to be passed as a string with its domain name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getDomainName();
    }

    /**
     * Sets configuration options from raw DirectAdmin data.
     *
     * @param UserContext $context Owning user context
     * @param array $config An array of settings
     */
    private function setConfig(UserContext $context, array $config)
    {
        $this->domainName = $config['domain'];

        // Determine owner
        if ($config['username'] === $context->getUsername()) {
            $this->owner = $context->getContextUser();
        } else {
            throw new DirectAdminException('Could not determine relationship between context user and domain');
        }

        // Parse plain options
        $bandwidths = array_map('trim', explode('/', $config['bandwidth']));
        $this->bandwidthUsed = floatval($bandwidths[0]);
        $this->bandwidthLimit = !isset($bandwidths[1]) || ctype_alpha($bandwidths[1]) ? null : floatval($bandwidths[1]);
        $this->diskUsage = floatval($config['quota']);

        $this->ssl = $config['ssl'];
        $this->php = $config['php'];
        $this->suspended = $config['suspended'];
        $this->localMail = $config['local_mail'];

        $this->aliases = array_filter(explode('|', $config['alias_pointers']));
        $this->pointers = array_filter(explode('|', $config['pointers']));
    }
}
