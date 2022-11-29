<?php

declare(strict_types = 1);

function getTransactionFiles(string $dirPath): array
{
    $files = [];

    foreach (scandir($dirPath) as $file) {
        if (is_dir($file)) {
            continue;
        }

        $files[] = $dirPath . $file;
    }

    return $files;
}

function getTransactions(string $fileName): array // mettendo ?callable e passiamo anche l'handler in questo modo abbiamo una funzione che chiama 
                                                // altri metodi di estrazione anche per altri tipi di file
{
    if (! file_exists($fileName)) {
        trigger_error('File "' . $fileName . '" does not exist.', E_USER_ERROR);
        
    }

    $file = fopen($fileName, 'r');
    
    $transactions = [];
    while(($transaction=fgetcsv($file))!==false){
        $transactions[]=extract_transactions($transaction);
    }

    
    return $transactions;
}

function extract_transactions(array $row): array{

    [$date, $check, $description, $amount] = $row;
    
    $amount= (float) str_replace(['$',','],'',$amount);


    return [
        'date' => $date,
        'check' => $check,
        'description' => $description,
        'amount' => $amount,
    ];

}

function totalAmount(array $transactions ): array {

    $total = [ 'tot In'=>0,'tot Ex' => 0,'net Tot' => 0];

    foreach ($transactions as $transaction){
        $total['net Tot'] += $transaction['amount'];
        if($transaction['amount']>0){
            $total['tot In'] += $transaction['amount'];
        }else{
            $total['tot Ex'] += $transaction['amount'];
        }
    }

   
    return $total;
}