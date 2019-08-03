<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Partenaire;
use App\Entity\Depot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Compte;
use App\Repository\PartenaireRepository;

/**
 * @Route("/security", name="api")
 */

class SecurityController extends AbstractController
{

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $values = json_decode($request->getContent());
        $rep = $this->getDoctrine()->getRepository(Partenaire::class);
        $com = $rep->findAll();
        foreach ($com as $key => $value) {
            $id = $value->getId();
        }
        if (isset($values->username, $values->password)) {



            $user = new User();
            $user->setUsername($values->username);
            $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
            $user->setRoles(["ROLE_ADMIN"]);
            $user->setNom($values->nom);
            $user->setEmail($values->email);
            $user->setAdresse($values->adresse);
            $user->setTelephone($values->telephone);
            $user->setStatut($values->statut);

            $errors = $validator->validate($user);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($user);
            $entityManager->flush();



            $comptes = new Compte();

            $num = rand(1, 11000);
            $compte = $id . $num;
            $comptes->setNumerocompte($compte);
            $comptes->setSolde(0);

            $errors = $validator->validate($comptes);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($comptes);
            $entityManager->flush();


            $part = new Partenaire();
            $dat = rand(0, 100210);
            $nine = $id . $dat;
            $part->setNinea($nine);
            $part->setRaionsociale($values->raionsociale);
            $part->setAdresse($values->adresse);
            $part->setStatut($values->statut);
            $part->addUser($user);
            $part->addCompte($comptes);
            $entityManager->persist($part);
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
     * @Route("/liste", name="liste", methods={"GET"})
     */
    public function liste(PartenaireRepository $partRepository, SerializerInterface $serializer)
    {
        $parte = $partRepository->findAll();
        $data = $serializer->serialize($parte, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }


    
}
