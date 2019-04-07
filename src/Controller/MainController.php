<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    
    /**
     * 
     * @Route("/otra", name="secondary")
     */
     public function otra_funcion()
     {
         return $this->render('main/second.html.twig', [
             'parametro' => 'SecondaryController',
        ]);
     }

    /**
     * @route("/otra2", name="secondary2")
     */
     public function otra_funcion2()
     {
        $db_custom_url = getenv('DATABASE_CUSTOM_URL');
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $db_custom_url ,);
        return $this->render('main/second.html.twig', [
             'parametro' => $db_custom_url,
        ]);
     }
    
}
