<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PanelController extends Controller
{
    private $passwordEncoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    /**
     * @Route("/panel/profile", name="panel")
     */
    public function index(): Response
    {
        if($this->getUser()->getRoles()[0] === 'ROLE_ADMIN') {
            return $this->redirectToRoute('admin');
        }

        return $this->render('panel/index.html.twig', [
            'tasks' => false
        ]);
    }

    /**
     * @Route("/panel", name="task")
     */
    public function task(Request $request): Response
    {
        $user = $this->getUser();

        if($user->getRoles()[0] === 'ROLE_ADMIN') {
            return $this->redirectToRoute('admin');
        }

        $tasks = $this->em->getRepository(Task::class)->findByUserId($user->getId());
        $req = $request->request->all();
        if(count($req) > 0) {
            $task = new Task();
            $task->setUser($user);

            $form = $this->createForm(TaskType::class, $task);
            $form->submit($req);

            $this->em->persist($task);
            $this->em->flush();

            $tasks = $this->em->getRepository(Task::class)->findByUserId($user->getId());


            return $this->render('panel/task.html.twig', [
                'tasks' => $tasks
            ]);
        }


        return $this->render('panel/task.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/panel/change-password", name="change_password")
     */
    public function changePassword(Request $request): Response
    {
        if($this->getUser()->getRoles()[0] === 'ROLE_ADMIN') {
            return $this->redirectToRoute('admin');
        }

        $password = $request->request->get('password');
        $rePassword = $request->request->get('rePassword');

        $error = false;

        if($password !== $rePassword) {
            $error = true;
        }

        if($password && !$error) {
            $user = $this->getUser();

            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

            $this->em->flush();

            return $this->redirectToRoute('panel');
        }

        return $this->render('panel/changePassword.html.twig', [
            'tasks' => false,
            'error' => $error
        ]);
    }
}
