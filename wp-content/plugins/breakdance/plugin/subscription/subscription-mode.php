<?php


namespace Breakdance\Subscription;

use Breakdance\Licensing\LicenseKeyManager;

class SubscriptionMode
{
    use \Breakdance\Singleton;

    /** @var "free"|"trial"|"pro"  */
    public string $subscriptionMode = "free";


    function __construct() {
        // if(!isTrialExpired()){
        //     $this->subscriptionMode = 'trial';
        //     return;
        // }

        $this->subscriptionMode = LicenseKeyManager::getInstance()->getSubscriptionModeEligibleForStoredLicenseKey();
    }
}
