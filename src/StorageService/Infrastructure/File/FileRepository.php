<?php

namespace srag\Plugins\OnlyOffice\StorageService\Infrastructure\File;

use srag\Plugins\OnlyOffice\StorageService\Infrastructure\Common\UUID;
use srag\Plugins\OnlyOffice\StorageService\DTO\File;

/**
 * interface FileRepository
 * @package srag\Plugins\OnlyOffice\StorageService\Infrastructure\File
 * @author  Theodor Truffer <theo@fluxlabs.ch>
 */
interface FileRepository
{

    /**
     * @param UUID   $file_uuid
     * @param int    $obj_id
     * @param string $getName
     * @param string $file_type
     * @return mixed
     */
    public function create(UUID $file_uuid, int $obj_id, string $getName, string $file_type, string $mime_type);

    public function getFile(int $obj_id) : File;

    public function getAR(int $file_id): \ActiveRecord;

    public function getAllFiles();

}