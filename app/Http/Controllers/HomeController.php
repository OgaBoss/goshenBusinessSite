<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;

class HomeController extends Controller
{
    //
    private $request;
    private $guzzle;

    public function __construct(Request $request, Client $client){
        $this->request = $request;
        $this->guzzle = $client;
    }

    public function callBack(){
        // Get Code from url
        $code = $this->request->code;

        if(!empty($code)){
            $client = new Client();
            $result = $client->request('POST','http://oncusp.com/ogaboss/d4m/token/getToken', [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                    'client_id'     => 'a518d648aa2fb1ad',
                    'client_secret' => '917998fef463df03',
                    'redirect_uri'  => 'http://localhost:8000/oauth2/callback'
                ]
            ]);

            $token = \GuzzleHttp\json_decode($result->getBody(), true);

            echo $token['access_token'];

            if(isset($token['access_token'])) {
                $resource = $client->request('POST','http://oncusp.com/ogaboss/d4m/resource/getResource', [
                    'form_params' => [
                        'access_token'    => $token['access_token']
                    ]
                ]);

                $resourceBody = json_decode($resource->getBody(), true);

                var_dump($resourceBody);

                if($resourceBody['status'] == 'success') {
                    return view('user', ['user' => $resourceBody['data']]);
                }else{
                    return view('user');
                }
            }else{
                return view('user');
            }
        }
    }
}
