<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Fake Store App
## How to run the project
1. Clone the repository
2. You need to have PHP 8.2 installed
3. Run `composer install`
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`. It will show a token that you can use to make requests.
6. Run `php artisan serve`

## Syncing Products
To sync products from the external API, you need to run the following command:
```bash
php artisan app:sync-fake-store-products
```
It will create new ones and update the existing ones. It will also sync the product categories.

## Updating a Product
Make a `PATCH` request to the following endpoint:
`/api/products/{id}` with a JSON body like this:
```json
{
  "title": "Some Title",
  "price": 20.56,
  "description": "Some short description"
}
```

Where `{id}` is the product id in the database.

You need to authenticate with the Authorization header: `Bearer {token}`, using the token shown in the migration step.

## Running tests
Run the following command:
```bash
php artisan test --parallel
```
