<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LocaleController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale(Request $request, string $locale): RedirectResponse
    {
        // Guarda el idioma en la sesión
        $request->getSession()->set('_locale', $locale);

        // Redirige a la página anterior
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_homepage'));
    }
}