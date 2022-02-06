## Finance

Simply add to your spork app through composer!

```
composer require spork/finance
```

Publish your assets

```
php artisan vendor:publish --provider=Spork\\Finance\\FinanceServiceProvider
```

You'll need to run `artisan migrate` to ensure your database gets the new repeating events schema

Lastly, register the Service Provider in your Spork App's `config/app.php` file. That will automatically add the Finance entry to the menu.

## Currently supported features
 - CSV Upload updates for accounts and transactions
 - Plaid integration
 - Plaid Webhook Updates
 - Custom transaction grouping
 - Budgeting based on transaction groupings

## Coming soon
 - A bill dashboard to help visualize the month's bills.
 - Transaction searching, search for transactions by name, amount, or by vendor.
 