/*
██ ██   ██  █████  ███    ███ ██ ██████   ██████  ██████  ███    ███     
██  ██ ██  ██   ██ ████  ████ ██ ██   ██ ██      ██    ██ ████  ████     
██   ███   ███████ ██ ████ ██ ██ ██████  ██      ██    ██ ██ ████ ██     
██  ██ ██  ██   ██ ██  ██  ██ ██ ██   ██ ██      ██    ██ ██  ██  ██     
██ ██   ██ ██   ██ ██      ██ ██ ██   ██  ██████  ██████  ██      ██ on GitHub : https://github.com/ixAmirCom
*/
<?php
if (!isset($_GET['hash']) || $_GET['hash'] !== 'okimking') {
    die("I'm safe =)");
}

ob_start();
error_reporting(0);

$token = '1234';//Token
define('API_KEY',$token);

function request($method , $array = [],$token = API_KEY)
{
    $url = 'https://api.telegram.org/bot'.$token.'/'.$method;
    $ch = curl_init();
    curl_setopt_array($ch,[
        CURLOPT_URL => $url ,
        CURLOPT_RETURNTRANSFER => true ,
        CURLOPT_POSTFIELDS => $array ,
        CURLOPT_TIMEOUT => 5
    ]);
    $result = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    } else{
        return json_decode($result);
    }
}
function sm($text,$key=null,$msg='',$markdown='html'){
    global $chat_id;
    return request('sendMessage',['chat_id'=>$chat_id,'text'=>$text,'reply_markup'=>$key,'reply_to_message_id'=>$msg,'parse_mode'=>$markdown]);
}
function editmessage($msg,$text,$key=null,$markdown='html',$ch=null){
    global $chat_id;
    if(isset($ch)) $chat_id = $ch;
    return request('editMessageText',['chat_id'=>$chat_id,'text'=>$text,'reply_markup'=>$key,'message_id'=>$msg,'parse_mode'=>$markdown]);
}
$rez = 0;
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
    $message    = $update->message ?? null;
    $text       = $message->text ?? null;
    $chat_id    = $message->chat->id ?? null;
    $from_id    = $message->from->id ?? null;
    $message_id = $message->message_id ?? null;
    $chattype   = $message->chat->type;
}else{
    $data = $update->callback_query->data;
    $chat_id = $update->callback_query->message->chat->id;
    $from_id = $update->callback_query->from->id;
    $chattype   = $update->callback_query->chat->type;
    $message_id  = $update->callback_query->message->message_id;
}
function administrator($chat_id){
  $res = request('getChatAdministrators',[
            'chat_id'=> $chat_id,
        ])->result;
  return $res;
}
function admins($chat_id) {
  foreach(administrator($chat_id) as $admin) {
    $ad[]= $admin->user->id;
  }
  return $ad;
}
function is_admin($user,$chat_id) {
	return in_array($user,admins($chat_id));
}
if (is_file("data/$chat_id/type.txt")){
$file = file_get_contents("data/$chat_id/type.txt");
}
$key = json_encode([
      'inline_keyboard' => [
          [['text' => 'گیف','callback_data' => 'set|gif'],['text' => 'تک تکست','callback_data' => 'set|text']],
          [['text' => 'پنج تکست','callback_data' => 'set|5']],
          ]
    ]);
