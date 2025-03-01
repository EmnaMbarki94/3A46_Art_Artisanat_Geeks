<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ElevenLabsTtsService
{
    private $httpClient;
    private $apiKey;
    private $audioFilePath;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $audioFilePath)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->audioFilePath = $audioFilePath;
    }

    public function synthesizeSpeech(string $text, string $voiceId = 'JdwJ7jL68CWmQZuo7KgG'): string
    {
        $url = "https://api.elevenlabs.io/v1/text-to-speech/$voiceId";

        
        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'xi-api-key' => $this->apiKey,
            ],
            'json' => [
                'text' => $text,
                'model_id' => "eleven_multilingual_v2",
                'voice_settings' => [
                    'stability' => 0.5,
                    'similarity_boost' => 0.5,
                ],
            ],
        ]);

        // Save the audio content to a file
        $audioContent = $response->getContent();
        file_put_contents($this->audioFilePath, $audioContent);

        return $this->audioFilePath;
    }
}