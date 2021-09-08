<?php

namespace Psychai\FittCommunicator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Psychai\FittCommunicator\FittCommunicator;

class FittCommunicatorController extends Controller
{
    public function login(Request $request, FittCommunicator $communicator)
    {
        return $communicator->login(
            $request->get('clientId'),
            $request->get('pass1'),
            $request->get('pass2'),
            $request->get('pass3'),
            $request->get('pass4'),
            $request->get('pass5')
        );
    }

    public function callback(Request $request, FittCommunicator $communicator)
    {
        return $communicator->callback($request);
    }
}