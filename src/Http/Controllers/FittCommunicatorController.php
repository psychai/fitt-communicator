<?php

namespace Psychai\FittCommunicator\Http\Controllers;

use Illuminate\Routing\Controller;
use Psychai\FittCommunicator\FittCommunicator;

class FittCommunicatorController extends Controller
{
    public function login(FittCommunicator $communicator)
    {
        return $communicator->login();
    }

    public function register(FittCommunicator $communicator)
    {
        return $communicator->register();
    }
}