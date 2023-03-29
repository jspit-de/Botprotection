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
require __DIR__.'/../class/Botprotection.php';

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

