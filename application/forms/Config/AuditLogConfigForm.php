<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\Forms\Config;

use Icinga\Forms\ConfigForm;

class AuditLogConfigForm extends ConfigForm
{
    public function createElements(array $formData): void
    {
        $this->addElement(
            'select',
            'log_type',
            [
                'autosubmit'    => true,
                'label'         => $this->translate('Standard Log'),
                'description'   => $this->translate('Human-readable message log'),
                'multiOptions'  => [
                    'none'      => $this->translate('None', 'log.type'),
                    'file'      => $this->translate('File'),
                    'syslog'    => 'Syslog'
                ]
            ]
        );
        if (isset($formData['log_type']) && $formData['log_type'] === 'file') {
            $this->addElement(
                'text',
                'log_path',
                [
                    'label'         => $this->translate('Standard Log Path'),
                    'description'   => $this->translate('The full path to the standard log'),
                    'placeholder'   => '/var/log/icingaweb2/audit.log'
                ]
            );
        } elseif (isset($formData['log_type']) && $formData['log_type'] === 'syslog') {
            $this->addElement(
                'text',
                'log_ident',
                [
                    'label'         => $this->translate('Ident'),
                    'description'   => $this->translate('The identifier to use for syslog messages'),
                    'placeholder'   => 'icingaweb2-audit'
                ]
            );
            $this->addElement(
                'select',
                'log_facility',
                [
                    'label'         => $this->translate('Facility'),
                    'description'   => $this->translate('The facility to send syslog messages to'),
                    'multiOptions'  => [
                        ''          => 'auth',      // The default
                        'authpriv'  => 'authpriv',
                        'user'      => 'user',
                        'local0'    => 'local0',
                        'local1'    => 'local1',
                        'local2'    => 'local2',
                        'local3'    => 'local3',
                        'local4'    => 'local4',
                        'local5'    => 'local5',
                        'local6'    => 'local6',
                        'local7'    => 'local7'
                    ]
                ]
            );
        }

        $this->addElement(
            'checkbox',
            'stream_format',
            [
                'autosubmit'        => true,
                'label'             => $this->translate('JSON Log'),
                'description'       => $this->translate('Machine-parsable JSON objects'),
                'checkedValue'      => 'json',
                'uncheckedValue'    => 'none'
            ]
        );
        if (isset($formData['stream_format']) && $formData['stream_format'] === 'json') {
            $this->addElement(
                'text',
                'stream_path',
                [
                    'label'         => $this->translate('JSON Log Path'),
                    'description'   => $this->translate('The full path to the JSON log'),
                    'placeholder'   => '/var/log/icingaweb2/audit.json'
                ]
            );
        }
        $this->addElement(
            'checkbox',
            'iplogging',
            [
                'autosubmit'        => true,
                'label'             => $this->translate('IP logging'),
                'description'       => $this->translate('Log remote IPs into the audit log'),
            ]
        );
    }
}
