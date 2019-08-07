<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Form\UserType;
use App\Form\CompteType;
use App\Entity\Partenaire;
use App\Form\PartenaireFormType;
use App\Repository\PartenaireRepository;
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

class SecurityController extends AbstractController
{

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $part = new Partenaire();
        $form = $this->createForm(PartenaireFormType::class, $part);
        $form->handleRequest($request);
        $values = $request->request->all();
        $form->submit($values);

        if ($form->isSubmitted()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($part);
            $entityManager->flush();




            $user = new User();

            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            $values = $request->request->all();
            $form->submit($values);

            $files = $request->files->all()['imageName'];
            if ($files->guessExtension() != "jpeg" && $files->guessExtension() != "png") {
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
            $user->setRoles(["ROLE_ADMIN"]);
            $user->setImageFile($files);
            $user->setStatut("debloquer");
            $user->setPartenaire($part);
            $errors = $validator->validate($user);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();





            $compte = new Compte();
            $form = $this->createForm(CompteType::class, $compte);
            $form->handleRequest($request);
            $values = $request->request->all();
            $form->submit($values);




            
            $num = "F";
            $numero = $num . rand(10000, 99999);
            $compte->setPartenaires($part);
            $compte->setNumerocompte($numero);
            $compte->setSolde(0);

            $errors = $validator->validate($compte);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($compte);
            $entityManager->flush();

            $data = [
                'status' => 201,
                'message' => 'Le partenaire son admin et son compte ont été créé'
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

    /**
     * @Route("/compte", name="compte", methods={"POST"})
     */
    public function ajoutcompte(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $values = json_decode($request->getContent());

       // if (!empty($value)) {
            $comt = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['ninea' => $values->ninea]);


            $compt = new Compte();
            $num = "F";
            $numer = $num . rand(10000, 99999);
            $compt->setNumerocompte($numer);
            $compt->setPartenaires($comt);
            $compt->setSolde(0);



            $errors = $validator->validate($compt);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }

            $entityManager->persist($compt);
            $entityManager->flush();

            $data = [
                'status' => 201,
                'message' => 'Le compte a été ajouté avec success'
            ];
    
            return new JsonResponse($data, 201);
        //}
        
    }


    
}
