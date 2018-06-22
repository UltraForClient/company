<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Form\UserType;
use Password\Generator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends Controller
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
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }

    /**
     * @Route("/form", name="form")
     */
    public function form(Request $request): Response
    {
        $req = $request->request->all();
        $em = $this->getDoctrine()->getManager();

        if(isset($req['mode'])) {
            $data = $this->matchDataForm($req);


            $newUser = false;

            if(!$user = $em->getRepository(User::class)->findOneBy(['email' => $data['user']['email']])) {
                $user = new User();
                $newUser = true;
            }


            $task = new Task();

            $formUser = $this->createForm(UserType::class, $user);
            $formTask = $this->createForm(TaskType::class, $task);

            $formUser->submit($data['user']);
            $formTask->submit($data['task']);

            if($newUser) {
                $password = $this->passwordGenerator->generate();
                $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

                //$this->sendMail($data['user']['email'], $password);
            }


            $task->setUser($user);

            $em->persist($user);
            $em->persist($task);

            $em->flush();


            return $this->redirectToRoute('form_summary', [
                'email' => $data['user']['email']
            ]);
        }

        return $this->render('form/form.html.twig', [

        ]);
    }

    /**
     * @Route("/form/summary", name="form_summary")
     */
    public function formSummary(Request $request): Response
    {
        return $this->render('form/formSummary.html.twig', [
            'email' => $request->query->get('email')
        ]);
    }

    /**
     * @Route("/email", name="email")
     */
    public function email(Request $request): Response
    {
        return $this->render('email/email.html.twig', [
            'email' => 'email@gmail.com',
            'password' => 'asdasdasdasdasd'
        ]);
    }

    private function sendMail(string $email, string $password): void
    {
        $message = (new \Swift_Message('Zakład wiercenia Studziennych'))
            ->setFrom(getenv('MAILER_URL'))
            ->setTo($email)
            ->setBody(
                $this->renderView('email/email.html.twig', [
                    'email' => $email,
                    'password' => $password
                ]),
                'text/html'
            );

        $this->mailer->send($message);
    }

    private function matchDataForm($data) {
        $dataTask = array_filter($data, function($key) {
            return strpos($key, 'task_') === 0;
        }, ARRAY_FILTER_USE_KEY);


        $data = array_filter($data, function($key) {
            return strpos($key, 'task_') !== 0;
        }, ARRAY_FILTER_USE_KEY);

        function removePrefix(array $input, string $prefix) {

            $return = [];
            foreach ($input as $key => $value) {
                if (strpos($key, $prefix) === 0) {
                    $key = substr($key, strlen($prefix));
                }

                if (is_array($value)) {
                    $value = removePrefix($value, $prefix);
                }

                $return[$key] = $value;
            }
            return $return;
        }

        $dataTask = removePrefix($dataTask, 'task_');

        return [
            'user' => $data,
            'task' => $dataTask
        ];
    }
}
