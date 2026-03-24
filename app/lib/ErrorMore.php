<?php

register_shutdown_function("fatal_handler");

function fatal_handler() {
    $errfile = "unknown file";
    $errstr = "shutdown";
    $errno = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if ($error !== NULL) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];
    }
    trigger_error(format_error($errno, $errstr, $errfile, $errline), E_USER_ERROR);
}

function format_error($errno, $errstr, $errfile, $errline) {
    $trace = print_r(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT), true);



    //$content  = "<table><thead bgcolor='#c8c8c8'><th>Item</th><th>Description</th></thead><tbody>";
    $content = "\nError: $errstr\n";
    $content .= "Errno:  $errno\n";
    $content .= "File: $errfile\n";
    $content .= "Line: $errline\n";
    $content .= "Trace: \n$trace\n";


    return $content;
}

?>
