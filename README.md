# Audit module for Icinga Web 2

#### Table of Contents

1. [About](#about)
2. [License](#license)
3. [Support](#support)
4. [Requirements](#requirements)
5. [Installation](#installation)
6. [Configuration](#configuration)

## About

## License

Icinga Web 2 and this Icinga Web 2 module are licensed under the terms of the GNU General Public License Version 2,
you will find a copy of this license in the LICENSE file included in the source package.

## Support

Join the [Icinga community channels](https://www.icinga.com/community/get-involved/) for questions.

## Requirements

* [Icinga Web 2](https://www.icinga.com/products/icinga-web-2/) (>= 2.6.0)

## Installation

Extract this module to your Icinga Web 2 modules directory as `audit` directory.

Git clone:

    cd /usr/share/icingaweb2/modules
    git clone https://github.com/Icinga/icingaweb2-module-audit.git audit

Tarball download (latest [release](https://github.com/Icinga/icingaweb2-module-audit/releases/latest)):

    cd /usr/share/icingaweb2/modules
    wget https://github.com/Icinga/icingaweb2-module-audit/archive/v1.0.0.zip
    unzip v1.0.0.zip
    mv icingaweb2-module-audit-1.0.0 audit

### Enable Icinga Web 2 module

Enable the module in the Icinga Web 2 frontend in `Configuration -> Modules -> audit -> enable`.
You can also enable the module by using the `icingacli` command:

    icingacli module enable audit

## Configuration

By default the audit module does not log anything. Its logging facilities need to be configured first.

There are currently two choices:

* Standard Log
* JSON Log

### Standard Log

The standard log is a normal log with human readable messages. It's possible to log to a file and to syslog.
The configuration view in the frontend is located here: `Configuration -> Modules -> audit -> Configuration`

> **Note**
>
> When logging to a file and with the proper permission, this can be viewed in the frontend under `System -> Audit Log`

### JSON Log

The JSON log is supposed to be consumed by other applications. It writes one JSON object per line to a file.

These objects have the following properties:

* `activity_time`  
    A unix timestamp specifying when the activity occurred.
* `activity`  
    An arbitrary identifier specifying the type of activity.
* `identity`  
    An arbitrary name identifying the responsible subject.
* `message`  
    A human readable message. This is the same that appears in the standard log.
* `data` *(may be absent)*  
    An arbitrary number of additional properties dependent on the type of activity.

Please see the documentation of the type of activity for more details. ([Icinga Web 2 activities](https://www.icinga.com/docs/icingaweb2/latest/doc/15-Auditing/))

An example for *Filebeat* how this log may be consumed can be found [here](https://www.elastic.co/blog/structured-logging-filebeat).
