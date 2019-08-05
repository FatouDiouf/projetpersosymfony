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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();





            $compte = new Compte();
            $form = $this->createForm(CompteType::class, $compte);
            $form->handleRequest($request);
            $values = $request->request->all();
            $form->submit($values);




            $date = new \DateTime();
            $num = "F";
            $numero = $num . rand(10000, 99999);
            $compte->setPartenaires($part);
            $compte->setNumerocompte($numero);
            $compte->setSolde(0);

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
     * @Route("/adminpart", name="app_register")
     */
    public function ajoutadmin(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $values = $request->request->all();
        $form->submit($values);
        $files = $request->files->all()['imageName'];



        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        $user->setRoles(["ROLE_USER"]);
        $user->setImageFile($files);
        $user->setStatut("debloquer");


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
