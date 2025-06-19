<?php

namespace App\Services;

use Jenssegers\Agent\Agent;

class DeviceDetectionService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Get device information from User-Agent string
     *
     * @param string $userAgent
     * @return array
     */
    public function getDeviceInfo($userAgent = null)
    {
        if ($userAgent) {
            $this->agent->setUserAgent($userAgent);
        }

        return [
            'device_name' => $this->getDeviceName(),
            'device_type' => $this->getDeviceType(),
            'device_brand' => $this->getDeviceBrand(),
            'os_name' => $this->getOperatingSystem(),
            'browser_name' => $this->getBrowserName(),
        ];
    }

    /**
     * Get device name (model)
     *
     * @return string|null
     */
    protected function getDeviceName()
    {
        // Get device model
        $device = $this->agent->device();

        if ($device) {
            return $device;
        }

        // Fallback: try to get from platform
        $platform = $this->agent->platform();
        if ($platform) {
            // For desktop, use platform as device name
            if ($this->agent->isDesktop()) {
                return $platform . ' Computer';
            }
        }

        return null;
    }

    /**
     * Get device type (mobile, tablet, desktop, robot)
     *
     * @return string
     */
    protected function getDeviceType()
    {
        if ($this->agent->isRobot()) {
            return 'robot';
        }

        if ($this->agent->isMobile()) {
            return 'mobile';
        }

        if ($this->agent->isTablet()) {
            return 'tablet';
        }

        if ($this->agent->isDesktop()) {
            return 'desktop';
        }

        return 'unknown';
    }

    /**
     * Get device brand/manufacturer
     *
     * @return string|null
     */
    protected function getDeviceBrand()
    {
        // Agent doesn't directly provide brand, but we can infer from device name
        $device = $this->agent->device();

        if (!$device) {
            return null;
        }

        // Common device brands mapping
        $brandMappings = [
            'iPhone' => 'Apple',
            'iPad' => 'Apple',
            'iPod' => 'Apple',
            'Macintosh' => 'Apple',
            'Samsung' => 'Samsung',
            'Galaxy' => 'Samsung',
            'Pixel' => 'Google',
            'Nexus' => 'Google',
            'Huawei' => 'Huawei',
            'Xiaomi' => 'Xiaomi',
            'OnePlus' => 'OnePlus',
            'LG' => 'LG',
            'Sony' => 'Sony',
            'HTC' => 'HTC',
            'Nokia' => 'Nokia',
            'Motorola' => 'Motorola',
        ];

        foreach ($brandMappings as $pattern => $brand) {
            if (stripos($device, $pattern) !== false) {
                return $brand;
            }
        }

        // Check user agent for brand hints
        $userAgent = $this->agent->getUserAgent();
        foreach ($brandMappings as $pattern => $brand) {
            if (stripos($userAgent, $pattern) !== false) {
                return $brand;
            }
        }

        return null;
    }

    /**
     * Get operating system name
     *
     * @return string|null
     */
    protected function getOperatingSystem()
    {
        $platform = $this->agent->platform();
        $version = $this->agent->version($platform);

        if ($platform) {
            return $version ? $platform . ' ' . $version : $platform;
        }

        return null;
    }

    /**
     * Get browser name
     *
     * @return string|null
     */
    protected function getBrowserName()
    {
        $browser = $this->agent->browser();
        $version = $this->agent->version($browser);

        if ($browser) {
            return $version ? $browser . ' ' . $version : $browser;
        }

        return null;
    }

    /**
     * Check if current request is from mobile device
     *
     * @return bool
     */
    public function isMobile()
    {
        return $this->agent->isMobile();
    }

    /**
     * Check if current request is from tablet
     *
     * @return bool
     */
    public function isTablet()
    {
        return $this->agent->isTablet();
    }

    /**
     * Check if current request is from desktop
     *
     * @return bool
     */
    public function isDesktop()
    {
        return $this->agent->isDesktop();
    }

    /**
     * Check if current request is from robot/bot
     *
     * @return bool
     */
    public function isRobot()
    {
        return $this->agent->isRobot();
    }

    /**
     * Get robot name if it's a bot
     *
     * @return string|null
     */
    public function getRobot()
    {
        return $this->agent->robot();
    }

    /**
     * Get all available information
     *
     * @param string|null $userAgent
     * @return array
     */
    public function getAllInfo($userAgent = null)
    {
        if ($userAgent) {
            $this->agent->setUserAgent($userAgent);
        }

        $deviceInfo = $this->getDeviceInfo();

        return array_merge($deviceInfo, [
            'is_mobile' => $this->isMobile(),
            'is_tablet' => $this->isTablet(),
            'is_desktop' => $this->isDesktop(),
            'is_robot' => $this->isRobot(),
            'robot_name' => $this->getRobot(),
            'user_agent' => $this->agent->getUserAgent(),
        ]);
    }
}
