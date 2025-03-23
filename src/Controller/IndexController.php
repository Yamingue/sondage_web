<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Repository\QuartierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/signaler', name: 'signaler')]
    function Signaler(
        Request $request,
        SerializerInterface $serializer,
        QuartierRepository $quartierRepository
        )  {

        $quartiers = $serializer->serialize($quartierRepository->findAll(), 'json',context:[
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $this->render('index/signaler.html.twig', [
            'quartiers' => $quartiers,
        ]);
    }

    #[Route('/api/signal', name:'saveSignale', methods:['POST'])]
    function saveSignale(
        Request $request,
        QuartierRepository $quartierRepository,
        EntityManagerInterface $em
        )  {
        // $data = $data = $request->request->all();
        $description = $request->request->get('description');
        $quartier = $quartierRepository->find($request->request->get('quartier'));
        $contact = $request->request->get('contact');
        $categorie = $request->request->get('categorie');
        $lat = $request->request->get('lat');
        $lng = $request->request->get('lng');
        $alert = new Alert();
        $alert->setDescription($description);
        $alert->setQuartier($quartier);
        $alert->setContact($contact);
        $alert->setCategorie($categorie);
        $alert->setLat($lat);
        $alert->setLng($lng);
        $alert->setStatus('En attente');
        if ($photo = $request->files->get('photo')) {
            $fileName = uniqid().'.'.$photo->guessExtension();
            $photo->move('Image/alert', $fileName);
            $alert->setPhoto('/Image/alert/'.$fileName);
        }

        try {
            $em->persist($alert);
            $em->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'Alert enregistrée avec succès',
                'code' => 200,
                'id' => $alert->getId(),
            ]);
        } catch (Exception $ex) {
            return $this->json([
                'status' =>'error',
                'message' => $ex->getMessage(),
                'code' => 500,
            ]);
        }
    }
}
