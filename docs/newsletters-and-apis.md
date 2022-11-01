[<-Go back](/README.md)

# Newsletters and APIs

## Mailchimp API Tinkering

Empezaremos a conocer API de Mailchimp. Aprenderemos cómo instalar el SDK oficial de PHP y luego revisaremos los conceptos básicos sobre cómo realizar algunas llamadas API iniciales.

En este episodio conocemos la funcionalidad de Mailchimp, para crear campañas publicitarias, con nuestros contactos, entre las actividades que desarrollamos es 

Crear cuenta -----https://us21.admin.mailchimp.com/signup/setup/

Generamos la API ---- https://us21.admin.mailchimp.com/account/api/

Instalamos los paquetes Mailchimp en nuestro proyecto, usando composer, (dentro la maquina vagrant cd /vagrant/sites/lfts.isw811.xyz)

```php
composer require mailchimp/marketing
```

Agregamos a `services.php`, pertenece a la carpeta config (del proyecto)

```php
 'mailchimp' => [
        'key' => env('MAILCHIMP_KEY'),
        'lists' => [
            'subscribers' => env('MAILCHIMP_LIST_SUBSCRIBERS')
        ]
    ]
```

Y al archivo `.env`


    MAILCHIMP_KEY= 
    MAILCHIMP_LIST_SUBSCRIBERS=

    
##
