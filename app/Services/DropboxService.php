<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\Dropbox\RefreshableTokenProvider;

class DropboxService
{
    private DropboxClient $client;

    public function __construct()
    {
        $this->client = new DropboxClient(new DropboxRefreshableTokenProvider(config('dropbox')));
    }

    /**
     * Uploads a file to Dropbox folder
     *
     * @param string $filepath path to the file you want to upload
     * @param string $upload_to path to the folder you want to upload the file to
     *
     * @return bool true if file was successfully uploaded
     */
    public function uploadFile(string $filepath, string $upload_to): bool
    {
        if (!is_file($filepath))
            return false;

        $result=$this->client->upload($upload_to.'/'.basename($filepath), file_get_contents($filepath));

        return isset($result['id']);
    }
}

class DropboxRefreshableTokenProvider implements RefreshableTokenProvider
{
    private string $token='';
    private array $config;

    public function __construct(array $config)
    {
        $this->config=$config;
    }

    public function refresh(ClientException $exception): bool
    {
        $guzzle_client = new GuzzleHttpClient;

        $refresh_token_response=$guzzle_client->request("POST", "https://{$this->config['key']}:{$this->config['secret']}@api.dropbox.com/oauth2/token", [
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->config['refresh_token'],
            ],
        ]);

        if ($refresh_token_response->getStatusCode()===200)
        {
            $result=json_decode($refresh_token_response->getBody(), true);

            if (!$result || !isset($result['access_token']))
                return false;

            $this->token=$result['access_token'];

            return true;
        }
        else
            return false;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
