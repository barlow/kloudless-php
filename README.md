# kloudless-php
PHP access class for Kloudless services. Nothing fancy, but it works.

I don't know about you, but getting Kloudless to work in PHP was a bit daunting because I was not overly familiar with
CURL, and there were no PHP examples to draw on. Anyway, I figured it out with a bit of experimentation and formed 
kloudlessClass.php as a wrapper for all that stuff. The example implements a simple and primitive folder browser.

You will need to plug in your kloudless info: API id, Key, and account id. You get all this from your Kloudless account
and dashboard. I connected to Dropbox and used that.

The class doesn't implement every interface that Kloudless offers - I just coded the main ones that were useful for general file and folder manipulation. There's a lot of repetition in the kloudlessClass.php functions, and I'm sure you will be able to cut-and-paste your way to any missing APIs you need. Or let me know...I'm not promising anything, however.

The example (kloudlessExample.php) makes a connection to MySQL so it can use mysql_real_escape_string() to clean inputs. You could replace these calls with something else if you don't want, or can't, connect to MySQL. Google "alternative mysql_real_escape_string" or something.

I found the "sendDownloadFile" API (in kloudlessClass.php) useful for what I was doing. It downloads a file from Kloudless and passes it on to the browser without the interim step of saving a file to the server file system, and all the management that it would entail. You will see three download APIs in the class - in some ways two of these are precurosrs to "sendDownloadFile".

The target application that this was all done for is a web-based file and folder sytsem that is a subsystem of a larger product. However, uploaded files might contain sensitive info and so the real application commpresses and encrypts every file that gets uploaded via Kloudless. Let me know if there is any interest in my publishing that stuff too - its just a tedious job of sanitizing for publication. Perhaps I outght to have done this class here with an "encryptAndUploadFile" API etc

Chris Barlow
barlow @ fhtsolutions DOT com
Feb 2015

