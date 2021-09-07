<?php

namespace Psychai\FittCommunicator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psychai\FittCommunicator\Exceptions\LoginException;
use Psychai\FittCommunicator\Exceptions\RegistrationException;

class FittCommunicator
{
    private Client $client;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->client = new Client([
            'base_uri' => self::getBaseUrl(),
            'X_FITT_COMMUNICATOR_ID' => $config['fitt-communicator.client_id'],
            'X_FITT_COMMUNICATOR_HASH' => hash('sha256', $config['fitt-communicator.client_id'].$config['fitt-communicator.client_secret'])
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
            if ($_SERVER['APP_DEBUG'] == true) {
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
            if ($_SERVER['APP_DEBUG'] == true) {
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
        $url = trim(strtolower($_SERVER['FITT_COMMUNICATOR_BASE_URL']));
        if (!empty($url)) {
            return $url;
        }

        $environment = trim(strtolower($_SERVER['APP_ENV']));
        if ($environment === 'prod' || $environment === 'production') {
            return 'https://manage.fitt.ai';
        }

        return 'https://manage-qa.fitt.ai';
    }
}
