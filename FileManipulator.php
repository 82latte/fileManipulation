<?php
//変数の数チェック（全コマンド）
$variableCheck = function($command,$argc){
    $commandArray = ["reverse","copy","duplicate-contents"];//コマンドをあらかじめ格納
    if  ($command === "replace-string"){
        if ($argc === 5) return true;
        else {
            echo "Error : コマンドと必要な引数の数が整合しません。";
            return false;
        }
    }
    else if (in_array($command,$commandArray,true)){
        if ( $argc === 4) return true;
        else {
            echo "Error : コマンドと必要な引数の数が整合しません。";
            return false;
        }
    } else {
        echo "Error : コマンドが存在しません。".PHP_EOL;
        return false;
    }
};

//取り込み元ファイル存在チェック（全コマンド）
$isExistFile = function($fileInput){
    $file = file_exists($fileInput);
    if ($file){  //ファイルがある時
        $content = file_get_contents($fileInput); 
        if ($content === ""){ //ファイルの中に文字列が存在しないとき
            echo "Error :読み込み元のファイルの中身が空です。 ".PHP_EOL;
            return false;
        } else {
            return true;
        }
    } else { //ファイルがない時
        echo "Error : 読み込み元のファイルが存在しません。" .PHP_EOL;
        return false;
    }
};

//拡張子チェック(全コマンド)
$fileExtensionCheck = function($file){
    $extension = mb_substr($file, -4);
    if ($extension === ".txt")return true;
    else {
        echo "Error : ".$file ." の拡張子が誤っています。".PHP_EOL;
        return false ; 
    }
};

//全コマンドに適用するチェックを一元管理
$commonCheck = function($argv,$argc){
    global $variableCheck,$isExistFile,$fileExtensionCheck;
    return $variableCheck($argv[1],$argc) && $isExistFile($argv[2]) && $fileExtensionCheck($argv[2]);
};

//出力先パスが存在しないことを確認（reverse）
$isNotExistFile = function($fileOutput){
    $file = file_exists($fileOutput);
    if ($file){
        echo "Error : 出力先のファイルは既に存在します。";
        return false;
    } else {
        return true;
    }
};

//整数かどうか・有効な回数かチェック(duplicate-contents)
$countTypeCheck = function($count){
    if (!ctype_digit($count)){
        echo "Error : 回数は半角整数で入力してください。" .PHP_EOL;
        return false;
    }else if($count < 1){ 
        echo "Error : 回数は1以上の整数で入力してください。".PHP_EOL;
        return false;
    } else return true;
};
//置き換え対象の有無をチェック(replace-string：ファイルパス内の文字列の有無)
$isStringExist = function($file,$string){
    $data = file_get_contents($file);
    if( strpos($data ,$string) !== false){
        return true;
    } else {
        echo "Error : 置き換え対象が見つかりませんでした。".PHP_EOL;
    }
};

//置き換え対象の有無をチェック(copy : 置き換え前後のファイルは異なっているか・replace-string：置き換え前後の文字列は異なっているか)
$isEqual = function($beforeReplace,$afterReplace){
    if ($beforeReplace === $afterReplace){ 
        echo "Error : 置き換え前後のファイル・文字列が同一のものです。".PHP_EOL;
        return false;
    } else {
        return true;
    }
};

//ここからコマンド本体
function reverse($fileInput,$fileOutput){
    $inputData = file_get_contents($fileInput);
    file_put_contents($fileOutput, strrev($inputData));
    echo "処理完了" .PHP_EOL; 
    return $fileOutput;
}

function copyContents($fileInput,$fileOutput){
    $inputData = file_get_contents($fileInput);
    file_put_contents($fileOutput, $inputData);
    echo "処理完了" .PHP_EOL;
    return $fileOutput;
} 

function duplicateContents($fileInput,$count){
    $inputData = file_get_contents($fileInput);
    $duplicated = fopen($fileInput,'a' );
    for ($i = 1 ; $i <= $count ; $i++){
        fwrite($duplicated, $inputData);
    }
    fclose($duplicated);
    echo "処理完了" .PHP_EOL; 
    return $duplicated;
}

function replaceString($fileInput,$beforeReplace,$afterReplace){
    $inputData = file_get_contents($fileInput);
    $replaced = fopen($fileInput,'w');
    $replacedData = str_replace($beforeReplace, $afterReplace, $inputData);
    fwrite($replaced, $replacedData);
    fclose($replaced);
    echo "処理完了" .PHP_EOL; 
    return $replaced;
}


//コマンド入力後のフロー

if ($commonCheck($argv, $argc)){
    if ($argv[1] === "reverse" && $fileExtensionCheck($argv[3]) && $isNotExistFile($argv[3])){
        reverse($argv[2],$argv[3]);
    } else if ($argv[1] === "copy" && $fileExtensionCheck($argv[3]) && $isEqual($argv[2],$argv[3])){
        copyContents($argv[2],$argv[3]);
    } else if ($argv[1] === "duplicate-contents" && $countTypeCheck($argv[3])){
        duplicateContents($argv[2],$argv[3]);
    } else if ($argv[1] === "replace-string" && $isStringExist($argv[2],$argv[3]) && $isEqual($argv[3],$argv[4])){
            replaceString($argv[2],$argv[3],$argv[4]);
    }
}