<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\Controllers;

use Icinga\Web\Controller;
use Icinga\Module\Audit\Forms\Config\AuditLogConfigForm;
use ipl\Html\HtmlString;
use ipl\Web\Compat\CompatController;

class ConfigController extends CompatController
{
    public function init()
    {
        $this->assertPermission('config/modules');

        foreach ($this->Module()->getConfigTabs()->getTabs() as $tab) {
            $this->tabs->add($tab->getName(), $tab);
        }
    }

    public function indexAction(): void
    {
        $this->getTabs()->activate('config');

        $form = new AuditLogConfigForm();
        $form->setIniConfig($this->Config());
        $form->setSubmitLabel($this->translate('Save Configuration'));

        $form->handleRequest();

        $this->addContent(new HtmlString((string) $form));
    }
}
