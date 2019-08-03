<?php

namespace App\Controller;
use App\Entity\User;
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
     * @Route("/adminpart", name="adminpart")
     */
    public function ajoutuser(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $values = json_decode($request->getContent());
        if (isset($values->username, $values->password)) {
            $cont = new SecurityController();
            $idpar = $cont->getUser()->getPartenaire();
            $depot = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['ninea'=>1930]);
            $car=$depot[1]->getId();
            $use = new User();


            $use->setUsername($values->username);
            $use->setPassword($passwordEncoder->encodePassword($use, $values->password));
            $use->setRoles($values->roles);
            $use->setNom($values->nom);
            $use->setEmail($values->email);
            $use->setAdresse($values->adresse);
            $use->setTelephone($values->telephone);
            $use->setStatut($values->statut);
            $use->setPartenaire($idpar);
            $use->setCompte($car);

            $errors = $validator->validate($use);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($use);
            $entityManager->flush();

            $data = [
                'status' => 201,
                'message' => 'L\'utilisateur a été créé'
            ];

            return new JsonResponse($data, 201);
        }
        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner les champs'
        ];
        return new JsonResponse($data, 500);
    }


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
