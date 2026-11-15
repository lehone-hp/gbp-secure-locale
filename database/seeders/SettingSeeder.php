<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (settings('app_name')) {
            return;
        }

        settings()->set([
            'app_name' => 'GBP Secure Local',

            'contact_email'   => 'support@gbp-securelocal.com',
            'contact_email_2' => 'info@gbp-securelocal.com',
            'contact_phone'   => '(237) 123-456-789',
            'contact_phone_2' => '(237) 123-456-789',

            'notify_email'   => 'contact@gbp-securelocal.com',
            'notify_email_2' => 'contact@gbp-securelocal.com',

            'work_hours'        => 'Mon-Fri | 9AM - 5PM',
            'address'           => 'Fk Street, PV, Cameroon',
            'map_direction_url' => '',

            'facebook_url'  => 'https://www.facebook.com',
            'twitter_url'   => 'https://www.twitter.com',
            'instagram_url' => 'https://www.instagram.com',
            'linkedin_url'  => 'https://www.linkedin.com',

            'square_exchange_rate' => '0.92'
        ]);
    }
}
