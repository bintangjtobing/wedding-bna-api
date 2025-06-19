<?php

namespace App\Helpers;

class MapHelper
{
    /**
     * Generate Google Maps URL
     */
    public static function googleMaps($latitude, $longitude, $zoom = 15)
    {
        return "https://www.google.com/maps?q={$latitude},{$longitude}&z={$zoom}";
    }

    /**
     * Generate Google Maps URL with label
     */
    public static function googleMapsWithLabel($latitude, $longitude, $label, $zoom = 15)
    {
        $encodedLabel = urlencode($label);
        return "https://www.google.com/maps?q={$latitude},{$longitude}({$encodedLabel})&z={$zoom}";
    }

    /**
     * Generate Apple Maps URL (works on iOS/macOS)
     */
    public static function appleMaps($latitude, $longitude, $zoom = 15)
    {
        return "https://maps.apple.com/?q={$latitude},{$longitude}&z={$zoom}";
    }

    /**
     * Generate Waze URL
     */
    public static function waze($latitude, $longitude)
    {
        return "https://waze.com/ul?ll={$latitude},{$longitude}&navigate=yes";
    }

    /**
     * Generate OpenStreetMap URL
     */
    public static function openStreetMap($latitude, $longitude, $zoom = 15)
    {
        return "https://www.openstreetmap.org/?mlat={$latitude}&mlon={$longitude}&zoom={$zoom}";
    }

    /**
     * Generate Bing Maps URL
     */
    public static function bingMaps($latitude, $longitude, $zoom = 15)
    {
        return "https://www.bing.com/maps?cp={$latitude}~{$longitude}&lvl={$zoom}";
    }

    /**
     * Get all available map links
     */
    public static function getAllMapLinks($latitude, $longitude, $label = null, $zoom = 15)
    {
        return [
            'google' => $label
                ? self::googleMapsWithLabel($latitude, $longitude, $label, $zoom)
                : self::googleMaps($latitude, $longitude, $zoom),
            'apple' => self::appleMaps($latitude, $longitude, $zoom),
            'waze' => self::waze($latitude, $longitude),
            'openstreetmap' => self::openStreetMap($latitude, $longitude, $zoom),
            'bing' => self::bingMaps($latitude, $longitude, $zoom),
        ];
    }

    /**
     * Generate coordinates link with dropdown for multiple map services
     */
    public static function generateMapDropdown($latitude, $longitude, $label = null, $zoom = 15)
    {
        $links = self::getAllMapLinks($latitude, $longitude, $label, $zoom);

        $html = '<div class="dropdown d-inline">';
        $html .= '<a href="' . $links['google'] . '" target="_blank" class="text-success text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" title="Open in Maps">';
        $html .= '<small>📍 ' . number_format($latitude, 4) . ', ' . number_format($longitude, 4) . '</small>';
        $html .= '</a>';
        $html .= '<ul class="dropdown-menu">';
        $html .= '<li><a class="dropdown-item" href="' . $links['google'] . '" target="_blank">🗺️ Google Maps</a></li>';
        $html .= '<li><a class="dropdown-item" href="' . $links['apple'] . '" target="_blank">🍎 Apple Maps</a></li>';
        $html .= '<li><a class="dropdown-item" href="' . $links['waze'] . '" target="_blank">🚗 Waze</a></li>';
        $html .= '<li><a class="dropdown-item" href="' . $links['openstreetmap'] . '" target="_blank">🌍 OpenStreetMap</a></li>';
        $html .= '<li><a class="dropdown-item" href="' . $links['bing'] . '" target="_blank">🔍 Bing Maps</a></li>';
        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }
}
