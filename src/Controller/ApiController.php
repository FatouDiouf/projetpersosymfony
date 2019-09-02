<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Form\UserType;
use App\Entity\Partenaire;
use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Controller\SecurityController;
use App\Entity\Tarif;
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

        $this->aaddUser($request, $passwordEncoder, $recupid, $numerocompte);

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

    public function aaddUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, $recupid, $numerocompte): Response
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
        $user->setRoles(["ROLE_USERSIMPLE"]);
        $user->setImageFile($files);
        $user->setStatut("bloqué");
        $user->setPartenaire($recupid);
        $user->setCompte(NULL);


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
     * @Route("/updatecompte", name="update_compte", methods={"PUT"})
     */
    public function updatecompte(Request $request, EntityManagerInterface $entityManager)
    {
        $cont = new SecurityController();
        $users = new User();
        $admin = $this->getUser();
        $com = $admin->getCompte();
        $comcol = $admin->getPartenaire();

        $depot = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['id' => $comcol]);
        $car = $depot->getComptes();
        $use = $depot->getUsers();
        $numcompte = 0;
        $values = json_decode($request->getContent());
        foreach ($car as $key => $value) {
            if ($value->getNumerocompte() != $values->numerocompte &&  $value->getSolde() >= 100000) {
                $numcompte = $values->numerocompte;
            }

            break;
        }

        foreach ($use as $key => $value) {
            if ($value->getTelephone() == $value->telephone) {
                $value->setCompte($numcompte);
            }

            break;
        }
        $entityManager->persist($users);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'Le compte de travail a été modifie'
        ];
        return new JsonResponse($data, 201);
    }

    /**
     * @Route("/envoi", name="envoi", methods={"POST"})
     */
    public function envoi(Request $request, EntityManagerInterface $entityManager)
    {
        $trans = new Transaction();
        $form = $this->createForm(TransactionType::class, $trans);
        $form->handleRequest($request);
        $values = $request->request->all();
        $form->submit($values);



        $cont = new SecurityController();
        $iduser = $this->getUser();
        $rep = $this->getDoctrine()->getRepository(Tarif::class);
        $com = $rep->findAll();
        $valeur = 0;
        foreach ($com as $value) {
            if ($value->getBonrneinferieure() <= $values['montantEnvoi'] && $value->getBornesuperieure() >= $values['montantEnvoi']) {
                $valeur = $value->getValeur();
                break;
            }
        }

        $comt = $this->getDoctrine()->getRepository(Compte::class)->findOneBy(['numerocompte' => $values['numerocompte']]);
        $result = $valeur * 0.1;
        $trans->setCommisionEnvoi($valeur * 0.1);
        $comt->setSolde($comt->getSolde() - $trans->getMontantEnvoi() + $result);
        $entityManager->persist($comt);
        $entityManager->flush();
        $num = rand(99999, 10000) . 966;
        $trans->setCodeEnvoi($num);
        $trans->setPrix($value);
        $trans->setCommissionEtat($valeur * 0.3);
        $trans->setCommissionSystem($valeur * 0.4);
        $trans->setDateEnvoi(new \DateTime());
        $trans->setUser($iduser);
        $trans->setTotal($values['montantEnvoi'] + $result);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trans);
        $entityManager->flush();

        $data = [
            'status' => 201,
            'message' => 'L\'envoi a été fait avec succes'
        ];

        return new JsonResponse($data, 201);
    }

    /**
     * @Route("/retrait", name="retrait", methods={"POST"})
     */
    public function retrait(Request $request, EntityManagerInterface $entityManager)
    {
        $cont = new SecurityController();
        $trans = new Transaction();
        $values = json_decode($request->getContent());
        $iduser = $this->getUser();
        $transa = $this->getDoctrine()->getRepository(Transaction::class)->findOneBy(['codeEnvoi' => $values->codeEnvoi]);
        $rep = $this->getDoctrine()->getRepository(Tarif::class);
        $com = $rep->findAll();
        foreach ($com as  $value) {
            if ($value->getBonrneinferieure() <= $transa->getMontantEnvoi() && $value->getBornesuperieure() >= $transa->getMontantEnvoi()) {
                $valeur = $value->getValeur();
                break;
            }
        }



        $nomreceveur = $transa->getNomReceveur();
        $telephone = $transa->getTelephoneReceveur();
        $montant = $transa->getMontantEnvoi();
        $code = $transa->getCodeEnvoi();

        $result = $valeur * 0.2;
        $trans->setCommissionRetrait($valeur * 0.2);
        $comt = $this->getDoctrine()->getRepository(Compte::class)->findOneBy(['numerocompte' => $values->numerocompte]);
        $comt->setSolde($comt->getSolde() + $montant + $result);
        $entityManager->persist($comt);
        $entityManager->flush();


        $trans->setNomReceveur($nomreceveur);
        $trans->setTelephoneReceveur($telephone);
        $trans->setMontantEnvoi($montant);
        $trans->setCodeEnvoi($code);
        $trans->setPrix($value);
        $trans->setDateRetrait(new \DateTime());
        $trans->setUser($iduser);
        $trans->setType("retrait");
        $trans->setTotal($montant + $result);


        $entityManager->persist($trans);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'Le retrait a été fait avec succes'
        ];
        return new JsonResponse($data, 201);
    }
}
