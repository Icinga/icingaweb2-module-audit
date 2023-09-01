<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\Controllers;

use Icinga\Web\Controller;
use Icinga\Module\Audit\Forms\Config\AuditLogConfigForm;

class ConfigController extends Controller
{
    public function indexAction(): void
    {
        $form = new AuditLogConfigForm();
        $form->setIniConfig($this->Config());
        $form->setSubmitLabel($this->translate('Save Configuration'));

        $form->handleRequest();

        $this->view->form = $form;
        $this->view->tabs = $this->Module()->getConfigTabs()->activate('config');
    }
}
