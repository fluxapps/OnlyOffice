<?php

namespace srag\Plugins\OnlyOffice\StorageService\DTO;

use srag\Plugins\OnlyOffice\StorageService\Infrastructure\Common\UUID;

/**
 * Class File
 *
 * @package srag\Plugins\OnlyOffice\StorageService\DTO
 *
 * @author  Theodor Truffer <theo@fluxlabs.ch>
 */
class File
{

    /**
     * @var UUID
     */
    protected $uuid;
    /**
     * @var int
     */
    protected $obj_id;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $file_type;

    /** @var string */
    protected $mime_type;


    /**
     * Determines the doc type (word, cell, or slide) based on the file extension
     * @param string $extension
     * @return string
     */
    public static function determineDocType(string $extension, bool $formatForEditor = true) : string
    {
        switch ($extension) {
            case "pptx":
            case "fodp":
            case "odp":
            case "otp":
            case "pot":
            case "potm":
            case "potx":
            case "pps":
            case "ppsm":
            case "ppsx":
            case "ppt":
            case "pptm":
                if ($formatForEditor) {
                    return "slide";
                }
                return "presentation";
            case "xlsx":
            case "csv":
            case "fods":
            case "ods":
            case "ots":
            case "xls":
            case "xlsm":
            case "xlt":
            case "xltm":
            case "xltx":
                if ($formatForEditor) {
                    return "cell";
                }
                return "table";
            case "doc":
            case "docx":
            case "dotx":
            case "fb2":
            case "odt":
            case "ott":
            case "rtf":
            case "txt":
            case "pdf":
            case "pdf/a":
            case "html":
            case "epub":
            case "xps":
            case "djvu":
            case "xml":
            case "docxf":
            case "oform":
                if ($formatForEditor) {
                    return "word";
                }
                return "text";
            default:
                return "";
        }
    }


    /**
     * File constructor.
     *
     * @param UUID          $uuid
     * @param int           $obj_id
     * @param string        $title
     * @param string        $file_type
     */
    public function __construct(UUID $uuid, int $obj_id, string $title, string $file_type, string $mime_type)
    {
        $this->uuid = $uuid;
        $this->title = $title;
        $this->file_type = $file_type;
        $this->obj_id = $obj_id;
        $this->mime_type = $mime_type;
    }


    /**
     * @return UUID
     */
    public function getUuid() : UUID
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @return string
     */
    public function getFileType() : string
    {
        return $this->file_type;
    }

    public function getFileUuid(): UUID
    {
        return $this->uuid;
    }

    public function getMimeType(): string {
        return $this->mime_type;
    }

}