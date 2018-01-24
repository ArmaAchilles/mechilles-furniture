<?php

namespace App\Http\Controllers;

use App\Furniture;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FurnitureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if the data was actually passed and if not return an error message.
        $this->validate($request, [
            'furnitureData' => 'required',
            'g-recaptcha-response' => 'required'
        ]);

        // Do a POST request to Google's reCAPTCHA API service and check if the CAPTCHA is valid.
        $captchaClient = new Client();
        $captchaResponse = $captchaClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => env('CAPTCHA_SECRET'),
                'response' => $request->input('g-recaptcha-response')
            ]
        ]);

        // Decode the JSON
        $captchaContents = json_decode($captchaResponse->getBody()->getContents());

        // If failed to contact server or returns as an error, then exit with error message.
        if ($captchaResponse->getStatusCode() != 200 || !$captchaContents->success)
        {
            return redirect('/')->with('error', 'An error occured when checking your reCAPTCHA status! Please try again later!');
        }

        // Create a Gist
        $gistClient = new Client();
        $gistResponse = $gistClient->request('POST', 'https://api.github.com/gists', 
        [
            'json' => 
            [
                'description' => 'Achilles generated Furniture module data.',
                'public' => false,
                'files' => 
                [
                    'achillesFurnitureData.sqf' => 
                    [
                        'content' => $request->input('furnitureData')
                    ]
                ]
            ]
        ]);

        if ($gistResponse->getStatusCode() != 201)
        {
            return redirect('/')->with('error', 'Failed to create a Gist. Please try again later!');
        }

        $gistURL = (json_decode($gistResponse->getBody()->getContents()))->html_url;
        
        // If everything is fine till here then create a new entry in our table and save it.
        $furniture = new Furniture;
        $furniture->furniture = $request->input('furnitureData');
        $furniture->ip = $request->ip();
        $furniture->gist = $gistURL;
        $furniture->save();
        
        // Create a message in Discord
        $discordClient = new Client();
        $discordResponse = $discordClient->request('POST', env('DISCORD_BOT'), [
            'form_params' => [
                'content' => $gistURL
            ]
        ]);

        // Redirect back with a success message.
        return redirect('/')->with('success', 'Data submited, thank you!');
    }
}
