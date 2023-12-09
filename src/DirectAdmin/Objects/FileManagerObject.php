<?php
/*
 * Copyright (c) 2022 by bHosted.nl B.V.  - All rights reserved
 */

namespace Mvdgeijn\DirectAdmin\Objects;

/**
 * FileManagerObject. Can be a file, directory or link.
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
     * Constructor method for the given class.
     *
     * @param string $path The path for the object.
     * @param FileManager $fileManager The FileManager object.
     * @param string $content The content to be parsed.
     *
     * @return void
     */
    public function __construct(string $path, FileManager $fileManager, string $content)
    {
        parent::__construct($path, $fileManager->getContext());
        $this->path = $path;

        $params = [];
        parse_str($content, $params);

        $this->assignParamsToProperties($params);
    }

    /**
     * Assigns the values from the given array to the corresponding properties of this object.
     *
     * @param array $params An associative array containing the property names as keys and their respective values.
     *                      The keys must match the property names of this object.
     *
     * @return void
     */
    private function assignParamsToProperties(array $params): void
    {
        foreach (get_object_vars($this) as $key => $value) {
            if (isset($params[$key])) {
                $this->$key = $params[$key];
            }
        }
    }
    
    /**
     * @return bool
     */
    public function isDir():bool
    {
        return $this->type == 'dir';
    }
}
