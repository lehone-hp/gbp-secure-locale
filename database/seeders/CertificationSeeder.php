<?php

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define certifications with their file paths and names
        $certifications = [
            [
                'file' => 'PremierBadge.png',
                'name' => [
                    'en' => 'Google Premier Partner',
                    'fr' => 'Partenaire Premier Google',
                ],
                'display_order' => 1,
            ],
            [
                'file' => 'semetis-certification_adobe-solution-partner.jpg',
                'name' => [
                    'en' => 'Adobe Solution Partner',
                    'fr' => 'Partenaire Solutions Adobe',
                ],
                'display_order' => 2,
            ],
            [
                'file' => 'semetis-certification_ga-certified-partner.jpg',
                'name' => [
                    'en' => 'Google Analytics Certified Partner',
                    'fr' => 'Partenaire Certifié Google Analytics',
                ],
                'display_order' => 3,
            ],
            [
                'file' => 'semetis-certification_google-web-designer.jpg',
                'name' => [
                    'en' => 'Google Web Designer Certified',
                    'fr' => 'Certifié Google Web Designer',
                ],
                'display_order' => 4,
            ],
            [
                'file' => 'semetis-certification_html5-studio-certification.jpg',
                'name' => [
                    'en' => 'HTML5 Studio Certification',
                    'fr' => 'Certification HTML5 Studio',
                ],
                'display_order' => 5,
            ],
            [
                'file' => 'semetis-certification_marin-search-certified-agency.jpg',
                'name' => [
                    'en' => 'Marin Search Certified Agency',
                    'fr' => 'Agence Certifiée Marin Search',
                ],
                'display_order' => 6,
            ],
            [
                'file' => 'semetis-certification_meta-business-partner.jpg',
                'name' => [
                    'en' => 'Meta Business Partner',
                    'fr' => 'Partenaire Commercial Meta',
                ],
                'display_order' => 7,
            ],
            [
                'file' => 'semetis-certification_segment.jpg',
                'name' => [
                    'en' => 'Segment Partner',
                    'fr' => 'Partenaire Segment',
                ],
                'display_order' => 8,
            ],
            [
                'file' => 'semetis-certification_tagmanager-certified-partner.jpg',
                'name' => [
                    'en' => 'Google Tag Manager Certified Partner',
                    'fr' => 'Partenaire Certifié Google Tag Manager',
                ],
                'display_order' => 9,
            ],
            [
                'file' => 'semetis-certification_tealium.jpg',
                'name' => [
                    'en' => 'Tealium Partner',
                    'fr' => 'Partenaire Tealium',
                ],
                'display_order' => 10,
            ],
            [
                'file' => 'semetis-certification_waze.jpg',
                'name' => [
                    'en' => 'Waze Ads Partner',
                    'fr' => 'Partenaire Waze Ads',
                ],
                'display_order' => 11,
            ],
            [
                'file' => 'semetis-certification_google-css-partner.jpg',
                'name' => [
                    'en' => 'Google CSS Partner',
                    'fr' => 'Partenaire CSS Google',
                ],
                'display_order' => 12,
            ],
        ];

        // Ensure storage directory exists
        if (!Storage::disk('public')->exists('certifications')) {
            Storage::disk('public')->makeDirectory('certifications');
        }

        $created = 0;
        $skipped = 0;

        foreach ($certifications as $cert) {
            $sourcePath = public_path('assets/img/certifications/' . $cert['file']);
            $destinationPath = 'certifications/' . $cert['file'];
            
            // Check if certification already exists (by image path or name)
            $exists = Certification::where('image', $destinationPath)
                ->orWhere('name->en', $cert['name']['en'])
                ->exists();

            if ($exists) {
                $this->command->info("⊘ Skipped (already exists): {$cert['name']['en']}");
                $skipped++;
                continue;
            }
            
            // Check if source file exists
            if (File::exists($sourcePath)) {
                // Copy file to storage (only if not already there)
                if (!Storage::disk('public')->exists($destinationPath)) {
                    $fileContents = File::get($sourcePath);
                    Storage::disk('public')->put($destinationPath, $fileContents);
                }

                // Create database entry
                Certification::create([
                    'name' => $cert['name'],
                    'image' => $destinationPath,
                    'display_order' => $cert['display_order'],
                    'is_active' => true,
                ]);

                $this->command->info("✓ Created: {$cert['name']['en']}");
                $created++;
            } else {
                $this->command->warn("✗ File not found: {$sourcePath}");
            }
        }

        $this->command->info("Certification seeder completed! Created: {$created}, Skipped: {$skipped}");
    }
}
