<?php

/*
 * DirectAdmin API Client
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvdgeijn\DirectAdmin;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use Mvdgeijn\DirectAdmin\Context\AdminContext;
use Mvdgeijn\DirectAdmin\Context\ResellerContext;
use Mvdgeijn\DirectAdmin\Context\UserContext;
use Mvdgeijn\DirectAdmin\Utility\Conversion;

/**
 * DirectAdmin API main class, encapsulating a specific account connection to a single server.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class DirectAdmin
{
    const ACCOUNT_TYPE_ADMIN = 'admin';
    const ACCOUNT_TYPE_RESELLER = 'reseller';
    const ACCOUNT_TYPE_USER = 'user';

    /** @var string */
    private $authenticatedUser;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $baseUrl;

    /** @var Client */
    private $connection;

    /** @var bool */
    private $verify = true;

    /**
     * Connects to DirectAdmin with an admin account.
     *
     * @param string $url The base URL of the DirectAdmin server
     * @param string $username The username of the account
     * @param string $password The password of the account
     * @param bool $validate Whether to ensure the account exists and is of the correct type
     * @return AdminContext
     */
    public static function connectAdmin($url, $username, $password, $validate = false)
    {
        return new AdminContext(new self($url, $username, $password), $validate);
    }

    /**
     * Connects to DirectAdmin with a reseller account.
     *
     * @param string $url The base URL of the DirectAdmin server
     * @param string $username The username of the account
     * @param string $password The password of the account
     * @param bool $validate Whether to ensure the account exists and is of the correct type
     * @return ResellerContext
     */
    public static function connectReseller($url, $username, $password, $validate = false)
    {
        return new ResellerContext(new self($url, $username, $password), $validate);
    }

    /**
     * Connects to DirectAdmin with a user account.
     *
     * @param string $url The base URL of the DirectAdmin server
     * @param string $username The username of the account
     * @param string $password The password of the account
     * @param bool $validate Whether to ensure the account exists and is of the correct type
     * @return UserContext
     */
    public static function connectUser($url, $username, $password, $validate = false)
    {
        return new UserContext(new self($url, $username, $password), $validate);
    }

    /**
     * Creates a connection wrapper to DirectAdmin as the specified account.
     *
     * @param string $url The base URL of the DirectAdmin server
     * @param string $username The username of the account
     * @param string $password The password of the account
     */
    protected function __construct($url, $username, $password)
    {
        $accounts = explode('|', $username);
        $this->authenticatedUser = current($accounts);
        $this->username = end($accounts);
        $this->password = $password;
        $this->baseUrl = rtrim($url, '/') . '/';
        $this->connection = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$username, $password],
        ]);
    }

    /**
     * Returns the username behind the current connection.
     *
     * @return string Currently logged in user's username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the password behind the current connection.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Invokes the DirectAdmin API with specific options.
     *
     * @param string $method HTTP method to use (ie. GET or POST)
     * @param string $command DirectAdmin API command to invoke
     * @param array $options Guzzle options to use for the call
     * @return array The unvalidated response
     *
     * @throws DirectAdminException If anything went wrong on the network level
     * @throws GuzzleException
     */
    public function invokeApi($method, $command, $options = [])
    {
        $result = $this->rawRequest($method, '/CMD_API_' . $command, $options);
        if (!empty($result['error'])) {
            throw new DirectAdminException("$method to $command failed: $result[details] ($result[text])");
        }

        return Conversion::sanitizeArray($result);
    }

    /**
     * Returns a clone of the connection logged in as a managed user or reseller.
     *
     * @param string $username
     * @return DirectAdmin
     */
    public function loginAs($username)
    {
        // DirectAdmin format is to just pipe the accounts together under the master password
        return ( new self($this->baseUrl, $this->authenticatedUser . "|{$username}", $this->password) )->setVerify( $this->verify );
    }

    /**
     * Sends a raw request to DirectAdmin.
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function rawRequest($method, $uri, $options)
    {
        try {
            $options['verify'] = $this->verify;

            $response = $this->connection->request($method, $uri, $options);
            if ('text/html' == $response->getHeader('Content-Type')[0]) {
                throw new DirectAdminException(sprintf('DirectAdmin API returned text/html to %s %s containing "%s"', $method, $uri, strip_tags($response->getBody()->getContents())));
            }
            $body = $response->getBody()->getContents();

            // Requested JSON response?
            if( strcasecmp($options['query']['json'] ?? '', 'yes') === 0 )
                return json_decode( $body, true );
            elseif( ( strcasecmp($options['form_params']['json'] ?? '', 'yes') === 0 ) )
                return json_decode( $body, true );
            else
                return Conversion::responseToArray($body);
        } catch( ClientException $exception) {
            throw new DirectAdminException(sprintf('%s %s failed: ' . $exception->getResponse()->getReasonPhrase(), $method, $uri), $exception->getCode(), $exception);            
        } catch (TransferException $exception) {
            // Rethrow anything that causes a network issue
            throw new DirectAdminException(sprintf('%s request to %s failed', $method, $uri), 0, $exception);
        }
    }

    /**
     * Should the certificate be checked or not.
     *
     * @param bool $verify
     * @return DirectAdmin
     */
    public function setVerify($verify = true): self
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * Should the response be json or the old return types
     *
     * @param $json
     * @return $this
     */
    public function setJson($json = true):self
    {
        $this->json = $json;

        return $this;
    }
}
