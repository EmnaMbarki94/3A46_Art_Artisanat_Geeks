<?php

namespace App\Controller;
 use App\Form\UserForAdminType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email; // Ajoutez cette ligne pour importer la classe Email
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Core\Security;
use App\Service\TwilioService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class UserController extends AbstractController
{

    private TwilioService $twilioService;

    // Injecter TwilioService dans le constructeur
    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }




    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
   
    #[Route('/user/profile', name: 'profile_user')]
    public function profile(): Response
    {
        $user = $this->getUser();
        
        // je remplace le mot de passe par des etoile pour l'affichage
        $userClone = clone $user;
        $userClone->setPassword('********');
    
        return $this->render('user/profile.html.twig', [
            'user' => $userClone,
        ]);
    }
    
    #[Route('/user/profile_user', name: 'profile_user_front')]
    public function profilef(): Response
    {
        $user = $this->getUser();
        
        // je remplace le mot de passe par des etoile pour l'affichage
        $userClone = clone $user;
        $userClone->setPassword('********');
    
        return $this->render('user/profilef.html.twig', [
            'user' => $userClone,
        ]);
    }

    #[Route('/acceuil', name: 'acceuil')]
    public function acceuil(): Response
    {
        return $this->render('back/acceuil.html.twig');
    }


    #[Route('/admin/user/list', name: 'admin_user')]
    public function showUsers(UserRepository $Rep): Response
    {
        $users = $Rep->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route(path: "/admin/user/stats", name:"admin_stat_user")]

    public function stats(UserRepository $userRepository): Response
    {
        $roleCounts = [
            'ROLE_ARTISTE' => 0,
            'ROLE_ENSEIGNANT' => 0,
            'ROLE_USER' => 0,
        ];
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $roles = $user->getRoles(); // Assuming getRoles() returns an array of roles

            // Count the roles
            foreach ($roles as $role) {
                if (isset($roleCounts[$role])) {
                    $roleCounts[$role]++;
                }
            }
        }
        $labels = ["Artistes :  ".$roleCounts["ROLE_ARTISTE"],"Enseignants : ".$roleCounts["ROLE_ENSEIGNANT"],"Abonnes: ".$roleCounts["ROLE_USER"]];
        $data = array_values($roleCounts);

        return $this->render('user/stat.html.twig', [
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    #[Route('/admin/user/add', name: 'admin_add_user')]
    public function addUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher, 

    ): Response
    {
        // Créer une nouvelle instance de user
        $user = new User();

        // Créer le formulaire
        $form = $this->createForm(UserForAdminType::class, $user);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(password: $passwordHasher->hashPassword($user, $form->get('password')->getData()));

            if (in_array("ROLE_USER", $user->getRoles())) {
                $user->setPoint(0);
            }
            // Persist (sauvegarder) l'auteur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush(); // Envoie les changements à la base de données

            // Redirige vers la liste des auteurs ou une autre page
            return $this->redirectToRoute('admin_user');
        }

        // Retourner la vue du formulaire
        return $this->render('user/user_form.html.twig', [
            'form' => $form->createView(), // Crée la vue du formulaire
            'action'=>"Ajouter User"
        ]);
    } 
    #[Route('/admin/user/edit/{id}', name: 'admin_edit_user')]
public function editUser(
    User $user,
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher, 

): Response {
    // Créer le formulaire en passant l'utilisateur existant
    $form = $this->createForm(UserForAdminType::class, $user);

    // Gérer la soumission du formulaire
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Enregistrer les modifications dans la base de données
        $user->setPassword(password: $passwordHasher->hashPassword($user, $form->get('password')->getData()));

        $entityManager->flush();

        // Rediriger vers la liste des utilisateurs ou autre page
        return $this->redirectToRoute('admin_user');
    }

    // Afficher la vue du formulaire
    return $this->render('user/user_form.html.twig', [
        'form' => $form->createView(),
        'action'=>"Modifier User"

    ]);
}

#[Route('user/edit/{id}', name: 'edit_user_profile')]
public function editprofile(
    User $user,
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher, 

): Response {
    // Créer le formulaire en passant l'utilisateur existant
    $form = $this->createForm(UserForAdminType::class, $user);

    // Gérer la soumission du formulaire
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Enregistrer les modifications dans la base de données
        $user->setPassword(password: $passwordHasher->hashPassword($user, $form->get('password')->getData()));

        $entityManager->flush();

        // Rediriger vers la liste des utilisateurs ou autre page
        return $this->redirectToRoute('profile_user');
    }

    // Afficher la vue du formulaire
    return $this->render('user/user_form.html.twig', [
        'form' => $form->createView(),
        'action'=>"Modifier User"

    ]);
}


