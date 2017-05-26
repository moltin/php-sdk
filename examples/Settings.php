<?php

require_once('./init.php');

try {

    $response = $moltin->settings->all();
    $settings = $response->data();
    echo "\n\tExecution time:\t\t" . $response->getExecutionTime() . " seconds (inc network)";
    echo "\n\tMoltin Trace ID:\t" . $response->getRequestID() . "\n\n";

    $format = 'table';
    if (isset($argv[1])) {
        if (explode("=", $argv[1])[1] === 'json') {
            $format = 'json';
        }
    }

    if ($format === 'table') {

        echo "Your store settings:\n\n";

        $table = new Console_Table();
        $table->setHeaders(['Page Length', 'Email SMTP PORT', 'Email Encryption', 'Timezone', 'Password Policy']);

        $table->addRow([
            $settings->page_length,
            $settings->email_smtp_port,
            $settings->email_encryption,
            $settings->timezone,
            $settings->password_policy,
        ]);

        echo $table->getTable();

    } else if ($format === 'json') {

        print_r($response->getRaw(), true);

    }

    // settings don't have an ID so the first param is false
    $newPageLength = 50;
    $moltin->settings->update(false, [
        'data' => ['type' => 'settings', 'page_length' => $newPageLength]
    ]);

    echo "\nPage Length updated to " . $newPageLength . "\n\n";

    // reset the page length
    $moltin->settings->update(false, [
        'data' => ['type' => 'settings', 'page_length' => $settings->page_length]
    ]);

    echo "[Page length reverted]\n";

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
