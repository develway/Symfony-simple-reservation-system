<?php
// src/AppBundle/Controller/ClientsController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientsController extends Controller
{

    /**
     * @Route("/clients", name="index_clients")
     */
    public function showIndex()
    {
        
        $clients = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->findAll();

        $data = ['clients' => $clients];


        return $this->render(   'clients/index.html.twig',
                                $data );
    }

    /**
     * @Route("/clients/modify/{client_id}", name="modify_client")
     */
    public function showDetails(Request $request, $client_id)
    {

        $data = [];
        
        $data['client']['name'] = '';
        $data['client']['last_name'] = '';
        $data['mode'] = 'm'; //Modify

        $form = $this   ->createFormBuilder()
                        ->add('title')
                        ->add('name')
                        ->add('lastName')
                        ->add('address')
                        ->add('zipCode')
                        ->add('city')
                        ->add('state')
                        ->add('email')
                        ->getForm();
        
        $form->handleRequest($request);
        $client_repo = $client = $this     
                                    ->getDoctrine()
                                    ->getRepository('AppBundle:Client');

        if ($form->isSubmitted()) 
        {

            $form_data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            
            $client = $client_repo->find($client_id);
            $client->setTitle($form_data['title']);
            $client->setName($form_data['name']);
            $client->setLastName($form_data['lastName']);
            $client->setAddress($form_data['address']);
            $client->setZipCode($form_data['zipCode']);
            $client->setCity($form_data['city']);
            $client->setState($form_data['state']);
            $client->setEmail($form_data['email']);

            //$em->persist($client); //We no longer need to persist
            $em->flush();

            return $this->redirectToRoute('index_clients');

        } else 
        {

            

            $client = $client_repo->find( $client_id );
            
            $data['form'] = [];
            $client_data['id'] = $client->getId();
            $client_data['titles'] = $client_repo->getTitles();
            $client_data['title'] = $client->getTitle();
            $client_data['name'] = $client->getName();
            $client_data['last_name'] = $client->getLastName();
            $client_data['address'] = $client->getAddress();
            $client_data['zip_code'] = $client->getZipCode();
            $client_data['city'] = $client->getCity();
            $client_data['state'] = $client->getState();
            $client_data['email'] = $client->getEmail();

            $data['form'] = $client_data; 
            $data['client_id'] = $client_id;

        }

        return $this->render(   'clients/form.html.twig',
                                $data );

    }

    /**
     * @Route("/clients/new", name="new_client")
     */
    public function showNew(Request $request)
    {

        $data = [];
        $data['mode'] = 'n'; //New
        
        $data['client']['name'] = '';
        $data['client']['last_name'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('title')
                        ->add('name')
                        ->add('lastName')
                        ->add('address')
                        ->add('zipCode')
                        ->add('city')
                        ->add('state')
                        ->add('email')
                        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) 
        {

            $form_data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            
            $client = new Client();
            $client->setTitle($form_data['title']);
            $client->setName($form_data['name']);
            $client->setLastName($form_data['lastName']);
            $client->setAddress($form_data['address']);
            $client->setZipCode($form_data['zipCode']);
            $client->setCity($form_data['city']);
            $client->setState($form_data['state']);
            $client->setEmail($form_data['email']);

            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('index_clients', $data);

        } 

        //$data = [];
        $client_repo = $client = $this     
                                ->getDoctrine()
                                ->getRepository('AppBundle:Client');

        $data['form'] = [];
        $data['form']['titles'] = $client_repo->getTitles();
        $data['form']['title'] = '';

        return $this->render(   'clients/form.html.twig',
                                $data );
    }

}