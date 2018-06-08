<?php
/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\Controllers;

use Icinga\Data\ConfigObject;
use Icinga\Protocol\File\FileReader;
use Icinga\Web\Controller;

class LogController extends Controller
{
    public function indexAction()
    {
        $this->assertPermission('audit/log');

        if ($this->Config()->get('log', 'type') !== 'file') {
            $this->httpNotFound('Page not found');
        }

        $resource = new FileReader(new ConfigObject(array(
            'filename'  => $this->Config()->get('log', 'path', '/var/log/icingaweb2/audit.log'),
            'fields'    => '/(?<!.)(?<datetime>[0-9]{4}(?:-[0-9]{2}){2}'    // date
                . 'T[0-9]{2}(?::[0-9]{2}){2}(?:[\+\-][0-9]{2}:[0-9]{2})?)'  // time
                . ' - (?<type>[A-Za-z]+)'                                   // type
                . ' - (?<message>.*)(?!.)/msS'                              // message
        )));
        $this->view->logData = $resource->select()->order('DESC');

        $this->setupLimitControl();
        $this->setupPaginationControl($this->view->logData);

        $this->getTabs()->add('audit/log', [
            'url'   => 'audit/log',
            'title' => $this->translate('Audit Log')
        ])->activate('audit/log');
    }
}