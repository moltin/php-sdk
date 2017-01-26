<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;
use Moltin\Exceptions\FileNotFoundException;

class Files extends Resource
{
    public $uri = 'files';

    public function create($data)
    {
        // build the data for multipart
        $multipartData = $this->parseMulitpartData($data);

        // get the local file path
        $localFile = $this->getFileLocation($data['file']);

        // add the file to the payload
        $multipartData[] = [
            'name' => 'file',
            'contents' => fopen($localFile, 'r')
        ];

        // make the API call
        $filesResponse = $this->call('POST', $multipartData, false, ['Content-Type' => 'multipart/form-data']);

        return $filesResponse;
    }

    /**
     *  Get the file location given the value passed by the method call
     *  Will retrieve a remote file if the passed location is a URL
     *
     *  @param string $file the local path or URL of the file
     *  @return string the local file path
     *  @throws Moltin\Exceptions\FileNotFoundException
     */
    public function getFileLocation($file)
    {
        // get the file info
        if (filter_var($file, FILTER_VALIDATE_URL)) { 

            // the file is a URL, download it and set the local file path
            $localFile = '/tmp/moltinfile_' . rand(100000, 900000);
            file_put_contents($localFile, file_get_contents($file));
        } else {

            // the file is stored locally, set it
            $localFile = $file;
        }

        // be sure it exists
        if (!file_exists($localFile)) {
            throw new FileNotFoundException();
        }

        return $localFile;
    }

    /**
     *  Given the call $data, parse and return it in a format that is compatible with the HTTP Client
     *
     *  @param array $data
     *  @return array the parsed array
     */
    public function parseMulitpartData($data)
    {
        $multipartData = [];
        foreach($data as $name => $contents) {
            $name = strtolower($name);
            if ($name !== 'file') {
                $multipartData[] = [
                    'name' => $name,
                    'contents' => $contents
                ];
            }
        }
        return $multipartData;
    }
}
