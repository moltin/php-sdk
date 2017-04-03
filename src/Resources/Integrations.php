<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;

class Integrations extends Resource
{
    public $uri = 'integrations';

    /**
     *  Return Jobs for an integration
     *
     *  @param string $integrationID
     *  @return Moltin\Response
     */
    public function getJobs($integrationID)
    {
        return $this->call('GET', false, $integrationID . '/jobs');
    }

    /**
     *  Return all logs
     *
     *  @param string $integrationID
     *  @return Moltin\Response
     */
    public function getLogs($integrationID = false, $jobID = false)
    {
        $uri = "logs"; // get all logs by default
        if (!empty($integrationID) && !empty($jobID)) {
            $uri = $integrationID . "/jobs/" . $jobID . "/logs"; // get logs for a given job
        }
        return $this->call('GET', false, $uri);
    }

}
