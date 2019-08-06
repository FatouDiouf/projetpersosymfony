<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient([],[
            'PHP_AUTH_USER'=>'toufa',
            'PHP_AUTH_PW'=>'passer'
        ] 

        );
         $client->request('POST', '/security/register', [],[],
         ['CONTENT_TYPE'=>"application/json"  ],
    ' {
        "password" :"passer",
        "nom" : "Fatou Diouf",
        "email" : "toufa@toufa.com",
        "adresse" : "Corniche",
        "telephone" : "778418109",
        "statut" : "bloqué",
        "partenaire" : Null,
        "compte" : Null,
        "image_name" : "admin.jpg",
        "updatedAt" : NULL



         }'

        );

        $a = $client->getResponse();
        var_dump($a);
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
}
