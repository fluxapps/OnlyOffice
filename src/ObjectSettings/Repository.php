<?php

namespace srag\Plugins\OnlyOffice\ObjectSettings;

use srag\Plugins\OnlyOffice\Utils\OnlyOfficeTrait;
use ilOnlyOfficePlugin;
use srag\DIC\OnlyOffice\DICTrait;

/**
 * Class Repository
 * Generated by SrPluginGenerator v1.3.4
 * @author          Theodor Truffer <thoe@fluxlabs.ch>
 * @author          Sophie Pfister <sophie@fluxlabs.ch>
 */
final class Repository
{

    use DICTrait;
    use OnlyOfficeTrait;

    const PLUGIN_CLASS_NAME = ilOnlyOfficePlugin::class;
    /**
     * @var self
     */
    protected static $instance = null;

    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Repository constructor
     */
    private function __construct()
    {

    }

    /**
     * @param ObjectSettings $object_settings
     * @return ObjectSettings
     */
    public function cloneObjectSettings(ObjectSettings $object_settings) : ObjectSettings
    {
        return $object_settings->copy();
    }

    /**
     * @param ObjectSettings $object_settings
     */
    public function deleteObjectSettings(ObjectSettings $object_settings)/*:void*/
    {
        $object_settings->delete();
    }

    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(ObjectSettings::TABLE_NAME, false);
    }

    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }

    /**
     * @param int $obj_id
     * @return ObjectSettings|null
     */
    public function getObjectSettingsById(int $obj_id)/*:?ObjectSettings*/
    {
        /**
         * @var ObjectSettings|null $object_settings
         */

        $object_settings = ObjectSettings::where([
            "obj_id" => $obj_id
        ])->first();

        return $object_settings;
    }

    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        ObjectSettings::updateDB();
    }

    /**
     * @param ObjectSettings $object_settings
     */
    public function storeObjectSettings(ObjectSettings $object_settings)/*:void*/
    {
        $object_settings->store();
    }
}
