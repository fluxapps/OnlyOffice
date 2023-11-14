<?php

namespace srag\Plugins\OnlyOffice\Config;

use srag\Plugins\OnlyOffice\Utils\OnlyOfficeTrait;
use ilOnlyOfficeConfigGUI;
use ilOnlyOfficePlugin;
use ilTextInputGUI;
use srag\ActiveRecordConfig\OnlyOffice\Config\Config;
use srag\CustomInputGUIs\OnlyOffice\PropertyFormGUI\PropertyFormGUI;
use ilFileInputGUI;
use srag\Plugins\OnlyOffice\StorageService\StorageService;
use srag\DIC\OnlyOffice\DICTrait;
use srag\Plugins\OnlyOffice\StorageService\Infrastructure\File\ilDBFileVersionRepository;
use srag\Plugins\OnlyOffice\StorageService\Infrastructure\File\ilDBFileRepository;
use srag\Plugins\OnlyOffice\StorageService\Infrastructure\File\ilDBFileChangeRepository;
use srag\Plugins\OnlyOffice\StorageService\Infrastructure\File\FileAR;

/**
 * Class ConfigFormGUI
 * Generated by SrPluginGenerator v1.3.4
 * @package srag\Plugins\OnlyOffice\Config
 * @author  Theodor Truffer <theo@fluxlabs.ch>
 *         Sophie Pfister<sophie@fluxlabs.ch>
 */
class ConfigFormGUI extends PropertyFormGUI
{

    use OnlyOfficeTrait;

    const PLUGIN_CLASS_NAME = ilOnlyOfficePlugin::class;

    const KEY_ONLYOFFICE_URL = "onlyoffice_url";
    const KEY_ONLYOFFICE_SECRET = "onlyoffice_secret";
    const KEY_NUM_VERSIONS = "number_of_versions";

    const LANG_MODULE = ilOnlyOfficeConfigGUI::LANG_MODULE;

    /**
     * ConfigFormGUI constructor
     * @param ilOnlyOfficeConfigGUI $parent
     */
    public function __construct(ilOnlyOfficeConfigGUI $parent)
    {
        parent::__construct($parent);
    }

    protected function getValue(string $key)
    {
        switch ($key) {
            default:
                return self::onlyOffice()->config()->getValue($key);
        }
    }

    protected function initCommands(): void
    {
        $this->addCommandButton(ilOnlyOfficeConfigGUI::CMD_UPDATE_CONFIGURE, $this->txt("save"));
    }

    protected function initFields(): void
    {
        $this->fields = [
            self::KEY_ONLYOFFICE_URL => [
                self::PROPERTY_CLASS => ilTextInputGUI::class,
                self::PROPERTY_REQUIRED => true
            ],
            self::KEY_ONLYOFFICE_SECRET => [
                self::PROPERTY_CLASS => \ilPasswordInputGUI::class,
                self::PROPERTY_REQUIRED => true
            ],
            self::KEY_NUM_VERSIONS => [
                self::PROPERTY_CLASS => \ilNumberInputGUI::class
            ]
        ];
    }

    protected function initId(): void
    {

    }

    protected function initTitle(): void
    {
        $this->setTitle($this->txt("configuration"));
    }

    protected function storeValue(string $key, $value): void
    {
        switch ($key) {
            // If less than 1 version should be loaded from storage, a default value (10) is stored
            case self::KEY_NUM_VERSIONS:
                if ($value < 1) {
                    self::onlyOffice()->config()->setValue($key, 10);
                } else {
                    self::onlyOffice()->config()->setValue($key, $value);
                }
                break;
            default:
                self::onlyOffice()->config()->setValue($key, $value);
                break;
        }
    }
}
