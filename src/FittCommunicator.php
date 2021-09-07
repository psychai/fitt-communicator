<?php

namespace Psychai\FittCommunicator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psychai\FittCommunicator\Exceptions\LoginException;
use Psychai\FittCommunicator\Exceptions\RegistrationException;

class FittCommunicator
{
    private array $config;
    private Client $client;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->client = new Client([
            'defaults' => [
                'verify' => in_array($this->config['app.env'], ['prod', 'production', 'live'])
            ],
            'base_uri' => self::getBaseUrl(),
            'X_FITT_COMMUNICATOR_ID' => $this->config['fitt-communicator']['client_id'],
            'X_FITT_COMMUNICATOR_HASH' => hash('sha256', $this->config['fitt-communicator']['client_id'].$this->config['fitt-communicator']['client_secret'])
        ]);
    }

    /**
     * @return string
     * @throws LoginException
     * @throws GuzzleException
     */
    public function login(): string
    {
        $response = $this->client->post('/fitt-communicator/login');

        if ($response->getStatusCode() !== 200) {
            if ($this->config['app.debug'] ?? null == true) {
                Log::error('FITT_COMMUNICATOR::login '.$response->getBody()->getContents());
            }

            throw new LoginException('Could not login to fitt communicator. Enable APP_DEBUG and see logs for more info.');
        }

        return $response->getBody()->getContents();
    }

    /**
     * @return string
     * @throws RegistrationException
     * @throws GuzzleException
     */
    public function register(): string
    {
        $response = $this->client->post('/fitt-communicator/register');

        if ($response->getStatusCode() !== 200) {
            if ($this->config['app.debug'] ?? null == true) {
                Log::error('FITT_COMMUNICATOR::login '.$response->getBody()->getContents());
            }

            throw new RegistrationException('Could not login to fitt communicator. Enable APP_DEBUG and see logs for more info.');
        }

        return $response->getBody()->getContents();
    }

    /**
     * @return string
     */
    private function getBaseUrl(): string
    {
        $url = trim(strtolower($this->config['fitt-communicator']['base_url'] ?? null));
        if (!empty($url)) {
            return $url;
        }

        $environment = trim(strtolower($this->config['app.env'] ?? null));
        if ($environment === 'prod' || $environment === 'production') {
            return 'https://manage.fitt.ai';
        }

        return 'https://manage-qa.fitt.ai';
    }
}
