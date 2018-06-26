<?php declare(strict_types=1);

namespace App\Controller\Auth;

use App\Entity\User;
use Password\Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    private $passwordEncoder;
    private $passwordGenerator;
    private $mailer;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $this->passwordEncoder = $passwordEncoder;

        $this->passwordGenerator = new Generator();
        $this->passwordGenerator->setMinLength(16);
        $this->passwordGenerator->setNumberOfUpperCaseLetters(2);
        $this->passwordGenerator->setNumberOfNumbers(2);
        $this->passwordGenerator->setNumberOfSymbols(1);

        $this->mailer = $mailer;
    }

    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('home');
        }
        $error        = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    /**
     * @Route("/login/forget", name="forget")
     */
    public function forget(Request $request): Response
    {
        $email = $request->request->get('email');

        $em = $this->getDoctrine()->getManager();

        $error = false;
        if($email) {
            if($user = $em->getRepository(User::class)->findOneBy(['email' => $email])) {
                $password = $this->passwordGenerator->generate();
                $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

                //$this->sendMail($email, $password);

                return $this->redirectToRoute('login');

            } else {
                $error = true;
            }
        }

        return $this->render('auth/forget.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET", "POST"})
     */
    public function logout(): void
    {
        throw new \Exception('Logout error!');
    }

    private function sendMail(string $email, string $password): void
    {
        $message = (new \Swift_Message('Zakład wiercenia Studziennych'))
            ->setFrom(getenv('MAILER_URL'))
            ->setTo($email)
            ->setBody(
                $this->renderView('email/forget.html.twig', [
                    'email' => $email,
                    'password' => $password
                ]),
                'text/html'
            );

        $this->mailer->send($message);
    }
}