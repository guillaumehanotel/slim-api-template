<?php

use Carbon\Carbon;

const LOG_FILE = "storage/logs/errors.log";

function isProdEnvionnement() {
    return APP_ENV != 'dev';
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false;
}

function printToLogs($message) {
    $date = Carbon::now()->toDateTimeString();
    error_log("[$date] $message\n", 3, LOG_FILE);
}

function contains($string, $substring) {
    return strpos($string, $substring) !== false;
}

function areExtensionsEnabled(array $extensions) {
    $areAllExtensionsEnabled = true;
    foreach ($extensions as $extension){
        if(!extension_loaded($extension)) {
            echo "$extension extension is not enabled";
            $areAllExtensionsEnabled = false;
        }
    }
    return $areAllExtensionsEnabled;
}
