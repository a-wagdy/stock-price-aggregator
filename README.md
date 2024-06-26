# Stock Price Aggregator

Using the Alpha Vantage API.

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
5. And then `docker-compose exec task composer install`
6. Please generate new [API key](https://www.alphavantage.co/support/#api-key) and place it as the value of `ALPHA_VANTAGE_KEY` in the `.env` file. 
7. Then execute `docker-compose exec task php artisan config:clear`

To make sure that the image is working, please open your browser and navigate to `http://127.0.0.1:8090/`.

# Upon successful installation

### Run the following commands

1. `docker-compose exec task php artisan migrate`
2. `docker-compose exec task php artisan db:seed --class=QuoteSeeder`

### Open two terminals to run the schedule and listener commands

1. `docker-compose exec task php artisan schedule:work`
2. `docker-compose exec task php artisan queue:listen`

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

# Sample Flow

```mermaid
graph TD;
    A[Start] --> B{Scheduler runs every min};
    B -->|Yes| C[Dispatch PopulateRealTimePrices job];
    C --> D[Call Alpha Vantage API];
    D --> E{Data received successfully?};
    E -->|Yes| F[Store prices in the database];
    F --> G[Cache prices for 15 minutes];
    G --> H[End];
    E -->|No| I[Log error];
    I --> B;
    B -->|No| H[End];
```


