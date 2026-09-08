<?php
/**
 * ErrorHandler.php
 *
 * @package controllerframework\error
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\error;

/**
 * Helper class to centralise the handling of all types of errors
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ErrorHandler
{
    /**
     *
     * @var string holds info whether controller is started as 'production' or 'development'
     */
    private static string $environment = 'production';

    /**
     * Method to initialise the Error Handler
     * 
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
    }

    /**
     * Method to initialise the $environment variable
     * 
     */
    public static function setEnvironment(string $environment): void
    {
        self::$environment = $environment;
    }


    /**
     * Method to add an error to the Error logfile
     * 
     */
    public static function handleException(\Throwable $exception): void
    {
        error_log(
            sprintf(
                "%s: %s in %s on line %d\n%s",
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            )
        );

        http_response_code(500);

        if (self::$environment === 'development') {
            echo '<h1>Er is een interne fout opgetreden.</h1>';
            echo '<p><strong>'
                . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8')
                . '</strong></p>';
            echo '<p>Bestand: '
                . htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8')
                . '</p>';
            echo '<p>Regel: ' . $exception->getLine() . '</p>';
        } else {
            echo 'There is an internal server error. Please contact ' . _MAILFROM;
        }
    }
}