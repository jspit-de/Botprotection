# Botprotection
PHP Botprotection - Simple php class for forms to protect against bots

Botprotection uses the protectionInput method to generate a string with a special combination of HTML and Javascript which is placed in a form.
After submitting the form, various checks are performed with the isBot method to identify a bot.

## Installation & loading

- Code -> Download ZIP Botprotection-main.zip
- Extract the file Botprotection.php to a new Folder 

## Usage

Botprotection needs a session_start() in advance before creating an instance.
The class creates an array with the key 'Botprotection_v1' in the session.
This array is required by the class for internal purposes and must not be used for any other purpose.

## Basic example

```php
session_start();
require __DIR__.'/rel_path_to_class/Botprotection.php';

$botprotect = new Botprotection;
$status = $botprotect->status('email2', false);
if(!empty($_POST)){
    $input = htmlspecialchars($_POST['inputtext']);
    $sender = $botprotect->isBot('email2') ? "Bot" : "Human";
}
else {
    $input = "";
    $sender = "Nobody";
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Botprotection</title>
  </head>
  <body>
    <h2>Botprotection</h2>
    <p>Status: $status</p>
    <p>Sender: $sender</p>
    <form method='POST'>
        <label>
            Input <input name='inputtext' type='text' value='$input'>
        </label>
        {$botprotect->protectionInput('email2')}
        <button type='submit'>Send</button>
    </form>
  </body>
</html>
HTML;
echo $html;
```

## Methods

### protectionInput(string $name) : string

Returns HTML as a string to be embedded in a form.
The form must use the POST method. 
The name is used for the generated input elements and is also reference.
The name must be unique, do not use the name for other elements.
Special information for verifying the form response is stored under these names in the session array.

### isBot(string $name, bool $setNameInvalid = false) : bool

Returns true if a bot has been identified. 
The name must be identical to the name used in the protectionInput method.
If the form has not yet been submitted, the method returns false.
With $setNameInvalid = true, the information under the names in the session is deleted.
The second parameter can be set to true if the isBot and status methods are no longer used afterwards.

### status(string $name, bool $setNameInvalid = false) : int
Returns a number as status for the verification of the protection input elements. 

 Number | Description                   
 -------| ------------------------------ 
-1      | Form not submitted       
 0      | Ok, no bot 
 1      | Name has expired, the name is unknown, or there is a session error
 2      | Name not exists in $_POST
 3      | $_POST[$name] is not a array
 4      | Keys 0 and/or TokenId not exists
 5      | Honeypot not empty
 6      | Invalid Token
 7      | Time < minInputTime
 8      | Time > maxInputTime

With $setNameInvalid = true, the information under the names in the session is deleted.
The second parameter can be set to true if the isBot and status methods are no longer used afterwards.

### setMinInputTime(int $minSeconds = 5) : self

Set the minimum input time in seconds. 
The time should be chosen as minimally as a human user needs to fill out the form.
The time can be set dynamically before applying isBot or status.

```php
$status = $botProtection
  ->setMinInputTime(3)  //3 seconds
  ->status('email1bcc')
;
```

### setMaxInputTime(int $maxSeconds = 1200) : self

Set the maximum input time in seconds.
The time should be chosen as a maximum human user needs to fill out the form.
The time can be set dynamically before applying isBot or status.

## Demo and Test
http://jspit.de/check/phpcheck.botprotection.php

## Documentation
http://jspit.de/tools/classdoc.php?class=Botprotection

## Requirements

- PHP 7.x, PHP 8.x