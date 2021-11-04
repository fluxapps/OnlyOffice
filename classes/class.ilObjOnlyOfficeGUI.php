<?php

use ILIAS\Filesystem\Exception\IOException;
use ILIAS\FileUpload\Exception\IllegalStateException;
use srag\Plugins\OnlyOffice\ObjectSettings\ObjectSettingsFormGUI;
use srag\Plugins\OnlyOffice\StorageService\Infrastructure\File\ilDBFileRepository;
use srag\Plugins\OnlyOffice\StorageService\Infrastructure\File\ilDBFileVersionRepository;
use srag\Plugins\OnlyOffice\StorageService\Infrastructure\File\ilDBFileChangeRepository;
use srag\Plugins\OnlyOffice\StorageService\StorageService;
use srag\Plugins\OnlyOffice\Utils\OnlyOfficeTrait;
use srag\DIC\OnlyOffice\DICTrait;
use srag\Plugins\OnlyOffice\InfoService\InfoService;
use srag\Plugins\OnlyOffice\Config\ConfigFormGUI;

/**
 * Class ilObjOnlyOfficeGUI
 * Generated by SrPluginGenerator v1.3.4
 * @author            Theodor Truffer <theo@fluxlabs.ch>
 * @author            Sophie Pfister <sophie@fluxlabs.ch>
 * @ilCtrl_isCalledBy ilObjOnlyOfficeGUI: ilRepositoryGUI
 * @ilCtrl_isCalledBy ilObjOnlyOfficeGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjOnlyOfficeGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjOnlyOfficeGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjOnlyOfficeGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjOnlyOfficeGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjOnlyOfficeGUI: ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjOnlyOfficeGUI: xonoContentGUI
 */
class ilObjOnlyOfficeGUI extends ilObjectPluginGUI
{

    use DICTrait;
    use OnlyOfficeTrait;

    const PLUGIN_CLASS_NAME = ilOnlyOfficePlugin::class;

    const CMD_MANAGE_CONTENTS = "manageContents";
    const CMD_PERMISSIONS = "perm";
    const CMD_SETTINGS = "settings";
    const CMD_SETTINGS_STORE = "settingsStore";
    const CMD_SHOW_CONTENTS = "showContents";
    const CMD_SHOW_VERSIONS = "showVersions";
    /* standard commands */
    const CMD_SAVE = 'save';
    const CMD_CANCEL = 'cancel';
    const CMD_SHOW_INFO = 'infoScreen';
    const CMD_TEMPLATE = 'createFromTemplate';

    const LANG_MODULE_OBJECT = "object";
    const LANG_MODULE_SETTINGS = "settings";

    const TAB_PERMISSIONS = "perm_settings";
    const TAB_SETTINGS = "settings";
    const TAB_INFO = "info_short";
    const TAB_SHOW_CONTENTS = "show_contents";

    const POST_VAR_FILE = 'upload_files';
    const POST_VAR_OPEN_SETTING = 'open_setting';
    const POST_VAR_ONLINE = 'online';
    const POST_VAR_EDIT = 'allow_edit';
    const POST_VAR_CREATE = 'createFrom';

    /**
     * @var ilObjOnlyOffice
     */
    public $object;
    /**
     * @var StorageService
     */
    protected $storage_service;
    /**
     * @var ilOnlyOfficePlugin
     */
    protected $plugin;

    /**
     * @inheritDoc
     */
    protected function afterConstructor()/*: void*/
    {
        $this->storage_service = new StorageService(
            self::dic()->dic(),
            new ilDBFileVersionRepository(),
            new ilDBFileRepository(),
            new ilDBFileChangeRepository()
        );
    }

    /**
     * @inheritDoc
     */
    public final function getType() : string
    {
        return ilOnlyOfficePlugin::PLUGIN_ID;
    }

