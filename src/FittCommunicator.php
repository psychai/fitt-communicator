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
        $params = [];
        if (!empty($clientId)) {
            $params[] = 'id='.$clientId;
        }
        if (!empty($pass1)) {
            $params[] = 'pass1='.$pass1;
        }
        if (!empty($pass2)) {
            $params[] = 'pass2='.$pass2;
        }
        if (!empty($pass3)) {
            $params[] = 'pass3='.$pass3;
        }
        if (!empty($pass4)) {
            $params[] = 'pass4='.$pass4;
        }
        if (!empty($pass5)) {
            $params[] = 'pass5='.$pass5;
        }

        $url = '/fitt-communicator/login';
        $string = '';
        if (!empty($params)) {
            $string = '?';
            foreach ($params as $param) {
                $string .= $param . '&';
            }
            $string = substr($string, 0, -1);
        }
        return redirect($this->client->get($url.$string)->getBody()->getContents());
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

        if (empty($this->config['fitt-communicator']['callback_url'])) {
            die('Please setup your FITT_COMMUNICATOR_CALLBACK_URL. See the README.md for more information.');
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
