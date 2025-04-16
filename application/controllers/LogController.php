<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\Controllers;

use DateTime;
use DateTimeZone;
use Icinga\Data\ConfigObject;
use Icinga\Protocol\File\FileReader;
use ipl\Html\Html;
use ipl\Html\HtmlString;
use ipl\Html\Table;
use ipl\Web\Compat\CompatController;
use ipl\Web\Widget\EmptyStateBar;

class LogController extends CompatController
{
    public function indexAction(): void
    {
        $this->assertPermission('audit/log');

        if ($this->Config()->get('log', 'type') !== 'file') {
            $this->httpNotFound('Page not found');
        }

        $this->addTitleTab($this->translate('Audit Log'));

        $file = $this->Config()->get('log', 'path', '/var/log/icingaweb2/audit.log');

        if (! @file_exists($file)) {
            $this->addContent(new EmptyStateBar($this->translate('No activity has been recorded yet.')));

            return;
        }

        $resource = new FileReader(new ConfigObject([
            'filename'  => $file,
            'fields'    => '/(?<!.)' // ^ can't handle multilines, don't ask *me* why this works
                . '(?<datetime>[0-9]{4}(?:-[0-9]{2}){2}'                    // date
                . 'T[0-9]{2}(?::[0-9]{2}){2}(?:[\+\-][0-9]{2}:[0-9]{2})?)'  // time
                . ' - (?<identity>.+)'                                      // identity
                . ' - (?<type>.+)'                                          // type
                . ' - (?<message>.+)'                                       // message
                . '(?!.)/msSU' // $ can't handle multilines, don't ...
        ]));

        $query = $resource->select()->order('DESC');

        $this->setupPaginationControl($query);
        $this->addControl(HtmlString::create((string) $this->view->paginator));

        $this->setupLimitControl();
        $this->addControl(Html::tag(
            'div',
            ['class' => 'sort-controls-container'],
            HtmlString::create((string) $this->view->limiter)
        ));

        $table = new Table();
        $table->addAttributes(['class' => 'action']);

        /** @var object{datetime: string, type: string, identity: string, message: string} $row */
        foreach ($query as $row) {
            $time = new DateTime($row->datetime);
            $time->setTimezone(new DateTimeZone(date_default_timezone_get()));
            $table->add([
                [$time->format('d.m. H:i'), Html::tag('br'), $row->type],
                $row->identity,
                nl2br(trim($row->message), false),
            ]);
        }

        $this->addContent($table);
    }
}
