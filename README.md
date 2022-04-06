# lhp
This project will serve as the API of the LHP UI, in order to run this application please follow the guide below.

### Prerequisites
Make sure you have Docker application installed on your machine.

### Steps
- Clone the Project anywhere on your machine
- Go to the directory by running `cd lhp`
- We assumed here that you have the Docker installed now, so run docker-compose up -d --build to install all dependencies(php, nginx, mysql, phpmyadmin)
- When all dependencies are running, let's install the Laravel Application by running the command `cd src`, since you already on the root folder.
- Then run `docker-compose exec app composer install`
- After composer install finish running, copy the .env.example using the command `cp .env.example .env` then run `docker-compose exec app php artisan key:generate`
- Always a good practice to run `optimize` when starting the project `docker-compose exec app php artisan optimize`
- Then all setups are done and make sure your url is running in `localhost:5010`

### Database
I used mysql for the database and phpmyadmin to access the data, the configuration are avaialble as well in docker-compose.yml and .env

### Side Notes
- In Laravel 9 JWT doesn't support yet the latest version of the laravel, so instead I used the default one which is Sanctum or we can use Passport for serving the token
- For the Files upload currently i used the default Flysystem disk which is local, as we need to configure AWS S3 if i want to store the files there
- I Only have a short time doing the challenge so i wasn't able to do the Social Login and other not really sure on the process

### I had fun doing the challenge though