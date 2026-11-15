<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceAddon;
use Illuminate\Database\Seeder;

class ServiceSeederOld extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'GOOGLE BUSINESS PROFILE',
                'slug' => 'google-business-profile',
                'caption' => 'For Google',
                'description' => 'Profile Setup & Optimization. SEO & Ranking Improvement. Review Management & Insights',
                'addons' => [
                    [
                        'name' => 'GOOGLE ACCESS CODE',
                        'slug' => 'google-access-code',
                        'description' => '<ul>
                                                <li>Edit business name, address, phone number (NAP)</li>
                                                <li>Update operating hours, holiday hours, and service areas</li>
                                                <li>Add or change the business category and attributes</li>
                                            </ul>',
                        'type' => 'one_time',
                        'price' => 129,
                    ],
                    [
                        'name' => 'Google Business Profile Setup & Optimization',
                        'slug' => 'google-business-profile-setup-optimization',
                        'description' => '<ul>
                                            <li>Claiming and verifying the GBP listing</li>
                                            <li>Optimizing business name, category, and description</li>
                                            <li>Adding high-quality images and videos</li>
                                            <li>Ensuring accurate NAP (Name, Address, Phone) details</li>
                                            <li>Setting up business hours, service areas, and attributes</li>
                                        </ul>',
                        'type' => null,
                        'price' => 229,
                    ],
                    [
                        'name' => 'Google Business Profile SEO',
                        'slug' => 'google-business-profile-seo',
                        'description' => '<ul>
                                            <li>Keyword optimization in business description and posts</li>
                                            <li>Managing services, products, and Q&A sections</li>
                                            <li>Implementing local SEO strategies for higher ranking</li>
                                            <li>Optimizing GBP for voice search</li>
                                        </ul>',
                        'type' => null,
                        'price' => 379,
                    ],
                    [
                        'name' => 'Content Posting',
                        'slug' => 'content-posting',
                        'description' => '<ul>
                                            <li>Creating engaging Google Posts (offers, updates, events, etc.)</li>
                                            <li>Scheduling weekly or monthly content updates</li>
                                            <li>Adding relevant images, videos, and promotions</li>
                                        </ul>',
                        'type' => null,
                        'price' => 159,
                    ],
                    [
                        'name' => 'Review Management',
                        'slug' => 'google-business-profile-ads',
                        'description' => '
                                        <ul>
                                            <li>Encouraging happy customers to leave reviews</li>
                                            <li>Responding to reviews (both positive and negative) professionally</li>
                                            <li>Handling customer complaints and reputation repair    </li>
                                        </ul>',
                        'type' => null,
                        'price' => 159,
                    ],
                    [
                        'name' => 'GBP Insights & Reporting',
                        'slug' => 'gpb-insights-reporting',
                        'description' => '
                                        <ul>
                                            <li>Monitoring GBP performance metrics (search queries, views, clicks, calls, etc.)</li>
                                            <li>Providing monthly reports and insights</li>
                                            <li>Making data-driven improvements based on trends</li>
                                        </ul>
                                        ',
                        'type' => null,
                        'price' => 119,
                    ],
                    [
                        'name' => 'Google Messaging & Engagement',
                        'slug' => 'google-messaging-engagement',
                        'description' => '<ul>
                                            <li>Managing direct messages from customers</li>
                                            <li>Setting up automated responses and FAQs</li>
                                            <li>Engaging with potential customers to boost conversions</li>
                                        </ul>',
                        'type' => null,
                        'price' => 99,
                    ],
                    [
                        'name' => 'Profile Recovery',
                        'slug' => 'google-business-profile-ads',
                        'description' => '<ul>
                                            <li>Helping businesses recover suspended or disabled profiles</li>
                                            <li>Fixing common issues (e.g., duplicate listings, category errors)</li>
                                            <li>Submitting appeals and communicating with Google support</li>
                                        </ul>',
                        'type' => null,
                        'price' => 219,
                    ],
                    [
                        'name' => 'Profile Recovery',
                        'slug' => 'profile-recovery',
                        'description' => '<ul>
                                            <li>Helping businesses recover suspended or disabled profiles</li>
                                            <li>Fixing common issues (e.g., duplicate listings, category errors)</li>
                                            <li>Submitting appeals and communicating with Google support</li>
                                        </ul>',
                        'type' => null,
                        'price' => 219,
                    ],
                    [
                        'name' => 'Google Ads & Local Service Ads (LSA)',
                        'slug' => 'google-ads-local-service-ads-lsa',
                        'description' => '<ul>
                                            <li>Running GBP-specific Google Ads campaigns</li>
                                            <li>Managing Local Service Ads for lead generation</li>
                                            <li>Retargeting website visitors through Google Ads</li>
                                        </ul>',
                        'type' => null,
                        'price' => 1000,
                    ],
                ],
            ],
            [
                'name' => 'FACEBOOK / SOCIAL MEDIA PROFILE',
                'slug' => 'facebook-social-media-profile',
                'caption' => 'For Facebook',
                'description' => 'Profile Creation & Management. Content Posting & Ads. Community Engagement',
                'addons' => [
                    [
                        'name' => 'Profile Setup & Optimization',
                        'slug' => 'profile-setup-optimization',
                        'description' => '<ul>
                                            <li>Create and optimize a Facebook Business Page</li>
                                            <li>Set up business details, contact info, and branding</li>
                                        </ul>',
                        'type' => 'one_time',
                        'price' => 189,
                    ],
                    [
                        'name' => 'Facebook Ads',
                        'slug' => 'facebook-ads',
                        'description' => '<ul>
                                            <li>Set up and manage ad campaigns</li>
                                            <li>Optimize audience targeting and ad performance</li>
                                        </ul>',
                        'type' => 'six_months',
                        'price' => 488,
                    ],
                    [
                        'name' => 'Analytics & Reporting',
                        'slug' => 'analytics-reporting',
                        'description' => '<ul>
                                            <li>Track page performance, engagement, and reach</li>
                                            <li>Provide reports with insights and recommendations</li>
                                        </ul>',
                        'type' => null,
                        'price' => 119,
                    ],
                    [
                        'name' => 'Automation & Chatbots',
                        'slug' => 'automation-chatbots',
                        'description' => '<ul>
                                            <li>Set up automated responses</li>
                                            <li>Schedule posts using automation tools</li>
                                        </ul>',
                        'type' => null,
                        'price' => 215,
                    ],
                    [
                        'name' => 'Reputation Management',
                        'slug' => 'reputation-management',
                        'description' => '<ul>
                                            <li>Monitor brand mentions and customer reviews</li>
                                            <li>Handle negative feedback and crisis situations</li>
                                        </ul>',
                        'type' => null,
                        'price' => 114,
                    ],
                    [
                        'name' => 'Training & Consulting',
                        'slug' => 'training-consulting',
                        'description' => '<ul>
                                            <li>Coach business owners on Facebook marketing</li>
                                            <li>Provide strategy guidance and workshops</li>
                                        </ul>',
                        'type' => 'annual',
                        'price' => 516,
                    ],
                ],
            ],
            [
                'name' => 'Websites',
                'slug' => 'websites',
                'caption' => 'For Websites',
                'description' => 'Website Development & Optimization. SEO & Content Creation. E-commerce & Automation',
                'addons' => [
                    [
                        'name' => 'Website Setup & Optimization basic',
                        'slug' => 'website-setup-optimization-basic',
                        'description' => '<ul>
                                            <li>Domain & hosting setup</li>
                                            <li>Speed & performance optimization</li>
                                            <li>Mobile responsiveness checks</li>
                                        </ul>',
                        'type' => null,
                        'price' => 1539,
                    ],
                    [
                        'name' => 'Content Creation & Management',
                        'slug' => 'content-creation-management',
                        'description' => '<ul>
                                            <li>Blog post and article creation</li>
                                            <li>Copywriting for landing pages</li>
                                            <li>Uploading and updating content</li>
                                            <li>Creating engaging visuals</li>
                                        </ul>',
                        'type' => 'monthly',
                        'price' => 399,
                    ],
                    [
                        'name' => 'Website Maintenance',
                        'slug' => 'website-maintenance',
                        'description' => '<ul>
                                            <li>Regular backups and updates</li>
                                            <li>Security patches and monitoring</li>
                                            <li>Bug fixes and troubleshooting</li>
                                        </ul>',
                        'type' => null,
                        'price' => 159,
                    ],
                    [
                        'name' => 'Conversion Rate Optimization (CRO)',
                        'slug' => 'conversion-rate-optimization',
                        'description' => '<ul>
                                            <li>A/B testing and heat mapping</li>
                                            <li>Improving calls-to-action (CTAs)</li>
                                            <li>UX/UI improvements</li>
                                        </ul>',
                        'type' => 'one_time',
                        'price' => 468,
                    ],
                    [
                        'name' => 'E-commerce Management',
                        'slug' => 'ecommerce-management',
                        'description' => '<ul>
                                            <li>Product catalog uploads</li>
                                            <li>Cart and checkout optimization</li>
                                            <li>Inventory and order tracking</li>
                                        </ul>',
                        'type' => null,
                        'price' => 455,
                    ],
                    [
                        'name' => 'Analytics & Reporting',
                        'slug' => 'website-analytics-reporting',
                        'description' => '<ul>
                                            <li>Google Analytics & Search Console setup</li>
                                            <li>Tracking user behavior and traffic sources</li>
                                            <li>Monthly performance reports</li>
                                        </ul>',
                        'type' => 'annual',
                        'price' => 229,
                    ],
                    [
                        'name' => 'Ads & Retargeting',
                        'slug' => 'ads-retargeting',
                        'description' => '<ul>
                                            <li>Setting up Google Ads or Facebook Ads campaigns</li>
                                            <li>Retargeting strategies for conversions</li>
                                            <li>Tracking and optimizing ad performance</li>
                                        </ul>',
                        'type' => 'three_months',
                        'price' => 614,
                    ],
                    [
                        'name' => 'Website Automation',
                        'slug' => 'website-automation',
                        'description' => '<ul>
                                            <li>Marketing automation setup</li>
                                            <li>Email automation for lead nurturing</li>
                                            <li>CRM integration</li>
                                        </ul>',
                        'type' => null,
                        'price' => 325,
                    ],
                    [
                        'name' => 'SEO (Search Engine Optimization)',
                        'slug' => 'seo-search-engine-optimization',
                        'description' => '<ul>
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
                        'type' => null,
                        'price' => 489,
                    ],
                    [
                        'name' => 'Converge online Payment Gateway Solutions',
                        'slug' => 'converge-online-payment-gateway',
                        'description' => '<ul>
                                            <li>Seamlessly integrate Converge secure payment solutions</li>
                                            <li>Ensure encrypted transactions, reducing fraud risks and boosting customer confidence</li>
                                            <li>Streamline payment processing, improving checkout speed and reducing cart abandonment</li>
                                            <li>Provide powerful tools for managing transactions, refunds, and reporting</li>
                                            <li>Adaptable tools to accommodate POS API</li>
                                        </ul>',
                        'type' => null,
                        'price' => 360,
                    ],
                    [
                        'name' => 'Training & Consulting',
                        'slug' => 'website-training-consulting',
                        'description' => '<ul>
                                            <li>Website management training</li>
                                            <li>SEO & digital marketing consulting</li>
                                            <li>Personalized strategy workshops</li>
                                        </ul>',
                        'type' => 'six_months',
                        'price' => 2589,
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
