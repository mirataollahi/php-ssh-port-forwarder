<?php

/*
 * Define Application Network Configs
 */

const SSH_PORT = 22;
const SSH_HOST = '10.10.10.1';
const PORTS = ['80:80', '443:443'];


/*
 * Application Logs Levels
 */
const INFO_LOG = 2;
const SUCCESS_LOG = 4;
const WARNING_LOG = 8;
const ERROR_LOG = 16;
const LOGS_TABLE = 'logs';
const ACTIVE_LOGS = INFO_LOG | SUCCESS_LOG | WARNING_LOG | ERROR_LOG;
const DATABASE_LOG = true;


