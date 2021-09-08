<?php

namespace Psychai\FittCommunicator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FittCommunicator
{
    /**
     * @var array
     */
    private array $config;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->client = new Client([
            'verify' => in_array($this->config['app.env'], ['prod', 'production', 'live']),
            'headers' => [
                'Accept' => 'application/json',
                'X-Fitt-Communicator-Id' => $this->config['fitt-communicator']['client_id'],
                'X-Fitt-Communicator-Hash' => hash('sha256', $this->config['fitt-communicator']['client_id'] . $this->config['fitt-communicator']['client_secret'])
            ],
            'base_uri' => self::getBaseUrl(),
        ]);
    }

    /**
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function login(): RedirectResponse
    {
        return redirect($this->client->get('/fitt-communicator/login')->getBody()->getContents());
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        $request->validate([
            'client_id' => 'required|string',
            'pid' => 'required|string',
            'nonce' => 'required|string',
            'action' => 'required|string',
        ]);

        if ($request->get('client_id') !== hash('sha256', $this->config['fitt-communicator']['client_id'].$this->config['fitt-communicator']['client_secret'].$request->get('nonce'))) {
            abort(401);
        }

        return redirect($this->config['fitt-communicator']['callback_url'].'?pid='.$request->get('pid').'&action='.$request->get('action'));
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
