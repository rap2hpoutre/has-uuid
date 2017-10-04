# HasUuid Trait
## What?
Why choose between Seq ID (aka auto-increment) and UUID when you can use **both**? 
This package helps you to *add* UUID to your Laravel Models.

*You can also use this class if you **only** wants UUID without increments.*
## Quick start
Install with composer:

```bash
composer require rap2hpoutre/has-uuid
```

Add the trait to your models:

```php
<?php
class User {
    
    use \Rap2hpoutre\HasUuid\HasUuid;
    
    // ...
}
```

Use the Trait to load your models:

```php
$user = User::uuid('e3ae1e6b-fabb-4839-bf65-de9a892c0d56');
```

And when you save a model, it will *magically* add a UUID to it:

```php
$user = new User;
$user->name = 'raph';
$user->save(); // <- Your user has now a UUID (and a ID if you
``` 

PS: don't forget to add the UUID in your migrations:

```php
<?php   
   class CreateSesNotificationsTable extends \Illuminate\Database\Migrations\Migration
   {
       public function up()
       {
           \Schema::create('user', function ($table) {
               $table->increments('id');
               $table->uuid('uuid')->index(); // <- THIS.
               $table->string('email')->index();
               $table->timestamps();
           });
       }
       // ...
   }
```