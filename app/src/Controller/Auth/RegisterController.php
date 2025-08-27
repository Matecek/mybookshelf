<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->getMethod() == 'POST') {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if (empty($email) || empty($password)) {
                $this -> addFlash('danger', 'Email i hasło są wymagane.');
                return $this->render('login/register.html.twig');
            }

            if (strlen($password) < 6) {
                $this->addFlash('danger', 'Hasło musi mieć co najmniej 6 znaków.');
                return $this->render('login/register.html.twig');
            }

            if ($password !== $confirmPassword) {
                $this->addFlash('danger', 'Hasła nie są takie same.');
                return $this->render('login/register.html.twig');
            }

            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('danger', 'Użytkownik o tym adresie email już istnieje.');
                return $this->render('login/register.html.twig');
            }

            $user = new User();
            $user->setEmail($email);
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Rejestracja zakończona sukcesem. Możesz się teraz zalogować.');

            return $this->redirectToRoute('app_login');
        }
        return $this->render('login/register.html.twig');
    }
}