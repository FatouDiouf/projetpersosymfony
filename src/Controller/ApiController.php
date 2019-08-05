<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Compte;
use App\Entity\Depot;
use App\Entity\Partenaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

 /**
     * @Route("/security", name="api")
     */
class ApiController extends AbstractController
{
   
    /**
     * @Route("/depot", name="add_depot", methods={"POST"})
     */

    public function ajoutdepot(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $values = json_decode($request->getContent());
        
        if (isset($values->montant)) {
            $depot = $this->getDoctrine()->getRepository(Compte::class)->findOneBy(['numerocompte' => $values->numerocompte]);
            

            $depot->setSolde($depot->getSolde() + $values->montant);
            
            $entityManager->persist($depot);
            $entityManager->flush();
            $dep = new Depot();
            $dep->setMontant($values->montant);
            $dep->setDatedepot(new \DateTime());
            $dep->setCompte($depot);

            

            $errors = $validator->validate($dep);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }

            $entityManager->persist($dep);
            $entityManager->flush();
        }
        $data = [
            'status' => 201,
            'message' => 'Le a été ajouté avec success'
        ];

        return new JsonResponse($data, 201);
    }

    
}
