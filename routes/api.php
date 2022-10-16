<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spork\Core\Models\FeatureList;
use Spork\Finance\Contracts\Services\PlaidServiceContract;
use Spork\Finance\Models\Account;

Route::middleware('auth:sanctum')->post('upload-accounts', function (Request $request) {
    $mapping = json_decode($request->get('mapping'));
    $filePath = $request->file('image')->store('local');

    $file = fopen(storage_path('app/'.$filePath), 'r');
    $line = 0;
    $headers = [];
    $accounts = [];
    try {
        while (! feof($file)) {
            $row = fgetcsv($file);
            if ($line === 0) {
                $line++;
                $headers = $row;

                continue;
            }

            if (empty($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);

            $line++;
            $modelFillable = [];
            foreach ($mapping as $key => $value) {
                $modelFillable[$key] = $data[$mapping->$key] ?? null;
            }
            $modelFillable['feature_list_id'] = $request->get('feature_list_id');
            $accounts[] = Account::create($modelFillable);
        }
    } finally {
        fclose($file);
        unlink(storage_path('app/'.$filePath));
    }

    return $accounts ?? [];
});
Route::middleware('auth:sanctum')->post('upload-transactions', function (Request $request) {
    request()->validate([
        'account_id' => 'string|exists:accounts,account_id|required',
    ], $request->all());
    $mapping = json_decode($request->get('mapping'));
    $filePath = $request->file('image')->store('local');

    $file = fopen(storage_path('app/'.$filePath), 'r');
    $line = 0;
    $headers = [];
    $transactions = [];
    $row = null;
    try {
        while (! feof($file)) {
            $row = fgetcsv($file);
            if ($line === 0) {
                $line++;
                $headers = $row;

                continue;
            }

            if (empty($headers) || empty($row)) {
                continue;
            }

            if (count($headers) !== count($row)) {
                continue;
            }

            $data = array_combine($headers, $row);

            $line++;
            $modelFillable = [];
            foreach ($mapping as $key => $value) {
                $modelFillable[$key] = $data[$mapping->$key] ?? null;
            }

            request()->validate([
                'name' => 'required_if:transaction_id,null',
                'vendor_name' => 'string',
                'amount' => 'required_if:transaction_id,null',
                'date' => 'required_if:transaction_id,null|date',
                'pending' => 'boolean|in:posted,pending',
                'type' => 'string',
                'transaction_id' => 'string',
            ], $modelFillable);

            if (empty($modelFillable['transaction_id'])) {
                $modelFillable['transaction_id'] = md5(sprintf('%s.%s.%s', $modelFillable['name'], $modelFillable['amount'], $modelFillable['date']));
            }

            if (! is_bool($modelFillable['pending'])) {
                $modelFillable['pending'] = $modelFillable['pending'] === 'pending';
            }
            if (! empty($modelFillable['date'])) {
                $modelFillable['date'] = \Carbon\Carbon::parse($modelFillable['date']);
            }

            if (! empty($modelFillable['amount'])) {
                $modelFillable['amount'] = stripos($modelFillable['amount'], '--') !== false ? trim($modelFillable['amount'], '-') : $modelFillable['amount'];
            }
            if (! empty(request('account_id'))) {
                $modelFillable['account_id'] = request('account_id');
            }

            if (request()->get('invert_values')) {
                $modelFillable['amount'] = $modelFillable['amount'] > 0 ? -abs($modelFillable['amount']) : abs($modelFillable['amount']);
            }

            $transaction = \App\Finance\Models\Transaction::firstWhere('transaction_id', $modelFillable['transaction_id']);

            if (empty($transaction)) {
                $transaction = \App\Finance\Models\Transaction::create($modelFillable);
            }

            $transactions[] = $transaction->update($modelFillable);
        }
    } catch (\Throwable $e) {
        dd($e, $row, $headers);
    } finally {
        fclose($file);
        unlink(storage_path('app/'.$filePath));
    }

    return $accounts ?? [];
});

Route::middleware('auth:sanctum')->delete('account/{account}', fn (Account $account) => $account->delete());
Route::middleware('auth:sanctum')->post('/plaid/create-link-token', fn (PlaidServiceContract $service) => response()->json($service->createLinkToken()));
Route::middleware('auth:sanctum')->post('/plaid/exchange-token', function (PlaidServiceContract $service) {
    $response = $service->exchangeLinkTokenForAccessToken(request()->get('public_token'));

    return FeatureList::create([
        'name' => $response->access_token,
        'feature' => 'finance',
        'user_id' => auth()->user()->id,
        'settings' => [
            'access_token' => $response->access_token,
            'item_id' => $response->item_id,
            'institution_id' => request()->get('institution'),
        ],
    ]);
});

Route::post('plaid/webhook', fn () => info(request()->all()));