#[Route('/admin/user/delete/{id}', name: 'admin_delete_user', methods: ['POST'])]
public function deleteUser(User $user, EntityManagerInterface $entityManager, Request $request): Response
{
    // Vérifier le jeton CSRF pour éviter les attaques
    if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
        $entityManager->remove($user);
        $entityManager->flush();
    }

    // Redirection après suppression
    return $this->redirectToRoute('admin_user');
}


    #[Route('/register', name: 'user_register')]
    public function register(
        Request $request, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $passwordHasher, 
        MailerInterface $mailer, 
        UserAuthenticatorInterface $userAuthenticator, 
        CsrfTokenManagerInterface $csrfTokenManager
        ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $user->setNumTel(20460927);
            if (in_array("ROLE_USER", $user->getRoles())) {
                $user->setPoint(0);
            }
            $em->persist($user);
            $em->flush();

             try {
               // $mailer->send($email);
                return $this->redirectToRoute('app_login');

            } catch (\Exception $e) {
                // Log the error or handle it as needed
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email de vérification.');
                return $this->redirectToRoute('user_register');
            }

 
             
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }


   #[Route('/verify', name: 'user_verify')]
   public function verify(
    Request $request, 
    EntityManagerInterface $em, 
    CsrfTokenManagerInterface $csrfTokenManager,
    TwilioService $twilioService,
    ): Response
  {
    // Récupérer l'utilisateur actuellement connecté
    $user = $this->getUser();
    if (!$user) {
        // Rediriger vers la page de connexion si aucun utilisateur n'est connecté
        return $this->redirectToRoute('app_login');
    }
    $userFromDataBase=$em->getRepository(User::class)->find($user->getId());

   

    if (!$userFromDataBase) {
        // Rediriger vers la page de connexion si aucun utilisateur n'est connecté
        return $this->redirectToRoute('app_login');
    }
    if($userFromDataBase->getVerificationCode()===null){

        $verificationCode = rand(100000, 999999);


            $userFromDataBase->setVerificationCode($verificationCode);
            $em->persist($userFromDataBase);
            $em->flush();

            $numTel = $userFromDataBase->getNumTel();
               if (!$numTel) {
                                 $this->addFlash('error', 'Numéro de téléphone manquant.');
                                      return $this->redirectToRoute('user_verify');
                    }

            // Envoyer le code par SMS
            try {
               // $twilioService->sendSms($userFromDataBase->getNumTel(), "Votre code de vérification est : $verificationCode");
            } catch (\Twilio\Exceptions\RestException $e) {
                // Erreur liée à l'API Twilio, par exemple si les informations d'identification sont incorrectes ou un problème avec la demande
                $this->addFlash('error', 'Erreur de communication avec Twilio : ' . $e->getMessage());
            } catch (\Symfony\Component\Notifier\Exception\TransportException $e) {
                // Erreur dans le transport du message, cela peut être dû à une mauvaise configuration du transport
                $this->addFlash('error', 'Problème de configuration du service d\'envoi : ' . $e->getMessage());
            } catch (\Exception $e) {
                // Attraper toute autre exception générale
                $this->addFlash('error', 'Une erreur inconnue s\'est produite : ' . $e->getMessage());
            }

    }

    if ($request->isMethod('POST')) {
        $submittedCode = $request->request->get('verification_code');
 
         
        // Vérifier si le code soumis correspond au code de vérification de l'utilisateur
        if ($userFromDataBase->getVerificationCode() === $submittedCode) {
            // Marquer l'utilisateur comme vérifié
            $userFromDataBase->setUserAgent($request->headers->get('User-Agent'));
            $userFromDataBase->setVerificationCode(null); // Réinitialiser le code de vérification
            $em->flush();

            if (in_array("ROLE_ARTISTE", $this->getUser()->getRoles())) {
                // Rediriger vers la page Abonné
                return $this->redirectToRoute('app_galerie');
            }
            if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
                // Rediriger vers la page Abonné
                return $this->redirectToRoute('admin_user');
            }           
            
            return $this->redirectToRoute('app_user');
        } else {
            // Ajouter un message d'erreur si le code est incorrect
            $this->addFlash('error', 'Code de vérification incorrect.');
        }
    }

    // Afficher la vue de vérification
    return $this->render('user/verify.html.twig', [
        'csrf_token' => $csrfTokenManager->getToken('verify')->getValue(),
        'numTel' => $userFromDataBase->getNumTel(),  // Passer le numéro de téléphone
        'userAgent' => $userFromDataBase->getUserAgent(),  // Passer le User-Agent
    ]);


  }


     #[Route(path: "/login", name:"app_login")]
     
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
  
        if ($this->getUser()) {
            // Check if the user agent is not null and matches the current user agent in the request
            $userAgent = $this->getUser()->getUserAgent();

            if ($userAgent === null || $userAgent !== $request->headers->get('User-Agent')) {
                // Redirect to verify if the user agent doesn't match or is null
                return $this->redirectToRoute('user_verify');
            }

            // Redirect to /user if the user agent is valid
            //redirect depends on role : 
            
            if (in_array("ROLE_ARTISTE", $this->getUser()->getRoles())) {
                // Rediriger vers la page Abonné
               
                return $this->redirectToRoute('app_galerie_index');
            }
            if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
                // Rediriger vers la page Abonné
                return $this->redirectToRoute('admin_user');
            }
            
            if (in_array("ROLE_ENSEIGNANT", $this->getUser()->getRoles())) {
                // Rediriger vers la page ENSEIGNANT
                return $this->redirectToRoute('app_cours_index');
            }     
            if (in_array("ROLE_USER", $this->getUser()->getRoles())) {
                // Rediriger vers la page Abonné
               
                return $this->redirectToRoute('app_home');
            }      
            
            return $this->redirectToRoute('app_user');
        }
        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('securite/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route(path: "/logout", name:"app_logout")]

    public function logout(Security $security): RedirectResponse
    {
        // Manually clear the session (logout process)
        $security->getTokenStorage()->setToken(null); // This removes the authentication token from the session
        $this->get('session')->invalidate(); // Invalidate the session

        // Redirect to home page after logout
        return $this->redirectToRoute('app_home'); // Change 'app_home' to your desired route
    }

}
