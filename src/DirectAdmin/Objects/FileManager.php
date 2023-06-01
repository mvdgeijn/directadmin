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
use Mvdgeijn\DirectAdmin\Objects\Users\User;

/**
 * File manager
 *
 * @author Marc van de Geijn <marc@vdgeijn.com>
 */
class FileManager extends BaseObject
{
    /** @var User */
    private $owner;

    /**
     * Construct the object.
     *
     * @param UserContext $context The owning user context
     */
    public function __construct( UserContext $context, $config = null )
    {
        $this->owner = $context;

        parent::__construct('filemanager', $context);
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function listDir( string $path = '/' ): ?array
    {
        $data = [];

        $parameters = [
            'path' => $path
        ];

        $response = $this->getContext()->invokeApiPost('FILE_MANAGER', $parameters);

        foreach( $response as $path => $line ) {
            $data[$path] = new FileManagerObject( $path, $this, $line );
        }

        return $data;
    }

    /**
     * Root dir is /home/<group>/. Same as in the actual DA file manager. Use $path = '/domains/' and
     * $dir = 'testdir' to create /home/<group>/domains/testdir folder.
     *
     * @param string $path
     * @param string $dir
     * @return bool
     * @throws GuzzleException
     */
    public function createDir( string $path, string $dir ): bool
    {
        $parameters = [
            'action' => 'folder',
            'path' => $path,
            'name' => $dir
        ];

        $response = $this->getContext()->invokeApiPost('FILE_MANAGER', $parameters);

        return $response['error'] == "0";
    }

    /**
     * @param string $path
     * @param string $file
     * @param string $content
     * @return bool
     */
    public function uploadFile( string $path, string $file, string $content ): bool
    {
        $parameters = [
            'enctype' => 'multipart/form-data',
            'action' => 'upload',
            'path' => $path,
            'multipart' => [
                [
                    'name' => 'FileContents',
                    'contents' => $content,
                    'filename' => $file
                ]
            ]
        ];

        $response = $this->getContext()->invokeApiPost('FILE_MANAGER', $parameters);

        dd( $response );
        return false;
    }

    /**
     * Invokes a POST command on a domain object.
     *
     * @param string $command Command to invoke
     * @param string $action Action to execute
     * @param array $parameters Additional options for the command
     * @return array Response from the API
     */
    public function invokePost($command, $action, $parameters = [])
    {
        $response = $this->getContext()->invokeApiPost($command, array_merge([
            'action' => $action,
        ], $parameters));
        return $response;
    }
}
