# Authorize

El llamado a este servicio WEB se hace desde el servidor del comercio y es el primer paso que se debe dar para interactuar con Plexo,
obteniendo como resultado una sesión de usuario.

> public *string* **Plexo\\Sdk\\Client::Authorize** ( *array* $auth )

## Parámetros

**$auth** (array)

  * **AuthorizationType** (int) Una de las constantes de *Plexo\\Sdk\\Type\\AuthorizationType:*
    * CLIENT_REFERENCE
    * OAUTH
    * ANONYMOUS
  * **MetaReference** (string)
  * **ActionType** (int) Máscara de bits formada con las constantes de *Plexo\\Sdk\\Type\\ActionType*:
    * SELECT_INSTRUMENT
    * REGISTER_INSTRUMENT
    * DELETE_INSTRUMENT
    * SESSION_EXTEND_AMOUNT
    * CLIENT_EXTEND_AMOUNT
  * **RedirectUri** (string)
  * **OptionalMetadata** *opcional*
  * **ClientInformation** *opcional* (array)
  * **LimitIssuers** *opcional*
  * **PromotionInfoIssuers** *opcional*
  * **ExtendableInstrumentToken** (opcional)

## Valores devueltos

(string) Id de sesión.

## Ejemplo

```php
<?php
// Require the Composer autoloader.
require_once 'vendor/autoload.php';

use Plexo\Sdk;
use Plexo\Sdk\Type;

$client = new Sdk\Client();

try {
    $response = $client->Authorize([
        'Action' => (Type\ActionType::SELECT_INSTRUMENT | Type\ActionType::REGISTER_INSTRUMENT),
        'Type' => Type\AuthorizationType::ANONYMOUS,
        'MetaReference' => '123456',
        'RedirectUri' => 'http://www.sitiocliente.com/plexo/XXX/YYY',
    ]);
    printf("ID de sesión: %s\n", $response);
} catch (Sdk\Exception\PlexoException $exc) {
    printf("[%s] (%d) %s\n", get_class($exc), $exc->getCode(), $exc->getMessage());
}
```

### Imprime

```
ID de sesión: 0e22e728c74046ce9353736c2c5bbe0b
```