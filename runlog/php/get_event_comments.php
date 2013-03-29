<?php

require_once 'ajax_page_init.php';

// single event to get comments for
$event_id = $_GET['event_id'];

$validationResult = validatePositiveInt($event_id);
if (!$validationResult->isValid()) {
    logAndDie("Invalid event id: " . $validationResult->getMessage(), debug_backtrace());
}

