<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Pages statiques (how it works, contact, mentions…).
 */
class StaticController extends AbstractController
{
    /**
     * Page “Comment ça marche ?”.
     */
    #[Route('/comment-ca-marche', name: 'how_it_works')]
    public function howItWorks(): Response
    {
        return $this->render('static/how_it_works.html.twig');
    }

    /**
     * Formulaire de contact (avec envoi via Mailer).
     */
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $name    = trim((string) $request->request->get('name'));
            $email   = trim((string) $request->request->get('email'));
            $subject = trim((string) $request->request->get('subject'));
            $message = trim((string) $request->request->get('message'));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $name === '' || $message === '' || $subject === '') {
                $this->addFlash('danger', 'Merci de remplir tous les champs avec une adresse e-mail valide.');
                return $this->redirectToRoute('contact');
            }

            $html = $this->renderView('emails/contact_support.html.twig', [
                'name'    => $name,
                'email'   => $email,
                'subject' => $subject,
                'message' => $message,
            ]);

            $mail = (new Email())
                ->from($email)
                ->to('supportEcoRide@ecoride.fr')
                ->subject(sprintf('[EcoRide] %s - %s', $subject, $name))
                ->text($message)
                ->html($html);

            $mailer->send($mail);
            $this->addFlash('success', 'Votre message a été envoyé. Nous revenons vers vous rapidement.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('static/contact.html.twig');
    }

    /**
     * Mentions légales (contenu statique).
     */
    #[Route('/mentions-legales', name: 'mentions')]
    public function mentions(): Response
    {
        return $this->render('static/mentions.html.twig');
    }

    /**
     * Page politique de confidentialité + modal cookies.
     */
    #[Route('/confidentialite', name: 'confidentiality')]
    public function confidentiality(): Response
    {
        return $this->render('static/confidentiality.html.twig');
    }
}
