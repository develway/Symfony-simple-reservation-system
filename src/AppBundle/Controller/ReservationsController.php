<?php
// src/AppBundle/Controller/ReservationsController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Client;
use AppBundle\Entity\Room;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReservationsController extends Controller
{
    /**
     * @Route("/reservations", name="reservations")
     */
    public function showIndex()
    {
        $data[] = [];
        $reservation_repo = $this     
                                ->getDoctrine()
                                ->getRepository('AppBundle:Reservation');
        
        $reservations = $reservation_repo->getCurrentReservations();
        $data['reservations'] = $reservations;

        return $this->render(   'reservations/index.html.twig', 
                                $data );
    }

    /**
     * @Route("/reservation/{client_id}", name="booking")
     */
    public function book(Request $request, $client_id)
    {

        $data = [];
        $data['rooms'] = null;
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('dateFrom')
                        ->add('dateTo')
                        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form_data = $form->getData();

            $data['dates']['from'] = $form_data['dateFrom'];
            $data['dates']['to'] = $form_data['dateTo'];

            $em = $this->getDoctrine()->getManager();
            $rooms = $em->getRepository('AppBundle:Room')
                ->getAvailableRooms($form_data['dateFrom'], $form_data['dateTo']);   

            $data['rooms'] = $rooms;

        } 
        
        $client = $this
                        ->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find($client_id);

        

        
        $data['client'] = $client;
        return $this->render(   'reservations/book.html.twig',
                                $data );
    }

    /**
     * @Route("/book_room/{client_id}/{room_id}/{date_in}/{date_out}", name="book_room")
     */
    public function bookRoom($client_id, $room_id, $date_in, $date_out)
    {

        $reservation = new Reservation();
        $date_start = new \DateTime($date_in);
        $date_end = new \DateTime($date_out);
        $reservation->setDateIn($date_start);
        $reservation->setDateOut($date_end);

        $client = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($client_id);

        $room = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Room')
                    ->find($room_id);

        $em = $this
                ->getDoctrine()
                ->getManager();

        $room_availability = $em
                                ->getRepository('AppBundle:Room')
                                ->checkRoomAvailability($room_id,$date_in, $date_out);

        //Check if there are booked rooms with those dates
        if(!$room_availability)
        {
            //Room is available
            $reservation->setClient($client);
            $reservation->setRoom($room);

            $em->persist($reservation);
            $em->flush();
            
            return $this->redirectToRoute('reservations');

        }else
        {

            throw new \Exception('Room is already booked!');

        }    

    }

    /**
     * @Route("/reservation/cancel/{reservation_id}", name="cancel_booking")
     */
    public function cancel($reservation_id)
    {
        $em = $this
                ->getDoctrine()
                ->getManager();

        $reservation = $em->getReference('AppBundle:Reservation', $reservation_id);
        $em->remove($reservation);
        $em->flush();

        return $this->redirectToRoute('reservations');


    }

}