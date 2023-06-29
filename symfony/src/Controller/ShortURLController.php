<?php

namespace App\Controller;

use App\Data\TinyUrl;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShortURLController extends AbstractController
{
    #[Route('/api/short_url')]
    public function shortUrl(Request $request, LoggerInterface $logger)
    {
        $url = $request->get('url');
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return new JsonResponse([
                'status' => false,
                'error'  => 'Invalid URL',
            ]);
        }
        $shortedUrl = TinyUrl::shortUrl($url);
        if ('Error' !== $shortedUrl) {
            return new JsonResponse([
                'status' => true,
                'url'    => $shortedUrl,
            ]);
        }
        return new JsonResponse([
            'status' => false,
            'error'  => 'Cannot generate URL via TinyUrl',
        ]);
    }
}
