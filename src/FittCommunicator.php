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
     * @param string|null $clientId
     * @param string|null $pass1
     * @param string|null $pass2
     * @param string|null $pass3
     * @param string|null $pass4
     * @param string|null $pass5
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function login(
        string $clientId = null,
        string $pass1 = null,
        string $pass2 = null,
        string $pass3 = null,
        string $pass4 = null,
        string $pass5 = null
    ): RedirectResponse
    {
        $url = '/fitt-communicator/login';

        return redirect($this->client->get($url, [
            'query' => [
                'clientId' => $clientId,
                'pass1' => $pass1,
                'pass2' => $pass2,
                'pass3' => $pass3,
                'pass4' => $pass4,
                'pass5' => $pass5
            ]
        ])->getBody()->getContents());
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
            'pass1' => 'optional|string',
            'pass2' => 'optional|string',
            'pass3' => 'optional|string',
            'pass4' => 'optional|string',
            'pass5' => 'optional|string',
        ]);

        if ($request->get('client_id') !== hash('sha256', $this->config['fitt-communicator']['client_id'].$this->config['fitt-communicator']['client_secret'].$request->get('nonce'))) {
            abort(401);
        }

        if (empty($this->config['fitt-communicator']['callback_url'])) {
            die('Please setup your FITT_COMMUNICATOR_CALLBACK_URL. See the README.md for more information.');
        }

        $string = '';
        if ($request->has('pass1')) {
            $string .= '&pass1='.$request->get('pass1');
        }
        if ($request->has('pass2')) {
            $string .= '&pass2='.$request->get('pass2');
        }
        if ($request->has('pass3')) {
            $string .= '&pass3='.$request->get('pass3');
        }
        if ($request->has('pass4')) {
            $string .= '&pass4='.$request->get('pass4');
        }
        if ($request->has('pass5')) {
            $string .= '&pass5='.$request->get('pass5');
        }

        return redirect($this->config['fitt-communicator']['callback_url'].'?pid='.$request->get('pid').'&action='.$request->get('action').$string);
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
