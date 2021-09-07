<?php

namespace Psychai\FittCommunicator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Psychai\FittCommunicator\FittCommunicator;

class FittCommunicatorController extends Controller
{
    public function login(FittCommunicator $communicator)
    {
        return $communicator->login();
    }

    public function callback(Request $request, FittCommunicator $communicator)
    {
        return $communicator->callback($request);
    }
}