<?php

namespace Psychai\FittCommunicator\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Psychai\FittCommunicator\FittCommunicator;

class FittCommunicatorController extends Controller
{
    public function login(FittCommunicator $communicator): RedirectResponse
    {
        $response = $communicator->login();

        dd($response);
    }

    public function register(FittCommunicator $communicator): RedirectResponse
    {
        $response = $communicator->register();

        dd($response);
    }
}