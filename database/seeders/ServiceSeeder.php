<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceAddon;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Service 1: Google Business Profile
            [
                'name' => [
                    'en' => 'GOOGLE BUSINESS PROFILE',
                    'fr' => 'PROFIL D\'ENTREPRISE GOOGLE',
                ],
                'slug' => 'google-business-profile',
                'caption' => [
                    'en' => 'For Google',
                    'fr' => 'Pour Google',
                ],
                'description' => [
                    'en' => 'Profile Setup & Optimization. SEO & Ranking Improvement. Review Management & Insights',
                    'fr' => 'Configuration et optimisation du profil. Amélioration du référencement. Gestion des avis et statistiques',
                ],
                'addons' => [
                    [
                        'name' => [
                            'en' => 'GOOGLE ACCESS CODE',
                            'fr' => 'CODE D\'ACCÈS GOOGLE',
                        ],
                        'slug' => 'google-access-code',
                        'description' => [
                            'en' => '<ul>
                                        <li>Edit business name, address, phone number (NAP)</li>
                                        <li>Update operating hours, holiday hours, and service areas</li>
                                        <li>Add or change the business category and attributes</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Modifier le nom, l\'adresse et le numéro de téléphone (NAP)</li>
                                        <li>Mettre à jour les heures d\'ouverture et les zones de service</li>
                                        <li>Ajouter ou modifier la catégorie et les attributs</li>
                                    </ul>',
                        ],
                        'type' => 'one_time',
                        'price' => 129,
                    ],
                    [
                        'name' => [
                            'en' => 'Google Business Profile Setup & Optimization',
                            'fr' => 'Configuration et optimisation du profil Google',
                        ],
                        'slug' => 'google-business-profile-setup-optimization',
                        'description' => [
                            'en' => '<ul>
                                        <li>Claiming and verifying the GBP listing</li>
                                        <li>Optimizing business name, category, and description</li>
                                        <li>Adding high-quality images and videos</li>
                                        <li>Ensuring accurate NAP (Name, Address, Phone) details</li>
                                        <li>Setting up business hours, service areas, and attributes</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Revendiquer et vérifier la fiche GBP</li>
                                        <li>Optimiser le nom, la catégorie et la description</li>
                                        <li>Ajouter des images et vidéos de qualité</li>
                                        <li>Assurer des détails NAP précis</li>
                                        <li>Configurer les heures et zones de service</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 229,
                    ],
                    [
                        'name' => [
                            'en' => 'Google Business Profile SEO',
                            'fr' => 'Référencement du profil Google',
                        ],
                        'slug' => 'google-business-profile-seo',
                        'description' => [
                            'en' => '<ul>
                                        <li>Keyword optimization in business description and posts</li>
                                        <li>Managing services, products, and Q&A sections</li>
                                        <li>Implementing local SEO strategies for higher ranking</li>
                                        <li>Optimizing GBP for voice search</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Optimisation des mots-clés dans la description</li>
                                        <li>Gestion des services, produits et Q&R</li>
                                        <li>Stratégies de référencement local</li>
                                        <li>Optimisation pour la recherche vocale</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 379,
                    ],
                    [
                        'name' => [
                            'en' => 'Content Posting',
                            'fr' => 'Publication de contenu',
                        ],
                        'slug' => 'content-posting',
                        'description' => [
                            'en' => '<ul>
                                        <li>Creating engaging Google Posts (offers, updates, events, etc.)</li>
                                        <li>Scheduling weekly or monthly content updates</li>
                                        <li>Adding relevant images, videos, and promotions</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Créer des publications Google engageantes</li>
                                        <li>Planifier des mises à jour de contenu</li>
                                        <li>Ajouter des images et promotions pertinentes</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 159,
                    ],
                    [
                        'name' => [
                            'en' => 'Review Management',
                            'fr' => 'Gestion des avis',
                        ],
                        'slug' => 'review-management',
                        'description' => [
                            'en' => '<ul>
                                        <li>Encouraging happy customers to leave reviews</li>
                                        <li>Responding to reviews (both positive and negative) professionally</li>
                                        <li>Handling customer complaints and reputation repair</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Encourager les clients satisfaits à laisser des avis</li>
                                        <li>Répondre aux avis de manière professionnelle</li>
                                        <li>Gérer les plaintes et la réparation de réputation</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 159,
                    ],
                    [
                        'name' => [
                            'en' => 'GBP Insights & Reporting',
                            'fr' => 'Statistiques et rapports GBP',
                        ],
                        'slug' => 'gpb-insights-reporting',
                        'description' => [
                            'en' => '<ul>
                                        <li>Monitoring GBP performance metrics (search queries, views, clicks, calls, etc.)</li>
                                        <li>Providing monthly reports and insights</li>
                                        <li>Making data-driven improvements based on trends</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Surveiller les métriques de performance GBP</li>
                                        <li>Fournir des rapports mensuels et des insights</li>
                                        <li>Améliorer basé sur les données et tendances</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 119,
                    ],
                    [
                        'name' => [
                            'en' => 'Google Messaging & Engagement',
                            'fr' => 'Messagerie et engagement Google',
                        ],
                        'slug' => 'google-messaging-engagement',
                        'description' => [
                            'en' => '<ul>
                                        <li>Managing direct messages from customers</li>
                                        <li>Setting up automated responses and FAQs</li>
                                        <li>Engaging with potential customers to boost conversions</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Gérer les messages directs des clients</li>
                                        <li>Configurer les réponses automatiques et FAQ</li>
                                        <li>Engager avec les clients potentiels</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 99,
                    ],
                    [
                        'name' => [
                            'en' => 'Profile Recovery',
                            'fr' => 'Récupération de profil',
                        ],
                        'slug' => 'profile-recovery',
                        'description' => [
                            'en' => '<ul>
                                        <li>Helping businesses recover suspended or disabled profiles</li>
                                        <li>Fixing common issues (e.g., duplicate listings, category errors)</li>
                                        <li>Submitting appeals and communicating with Google support</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Aider à récupérer les profils suspendus</li>
                                        <li>Corriger les problèmes courants</li>
                                        <li>Soumettre des appels au support Google</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 219,
                    ],
                    [
                        'name' => [
                            'en' => 'Google Ads & Local Service Ads (LSA)',
                            'fr' => 'Google Ads et annonces de services locaux',
                        ],
                        'slug' => 'google-ads-local-service-ads-lsa',
                        'description' => [
                            'en' => '<ul>
                                        <li>Running GBP-specific Google Ads campaigns</li>
                                        <li>Managing Local Service Ads for lead generation</li>
                                        <li>Retargeting website visitors through Google Ads</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Gérer des campagnes Google Ads spécifiques GBP</li>
                                        <li>Gérer les annonces de services locaux</li>
                                        <li>Recibler les visiteurs via Google Ads</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 1000,
                    ],
                ],
            ],

            // Service 2: Facebook / Social Media
            [
                'name' => [
                    'en' => 'FACEBOOK / SOCIAL MEDIA PROFILE',
                    'fr' => 'PROFIL FACEBOOK / MÉDIAS SOCIAUX',
                ],
                'slug' => 'facebook-social-media-profile',
                'caption' => [
                    'en' => 'For Facebook',
                    'fr' => 'Pour Facebook',
                ],
                'description' => [
                    'en' => 'Profile Creation & Management. Content Posting & Ads. Community Engagement',
                    'fr' => 'Création et gestion de profil. Publication de contenu et publicités. Engagement communautaire',
                ],
                'addons' => [
                    [
                        'name' => [
                            'en' => 'Profile Setup & Optimization',
                            'fr' => 'Configuration et optimisation du profil',
                        ],
                        'slug' => 'profile-setup-optimization',
                        'description' => [
                            'en' => '<ul>
                                        <li>Create and optimize a Facebook Business Page</li>
                                        <li>Set up business details, contact info, and branding</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Créer et optimiser une page Facebook professionnelle</li>
                                        <li>Configurer les détails et l\'image de marque</li>
                                    </ul>',
                        ],
                        'type' => 'one_time',
                        'price' => 189,
                    ],
                    [
                        'name' => [
                            'en' => 'Facebook Ads',
                            'fr' => 'Publicités Facebook',
                        ],
                        'slug' => 'facebook-ads',
                        'description' => [
                            'en' => '<ul>
                                        <li>Set up and manage ad campaigns</li>
                                        <li>Optimize audience targeting and ad performance</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Configurer et gérer les campagnes publicitaires</li>
                                        <li>Optimiser le ciblage et les performances</li>
                                    </ul>',
                        ],
                        'type' => 'six_months',
                        'price' => 488,
                    ],
                    [
                        'name' => [
                            'en' => 'Analytics & Reporting',
                            'fr' => 'Analyses et rapports',
                        ],
                        'slug' => 'analytics-reporting',
                        'description' => [
                            'en' => '<ul>
                                        <li>Track page performance, engagement, and reach</li>
                                        <li>Provide reports with insights and recommendations</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Suivre les performances et l\'engagement</li>
                                        <li>Fournir des rapports avec recommandations</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 119,
                    ],
                    [
                        'name' => [
                            'en' => 'Automation & Chatbots',
                            'fr' => 'Automatisation et chatbots',
                        ],
                        'slug' => 'automation-chatbots',
                        'description' => [
                            'en' => '<ul>
                                        <li>Set up automated responses</li>
                                        <li>Schedule posts using automation tools</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Configurer les réponses automatiques</li>
                                        <li>Planifier les publications automatiquement</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 215,
                    ],
                    [
                        'name' => [
                            'en' => 'Reputation Management',
                            'fr' => 'Gestion de la réputation',
                        ],
                        'slug' => 'reputation-management',
                        'description' => [
                            'en' => '<ul>
                                        <li>Monitor brand mentions and customer reviews</li>
                                        <li>Handle negative feedback and crisis situations</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Surveiller les mentions et avis clients</li>
                                        <li>Gérer les commentaires négatifs et crises</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 114,
                    ],
                    [
                        'name' => [
                            'en' => 'Training & Consulting',
                            'fr' => 'Formation et consultation',
                        ],
                        'slug' => 'training-consulting',
                        'description' => [
                            'en' => '<ul>
                                        <li>Coach business owners on Facebook marketing</li>
                                        <li>Provide strategy guidance and workshops</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Former les propriétaires au marketing Facebook</li>
                                        <li>Fournir des conseils stratégiques</li>
                                    </ul>',
                        ],
                        'type' => 'annual',
                        'price' => 516,
                    ],
                ],
            ],

            // Service 3: Websites
            [
                'name' => [
                    'en' => 'Websites',
                    'fr' => 'Sites Web',
                ],
                'slug' => 'websites',
                'caption' => [
                    'en' => 'For Websites',
                    'fr' => 'Pour les sites Web',
                ],
                'description' => [
                    'en' => 'Website Development & Optimization. SEO & Content Creation. E-commerce & Automation',
                    'fr' => 'Développement et optimisation Web. SEO et création de contenu. E-commerce et automatisation',
                ],
                'addons' => [
                    [
                        'name' => [
                            'en' => 'Website Setup & Optimization basic',
                            'fr' => 'Configuration et optimisation de site Web basique',
                        ],
                        'slug' => 'website-setup-optimization-basic',
                        'description' => [
                            'en' => '<ul>
                                        <li>Domain & hosting setup</li>
                                        <li>Speed & performance optimization</li>
                                        <li>Mobile responsiveness checks</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Configuration domaine et hébergement</li>
                                        <li>Optimisation vitesse et performance</li>
                                        <li>Vérification adaptation mobile</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 1539,
                    ],
                    [
                        'name' => [
                            'en' => 'Content Creation & Management',
                            'fr' => 'Création et gestion de contenu',
                        ],
                        'slug' => 'content-creation-management',
                        'description' => [
                            'en' => '<ul>
                                        <li>Blog post and article creation</li>
                                        <li>Copywriting for landing pages</li>
                                        <li>Uploading and updating content</li>
                                        <li>Creating engaging visuals</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Création d\'articles de blog</li>
                                        <li>Rédaction pour pages de destination</li>
                                        <li>Téléchargement et mise à jour de contenu</li>
                                        <li>Création de visuels engageants</li>
                                    </ul>',
                        ],
                        'type' => 'monthly',
                        'price' => 399,
                    ],
                    [
                        'name' => [
                            'en' => 'Website Maintenance',
                            'fr' => 'Maintenance de site Web',
                        ],
                        'slug' => 'website-maintenance',
                        'description' => [
                            'en' => '<ul>
                                        <li>Regular backups and updates</li>
                                        <li>Security patches and monitoring</li>
                                        <li>Bug fixes and troubleshooting</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Sauvegardes et mises à jour régulières</li>
                                        <li>Correctifs de sécurité et surveillance</li>
                                        <li>Correction de bugs et dépannage</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 159,
                    ],
                    [
                        'name' => [
                            'en' => 'Conversion Rate Optimization (CRO)',
                            'fr' => 'Optimisation du taux de conversion (CRO)',
                        ],
                        'slug' => 'conversion-rate-optimization',
                        'description' => [
                            'en' => '<ul>
                                        <li>A/B testing and heat mapping</li>
                                        <li>Improving calls-to-action (CTAs)</li>
                                        <li>UX/UI improvements</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Tests A/B et carte thermique</li>
                                        <li>Amélioration des appels à l\'action</li>
                                        <li>Améliorations UX/UI</li>
                                    </ul>',
                        ],
                        'type' => 'one_time',
                        'price' => 468,
                    ],
                    [
                        'name' => [
                            'en' => 'E-commerce Management',
                            'fr' => 'Gestion E-commerce',
                        ],
                        'slug' => 'ecommerce-management',
                        'description' => [
                            'en' => '<ul>
                                        <li>Product catalog uploads</li>
                                        <li>Cart and checkout optimization</li>
                                        <li>Inventory and order tracking</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Téléchargement de catalogue produits</li>
                                        <li>Optimisation panier et paiement</li>
                                        <li>Suivi inventaire et commandes</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 455,
                    ],
                    [
                        'name' => [
                            'en' => 'Analytics & Reporting',
                            'fr' => 'Analyses et rapports',
                        ],
                        'slug' => 'website-analytics-reporting',
                        'description' => [
                            'en' => '<ul>
                                        <li>Google Analytics & Search Console setup</li>
                                        <li>Tracking user behavior and traffic sources</li>
                                        <li>Monthly performance reports</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Configuration Google Analytics et Search Console</li>
                                        <li>Suivi du comportement utilisateur</li>
                                        <li>Rapports de performance mensuels</li>
                                    </ul>',
                        ],
                        'type' => 'annual',
                        'price' => 229,
                    ],
                    [
                        'name' => [
                            'en' => 'Ads & Retargeting',
                            'fr' => 'Publicités et reciblage',
                        ],
                        'slug' => 'ads-retargeting',
                        'description' => [
                            'en' => '<ul>
                                        <li>Setting up Google Ads or Facebook Ads campaigns</li>
                                        <li>Retargeting strategies for conversions</li>
                                        <li>Tracking and optimizing ad performance</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Configuration de campagnes Google Ads ou Facebook</li>
                                        <li>Stratégies de reciblage pour conversions</li>
                                        <li>Suivi et optimisation des performances</li>
                                    </ul>',
                        ],
                        'type' => 'three_months',
                        'price' => 614,
                    ],
                    [
                        'name' => [
                            'en' => 'Website Automation',
                            'fr' => 'Automatisation de site Web',
                        ],
                        'slug' => 'website-automation',
                        'description' => [
                            'en' => '<ul>
                                        <li>Marketing automation setup</li>
                                        <li>Email automation for lead nurturing</li>
                                        <li>CRM integration</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Configuration automatisation marketing</li>
                                        <li>Automatisation e-mail pour leads</li>
                                        <li>Intégration CRM</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 325,
                    ],
                    [
                        'name' => [
                            'en' => 'SEO (Search Engine Optimization)',
                            'fr' => 'SEO (Optimisation pour les moteurs de recherche)',
                        ],
                        'slug' => 'seo-search-engine-optimization',
                        'description' => [
                            'en' => '<ul>
                                        <li>Keyword research and on-page SEO</li>
                                        <li>Optimizing meta tags and headers</li>
                                        <li>Building backlinks and improving DA</li>
                                        <li>Technical SEO (sitemaps, robots.txt, etc.)</li>
                                        <li>Local SEO for geo-targeted searches</li>
                                        <li>Competitor analysis and strategy</li>
                                        <li>Monthly SEO performance reports</li>
                                        <li>Schema markup and structured data</li>
                                        <li>Improving page load speed for SEO</li>
                                        <li>Content optimization for search engines</li>
                                        <li>Fixing crawl errors and broken links</li>
                                        <li>Ensuring mobile-friendliness for SEO</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Recherche de mots-clés et SEO on-page</li>
                                        <li>Optimisation des balises meta et en-têtes</li>
                                        <li>Construction de backlinks et amélioration DA</li>
                                        <li>SEO technique (sitemaps, robots.txt, etc.)</li>
                                        <li>SEO local pour recherches géo-ciblées</li>
                                        <li>Analyse concurrentielle et stratégie</li>
                                        <li>Rapports de performance SEO mensuels</li>
                                        <li>Balisage schema et données structurées</li>
                                        <li>Amélioration vitesse de chargement</li>
                                        <li>Optimisation contenu pour moteurs de recherche</li>
                                        <li>Correction erreurs d\'exploration et liens cassés</li>
                                        <li>Assurer compatibilité mobile pour SEO</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 489,
                    ],
                    [
                        'name' => [
                            'en' => 'Converge online Payment Gateway Solutions',
                            'fr' => 'Solutions de passerelle de paiement Converge',
                        ],
                        'slug' => 'converge-online-payment-gateway',
                        'description' => [
                            'en' => '<ul>
                                        <li>Seamlessly integrate Converge secure payment solutions</li>
                                        <li>Ensure encrypted transactions, reducing fraud risks and boosting customer confidence</li>
                                        <li>Streamline payment processing, improving checkout speed and reducing cart abandonment</li>
                                        <li>Provide powerful tools for managing transactions, refunds, and reporting</li>
                                        <li>Adaptable tools to accommodate POS API</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Intégrer les solutions de paiement sécurisées Converge</li>
                                        <li>Assurer des transactions cryptées, réduisant les risques de fraude</li>
                                        <li>Rationaliser le traitement des paiements</li>
                                        <li>Fournir des outils pour gérer transactions et remboursements</li>
                                        <li>Outils adaptables pour API POS</li>
                                    </ul>',
                        ],
                        'type' => null,
                        'price' => 360,
                    ],
                    [
                        'name' => [
                            'en' => 'Training & Consulting',
                            'fr' => 'Formation et consultation',
                        ],
                        'slug' => 'website-training-consulting',
                        'description' => [
                            'en' => '<ul>
                                        <li>Website management training</li>
                                        <li>SEO & digital marketing consulting</li>
                                        <li>Personalized strategy workshops</li>
                                    </ul>',
                            'fr' => '<ul>
                                        <li>Formation à la gestion de site Web</li>
                                        <li>Consultation SEO et marketing numérique</li>
                                        <li>Ateliers de stratégie personnalisés</li>
                                    </ul>',
                        ],
                        'type' => 'six_months',
                        'price' => 2589,
                    ],
                ],
            ],
            // Hidden service used by clients to test the payment gateway
            [
                'name' => ['en' => 'TEST PAYMENT', 'fr' => 'PAIEMENT TEST'],
                'slug' => 'test-payment',
                'is_hidden' => true,
                'caption' => ['en' => 'Payment gateway test', 'fr' => 'Test de la passerelle de paiement'],
                'description' => [
                    'en' => 'Test product used to verify the payment gateway.',
                    'fr' => 'Produit de test pour vérifier la passerelle de paiement.',
                ],
                'addons' => [
                    [
                        'name' => ['en' => 'TEST PAYMENT', 'fr' => 'PAIEMENT TEST'],
                        'slug' => 'test-payment-addon',
                        'description' => [
                            'en' => 'One dollar test payment.',
                            'fr' => 'Paiement de test d\'un dollar.',
                        ],
                        'price' => 1,
                        'type' => 'one_time',
                    ],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $addons = $serviceData['addons'];
            unset($serviceData['addons']);

            $service = Service::firstOrCreate(
                ['slug' => $serviceData['slug']],
                $serviceData
            );

            foreach ($addons as $addonData) {
                $addonData['service_id'] = $service->id;

                ServiceAddon::firstOrCreate(
                    ['slug' => $addonData['slug']],
                    $addonData
                );
            }
        }
    }
}
