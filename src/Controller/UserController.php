<?php

namespace App\Controller;
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


    #[Route('/acceuil', name: 'app_home')]
    public function acceuil(): Response
    {
        return $this->render('back/acceuil.html.twig');
    }


    #[Route('/list_User', name: 'user_list')]
    public function showUsers(UserRepository $Rep): Response
    {
        $users = $Rep->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/add-user', name: 'add_user')]
    public function addUser(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Créer une nouvelle instance de user
        $user = new User();

        // Créer le formulaire
        $form = $this->createForm(UserType::class, $user);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist (sauvegarder) l'auteur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush(); // Envoie les changements à la base de données

            // Redirige vers la liste des auteurs ou une autre page
            return $this->redirectToRoute('user_list');
        }

        // Retourner la vue du formulaire
        return $this->render('user/add.html.twig', [
            'form' => $form->createView(), // Crée la vue du formulaire
        ]);
    }

   /**#[Route('/register', name: 'user_register')]
    public function register(Request $request, EntityManagerInterface $em,  UserPasswordHasherInterface $passwordHasher , MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $em->persist($user);
            $em->flush();

            // Send verification email
            $email = (new Email())
            ->from('chihebhsassi@yahoo.fr')
            ->to('chihebhsassi@yahoo.fr')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
            /*$email = (new Email())
                ->from('no-reply@example.com')
                ->to($user->getEmail())
                ->subject('Vérification de votre compte')
                ->text('Votre code de vérification est: 123456'); // Generate a real code

            $mailer->send($email);*/

           // return $this->redirectToRoute('user_verify');
        //}

       // return $this->render('user/register.html.twig', [
          //  'form' => $form->createView(),
       // ]);
    //}

   /**#[Route("/verify", name:"user_verify")]
    public function verify(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        if (!$user) {
            // Rediriger vers la page de connexion si aucun utilisateur n'est connecté
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $submittedCode = $request->request->get('verification_code');

            // Vérifier si le code soumis correspond au code de vérification de l'utilisateur
            if ($user->getVerificationCode() === $submittedCode) {
                // Marquer l'utilisateur comme vérifié
                $user->setIsVerified(true);
                $user->setVerificationCode(null); // Réinitialiser le code de vérification
                $em->persist($user);
                $em->flush();

                // Rediriger vers la page d'accueil ou une autre page après vérification
                return $this->redirectToRoute('home');
            } else {
                // Ajouter un message d'erreur si le code est incorrect
                $this->addFlash('error', 'Code de vérification incorrect.');
            }
        }

        // Afficher la vue de vérification
        return $this->render('user/verify.html.twig');
    }**/


    /* 
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('faycalhsassi@yahoo.fr') // Adresse personnalisée
            ->to('chihebhsassi@yahoo.fr')
            ->subject('Test Email')
            ->text('Ceci est un email de test.');
    
        try {
            $mailer->send($email);
            return new Response('Email envoyé avec succès !');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }**/

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
            $em->persist($user);
            $em->flush();

         /*   // Generate a unique verification code
            $verificationCode = rand(100000, 999999);
            $user->setVerificationCode($verificationCode);
            $em->persist($user);
            $em->flush();

            // Send verification email
            $email = (new Email())
                ->from('no-reply@example.com')
                ->to($user->getEmail())
                ->subject('Vérification de votre compte')
                ->text('Votre code de vérification est: ' . $verificationCode);*/

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

            // Rediriger vers la page d'accueil ou une autre page après vérification
            return $this->redirectToRoute('app_home');
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
        // Récupérer l'erreur de connexion s'il y en a une
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
                return $this->redirectToRoute('app_galerie');
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
