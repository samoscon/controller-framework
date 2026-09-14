# controllerFramework

MVC Controller Framework based on the book *PHP 8 Objects, Patterns, and Practice* by M. Zandstra.

## Documentation

For a complete guide to developing a client application with Controller Framework 1.0.31, see the **[Controller Framework 1.0.31 — Client Application Developer Guide](https://samoscon.github.io/2026/09/11/controller-Framework-Client-Application-Developer-Guide.html)**.

For a basic example application, see the **[Controller Framework 1.0.31 — Basic Example Guide](https://samoscon.github.io/2025/11/10/controller-Framework-Basic-Example.html)**.

The guide covers the framework architecture, installation, application structure, configuration, `controls.xml`, Commands, Requests, rendering, authentication and sessions, CSRF protection, access tokens, database/domain objects, members, mail, audit tracing, error handling, security rules, and the recommended development workflow.

## Installation

The following steps describe how to deploy the basic Controller Framework application on an Apache2 web server with a MySQL/MariaDB database running in a Linux environment.

1. Create a root folder for your project with the name `[Your Project Root Folder]`.

2. Execute the following command in your project root folder:

   ```bash
   composer init
   ```

   Add the following dependency to your `composer.json`:

   ```json
   "require": {
       "samoscon/controller-framework": "^1.0"
   }
   ```

3. Execute:

   ```bash
   composer install
   ```

4. Copy the files and folders under:

   ```text
   ./vendor/samoscon/controller-framework/example/
   ```

   to your project root folder.

5. Set up a MySQL/MariaDB database using `DatabaseSetup.sql` to create the required members table.

6. Update:

   ```text
   ./config/app_options.ini
   ```

   with your database credentials, passwords, and other application settings.

7. Insert a first member manually into the database with:

   * `[name]` — the member's name
   * `[email]` — the member's email address
   * `role = "A"`
   * `active = "1"`
   * `subscriptionuntil = "2099-12-31"`

   No password is required at this stage, as the password can be configured during the first login.

You are now ready to test the framework and start developing your own application.

---

## Security

The Controller Framework provides several mechanisms to protect client applications, including:

* authentication and session management
* CSRF protection
* password hashing and verification
* access tokens
* audit tracing
* controlled error handling
* security-related configuration

Application developers remain responsible for correctly configuring and using these mechanisms in their client applications.

### AccessToken

Controller Framework 1.0.31 introduces the `AccessToken` class in the `controllerframework\security` namespace.

`AccessToken` provides an additional token-based security mechanism for protecting URLs or actions that require access beyond the normal application authentication mechanism.

It can be used, for example, for URLs that need an additional secret token before access is granted.

The token should be treated as a secret credential. Applications must therefore:

* generate tokens using secure random values;
* avoid exposing tokens unnecessarily;
* transmit tokens only over HTTPS;
* validate tokens before performing the protected operation;
* avoid logging tokens in application or audit logs;
* apply an appropriate lifetime or invalidation mechanism where required.

The `AccessToken` mechanism is intended as an additional security layer. It does not replace normal authentication, authorization, CSRF protection, or HTTPS.

---

## Security Considerations

### Production Environment

The example application included with the Controller Framework is configured for development and testing purposes. It must **not** be used as-is in a production environment.

Before deploying an application based on the Controller Framework to a production server, make sure that the application configuration contains:

```ini
environment=production
```

Error display must also be disabled in production. The example `.htaccess` file may contain:

```apache
php_flag display_errors On
```

for development and testing purposes. This setting must be disabled or removed in a production environment.

Displaying PHP errors to end users can expose sensitive information about the application, including file paths, database queries, configuration details, and internal implementation details.

---

### `Mapper::findAll()` and SQL Input

The `Mapper::findAll()` method accepts a free SQL `selectclause`:

```php
findAll(string $classname, string $selectclause = '')
```

The `selectclause` is intentionally designed to allow the application developer to specify SQL conditions, ordering, joins, and other SQL clauses when required.

Because this value is incorporated directly into the SQL statement, it must **never contain untrusted user input**.

For example, do not construct a `selectclause` directly from values received through `$_GET`, `$_POST`, cookies, or other client-controlled sources.

The use of `PDO::prepare()` does not make the `selectclause` itself safe, because the clause is part of the SQL statement rather than a bound parameter.

Application code is responsible for validating and restricting any user-supplied values before they are used to construct a `selectclause`.

---

### Redirects and HTTP Headers

Some Controller Framework components use request values when constructing HTTP redirects or HTTP headers.

In particular, values used by components such as `MollieRenderComponent`, `DownloadRenderComponent`, and `AgendaRenderComponent` must be generated by trusted application or framework code.

Values originating from users or other untrusted client-controlled sources must **not** be passed directly into HTTP `Location` or `Content-Disposition` headers.

For filenames used in download headers, applications should ensure that the value is a valid and safe filename and does not contain carriage-return, line-feed, or other HTTP header control characters.

Similarly, redirect destinations should be controlled by the application and should not be constructed directly from untrusted user input.

The framework components are intentionally generic and therefore rely on the application developer to ensure that the values supplied to these APIs are trusted and appropriately validated.

---

### Example Application

The example application is provided to demonstrate the use of the Controller Framework and to facilitate development and testing.

Its configuration, including error reporting and other development-oriented settings, should be reviewed before deploying an application to production.

Always review the security-related configuration of the application and hosting environment before making the application publicly accessible.
