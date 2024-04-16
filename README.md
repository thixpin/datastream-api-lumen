# DataStream API - Lumen


This is a simple API that allows you to stream data for the food delivery app. It is built using the [Lumen](https://lumen.laravel.com/) framework.

## Requirements
- PHP >= 8.3
- Composer
- MongoDB 

##### php-extenstions
- ext-mongodb

##### MongoDB
- We need to use cluster mode for the MongoDB. Cause of the MongoDB standalone mode does not support the change stream feature.


## Installation
```bash
$ git clone
$ cd datastream-api-lumen
$ composer install
$ cp .env.example .env
$ php artisan key:generate
$ php artisan db:seed
$ php  -S localhost:8080 -t public
```


## API Endpoints
- [x] POST /seed - Seed data
- [x] DELETE /seed - Clear data
- [x] POST /orders - Create order
- [x] PATCH /orders/{id}/cancel - Cancel order
- [x] GET /orders - Stream orders
- [x] GET /orders/{id} - Stream order detail
- [x] GET /drivers - Stream drivers
- [x] GET /drivers/{id} - Stream driver detail
- [x] 
- [x] GET /nostream/orders - Index orders
- [x] GET /nostream/orders/{id} - Show order
- [x] GET /nostream/drivers - Index drivers
- [x] GET /nostream/drivers/{id} - Show driver
- [x] GET /nostream/customers - Index customers
- [x] GET /nostream/customers/{id} - Show customer



## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
