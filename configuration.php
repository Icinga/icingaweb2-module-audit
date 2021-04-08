<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

/** @var $this \Icinga\Application\Modules\Module */

$this->provideConfigTab('config', [
    'url'   => 'config',
    'title' => $this->translate('Configuration')
]);

$this->providePermission('audit/log', $this->translate('Allow access to the audit log'));

try {
    if ($this->getConfig()->get('log', 'type') === 'file') {
        $section = $this->menuSection(N_('System'));
        $section->add(N_('Audit Log'), [
            'permission'    => 'audit/log',
            'url'           => 'audit/log',
            'icon'          => 'eye',
            'priority'      => 910
        ]);
    }
} catch (Exception $e) {
    // This pops up again sooner or later anyway..
}
