<?php

require_once('./init.php');

try {

    $payload = [
        'type' => 'integration',
        'integration_type' => 'webhook',
        'enabled' => true,
        'name' => 'My Webhook Name',
        'description' => 'An example webhook integration from the SDK',
        'observes' => [
            'product.created',
            'product.updated',
            'product.deleted'
        ],
        'configuration' => [
            'url' => 'https://your.domain.com/webhooks',
            'secret' => 'opensesame'
        ]
    ];

    $created = $moltin->integrations->create($payload);
    echo "\n\tCreated in:\t\t" . $created->getExecutionTime() . " seconds (inc network)";

    $payload['id'] = $created->data()->id;
    $payload['enabled'] = false;

    $updated = $moltin->integrations->update($created->data()->id, ['data' => $payload]);
    echo "\n\tUpdated in:\t\t" . $updated->getExecutionTime() . " seconds (inc network)";

    $response = $moltin->integrations->all();
    $integrations = $response->data();
    echo "\n\tListed in:\t\t" . $response->getExecutionTime() . " seconds (inc network)";
    echo "\n\tMoltin Trace ID:\t" . $response->getRequestID() . "\n\n";

    $format = 'table';
    if (isset($argv[1])) {
        if (explode("=", $argv[1])[1] === 'json') {
            $format = 'json';
        }
    }

    if ($format === 'table') {

        $table = new Console_Table();
        $table->setHeaders(['ID', 'Type', 'Name', 'Description', 'Enabled', 'Observes', 'Configuration']);

        $i = 0;
        foreach($integrations as $integration) {
            $table->addRow([
                $integration->id,
                $integration->type,
                $integration->name,
                $integration->description,
                $integration->enabled ? 'true' : 'false',
                print_r($integration->observes, true),
                print_r($integration->configuration, true)
            ]);
            if ($i < count($integrations) - 1) {
                $table->addSeparator();
            }
            $i++;
        }

        echo $table->getTable();

    } else if ($format === 'json') {

        print_r($response->getRaw());

    }

    // Here's how you can get your logs for this integration (commented out because there won't be any):
#    $jobs = $moltin->integrations->getJobs($integration->id)->data();
#    $logs = $moltin->integrations->getLogs($integration->id, $jobs[0]->id);

    $moltin->integrations->delete($created->data()->id);

} catch(Exception $e) {

    echo "An exception occurred calling the moltin API:\n\n";
    echo $e->getMessage() . "\n\n";
    var_dump($e);
    exit;

}
