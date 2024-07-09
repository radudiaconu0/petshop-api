## Install dependencies
```bash
composer update
```

## Generate the RSA Key Pair
You can use OpenSSL to generate the RSA key pair.

Install OpenSSL: If you don't have OpenSSL installed, you can download it from here.

Generate the Private Key:
Open a Command Prompt and run the following command:
```bash
openssl genpkey -algorithm RSA -out private.key
```
Generate the Public Key:
Run the following command to generate the public key from the private key:

```bash
openssl rsa -pubout -in private.key -out public.key
```

You should now have two files: private.key and public.key.


move them to storage/app/keys

## Copy the .env.example file to .env
```bash
cp .env.example .env
```

## Generate the application key
```bash
php artisan key:generate
```

## Run the database migrations
```bash
php artisan migrate
```

## Run the database seeder
```bash

php artisan db:seed
```

## Start the local development server
```bash
php artisan serve
```

You can now access the server at http://localhost:8000

## access open api documentation
```bash
http://localhost:8000/api/documentation
```
