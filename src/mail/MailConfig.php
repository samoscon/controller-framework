<?php
/**
 * MailConfig.php
 *
 * @package controllerframework\mail
 * @version 1.0
 * @copyright (c) 2026, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */

namespace controllerframework\mail;

use controllerframework\registry\Registry;

/**
 * Provides configuration settings for the mail functionality.
 */
class MailConfig
{
    /**
     * Determines whether email sending is enabled.
     *
     * The default is enabled to maintain backward compatibility
     * with existing applications.
     */
    public static function isEnabled(): bool
    {
        $config = Registry::instance()->getAppConfig();

        $enabled = $config->get('mailenabled');

        // Backward compatibility: enabled when not configured.
        if ($enabled === null) {
            return true;
        }

        return strtoupper(trim((string) $enabled)) !== 'N';
    }
}