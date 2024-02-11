<?php
require_once(__DIR__.'/connect.php');



//讀取15分鐘內還沒被處理的項目
$sql = "SELECT * FROM `queue` WHERE `status` = 0 AND `date` >= :date ORDER BY `date` ASC LIMIT 0,1";
$sth = $DBC->prepare($sql);
$sth->execute(array(
    'date' => date('Y-m-d H:i:s', time() - (60 * 15)),
));
$queue = $sth->fetch(PDO::FETCH_ASSOC);

//沒有需要產生的項目
if(empty($queue)){
    exit;
}


$sql = "UPDATE `queue` SET `status` = 1 WHERE `id`=:id  ";
$sth = $DBC->prepare($sql);
$sth->execute(array(
    'id' => $queue['id']
));


try {

    $prompt =  '{
        "contents": [
            {
                "parts": [
                    {
                        "text": "利用 js + canvas 生成一個「'.$queue['noun'].'」的畫像，畫布大小寬300px高300px ，canvas 的 ID 是 #canvas，只需要給我 js ，不需要 html，程式碼不超過500字"
                    }
                ]
            }
        ]
    }';
    
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key='.GEMINI_APIKEY,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $prompt,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    
    if($response === false){
        throw new Exception('Curl error: ' . curl_error($curl)."\n");
    }
    
    if(!isJson($response)){
        throw new Exception('response not json.'."\n");
    }
    
    $response = json_decode($response, true);
    
    if(isset($response['error'])){
        throw new Exception('response has error.'."\n");
    }
    
    
    $code = '';
    $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
    
    $pattern = '/```(javascript|js)(.*?)```/s';
    if (preg_match($pattern, $text, $matches)) {
        $code = $matches[2];
    } else {
        throw new Exception('code not match.'."\n");
    }
    
    $code = trim($code ?? '');
    
    if(empty($code)){
        throw new Exception('code empty.'."\n");
    }
    
    
    $sql = "INSERT INTO `gallery` SET 
        `author` = :author,
        `code` = :code,
        `name` = :name,
        `date` = :date
    ";
    $sth = $DBC->prepare($sql);
    $sth->execute(array(
        'author' => 'gemini',
        'code' => $code,
        'name' => $queue['noun'],
        'date' => date('Y-m-d H:i:s')
    ));
    
    
    $sql = "UPDATE `queue` SET `status` = 2 WHERE `id`=:id  ";
    $sth = $DBC->prepare($sql);
    $sth->execute(array(
        'id' => $queue['id']
    ));


} catch (\Exception $e) {

    file_put_contents(__DIR__.'/errorLog.txt', $e->getMessage(), FILE_APPEND);
    
    //降回原始狀態
    $sql = "UPDATE `queue` SET `status` = 0 WHERE `id`=:id  ";
    $sth = $DBC->prepare($sql);
    $sth->execute(array(
        'id' => $queue['id']
    ));

}

