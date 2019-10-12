# GameTactic Authentication

This is part of GameTactic concept. 
This microservice is responsible for handling users
login and provide GameTactic JWT after user has signed
in via 3rnd party provider like Google or Twitter.

Currently supported authentication providers are:

- Wargaming (NA,EU,ASIA,RU)

## Installation

#### Development

Please run `docker-compose up` in project root directory.
App will be available via localhost:8080 and phpMyAdmin 
will boot into localhost. After this you can just continue
developing.

Please note, some login providers like Wargaming requires
extra key to be setup. For this please create file 
`api/.env.local` and add there missing values, which are
not set in the `api/.env`. In Wargaming case it would be 
`GUARD_WG=xxxxxx`

#### Production

Please deploy this app to production like you would deploy
normal Symfony application. Please refer Symfony guide 
[here](https://symfony.com/doc/current/deployment.html).

For production variables, please create `api/.env.prod.local`.

## Usage

This application can be used via http/https according 
to your configuration. Currently the flow works you
request authentication via GET request into one of the
authentication URLs. Then it will redirect you to your
provided url with GET parameter `code`.

If redirect parameter is not present, you will be redirected
to the index of microservice, where is JSON with following data

```json
{
"issuer": "GameTactic",
"publicKey": "-----BEGIN PUBL...PUBLIC KEY---......\n-----END PUBLIC KEY-----\n",
"currentToken": "eyJ0eXAiOiJKV.............6OF6ZokuCWr8"
}
```

In the index of the microservice will be shown always the current
JWT token public key, so you can use it validate it somewhere else,
also you can see the current token, if you did not specify redirect
url. Please note this is null, if it was not redirection.

You should always specify the redirection parameter. Leaving it out
will leave client into this awkward data page. It's working only like
this, so developer can easily develop other applications or test current.

##### Currently available endpoints.

Provider | URL 
--- | ---
Wargaming | /connect/wargaming/{eu,na,ru,asia}/{redirect_url}

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request.

## Credits

 - [Niko Granö](https://github.com/niko9911)

## License

GPLv3. Please see LICENSE file for further information.

