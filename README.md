DailyOje (Daily OnlineJournalEntry) is a javascript/html/php/mysql project for easy, distraction free writing. 

There is a basic login system with Twitter OAuth integration or the standard email/password combo.

##Setup

resources/dailyoje.sql has the SQL to create the necessary SQL tables.

All configuration is done in includes/class.config.php. This project heavily utilizes [Simple-Php-Framework](https://github.com/tylerhall/simple-php-framework). Please follow this project for configuration details. There are some additional configurations items that we have added. They are all commented.

At the very least set $twitterConsumerKey, $twitterConsumerSecret, $dbReadUsername,$dbWriteUsername,$dbReadPassword, and $dbWritePassword.


##Demo 

There is a loginless and stateless demo [available here](http://kylethielk.github.io/DailyOje).

##Screens

![Homepage](/screenshots/Screenshot-homepage.png?raw=true "Homepage")

![Login Screen](/screenshots/Screenshot-login.png?raw=true "Login Screen")

![Sample Note](/screenshots/Screenshot-sample.png?raw=true "Sample Note")

##License

The MIT License (MIT)

Copyright (c) 2014 Kyle Thielk

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
