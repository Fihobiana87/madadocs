<?php

namespace App\Core;

/**
 * Construit le squelette HTML (avec jetons {{champ}}) d'un document.
 * Le même squelette sert à l'aperçu live (rempli en JS) et au rendu PDF
 * (rempli côté serveur via fill_template) afin qu'ils restent identiques.
 */
class DocumentRenderer
{
    /**
     * Rendu final du CV pour le PDF : masque les sections facultatives
     * laissées vides plutôt que d'afficher un titre sans contenu.
     */
    public static function renderCv(array $data): string
    {
        $get = fn (string $key) => trim((string) ($data[$key] ?? ''));

        $experiences = '';
        foreach ([1, 2] as $i) {
            if ($get("experience_{$i}") !== '') {
                $experiences .= '<p><strong>' . e($get("experience_{$i}")) . '</strong>';
                if ($get("experience_{$i}_detail") !== '') {
                    $experiences .= '<br>' . nl2br(e($get("experience_{$i}_detail")));
                }
                $experiences .= '</p>';
            }
        }

        $formations = '';
        foreach ([1, 2] as $i) {
            if ($get("formation_{$i}") !== '') {
                $formations .= '<p>' . e($get("formation_{$i}")) . '</p>';
            }
        }

        $sections = '';
        if ($get('profil') !== '') {
            $sections .= '<section><h3>Profil</h3><p>' . nl2br(e($get('profil'))) . '</p></section>';
        }
        if ($experiences !== '') {
            $sections .= '<section><h3>Expérience</h3>' . $experiences . '</section>';
        }
        if ($formations !== '') {
            $sections .= '<section><h3>Formation</h3>' . $formations . '</section>';
        }
        if ($get('competences') !== '') {
            $sections .= '<section><h3>Compétences</h3><p>' . nl2br(e($get('competences'))) . '</p></section>';
        }
        if ($get('langues') !== '') {
            $sections .= '<section><h3>Langues</h3><p>' . e($get('langues')) . '</p></section>';
        }

        $contact = implode(' · ', array_filter([e($get('email')), e($get('telephone')), e($get('adresse'))]));

        return '<div class="doc doc-cv"><header class="doc-cv__head"><h2>' . e($get('nom')) . '</h2>'
            . '<p class="doc-cv__title">' . e($get('titre')) . '</p>'
            . '<p class="doc-cv__contact">' . $contact . '</p></header>'
            . $sections . '</div>';
    }

    public static function skeleton(array $template): string
    {
        return match ($template['pdf_view']) {
            'invoice' => self::invoiceSkeleton(),
            'cv' => self::cvSkeleton(),
            'attestation' => self::attestationSkeleton($template),
            default => self::letterSkeleton($template),
        };
    }

    private static function letterSkeleton(array $template): string
    {
        $subject = $template['subject_template'] ?? '';
        $body = nl2br((string) ($template['body_template'] ?? ''));

        return <<<HTML
        <div class="doc doc-letter">
            <div class="doc-letter__from"><strong>{{nom}}</strong><br>{{adresse}}</div>
            <div class="doc-letter__date">{{ville}}, le {{date}}</div>
            <div class="doc-letter__to">{{destinataire}}</div>
            <div class="doc-letter__subject">{$subject}</div>
            <div class="doc-letter__body">{$body}</div>
            <div class="doc-letter__signature">{{nom}}</div>
        </div>
        HTML;
    }

    private static function attestationSkeleton(array $template): string
    {
        $body = nl2br((string) ($template['body_template'] ?? ''));

        return <<<HTML
        <div class="doc doc-attestation">
            <div class="doc-attestation__title">{{objet}}</div>
            <div class="doc-attestation__body">{$body}</div>
            <div class="doc-attestation__place">Fait à {{ville}}, le {{date}}</div>
            <div class="doc-attestation__signature">{{representant}}<br>{{fonction_representant}}</div>
        </div>
        HTML;
    }

    private static function cvSkeleton(): string
    {
        return <<<'HTML'
        <div class="doc doc-cv">
            <header class="doc-cv__head">
                <h2>{{nom}}</h2>
                <p class="doc-cv__title">{{titre}}</p>
                <p class="doc-cv__contact">{{email}} · {{telephone}} · {{adresse}}</p>
            </header>
            <section>
                <h3>Profil</h3>
                <p>{{profil}}</p>
            </section>
            <section>
                <h3>Expérience</h3>
                <p><strong>{{experience_1}}</strong><br>{{experience_1_detail}}</p>
                <p><strong>{{experience_2}}</strong><br>{{experience_2_detail}}</p>
            </section>
            <section>
                <h3>Formation</h3>
                <p>{{formation_1}}</p>
                <p>{{formation_2}}</p>
            </section>
            <section>
                <h3>Compétences</h3>
                <p>{{competences}}</p>
            </section>
            <section>
                <h3>Langues</h3>
                <p>{{langues}}</p>
            </section>
        </div>
        HTML;
    }

    private static function invoiceSkeleton(): string
    {
        return <<<'HTML'
        <div class="doc doc-invoice">
            <header class="doc-invoice__head">
                <div><strong>{{vendeur_nom}}</strong><br>{{vendeur_adresse}}</div>
                <div class="doc-invoice__meta">N° {{numero}}<br>{{date}}</div>
            </header>
            <div class="doc-invoice__client">Client : {{client_nom}}<br>{{client_adresse}}</div>
            <div data-invoice-table></div>
        </div>
        HTML;
    }
}
