<?php

namespace App\Helpers;

use App\DTO\NavLink;

class Navigator
{

    const PROPERTY_TYPE_BUY = 'buy';
    const PROPERTY_TYPE_RENT = 'rent';

    const NAV_LINKS = [
        [
            'label' => 'Home',
            'route' => 'index'
        ],
        [
            'label' => 'Buy',
            'route' => 'properties.index',
            'params' => ['type' => self::PROPERTY_TYPE_BUY]
        ],
        [
            'label' => 'Rent',
            'route' => 'properties.index',
            'params' => ['type' => self::PROPERTY_TYPE_RENT]
        ],
        [
            'label' => 'Sell',
            'route' => 'sell'
        ],
        [
            'label' => 'Car Rentals',
            'route' => 'cars.index'
        ],
        [
            'label' => 'Architectural Design',
            'route' => 'designs.index'
        ],
        [
            'label' => 'Manage',
            'route' => 'manage'
        ],
        [
            'label' => 'About',
            'route' => 'about'
        ],
        [
            'label' => 'Contact',
            'route' => 'contact'
        ],
    ];

    public static function getNavLinks()
    {
        return collect(self::NAV_LINKS)->map(function ($link) {
            $navLink = new NavLink();

            $navLink->setLabel($link['label']);
            if ($link['route'] !== null) {
                $url = route($link['route'], $link['params'] ?? []);

                $navLink->setUrl($url);

                if ($link['route'] === 'properties.index') {
                    $navLink->setActive(
                        (request()->routeIs($link['route']) || request()->routeIs('properties.show')) &&
                        request()->route()->parameter('type') == $link['params']['type']);
                } else if ($link['route'] === 'cars.index') {
                    $navLink->setActive(request()->routeIs($link['route']) || request()->routeIs('cars.show'));
                } else if ($link['route'] === 'designs.index') {
                    $navLink->setActive(request()->routeIs($link['route']) || request()->routeIs('designs.show'));
                } else {
                    $navLink->setActive(request()->routeIs($link['route']));
                }
            } else {
                $navLink->setUrl('#');
            }

            return $navLink;
        })->values();
    }
}
