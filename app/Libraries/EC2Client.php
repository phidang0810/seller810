<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/17/18
 * Time: 11:49 AM
 */

namespace App\Libraries;

class EC2Client
{
    protected $client;

    public function __construct()
    {
        $this->client = new \Aws\Ec2\Ec2Client([
            'version' => '2016-11-15',
            'region' => env('AWS_S3_REGION'),
            'credentials' => [
                'key'    => env('AWS_KEY_ID'),
                'secret' => env('AWS_KEY_SECRET')
            ]
        ]);
    }

    public function start()
    {
        $result = $this->client->startInstances([
            'InstanceIds' => [env('PROCESS_INSTANCE_ID')]
        ]);

        return $result;
    }

    public function stop()
    {
        $result = $this->client->stopInstances([
            'InstanceIds' => [env('PROCESS_INSTANCE_ID')]
        ]);

        return $result;
    }
}