<?php

/* Icinga Web 2 | (c) 2018 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Audit\ProvidedHook;

use InvalidArgumentException;
use Icinga\Application\Config;
use Icinga\Application\Hook\AuditHook;
use Icinga\Util\File;

class AuditLog extends AuditHook
{
    public function logMessage($time, $identity, $type, $message, array $data = null): void
    {
        $logConfig = Config::module('audit')->getSection('log');
        if ($logConfig->type === 'file') {
            $remoteip = (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            $file = new File($logConfig->get('path', '/var/log/icingaweb2/audit.log'), 'a');
            $file->fwrite(date('c', $time) . ' - ' . $remoteip . ' - ' . $identity . ' - ' . $type . ' - ' . $message . PHP_EOL);
            $file->fflush();
        } elseif ($logConfig->type === 'syslog') {
            openlog(
                $logConfig->get('ident', 'icingaweb2-audit'),
                LOG_PID,
                $this->resolveSyslogFacility($logConfig->get('facility', 'auth'))
            );
            $date = date('c', $time);
            syslog(LOG_INFO, "[$date] <$identity> <$type> $message");
        }
    }

    /**
     * Resolve the given syslog facility name to a valid identifier
     *
     * @param   string  $name
     *
     * @return  int
     *
     * @throws  InvalidArgumentException    In case of an unknown name
     */
    protected function resolveSyslogFacility(string $name): int
    {
        switch ($name) {
            case 'auth':
                return LOG_AUTH;
            case 'authpriv':
                return LOG_AUTHPRIV;
            case 'user':
                return LOG_USER;
            case 'local0':
                return LOG_LOCAL0;
            case 'local1':
                return LOG_LOCAL1;
            case 'local2':
                return LOG_LOCAL2;
            case 'local3':
                return LOG_LOCAL3;
            case 'local4':
                return LOG_LOCAL4;
            case 'local5':
                return LOG_LOCAL5;
            case 'local6':
                return LOG_LOCAL6;
            case 'local7':
                return LOG_LOCAL7;
            default:
                throw new InvalidArgumentException("Unknown syslog facility '$name'");
        }
    }
}
