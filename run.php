<?php

// SPDX-FileCopyrightText: 2018 Icinga GmbH <https://icinga.com>
// SPDX-License-Identifier: GPL-3.0-or-later

/** @var $this \Icinga\Application\Modules\Module */

$this->provideHook('audit', 'AuditLog', true);
$this->provideHook('audit', 'AuditStream', true);
