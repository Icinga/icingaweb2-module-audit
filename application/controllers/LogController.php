<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\Controllers;

use Icinga\Data\ConfigObject;
use Icinga\Protocol\File\FileReader;
use Icinga\Web\Controller;

class LogController extends Controller
{
    public function indexAction(): void
    {
        $this->assertPermission('audit/log');

        if ($this->Config()->get('log', 'type') !== 'file') {
            $this->httpNotFound('Page not found');
        }

        $this->getTabs()->add('audit/log', [
            'active' => true,
            'label'  => $this->translate('Audit Log'),
            'url'    => 'audit/log',
        ]);

        $file = $this->Config()->get('log', 'path', '/var/log/icingaweb2/audit.log');

        if (! @file_exists($file)) {
            $this->render('log-empty');

            return;
        }

        $resource = new FileReader(new ConfigObject([
            'filename'  => $file,
            'fields'    => '/(?<!.)' // ^ can't handle multilines, don't ask *me* why this works
                . '(?<datetime>[0-9]{4}(?:-[0-9]{2}){2}'                    // date
                . 'T[0-9]{2}(?::[0-9]{2}){2}(?:[\+\-][0-9]{2}:[0-9]{2})?)'  // time
                . ' - (?<remoteip>.+)'                                      // remoteip
                . ' - (?<identity>.+)'                                      // identity
                . ' - (?<type>.+)'                                          // type
                . ' - (?<message>.+)'                                       // message
                . '(?!.)/msSU' // $ can't handle multilines, don't ...
        ]));

        $this->view->logData = $resource->select()->order('DESC');

        $this->setupLimitControl();
        $this->setupPaginationControl($this->view->logData);
    }
}
