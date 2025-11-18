<?php
//変数の数チェック（全コマンド）
$variableCheck = function($command,$argc){
    if  ($command === "replace-string" && $argc === 5) return true;
    else if ($argc === 4) return true;
    else {
        echo "Error : コマンドと必要な引数の数が整合しません。".PHP_EOL;
        return false;
    }
};

//ファイル存在チェック（全コマンド）
function isExistFile($fileInput){
    global $isFileOpened;
    $file = file_exists($fileInput);
    if ($file){  //ファイルがある時
        $content = file_get_contents($fileInput); 
        if ($content === ""){ //ファイルの中に文字列が存在しないとき
            echo "Error :ファイルの中身が空です。 ".PHP_EOL;
            return false;
        } else {
            return true;
        }
    } else { //ファイルがない時
        echo "Error : 読み込み元のファイルが存在しません。" .PHP_EOL;
        return false;
    }
}

//拡張子チェック(全コマンド)
function fileExtensionCheck($file){
    $extension = mb_substr($file, -4);
    if ($extension === ".txt")return true;
    else {
        echo "Error : ".$file ." の拡張子が誤っています。".PHP_EOL;
        return false ; 
    }
}

//整数かどうかチェック(duplicate-contents)
function countTypeCheck($count){
    if (!ctype_digit($count)){
        echo "Error : 回数は半角整数で入力してください。" .PHP_EOL;
        return false;
    } return true;
}

//置き換え対象の有無をチェック(replace-string)
function isStringExist($file,$string){
    $data = file_get_contents($file);
    if( strpos($data ,$string) !== false){
        return true;
    } else {
        echo "Error : 置き換え対象が見つかりませんでした。".PHP_EOL;
    }
}

function isEqual($beforeReplace,$afterReplace){
    if ($beforeReplace === $afterReplace){ 
        echo "Error : 置き換え対象が見つかりませんでした。".PHP_EOL;
        return false;
    } else {
        return true;
    }
}

//各変数のチェックreverseとcopy
$reverseCopyCheck = function($fileInput,$fileOutput){
    $isExistsInput = isExistFile($fileInput);
    $extensionOfInput = fileExtensionCheck($fileInput);
    $extensionOfOutput = fileExtensionCheck($fileOutput);
    return $isExistsInput && $extensionOfInput&& $extensionOfOutput;
};

//各変数のチェック duplicate-contents
$duplicateContentsCheck = function($fileInput,$count){
    $isExistsInput = isExistFile($fileInput);
    $extensionOfInput = fileExtensionCheck($fileInput);
    $countType = countTypeCheck($count);
    if ($count < 1){
        echo "Error : 回数は1以上の整数で入力してください。";
        return false;
    } else {
    return $isExistsInput && $extensionOfInput && $countType;
    }
};

//各変数のチェック replace-string
$replaceStringCheck = function($fileInput,$beforeReplace,$afterReplace){
    $isExistsInput = isExistFile($fileInput);
    $extensionOfInput = fileExtensionCheck($fileInput);
    $stringExist = isStringExist($fileInput,$beforeReplace);//元の文字列があるかチェック
    $equal = isEqual ($beforeReplace,$afterReplace);
    return $isExistsInput && $extensionOfInput && $stringExist && $equal;
};

//ここからコマンド本体
function reverse($fileInput,$fileOutput){
    $inputData = file_get_contents($fileInput);
    file_put_contents($fileOutput, strrev($inputData));
    return $fileOutput;
}

function copyContents($fileInput,$fileOutput){
    $inputData = file_get_contents($fileInput);
    file_put_contents($fileOutput, $inputData);
    return $fileOutput;
} 

function duplicateContents($fileInput,$count){
    $inputData = file_get_contents($fileInput);
    $duplicated = fopen($fileInput,'a' );
    for ($i = 1 ; $i <= $count ; $i++){
        fwrite($duplicated, $inputData);
    }
    fclose($duplicated);
    return $duplicated;
}

function replaceString($fileInput,$beforeReplace,$afterReplace){
    $inputData = file_get_contents($fileInput);
    $replaced = fopen($fileInput,'w');
    $replacedData = str_replace($beforeReplace, $afterReplace, $inputData);
    fwrite($replaced, $replacedData);
    fclose($replaced);
    return $replaced;
}


//コマンド入力後のフロー
if ($argv[1] === "reverse") {
    if ($variableCheck($argv[1],$argc)){
        if($reverseCopyCheck($argv[2],$argv[3])) {
	        reverse($argv[2],$argv[3]);
            echo "処理完了" .PHP_EOL; 

        }
    }
} else if ($argv[1] === "copy") {
    if ($variableCheck($argv[1],$argc)){
        if($reverseCopyCheck($argv[2],$argv[3])) {
	        copyContents($argv[2],$argv[3]);
            echo "処理完了" .PHP_EOL; 
        }
    }
} else if ($argv[1] === "duplicate-contents" ){
    if ($variableCheck($argv[1],$argc)){
       if($duplicateContentsCheck($argv[2],$argv[3])) {
	        duplicateContents($argv[2],$argv[3]);
            echo "処理完了".PHP_EOL;
        }
    }
} else if ($argv[1] === "replace-string"){
    if ($variableCheck($argv[1],$argc)){
        if($replaceStringCheck($argv[2],$argv[3],$argv[4])) {
	        replaceString($argv[2],$argv[3],$argv[4]);
            echo "処理完了".PHP_EOL;
        }
    }
} else {
    echo "Error : コマンドが見つかりません。 " .PHP_EOL;
}