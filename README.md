# Stock Price Aggregator

# Stack

- PHP 8.2
- Laravel 11
- MySQL 8
- Nginx
- Redis

# Installation

> Make sure to have Docker and docker-compose installed.

1. Clone the repo.
2. Navigate to the app's directory
3. Build the image by running `docker-compose build`. This will take a few minutes.
4. Then run `docker-compose up -d`.
5. And then `docker-compose exec import composer install`

To make sure that the image is working, please open your browser and navigate to `http://127.0.0.1:8090/`.

# Upon successful installation

Run the following commands

1. `docker-compose exec task php artisan migrate`
2. `docker-compose exec task php artisan db:seed --class=QuoteSeeder`
3. `docker-compose exec task php artisan db:seed --class=PriceSeeder`

> Open two terminals to run the schedule and listener commands

`docker-compose exec task php artisan schedule:work`
`docker-compose exec task php artisan queue:listen`

# REST API

In this repo, you'll find the [Postman Collection](https://github.com/a-wagdy/task/blob/main/postman_collection.json)

## Stock prices report

- **[GET]**: http://127.0.0.1:8090/api/report

## Report per stock price

- **[GET]**: http://127.0.0.1:8090/api/report/{symbol}
> Where the symbol could be IBM or AAPL.

# DB structure

<img width="669" alt="Screenshot 2024-03-17 at 3 20 01 AM" src="https://github.com/a-wagdy/task/assets/64163189/99382300-06d1-4433-97c7-d6a2b5d3609d">

# Running test cases

Run the following commands

1. `docker-compose exec task php artisan migrate --env=testing`
2. `docker-compose exec task php artisan test --env=testing`  


