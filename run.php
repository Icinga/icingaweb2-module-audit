<?php
/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

/** @var $this \Icinga\Application\Modules\Module */

$this->provideHook('audit', 'AuditLog', true);
$this->provideHook('audit', 'AuditStream', true);
