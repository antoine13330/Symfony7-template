<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\RefreshTokenController;

#[ORM\Entity]
#[ApiResource(
    shortName: 'Refresh token',
    operations: [
        new Post(
            name: 'refresh_token',
            uriTemplate: '/refresh/token',
            controller: RefreshTokenController::class,
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'json',
                                'properties' => [
                                    'refreshToken' => [
                                        'type' => 'string',
                                        'example' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjYwNzIwNzYsImV4cCI6MTYyNjA3NTY3Niwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlcm5hbWVAZ21haWwuY2'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'responseBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'json',
                                'properties' => [
                                    'refreshToken' => [
                                        'type' => 'string',
                                        'example' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjYwNzIwNzYsImV4cCI6MTYyNjA3NTY3Niwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlcm5hbWVAZ21haWwuY29tIn0.0'
                                        ],
                                    
                                    'token' => [
                                        'type' => 'string',
                                        'example' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjYwNzIwNzYsImV4cCI6MTYyNjA3NTY3Niwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlcm5hbWVAZ21haWwuY29tIn0.0'
                                    ],
                                
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        )
    ],
)]
class RefreshToken extends BaseRefreshToken
{
}
