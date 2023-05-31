<?php
/*
 * Copyright (c) 2022 by bHosted.nl B.V.  - All rights reserved
 */

namespace Mvdgeijn\DirectAdmin\Objects;

/**
 * FileManagerObject
 *
 * @author Marc van de Geijn <marc@vdgeijn.com>
 */
class FileManagerObject extends BaseObject
{
    private string $path;

    private int $date;

    private int $atime;

    private int $mtime;

    private string $gid;

    private string $uid;

    private string $permission;

    private string $showSize;

    private int $size;

    private bool $trash;

    private bool $isLink;

    private string $linkPath;

    private string $type;

    private string $truePath;

    /**
     * FileManager constructor.
     *
     * @param string $path
     * @param UserContext $context Context within which the object is valid
     */
    public function __construct(string $path, FileManager $fileManager, string $content )
    {
        parent::__construct($path, $fileManager->getContext() );

        parse_str( $content, $params );

        $this->path = $path;

        $this->date = $params['date'];

        $this->atime = $params['atime'];

        $this->mtime = $params['mtime'];

        $this->gid = $params['gid'];

        $this->uid = $params['uid'];

        $this->permission = $params['permission'];

        $this->showSize = $params['showsize'];

        $this->size = $params['size'];

        $this->trash = $params['trash'];

        $this->isLink = $params['islink'];

        $this->linkPath = $params['linkpath'];

        $this->type = $params['type'];

        $this->truePath = $params['truepath'];
    }

    /**
     * @return bool
     */
    public function isDir():bool
    {
        return $this->type == 'dir';
    }
}
