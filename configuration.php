<?php
/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

/** @var $this \Icinga\Application\Modules\Module */

$this->provideConfigTab('config', [
    'url'   => 'config',
    'title' => $this->translate('Configuration')
]);
