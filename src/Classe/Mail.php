<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    //Clés à compléter :
    private string $api_key = '';
    private string $api_key_secret = '';

    public function send($to_email, $to_name, $subject, $content)
    {
        /* Manipulation effectuée :
         * Pour régler le problème du certificat
         * Allez dans : vendor/guzzlehttp/guzzle/src/Handler/Client.php. et à la ligne 233 changer le 'verify' = true, en 'verify' = false,
         */

        /* Manipulation effectuée :
         * essayer de changer ceux deux lignes 358 et 359 de fichier   CurlFactory.php
         * $conf[\CURLOPT_SSL_VERIFYHOST] = 2;
         * $conf[\CURLOPT_SSL_VERIFYPEER] = true;
         * par
         * $conf[\CURLOPT_SSL_VERIFYHOST] = 0; $conf[\CURLOPT_SSL_VERIFYPEER] = false;
         * ce fichier vous le trouvez en suivant ce chemin vendor\guzzlehttp\guzzle\src\Handler\CurlFactory.php
         */


        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "nicolas@devnantes.fr",
                        'Name' => "MÉGA PROMO"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3441742,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        // La variable 'content' se situe dans le template du message sur mailJet
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}