<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\Controllers;

use DateTime;
use Icinga\Data\ConfigObject;
use Icinga\Protocol\File\FileReader;
use Icinga\Util\Json;
use Icinga\Web\Controller;
use Icinga\Web\Widget\Tabextension\DashboardAction;
use Icinga\Web\Widget\Tabextension\MenuAction;
use Icinga\Web\Widget\Tabextension\OutputFormat;

class LogController extends Controller
{
    protected $auditResource = null;
    protected $csvDelimiter = ';';
    protected $csvEnclosure = '"';


    public function init()
    {
        if ($this->Config()->get('log', 'type') !== 'file') {
            $this->httpNotFound('Page not found');
        }

        $file = $this->Config()->get('log', 'path', '/var/log/icingaweb2/audit.log');

        if (!@file_exists($file)) {
            $this->render('log-empty');

            return;
        }

        $this->auditResource = new FileReader(new ConfigObject([
            'filename' => $file,
            'fields' => '/(?<!.)' // ^ can't handle multilines, don't ask *me* why this works
                . '(?<datetime>[0-9]{4}(?:-[0-9]{2}){2}'                    // date
                . 'T[0-9]{2}(?::[0-9]{2}){2}(?:[\+\-][0-9]{2}:[0-9]{2})?)'  // time
                . ' - (?<identity>.+)'                                      // identity
                . ' - (?<type>.+)'                                          // type
                . ' - (?<message>.+)'                                       // message
                . '(?!.)/msSU' // $ can't handle multilines, don't ...
        ]));
    }

    protected function auditToArray()
    {
        $result = [];
        $a = $this->auditResource;

        $query = $a->select()->order('DESC');
        $query->limit(PHP_INT_MAX, 0);

        foreach ($query as $value) {
            $datetime = new Datetime($value->datetime);

            $entry = [];
            $entry['datetime'] = $datetime->format('d.m.Y H:i:s');
            $entry['type'] = $value->type;
            $entry['identity'] = $value->identity;
            $entry['message'] = $value->message;
            array_push($result, $entry);
        }
        return $result;
    }

    protected function handleFormatRequest()
    {
        $desiredContentType = $this->getRequest()->getHeader('Accept');

        if ($desiredContentType === 'application/json') {
            $desiredFormat = 'json';
        } elseif ($desiredContentType === 'text/csv') {
            $desiredFormat = 'csv';
        } else {
            $desiredFormat = strtolower($this->params->get('format', 'html'));
        }

        switch ($desiredFormat) {
            case 'json':
                $response = $this->getResponse();
                $response
                    ->setHeader('Content-Type', 'application/json')
                    ->setHeader('Cache-Control', 'no-store')
                    ->setHeader(
                        'Content-Disposition',
                        'inline; filename=' . $this->getRequest()->getActionName() . '.json'
                    )
                    ->appendBody(
                        Json::sanitize(
                            $this->auditToArray()
                        )
                    )
                    ->sendResponse();
                exit;
            case 'csv':
                $response = $this->getResponse();

                $contents = "";
                $handle = fopen('php://temp', 'r+');
                foreach ($this->auditToArray() as $line) {
                    fputcsv($handle, $line, $this->csvDelimiter, $this->csvEnclosure);
                }
                rewind($handle);
                while (!feof($handle)) {
                    $contents .= fread($handle, 8192);
                }
                $response
                    ->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Cache-Control', 'no-store')
                    ->setHeader(
                        'Content-Disposition',
                        'attachment; filename=' . $this->getRequest()->getActionName() . '.csv'
                    )
                    ->appendBody($contents)
                    ->sendResponse();
                exit;
        }
    }

    public function indexAction()
    {
        $this->handleFormatRequest();
        $this->assertPermission('audit/log');

        $this->getTabs()->add('audit/log', [
            'active' => true,
            'label' => $this->translate('Audit Log'),
            'url' => 'audit/log',
        ])->extend(new OutputFormat())->extend(new DashboardAction())->extend(new MenuAction());

        $this->view->logData = $this->auditResource->select()->order('DESC');
        
        $this->setupLimitControl();
        $this->setupPaginationControl($this->view->logData);
    }
}
