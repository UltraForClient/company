<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    private $em;
    private $mailer;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $tasks = $this->em->getRepository(Task::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_user")
     */
    public function users(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();
        array_shift($users);
        return $this->render('admin/listClient.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin_user_one")
     */
    public function user(User $user): Response
    {
        $tasks = $this->em->getRepository(Task::class)->findByUserId($user->getId());

        return $this->render('admin/user.html.twig', [
            'user' => $user,
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/admin/valuation/{id}", name="valuation")
     */
    public function valuation(Task $task, Request $request): Response
    {
        $req = $request->request->all();

        if(count($req) > 0) {
            $task->setValuation($req['price']);

//            $this->sendMail($task->getUser()->getEmail(), $req);

            $this->em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/valuation.html.twig', [
            'task' => $task
        ]);
    }

    /**
     * @Route("admin/delete/task/{id}", name="remove_task")
     */
    public function removeTask(Task $task): Response
    {
        $task->setDeletedAt(new \DateTime());
        $this->em->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("admin/delete/user/{id}", name="remove_user")
     */
    public function removeUser(User $user): Response
    {
        $user->setDeletedAt(new \DateTime());
        $this->em->flush();

        return $this->redirectToRoute('admin');
    }

    private function sendMail(string $email, array $param): void
    {
        $message = (new \Swift_Message('Zakład wiercenia Studziennych'))
            ->setFrom(getenv('MAILER_URL'))
            ->setTo($email)
            ->setBody(
                $this->renderView('email/valuation.html.twig', [
                    'price' => $param['price'],
                    'date' => $param['date'],
                    'comments' => $param['comments']
                ]),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
