>In BETA!

![blua.blue](asset/img/blua-blue-logo.png)
# Content Enablement Platform
Stateles (JWT) hybrid (frontend & headless) CMS built with

- PHP ( [neoan3](https://github.com/sroehrl/neoan3) )
- MySQL
- [Vue](https://vuejs.org) (administration & optional frontend)
- [Bulma](https://bulma.io) (administration & optional frontend)

## Requirements

PHP7

[composer](https://getcomposer.org/)

[nodeJS & npm](https://nodejs.org)

[neoan3-cli](https://www.npmjs.com/package/neoan3-cli)


## Installation
### For usage & production
1. `composer create-project blua-blue/blua-blue -s beta`
2. Create [Credentials](#credentials) (`neoan3 credentials`)
3. `neoan3 migrate models up`

### Collaborators/developers
Download/clone/fork the repository @ https://github.com/blua-blue/blua-blue

1. Change the "RewriteBase" in the .htaccess file (or create a route-script for Nginx)
2. Run `composer install`
3. Run `npm install`
4. Create [Credentials](#credentials) (`neoan3 credentials`)
5. Run `neoan3 migrate models up`

### Credentials
Credentials are expected to be in a folder "credentials" outside the web-root. 
Please make changes to the neoan frame (frame/neoan/Neoan.php) in order to provide credentials for:

_neoan3-apps/db_ https://github.com/sroehrl/neoan3-db

_neoan3-apps/stateless_ https://github.com/sroehrl/neoan3-stateless

_phpmailer_ https://github.com/PHPMailer/PHPMailer

*Example:*

```JSON
{
"blua_db": {
  "name": "your_db",
  "assumes_uuid": true,
  "password": "yourPassword",
  "user": "phpDbUser"

  },
"blua_stateless": {
  "secret": "yourSEcretKey"
  },
"blua_mail": {
  "host": "mail.example.com",
  "username": "some@example.com",
  "password": "MailSMTPpassword"
  }
}
```
Don't have access outside of the project? Alternatively, you can create a private function in the frame and set
`$this->credentials` with an array supplying these credentials. However, be aware that when using credentials in your project, sharing the codebase becomes a security consideration.
```PHP
$this->credentials = [
    'blua_db'=>[...],
    ...
]
```
You can also use `neoan3 credentials` to manage your credentials

> You might have to provide additional settings depending on your services. 
> Additional mailing-setup can be managed in the function newMail() (in the neoan-frame) and within your credentials when dealing with database-credentials.
> Please see the respective documentation for these packages.

### License

MIT license [opensource](https://opensource.org/licenses/MIT)

Copyright 2019 [blua.blue](https://blua.blue)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

# Built with love & neoan3?

Blua.blue is written as [neoan3](https://github.com/sroehrl/neoan3/) components. We are currently working on integrating blua.blue in the neoan3-cli to be included with your neoan3 projects.

