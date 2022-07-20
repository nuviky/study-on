<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Exception\BillingUnavailableException;
use App\Security\User;
use JsonException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;

class BillingClient
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @throws BillingUnavailableException
     * @throws JsonException
     */
    public function auth($data): User
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $_ENV['BILLING_URL'] . '/api/v1/auth',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ]
        ];
        curl_setopt_array($ch, $options);
        $session = curl_exec($ch);

        if ($session === false) {
            throw new BillingUnavailableException('Сервис временно недоступен. Попробуйте авторизоваться позднее');
        }
        curl_close($ch);
        $errors = json_decode($session, true, 512, JSON_THROW_ON_ERROR);
        if (isset($errors['code'])) {
            if ($errors['code'] === 401 || $errors['code'] === 400) {
                throw new UserNotFoundException('Неправильная пара логин/пароль');
            }
        }

        $userDTO = $this->serializer->deserialize($session, UserDTO::class, "json");
        $user = new User();
        $user->setApiToken($userDTO->token);
        $decodedJWT = $this->getJWT($userDTO->token);
        $user->setEmail($decodedJWT['email']);
        $user->setRoles($decodedJWT['roles']);
        return $user;
    }

    public function getCurrentUser($user)
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $_ENV['BILLING_URL'] . '/api/v1/users/current',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $user->getApiToken()
            ]
        ];
        curl_setopt_array($ch, $options);
        $session = curl_exec($ch);

        if ($session === false) {
            throw new BillingUnavailableException('Сервис временно недоступен. Попробуйте повторить позднее');
        }

        return $this->serializer->deserialize($session, UserDTO::class, 'json');
    }

    /**
     * @throws BillingUnavailableException
     * @throws JsonException
     */
    public function register($data): User
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $_ENV['BILLING_URL'] . '/api/v1/register',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data, JSON_THROW_ON_ERROR),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ]
        ];
        curl_setopt_array($ch, $options);
        $session = curl_exec($ch);

        if ($session === false) {
            throw new BillingUnavailableException('Сервис временно недоступен. Попробуйте повторить позднееа');
        }
        curl_close($ch);
        $errors = json_decode($session, true, 512, JSON_THROW_ON_ERROR);
        if (isset($errors['errors'])) {
            throw new BillingUnavailableException("Проверьте правильность введных вами данных");
        }

        $userAuthDTO = $this->serializer->deserialize($session, UserDTO::class, "json");
        $user = new User();
        $user->setApiToken($userAuthDTO->token);
        $decodedJWT = $this->getJWT($userAuthDTO->token);
        $user->setEmail($decodedJWT['email']);
        $user->setRoles($decodedJWT['roles']);
        return $user;
    }

    /**
     * @throws JsonException
     */
    public function getJWT($token)
    {
        $parts = explode('.', $token);
        $payload = json_decode(base64_decode($parts[1]), true, 512, JSON_THROW_ON_ERROR);
        return [
            'email' => $payload['username'],
            'roles' => $payload['roles'],
            'exp' => $payload['exp']
        ];
    }
}
