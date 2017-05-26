<?php

require_once('./init.php');

try {

    // from the local disk
    $fileCreateLocal = $moltin->files->create(['public' => 'false', 'file' => './assets/image.jpg']);
    $fileIDs[] = $fileCreateLocal->data()->id;

    // from a URL
    $fileCreateLocal = $moltin->files->create(['public' => 'true', 'file' => 'https://placeholdit.imgix.net/~text?&w=350&h=150']);
    $fileIDs[] = $fileCreateLocal->data()->id;

    $filesResponse = $moltin->files->all();
    $files = $filesResponse->data();

    $format = 'table';
    if (isset($argv[1])) {
        if (explode("=", $argv[1])[1] === 'json') {
            $format = 'json';
        }
    }

    if ($format === 'table') {

        $table = new Console_Table();
        $table->setHeaders(['ID / Name', 'Public', 'URL', 'Size']);

        $i = 0;
        foreach($files as $file) {
            $table->addRow([
                $file->id . "\n" . $file->file_name,
                $file->public ? 'true' : 'false',
                $file->link->href,
                $file->file_size,
            ]);
            if ($i < count($files) - 1) {
                $table->addSeparator();
            }
            $i++;
        }

        echo $table->getTable();

    } else if ($format === 'json') {

        print_r($file->getRaw());

    }

    // cleanup
    foreach($fileIDs as $fileID) {
        $moltin->files->delete($fileID);
        echo "[DELETED file " . $fileID . "]\n";
    }


} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e->getMessage());
    exit;

}