    /**
     * @param string $cmd
     * @throws ilCtrlException
     */
    public function performCommand(string $cmd)/*: void*/
    {
        self::dic()->help()->setScreenIdComponent(ilOnlyOfficePlugin::PLUGIN_ID);
        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(xonoContentGUI::class):
                $xonoContentGUI = new xonoContentGUI(self::dic()->dic(), $this->plugin, $this->object_id);
                self::dic()->ctrl()->forwardCommand($xonoContentGUI);
                break;
            case strtolower(xonoEditorGUI::class):
                $xonoEditorGUI = new xonoEditorGUI(self::dic()->dic(), $this->plugin, $this->obj_id);
                self::dic()->ctrl()->forwardCommand($xonoEditorGUI);
                break;
            default:
                switch ($cmd) {
                    case self::CMD_SHOW_CONTENTS:
                    case self::CMD_MANAGE_CONTENTS:
                        // Read commands
                        if (!ilObjOnlyOfficeAccess::hasReadAccess() &&
                            !self::onlyOffice()->objectSettings()->getObjectSettingsById($this->object_id)->allowEdit()) {
                            ilObjOnlyOfficeAccess::redirectNonAccess(ilRepositoryGUI::class);
                        }
                        $open_setting = InfoService::getOpenSetting($this->obj_id);
                        switch ($open_setting) {
                            case "download":
                                $next_cmd = xonoContentGUI::CMD_DOWNLOAD;
                                $file = $this->storage_service->getFile($this->obj_id);
                                $file_version = $this->storage_service->getLatestVersion($file->getUuid());
                                $ext = pathinfo($file_version->getUrl(), PATHINFO_EXTENSION);
                                $filename = rtrim($file->getTitle(), '.' . $ext);
                                self::dic()->ctrl()->setParameterByClass(xonoContentGUI::class, 'path',
                                    ILIAS_ABSOLUTE_PATH . '/data/' . CLIENT_ID . $file_version->getUrl());
                                self::dic()->ctrl()->setParameterByClass(xonoContentGUI::class, 'name',
                                    $filename . '_V' . $file_version->getVersion() . '.' . $file->getFileType());
                                self::dic()->ctrl()->setParameterByClass(xonoContentGUI::class, 'mime',
                                    $file->getMimeType());
                                break;
                            case "editor":
                                $next_cmd = xonoContentGUI::CMD_EDIT;
                                break;
                            default: // "ilias" / "0"
                                $next_cmd = xonoContentGUI::CMD_SHOW_VERSIONS;
                        }

                        self::dic()->ctrl()->redirectByClass(xonoContentGUI::class, $next_cmd);
                        break;

                    case self::CMD_SHOW_VERSIONS:
                        self::dic()->ctrl()->redirectByClass(xonoContentGUI::class, xonoContentGUI::CMD_SHOW_VERSIONS);
                        break;

                    case self::CMD_SETTINGS:
                    case self::CMD_SETTINGS_STORE:
                        // Write commands
                        if (!ilObjOnlyOfficeAccess::hasWriteAccess()) {
                            ilObjOnlyOfficeAccess::redirectNonAccess($this);
                        }

                        $this->{$cmd}();
                        break;

                    default:
                        // Unknown command
                        ilObjOnlyOfficeAccess::redirectNonAccess(ilRepositoryGUI::class);
                        break;
                }
                break;
        }
    }

    /**
     * @param string $html
     */
    protected function show(string $html)/*: void*/
    {
        if (!self::dic()->ctrl()->isAsynch()) {
            self::dic()->ui()->mainTemplate()->setTitle($this->object->getTitle());

            self::dic()->ui()->mainTemplate()->setDescription($this->object->getDescription());

            if (!$this->object->isOnline()) {
                self::dic()->ui()->mainTemplate()->setAlertProperties([
                    [
                        "alert" => true,
                        "property" => self::plugin()->translate("status", self::LANG_MODULE_OBJECT),
                        "value" => self::plugin()->translate("offline", self::LANG_MODULE_OBJECT)
                    ]
                ]);
            }
        }

        self::output()->output($html);
    }

    /* --- Create new OnlyOffice File --- */
    /**
     * @inheritDoc
     */
    protected function initCreationForms($a_new_type): array
    {
        $forms = parent::initCreationForms($a_new_type);
        return $forms;
    }

    /**
     * @inheritDoc
     */
    public function initCreateForm(/*string*/ $a_new_type) : ilPropertyFormGUI
    {
        $form = new ilPropertyFormGUI();
        $form->setTarget("_top");
        $form->setFormAction($this->ctrl->getFormAction($this, "save"));
        $form->setTitle($this->txt($a_new_type . "_new"));

        // title
        $ti = new ilTextInputGUI($this->lng->txt("title"), "title");
        $ti->setSize(min(40, ilObject::TITLE_LENGTH));
        $ti->setMaxLength(ilObject::TITLE_LENGTH);
        $ti->setInfo(self::plugin()->translate("create_title_info"));
        $form->addItem($ti);

        // description
        $ta = new ilTextAreaInputGUI($this->lng->txt("description"), "desc");
        $ta->setCols(40);
        $ta->setRows(2);
        $form->addItem($ta);

        // file
        $file_input = new ilFileInputGUI(self::plugin()->translate('form_input_file'), self::POST_VAR_FILE);
        $file_input->setRequired(true);
        $form->addItem($file_input);

        // online checkbox
        $online = new ilCheckboxInputGUI(self::plugin()->translate('online', ilObjOnlyOfficeGUI::LANG_MODULE_SETTINGS),
            self::POST_VAR_ONLINE);
        $form->addItem($online);

        // Users are allowed to edit checkbox
        $edit = new ilCheckboxInputGUI(self::plugin()->translate('allow_edit',
            ilObjOnlyOfficeGUI::LANG_MODULE_SETTINGS), self::POST_VAR_EDIT);
        $edit->setInfo(self::plugin()->translate('allow_edit_info',
            ilObjOnlyOfficeGUI::LANG_MODULE_SETTINGS));
        $form->addItem($edit);

        // Settings for opening a file
        $opening_setting = new ilRadioGroupInputGUI(self::plugin()->translate("form_open_setting"),
            self::POST_VAR_OPEN_SETTING);
        $opening_setting->addOption(new ilRadioOption(self::plugin()->translate("open_setting_ilias",
            self::LANG_MODULE_SETTINGS), "ilias"));
        $opening_setting->addOption(new ilRadioOption(self::plugin()->translate("open_setting_editor",
            self::LANG_MODULE_SETTINGS), "editor"));
        $opening_setting->addOption(new ilRadioOption(self::plugin()->translate("open_setting_download",
            self::LANG_MODULE_SETTINGS), "download"));
        $opening_setting->setValue("ilias");
        $opening_setting->setRequired(true);
        $form->addItem($opening_setting);

        // Buttons
        $form->addCommandButton("save", $this->txt($a_new_type . "_add"));
        $form->addCommandButton("cancel", $this->lng->txt("cancel"));

        return $form;
    }


    /**
     * @inheritDoc
     * @param ilObject $a_new_object
     * @throws IllegalStateException
     * @throws IOException
     * @throws ilDateTimeException
     */
    public function afterSave(/*ilObjOnlyOffice*/ ilObject $a_new_object)/*: void*/
    {
        $form = $this->initCreateForm($a_new_object->getType());
        $form->checkInput();
        self::dic()->upload()->process();
        $results = self::dic()->upload()->getResults();
        $result = end($results);
        $this->storage_service->createNewFileFromUpload($result, $a_new_object->getId());
        $title = $a_new_object->title;
        if ($title == "") {
            $a_new_object->title = explode(".", $result->getName())[0];
            $a_new_object->update();
        }
        parent::afterSave($a_new_object);
    }

    /* -- Setiings -- */
    /**
     * @return ObjectSettingsFormGUI
     */
    protected function getSettingsForm() : ObjectSettingsFormGUI
    {
        $form = new ObjectSettingsFormGUI($this, $this->object);
        return $form;
    }

    /**
     *
     */
    protected function settings()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = $this->getSettingsForm();

        self::output()->output($form);
    }

    /**
     *
     */
    protected function settingsStore()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = $this->getSettingsForm();

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }
        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE_SETTINGS), true);

        self::dic()->ctrl()->redirect($this, self::CMD_SETTINGS);
    }

    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_SHOW_CONTENTS,
            self::plugin()->translate("show_contents", self::LANG_MODULE_OBJECT), self::dic()->ctrl()
                                                                                      ->getLinkTarget($this,
                                                                                          self::CMD_SHOW_VERSIONS));
        self::dic()->tabs()->addTab(self::TAB_INFO, self::plugin()->translate("tab_info", self::LANG_MODULE_OBJECT),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_SHOW_INFO));

        if (ilObjOnlyOfficeAccess::hasWriteAccess()) {
            self::dic()->tabs()->addTab(self::TAB_SETTINGS,
                self::plugin()->translate("settings", self::LANG_MODULE_SETTINGS), self::dic()->ctrl()
                                                                                       ->getLinkTarget($this,
                                                                                           self::CMD_SETTINGS));
        }

        if (ilObjOnlyOfficeAccess::hasEditPermissionAccess()) {
            self::dic()->tabs()->addTab(self::TAB_PERMISSIONS,
                self::plugin()->translate(self::TAB_PERMISSIONS, "", [], false), self::dic()->ctrl()
                                                                                     ->getLinkTargetByClass([
                                                                                         self::class,
                                                                                         ilPermissionGUI::class
                                                                                     ], self::CMD_PERMISSIONS));
        }

        self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
    }

    /**
     * @return string
     */
    public static function getStartCmd() : string
    {
        return self::CMD_SHOW_CONTENTS;
    }

    /**
     * @inheritDoc
     */
    public function getAfterCreationCmd() : string
    {
        return self::getStartCmd();
    }

    /**
     * @inheritDoc
     */
    public function getStandardCmd() : string
    {
        return self::getStartCmd();
    }

}
