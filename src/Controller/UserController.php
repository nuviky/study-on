<?php

namespace App\Controller;

use App\Dto\UserDTO;
use App\Service\BillingClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(BillingClient $billingClient): Response
    {
        /** @var UserDTO $currentUser */
        $currentUser = $billingClient->getCurrentUser($this->getUser());

        return $this->render('user/user_profile.html.twig', [
            'username' => $currentUser->username,
            'role' => in_array('ROLE_SUPER_ADMIN', $currentUser->roles) ? 'Администратор' : 'Пользователь',
            'balance' => $currentUser->balance
        ]);
    }
}