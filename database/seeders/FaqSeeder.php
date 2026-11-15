<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        if (Faq::exists()) {
            return;
        }

        $faqs = [
            [
                'question' => ['en' => 'What services do you offer?', 'fr' => 'Quels services offrez-vous ?'],
                'answer' => [
                    'en' => 'We manage and protect your Google presence: Google Business Profile setup and optimisation, local SEO, review management, social media profiles and website services.',
                    'fr' => 'Nous gérons et protégeons votre présence Google : configuration et optimisation du profil d\'entreprise Google, référencement local, gestion des avis, profils de médias sociaux et services de site Web.',
                ],
            ],
            [
                'question' => ['en' => 'How does ordering work?', 'fr' => 'Comment fonctionne la commande ?'],
                'answer' => [
                    'en' => 'Pick a service, enter your billing details, accept the terms and pay securely online. We confirm by email and start work as soon as your order is paid.',
                    'fr' => 'Choisissez un service, saisissez vos coordonnées de facturation, acceptez les conditions et payez en ligne en toute sécurité. Nous confirmons par courriel et commençons dès que votre commande est payée.',
                ],
            ],
            [
                'question' => ['en' => 'Can I cancel or get a refund?', 'fr' => 'Puis-je annuler ou obtenir un remboursement ?'],
                'answer' => [
                    'en' => 'Yes, within the limits set out in our refund policy. One-time services can be refunded before work starts, and subscriptions can be cancelled at any time.',
                    'fr' => 'Oui, dans les limites prévues par notre politique de remboursement. Les services ponctuels sont remboursables avant le début du travail et les abonnements peuvent être annulés à tout moment.',
                ],
            ],
            [
                'question' => ['en' => 'Do you guarantee a Google ranking?', 'fr' => 'Garantissez-vous un classement sur Google ?'],
                'answer' => [
                    'en' => 'No one can guarantee a position on Google. We follow Google\'s guidelines and focus on a complete, accurate and active profile that gives you the best chance of being found.',
                    'fr' => 'Personne ne peut garantir une position sur Google. Nous suivons les consignes de Google et nous concentrons sur un profil complet, exact et actif qui maximise vos chances d\'être trouvé.',
                ],
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
