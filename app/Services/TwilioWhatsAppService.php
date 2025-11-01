<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwilioWhatsAppService
{
    public function __construct()
    {
        // Constructor can be used for dependency injection if needed
    }

    /**
     * Send a WhatsApp message using the VeevoTech API.
     *
     * @param string $to The recipient's phone number in E.164 format.
     * @param array $bodyVariables The dynamic variables for the message template.
     * @param int $templateId The ID of the message template.
     * @return array The API response as an associative array.
     */
    public function sendMessage(string $to, array $bodyVariables, int $templateId)
    {
        try {
            $response = Http::withHeaders([
                'hash' => '38bd44690170d1afe9f89edebf147d4b', // Replace with your actual VeevoTech hash
            ])->post('https://wa-api.veevotech.com/wa/v1/send_message', [
                'to' => $to,
                'type' => 'template',
                'template_id' => $templateId,
                'header_variables' => [], // None used in your template
                'body_variables' => $bodyVariables,
                'media_url' => [],
                'priority' => 1,
            ]);

            // return $response->json();
        } catch (\Exception $e) {
            // Log the error for debugging
           
            // return ['error' => $e->getMessage()];
        }
    }
}