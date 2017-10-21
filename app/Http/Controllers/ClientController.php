<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;

class ClientController extends Controller
{
    public function getClients()
    {
        return Client::all();
    }

    public function addClient(Request $request)
    {
        $client_name = json_decode($request->getContent(), true)['client_name'];
    	
        $client = new Client;
        $client->name = $client_name;
        $client->save();
        return $client->id;
    }
}