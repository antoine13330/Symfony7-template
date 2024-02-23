<?php

namespace App\Tests\Controller\Authentification;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Toolbox\PHPUnit\LogFormat;

class AuthenticationTest extends ApiTestCase
{
    private $token = null;
    private $refreshToken = null;


    public function testRegister(): void
    {
        $registerPATH = '/api/register';
        $response = static::createClient()->request('POST', $registerPATH, [
            'json' => [
                'username' => 'admin@admin.com',
                'password' => 'password',
            ],
        ]);

        $assertMessageFormatter = new LogFormat('Authentification', 'testRegister', $registerPATH, 'POST', $response->getStatusCode());
        $this->assertResponseIsSuccessful($assertMessageFormatter->getTestMessage('The response is not successful'));
        $this->assertJson($response->getContent(),  $assertMessageFormatter->getTestMessage('JSON response is not valid'));
        $registerDatas = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $registerDatas, $assertMessageFormatter->getTestMessage('Token key is missing'));
        $this->assertArrayHasKey('refreshToken', $registerDatas, $assertMessageFormatter->getTestMessage('RefreshToken key is missing'));
    }
    /**
     * @depends testRegister
     */
    public function testLoginCheck(): array
    {
        $loginPATH = '/api/login_check';
        $response = static::createClient()->request('POST', $loginPATH, [
            'json' => [
                'username' => 'admin@admin.com',
                'password' => 'password',
            ],
        ]);
        $assertMessageFormatter = new LogFormat('Authentification', 'testLoginCheck', $loginPATH, 'POST', $response->getStatusCode());
        $this->assertResponseIsSuccessful($assertMessageFormatter->getTestMessage('The response is not successful'));
        $this->assertJson($response->getContent(), $assertMessageFormatter->getTestMessage('JSON response is not valid'));
        $loginDatas = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $loginDatas, $assertMessageFormatter->getTestMessage('Token key is missing'));
        $this->assertArrayHasKey('refreshToken', $loginDatas, $assertMessageFormatter->getTestMessage('RefreshToken key is missing'));
        return ['refreshToken' => $loginDatas['refreshToken']];
    }

    /**
     * @depends testLoginCheck
     */
    public function testRefreshToken(array $tokens): void
    {
        $refreshTokenPATH = '/api/refresh/token';
        $response = static::createClient()->request('POST', $refreshTokenPATH, [
            'json' => [
                'refreshToken' => $tokens['refreshToken'],
            ],
        ]);
        $assertMessageFormatter = new LogFormat('Authentification', 'testRefreshToken', $refreshTokenPATH, 'POST', $response->getStatusCode());
        $this->assertResponseIsSuccessful($assertMessageFormatter->getTestMessage('The response is not successful'));
        $this->assertJson($response->getContent(), $assertMessageFormatter->getTestMessage('JSON response is not valid'));
        $refreshDatas = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $refreshDatas, $assertMessageFormatter->getTestMessage('Token key is missing'));
        $this->assertArrayHasKey('refreshToken', $refreshDatas, $assertMessageFormatter->getTestMessage('RefreshToken key is missing'));
    }
}
