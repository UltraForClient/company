<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
        ]);
    }

    /**
     * @Route("/form", name="form")
     */
    public function form()
    {
        return $this->render('form/form.html.twig', [

        ]);
    }
}
