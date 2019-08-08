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
    public function ajoutadmin(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $cont = new SecurityController();
        $recupid = $this->getUser()->getPartenaire();


        $depot = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['id' => $recupid]);
        $car = $depot->getComptes();
        $numerocompte = $car[0]->getNumerocompte();

        $this->aaddUser($recupid, $numerocompte, $request, $passwordEncoder);

        $data = [
            'status' => 201,
            'message' => 'Le a été ajouté avec success'
        ];

        return new JsonResponse($data, 201);
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

    public function aaddUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, $recupid, $numerocompte = NULL): Response
    {


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
        $user->setStatut("bloqué");
        $user->setPartenaire($recupid);
        $user->setCompte($numerocompte);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);

        $entityManager->flush();

        $data = [
            'status' => 201,
            'message' => 'L\'utilisateur a été créé'
        ];

        return new JsonResponse($data, 201);
    }

    /**
     * @Route("/compte/{id}", name="update_compte", methods={"PUT"})
     */
    public function update(Request $request, SerializerInterface $serializer, Compte $compte, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $com = new Compte();

        $values = json_decode($request->getContent());

        $recpid = $this->getUser()->getPartenaire();
        $comp = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['id' => $recpid]);
        $car = $comp->getComptes();

        foreach ($car as $key => $value) {
            $id = $value->getId();
            if ($value->getNumerocompte() != $values->numerocompte && $value->getSolde(9) > 75000) {

                $com->setNumerocompte($values->numerocompte);
            }
        }

        $compteUpdate = $entityManager->getRepository(Compte::class)->find($compte->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $compteUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($compteUpdate);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'Le téléphone a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }
}
