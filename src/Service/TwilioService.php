<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private Client $client;
    private string $twilioNumber;

    public function __construct(string $sid, string $token, string $twilioNumber)
    {
        $this->client = new Client($sid, $token);
        $this->twilioNumber = $twilioNumber;
    }

    public function sendSms(string $to, string $message): void
    {
        $reciver="+216".$to;
        $this->client->messages->create(
            $reciver,
            [
                'from' => $this->twilioNumber,
                'body' => $message
            ]
        );
    }
}
