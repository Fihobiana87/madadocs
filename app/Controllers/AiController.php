<?php

namespace App\Controllers;

use App\Core\AiClient;
use App\Core\Controller;
use App\Core\Csrf;

class AiController extends Controller
{
    public function index(): void
    {
        $this->render('home/assistant', [
            'pageTitle' => 'Assistant MadaDocs',
            'pageDescription' => 'Laissez l’assistant IA de MadaDocs vous aider à rédiger vos documents.',
            'aiAvailable' => AiClient::isAvailable(),
        ]);
    }

    public function generate(): void
    {
        if (!Csrf::verifyRequest()) {
            $this->json(['ok' => false, 'error' => 'invalid_csrf'], 419);
        }

        $situation = trim((string) $this->input('situation', ''));
        if ($situation === '') {
            $this->json(['ok' => false, 'error' => 'invalid_response']);
        }

        $system = "Tu es un assistant de rédaction administrative pour Madagascar. "
            . "Rédige en français, dans un style professionnel et courtois, un texte de document "
            . "(lettre, demande ou message) adapté à la situation décrite par l'utilisateur. "
            . "Réponds uniquement avec le texte final du document, sans commentaire ni introduction.";

        $result = AiClient::complete($system, $situation);
        $this->json($result);
    }

    public function improve(): void
    {
        if (!Csrf::verifyRequest()) {
            $this->json(['ok' => false, 'error' => 'invalid_csrf'], 419);
        }

        $text = trim((string) $this->input('text', ''));
        $style = (string) $this->input('style', 'professionnel');

        if ($text === '') {
            $this->json(['ok' => false, 'error' => 'invalid_response']);
        }

        $styles = [
            'simple' => 'simple et facile à comprendre',
            'professionnel' => 'professionnel et clair',
            'formel' => 'formel et soigné',
            'administratif' => 'administratif, précis et respectueux des conventions malgaches',
        ];
        $styleLabel = $styles[$style] ?? $styles['professionnel'];

        $system = "Tu es un correcteur et rédacteur professionnel. Améliore la grammaire, la clarté "
            . "et le style du texte fourni par l'utilisateur, dans un registre {$styleLabel}, en français. "
            . "Conserve le sens original. Réponds uniquement avec le texte amélioré.";

        $result = AiClient::complete($system, $text);
        $this->json($result);
    }
}