if (strtolower($text) == '/start' and $chattype == 'private'){
  $key = json_encode([
        'inline_keyboard'=>[
         [['text'=>"🚀 افزودن ربات به گروه",'url'=>"https://t.me/ComAvalBot?startgroup=new"]],
         [['text'=>"📣 کانال آپدیت ها",'url'=>"https://t.me/Texo_Tm"],['text'=>"🌊 گروه پشتیبانی",'url'=>"https://t.me/TexoGap"]],
         [['text'=>"📄 راهنما",'callback_data'=>"help"]],
         ]
      ]);
    sm("سلام🙋🏻‍♂️\n\nبه ربات کامنت گیر ما خیلی خوش اومدی😁❤️\n\nبا ربات ما میتونی همیشه کامنت اول چنلتو امن نگه داری😃👌\n\nچرا معطلی ؟😳\n\nهمین الان ربات رو از طریق دکمه ی اضافه کردن به گروه به گروهت دعوت کن😍",$key);
}
if($data=="help"){
request('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"تست",
'reply_markup'=> json_encode([
        'inline_keyboard' => [
          [['text' => 'بازگشت','callback_data' => 'back']],
          ]
          ])
          ]);
}
if($update->message->new_chat_member->id == 1877929601){
	sm("−◾️┈┅━ ربات با موفقیت نصب شد👌
−◾️┈┅━ لطفا ربات را در گروه خود ادمین کنید❤️
┈┅━━━━┅┈ ┈┅━━━━┅┈
−◽️┈┅━  ❗️ با دستور
/panel
 میتوانید وارد تنظیمات ربات شوید و نوع گرفتن کامنت را انتخاب کنید.
┈┅━━━━┅┈ ┈┅━━━━┅┈
−◾️┈┅━ موفق باشید کاربر عزیز🤝");
    request('sendmessage',[
        'chat_id' => 5103113068, //User id Admin
        'text'    => 'یک کاربر جدید ربات را نصب کرد'
        ]);
    if (!is_dir("data/$chat_id")){
        mkdir("data/$chat_id");
        file_put_contents("data/$chat_id/type.txt",null);
    }
}
else if ($text == 'پنل پیوی' and is_admin($from_id,$chat_id)){
    sm("پنل پیوی شما ارسال شد",null,$message_id);
    if ($file == NULL){
      $type = 'تنظیم نشده !';
      }else{
      $type = $file;
    }
    request('sendmessage',[
        'chat_id'      => $from_id,
        'text'         => "🔰 لطفا نوع کامنت خود را انتخاب کنید\n\n🌟 نوع فعلی : $type",
        'reply_markup' => $key
        ]);
}
else if (strpos($data,'set|') !== false  and is_admin($from_id,$chat_id)){
    $ex = explode('set|',$data)[1];
    if ($ex == '5'){
        $type = 'پنج تکست';
    }
    if ($ex == 'text'){
        $type = 'تک تکست';
    }
    if ($ex == 'gif'){
        $type =  'گیف';
    }
    
    mkdir("data/$chat_id");
    file_put_contents("data/$chat_id/type.txt",$type);
    editmessage($message_id,"✅ با موفقیت تنظیم شد\n\n🌟 نوع فعلی : $type",$key);
}
if ($chattype == 'supergroup'){
 if ($text == '/panel'  and is_admin($from_id,$chat_id)){
  if ($file == NULL){
      $type = 'تنظیم نشده !';
   }else{
      $type = $file;
  }
  sm("🔰 لطفا نوع کامنت خود را انتخاب کنید\n\n🌟 نوع فعلی : $type",$key);  
}
if ($file == 'گیف'){
 if ($from_id == 777000){
        $i = rand(2,7);
        request('sendVideo',[
            'chat_id'=>$chat_id,
            'video'=>"https://t.me/idChannel/$i", //id Channel
            'reply_to_message_id'=>$update->message->message_id,
        ]);
		
 }
}
if ($file == 'تک تکست'){
 if ($from_id == 777000){
    sm("کامنت اول با موفقیت توسط بنده میل شد.🤝",null,$message_id);
 }
}
if ($file == 'پنج تکست'){
 if ($from_id == 777000){
    sm("کامنت اول 🥇",null,$message_id);
    sm("کامنت دوم 🥈",null,$message_id);
    sm("کامنت سوم 🥉",null,$message_id);
    sm("کامنت آخر 🔚",null,$message_id);
    sm("ناموس این پست با موفقیت امن شد🤝",null,$message_id);
   }
  }
}
if($data=="back"){
    request('editMessageText',[
    'chat_id'=>$from_id,
    'message_id'=>$message_id,
    'text'=>"سلام🙋🏻‍♂️

    به ربات کامنت گیر ما خیلی خوش اومدی😁❤️
    
    با ربات ما میتونی همیشه کامنت اول چنلتو امن نگه داری😃👌
    
    چرا معطلی ؟😳
    
    همین الان ربات رو از طریق دکمه ی اضافه کردن به گروه به گروهت دعوت کن😍",
    'reply_markup'=>json_encode([
            'inline_keyboard'=>[
             [['text'=>"🚀 افزودن ربات به گروه",'url'=>"https://t.me/ComAvalBot?startgroup=new"]],
             [['text'=>"📣 کانال آپدیت ها",'url'=>"https://t.me/Texo_Tm"],['text'=>"🌊 گروه پشتیبانی",'url'=>"https://t.me/TexoGap"]],
             [['text'=>"📄 راهنما",'callback_data'=>"help"]],
             ]
             ])
          ]);
        }
