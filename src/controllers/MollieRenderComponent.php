<?php
/**
 * MollieRenderComponent.php
 *
 * @package controllerframework\controllers
 * @version 1.1
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne
 */

namespace controllerframework\controllers;

use controllerframework\registry\Request;

/**
 * Redirects the browser to the Mollie payment screen.
 *
 * When the application runs inside an iframe, the top-level
 * window is redirected to Mollie.
 */
class MollieRenderComponent extends DatarequestRenderComponent {

    /**
     * Redirects to the Mollie checkout page.
     *
     * @param Request $request Must contain 'results'
     */
    #[\Override]
    public function render(Request $request): void {

        $checkoutUrl = $request->get('results');

        if (!is_string($checkoutUrl)) {
            throw new \RuntimeException(
                'Invalid Mollie checkout URL.'
            );
        }

        $url = parse_url($checkoutUrl);

        if (
            $url === false ||
            ($url['scheme'] ?? '') !== 'https' ||
            empty($url['host']) ||
            isset($url['user']) ||
            isset($url['pass'])
        ) {
            throw new \RuntimeException(
                'Invalid Mollie checkout URL.'
            );
        }

        /*
         * Allow only Mollie's checkout domain.
         */
        $host = strtolower($url['host']);

        if (
            $host !== 'www.mollie.com' &&
            $host !== 'checkout.mollie.com'
        ) {
            throw new \RuntimeException(
                'Unexpected Mollie checkout host.'
            );
        }

        $checkoutUrl = htmlspecialchars(
            $checkoutUrl,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-store');

        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to payment</title>
</head>
<body>
    <p>Redirecting to the secure payment page...</p>
    <script>
        window.top.location.replace(' .
            json_encode(
                $url['scheme'] . '://' . $url['host'] .
                ($url['path'] ?? '/') .
                (isset($url['query']) ? '?' . $url['query'] : '') .
                (isset($url['fragment']) ? '#' . $url['fragment'] : ''),
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
            ) .
        ');
    </script>
</body>
</html>';

        exit;
    }
}