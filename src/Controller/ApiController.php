<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Form\UserType;
use App\Entity\Partenaire;
use App\Controller\SecurityController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api", name="api")
 */
class ApiController extends AbstractController
{


    /**
     * @Route("/adminpart", name="app_register")
     */
    public function ajoutadmin(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $cont = new SecurityController();
        $idpar = $this->getUser()->getPartenaire();
        
       
       $depot = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['id' => $idpar]);
        $car = $depot->getComptes();
        $numero = $car[0]->getNumerocompte();


        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $values = $request->request->all();
        $form->submit($values);
        $files = $request->files->all()['imageName'];

       if($files->guessExtension()!="jpeg" && $files->guessExtension()!="png" )
       {
        $data = [
            'status' => 500,
            'message' => 'Vous devez devez choisir une image'
        ];
        return new JsonResponse($data, 500);
       }



        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        $user->setRoles(["ROLE_USER"]);
        $user->setImageFile($files);
        $user->setStatut("bloqué");
        $user->setPartenaire($idpar);
        $user->setCompte($numero);
        

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);

        $entityManager->flush();

        $data = [
            'status' => 201,
            'message' => 'L\'utilisateur a été créé'
        ];

        return new JsonResponse($data, 201);


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
