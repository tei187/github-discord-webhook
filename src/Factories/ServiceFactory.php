<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Helpers\PlatformDetector;
use tei187\GitDisWebhook\Interfaces\ServiceInterface;

/** 
 * Factory class to create service instances based on detected platform.
 * 
 * Detection is done via rules defined in the `services.php` configuration file,
 * handled by the PlatformDetector helper class.
 * 
 * @package tei187\GitDisWebhook\Factories
 */
class ServiceFactory {
    private Config $config;

    /** 
     * Detects the service based on the incoming request and creates an instance of it.
     * @return ServiceInterface
     * @throws \InvalidArgumentException if no valid service is detected.
     */
    public function __construct(?Config $config = null) {
        $this->config = $config ?? new Config();
    }

    /** 
     * Creates an instance of the detected service.
     * @param string|null $serviceClass Optional service class to instantiate directly. 
     *                                  If null, detection will be performed per `services.php` configuration file.
     * @param array $limit Optional array of service classes to limit detection to.
     * @return ServiceInterface
     * @throws \InvalidArgumentException if no valid service is detected.
     */
    public function createService(?string $serviceClass = null, array $limit = []): ServiceInterface {
        if ($serviceClass === null || $serviceClass === "auto") {
            $serviceClass = $this->detectService($limit);
        }

        // throw exception if no service could be detected
        if( $serviceClass === null ) {
            throw new \InvalidArgumentException("No service could be detected for the incoming request. Use manual service assignment if possible or check the services configuration file.");
        }

        // throw exception if detected class does not implement the ServiceInterface
        if (!is_subclass_of($serviceClass, ServiceInterface::class)) {
            if($this->config->services['registry'][$serviceClass] ?? false) {
                $serviceClass = $this->config->services['registry'][$serviceClass];
            } else {
                throw new \InvalidArgumentException("Detected service class is not valid.");
            }
        }

        return new $serviceClass();
    }

    /** 
     * Detects the service class based on the incoming request.
     *
     * Uses the PlatformDetector helper to evaluate detection rules, correlated with the
     * configuration defined in the `services.php` file.
     *
     * @return string|null The detected service class or null if none matched.
     */
    public function detectService(?array $limit = []): ?string {
        // iterate over configured detectors
        foreach ($this->config->services['detectors'] as $serviceClass => $rules) {
            $matches = [];
            // evaluate each rule type
            foreach($rules as $ruleType => $values) {
                $matches[] = match ($ruleType) {
                    'headers'        => PlatformDetector::checkRequiredHeaders($values, $_SERVER),
                    'values_match'   => PlatformDetector::checkHeaderValuesMatch($values, $_SERVER),
                    'values_contain' => PlatformDetector::checkHeaderValuesContain($values, $_SERVER),
                    'method'         => PlatformDetector::checkRequestMethod($values, $_SERVER['REQUEST_METHOD']),
                    'origin'         => PlatformDetector::checkOrigin($values, $_SERVER['HTTP_ORIGIN'] ?? null),
                    default          => false,
                };
            }

            // if all rules matched, return the service class
            if (!in_array(false, $matches, true)) {
                // if a limit is set, check if the service class is in the limit
                if (!empty($limit) && !in_array($serviceClass, $limit, true)) {
                    continue; // skip if not in limit
                }
                return $serviceClass;
            }
        }

        // no matching service found
        return null;
    }
}