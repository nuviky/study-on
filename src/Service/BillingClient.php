<?php

namespace App\Service;

use App\DTO\CourseDTO;
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
        $errors = json_decode($session, true);
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
            throw new BillingUnavailableException('Сервис временно недоступен. Попробуйте повторить позднее');
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

    public function getCourses()
    {
        $ch = curl_init($_ENV['BILLING_URL'] . '/api/v1/courses');
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ]
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);


        if ($res === false) {
            throw new BillingUnavailableException('Сервис временно недоступен. Попробуйте повторить позднееа');
        }
        curl_close($ch);
        return json_decode($res, true);
    }

    public function getTransactions($filter, $token)
    {
        $urlFilters = '?';
        if (isset($filter['type'])) {
            $urlFilters .= 'filter[type]=' . $filter['type'] . '&';
        }
        if (isset($filter['course_code'])) {
            $urlFilters .= 'filter[course_code]=' . $filter['course_code'] . '&';
        }
        if (isset($filter['skip_expired'])){
            $urlFilters .= 'filter[skip_expired]=' . $filter['skip_expired'];
        }
        $ch = curl_init($_ENV['BILLING_URL'] . '/api/v1/transactions' . $urlFilters);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);


        if ($res === false) {
            throw new BillingUnavailableException('Ошибка со стороны сервера');
        }
        if (isset($resJSON['code'])) {
            throw new BillingUnavailableException('Ошибка со стороны сервера');
        }
        return json_decode($res, true);
    }

    public function getCourse(string $code)
    {
        $ch = curl_init($_ENV['BILLING_URL'] . '/api/v1/courses/' . $code);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ]
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);


        if ($res === false) {
            throw new BillingUnavailableException('Ошибка со стороны сервера');
        }
        curl_close($ch);
        return json_decode($res, true);
    }

    public function pay($course, $token)
    {
        $ch = curl_init($_ENV['BILLING_URL'] . '/api/v1/courses/' . $course->getCharacterCode() . '/pay');
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);


        if ($res === false) {
            throw new BillingUnavailableException('Ошибка со стороны сервера');
        }
        return json_decode($res, true);
    }

    public function newCourse(User $user, CourseDTO $courseNewDto) {
        $data = $this->serializer->serialize($courseNewDto, 'json');
        $ch = curl_init($_ENV['BILLING_URL'] . '/api/v1/courses');
        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $user->getApiToken()
            ]
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);


        if ($res === false) {
            throw new BillingUnavailableException('Ошибка со стороны сервера');
        }
        curl_close($ch);
        $result = json_decode($res, true);
        if (isset($result['errors'])) {
            throw new BillingUnavailableException(json_encode($result['errors']));
        }
        return $result;
    }

    public function editCourse(User $user, CourseDTO $courseNewDto, $courseCode) {
        $data = $this->serializer->serialize($courseNewDto, 'json');
        $ch = curl_init($_ENV['BILLING_URL'] . '/api/v1/courses/' . $courseCode);
        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $user->getApiToken()
            ]
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);


        if ($res === false) {
            throw new BillingUnavailableException('Ошибка со стороны сервера');
        }
        curl_close($ch);
        $result = json_decode($res, true);
        if (isset($result['errors'])) {
            throw new BillingUnavailableException(json_encode($result['errors']));
        }
        return $result;
    }
}
