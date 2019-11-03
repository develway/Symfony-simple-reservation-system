<?php
// src/AppBundle/Controller/AdminController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function showIndex()
    {
        
        $data['current_page'] = 'admin';

        return $this->render(   'admin/index.html.twig', 
                                $data );
                                
    }
}