<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\ProvidedHook;

use InvalidArgumentException;
use Icinga\Application\Config;
use Icinga\Application\Hook\AuditHook;
use Icinga\Util\File;

class AuditStream extends AuditHook
{
    public function logMessage($time, $identity, $type, $message, array $data = null): void
    {
        $activityData = [
            'activity_time' => $time,
            'activity'      => $type,
            'message'       => $message,
            'identity'      => $identity,
            'remoteip'      => $remoteip
        ];
        if (! empty($data)) {
            $activityData['data'] = $data;
        }

        $logConfig = Config::module('audit')->getSection('stream');
        if ($logConfig->format === 'json') {
            $json = json_encode($activityData, JSON_FORCE_OBJECT);
            if ($json === false) {
                throw new InvalidArgumentException('Failed to encode message data to JSON: ' . json_last_error_msg());
            }

            $file = new File($logConfig->get('path', '/var/log/icingaweb2/audit.json'), 'a');
            $file->fwrite($json . PHP_EOL);
            $file->fflush();
        }
    }
}
