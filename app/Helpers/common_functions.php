<?php
namespace App\Helpers;
use DB;
use Mail;
use DateTime;
use DateInterval;
use DatePeriod;
use App;
use Twilio\Rest\Client;
use App\Services\TwilioService;
use Twilio\TwiML\VoiceResponse;
use Illuminate\Support\Facades\File;

define( 'TIMEBEFORE_NOW',         'Just Now' );
    define( 'TIMEBEFORE_MINUTE',      '{num} minute ago' );
    define( 'TIMEBEFORE_MINUTES',     '{num} minutes ago' );
    define( 'TIMEBEFORE_HOUR',        '{num} hour ago' );
    define( 'TIMEBEFORE_HOURS',       '{num} hours ago' );
    define( 'TIMEBEFORE_YESTERDAY',   'Yesterday' );
    define( 'TIMEBEFORE_FORMAT',      '%e %b, %I:%M %p' );
    define( 'TIMEBEFORE_FORMAT_YEAR', '%e %b, %Y - %I:%M %p' );

class common_functions
{
    public function cdTofL($cd) {
        return round($cd/3.4262591, 2);
    }
    
    public function fLtocd($fL) {
        return round($fL*3.4262591, 2);
    }

    public static function appVersion() {
        // todo: env file version
        $version = File::get(base_path().'/version.txt');
        return $version;
    }

    /**
	 * Get MAC Address using PHP
	 * @return string
	 */
    public function getMacAddress(){

        $mac = "Not Found";

        if(strtolower(PHP_OS) == 'linux'){
            ob_start(); // Turn on output buffering
            system('ifconfig -a'); //Execute external program to display output
            $mycom = ob_get_contents(); // Capture the output into a variable
            ob_end_clean(); // Clean (erase) the output buffer

            $findme = "ether";
            $pmac = strpos($mycom, $findme); // Find the position of Physical text
            $mac = substr($mycom, ($pmac+strlen($findme)+1), 17); // Get Physical Address
        } else { // window
            ob_start(); // Turn on output buffering
            system('ipconfig /all'); //Execute external program to display output
            $mycom = ob_get_contents(); // Capture the output into a variable
            ob_end_clean(); // Clean (erase) the output buffer

            $findme = "Physical";
            $pmac = strpos($mycom, $findme); // Find the position of Physical text
            $mac = substr($mycom, ($pmac+36), 17); // Get Physical Address
        }
        // TEST
        $mac = '';
        if ($mac == '') {
            // try to get from files
            $licensedFile = storage_path('app/sys.data');
            $mac = @file_get_contents($licensedFile);
            if ($mac!='') $mac = decrypt($mac);
            if ($mac == '') {
                $mac = strtoupper(str_random(4).'-'.str_random(4).'-'.str_random(4));
                file_put_contents($licensedFile, encrypt($mac));
            }
        }
            

        return $mac;
    }

    public function check_user_data($user_id)
    {
        $t_date=date('Y-m-d');
        $user=\App\Models\User::find($user_id);
        if(isset($user->id))
        {
            if($user->expired=='0' AND $user->plan_end<$t_date)
            {
                \App\Models\User::where('id', $user->id)->update([
                    'expired' => '1'
                ]);
            }
            else if($user->expired=='1')
            {
                $payment=\App\Models\Payment::where([['user_id', $user->id], ['expiry', '>', $t_date]])->limit(1)->get();
                if(count($payment)==1)
                {
                    $payment=collect($payment)->first();
                    \App\Models\User::where('id', $user->id)->update([
                        'expired' => '0',
                        'plan_end' => $payment->expiry
                    ]);
                }
            }
            
            $age=$this->get_age($user->dob);
            $years=$age['years'];
            $months=$age['months'];
            
            \App\Models\User::where('id', $user->id)->update([
                'years' => $years,
                'months' => $months
            ]);
        }
    }
    
    public function get_age($dob)
    {
        $dateOfBirth = $dob;
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        $age_data['years']=$diff->format('%y');
        $age_data['months']=$diff->format('%m');
        
        return $age_data;
    }
    
    public function determineVideoUrlType($url) {
    $yt_rx = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
    $has_match_youtube = preg_match($yt_rx, $url, $yt_matches);


    $vm_rx = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';
    $has_match_vimeo = preg_match($vm_rx, $url, $vm_matches);


    //Then we want the video id which is:
    if($has_match_youtube) {
        $video_id = $yt_matches[5]; 
        $type = 'youtube';
    }
    elseif($has_match_vimeo) {
        $video_id = $vm_matches[5];
        $type = 'vimeo';
    }
    else {
        $video_id = 0;
        $type = 'none';
    }


    $data['video_id'] = $video_id;
    $data['video_type'] = $type;

    return $data;

}

    function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];

    switch($mime){
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;

        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;

        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;

        default:
            return false;
            break;
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);

    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if($width_new > $width){
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        //copy image
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    }else{
        //cut point by width
        $w_point = (($width - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }

    $image($dst_img, $dst_dir, $quality);

    if($dst_img)imagedestroy($dst_img);
    if($src_img)imagedestroy($src_img);
}


function image_cropper($max_width, $max_height, $source_file, $dst_dir, $quality = 100, $x= 0, $y= 0){

    $imgsize = getimagesize($source_file);
    $mime = $imgsize['mime'];

    switch($mime){
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;

        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 100;
            break;

        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 100;
            break;

        default:
            return false;
            break;
    }

    $src_img = $image_create($source_file);


    $size = min(imagesx($src_img), imagesy($src_img));

    // Set the crop image size
    $im2 = imagecrop($src_img, ['x' => (int)$x, 'y' => (int)$x, 'width' => $size, 'height' => $size]);
    if ($im2 !== FALSE) {
        $image($im2, $dst_dir, $quality);

        // print("<pre>");
        // print_r($_REQUEST);


        // print($x.'---');
        // print($y.'---');
        // print($max_height.'---');
        // print($max_width.'---');



        // print($dst_dir);die;

        if($src_img)imagedestroy($src_img);
        imagedestroy($im2);

        return  $dst_dir;

    }

    return false;

}

    public function send_sms($message, $recipients)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $message = $client->messages->create($recipients,
        ['from' => $twilio_number, 'body' => $message] );

        return $message->sid;
    }

    public function log_activity($request, $activity)
    {
        $admin_id=$request->session()->get('admin_id');
        $admin_type=$request->session()->get('admin_type');
        DB::insert("INSERT INTO activity_log (user_id, user_type, activity, on_date) VALUES ('$admin_id', '$admin_type', '$activity', NOW())");
    }

    public function alert_email($request, $user1, $user2, $link_id, $type)
    {
        if($type=='1')
        {
            //user status changed, send alert to user

            $site=\App\Models\Site::find(1);
            //send email START
            $name=$user1->first_name.' '.$user1->last_name;
            $email=$user1->email;
            //App::setLocale($user2->lang);

            $url1='';
            $url1_title='';
            $url2='';
            $url2_title='';
            
            if($user1->status=='1')
            {
                $strong_title='Good News!';
                $title='Account Approved | '.$site->name;
                
                if($user1->sub_type=='model')
                $body="Hello ".$name.",<br><br>We are happy to inform you that your account has been approved. You can now login to join the agency and complete your portfolio. Once your portfolio is complete, you can start receiving offers of work from our clients, browse jobs and book a test shoot. We are excited to be a part of your journey.";
                else
                $body="Hello ".$name.",<br><br>We are happy to inform you that your account has been approved. You can now login and search for models or post a job. We are excited to be a part of your journey.";
                
                $url1=url('dashboard');
                $url1_title='Login';
            }
            elseif($user1->status=='2')
            {
                $strong_title='';
                $title='Account Rejected | '.$site->name;
                
                $body="Hello ".$name.",<br><br>We are sorry to inform you that your account has been rejected. You can email at info@babymodels.co.uk for any further assistance.";
            }

            $from_email=env('MAIL_FROM_ADDRESS');
            $from_name=env('MAIL_FROM_NAME');

            $data2=array(
                'email'=>$email,
                'from_email'=>$from_email,
                'from_name'=>$from_name,
                'title'=>$title,
                'strong_title'=>$strong_title,
                'body'=>$body,
                'url1'=>$url1,
                'url1_title'=>$url1_title,
                'url2'=>$url2,
                'url2_title'=>$url2_title,
                'site' => $site
            );

            Mail::send('email_templates.notification', $data2, function($message) use($email, $from_email, $from_name, $title) {
                $message->from($from_email, $from_name);
                $message->to($email);
                $message->subject($title);
                //$message->attach($pathToFile);
            });
            //send email END
        }
        elseif($type=='2')
        {
            //user notification to the user

            $site=\App\Models\Site::find(1);
            $user1=\App\Models\user::find($user1);
            //send email START
            $name=$user1->first_name.' '.$user1->last_name;
            $email=$user1->email;
            //App::setLocale($user2->lang);

            $url1='';
            $url1_title='';
            $url2='';
            $url2_title='';
            
            $strong_title=$link_id['strong_title'];
            $title=$link_id['title'];
            $body=$link_id['body'];

            $from_email=env('MAIL_FROM_ADDRESS');
            $from_name=env('MAIL_FROM_NAME');

            $data2=array(
                'email'=>$email,
                'from_email'=>$from_email,
                'from_name'=>$from_name,
                'title'=>$title,
                'strong_title'=>$strong_title,
                'body'=>$body,
                'url1'=>$url1,
                'url1_title'=>$url1_title,
                'url2'=>$url2,
                'url2_title'=>$url2_title,
                'site' => $site
            );

            Mail::send('email_templates.notification', $data2, function($message) use($email, $from_email, $from_name, $title) {
                $message->from($from_email, $from_name);
                $message->to($email);
                $message->subject($title);
                //$message->attach($pathToFile);
            });
            //send email END
        }
        else if($type=='3')
        {
            //newsletter
            $message=$user2;
            $link=$link_id;
            
            //send email START
            $name=$user1['name'];
            $email=$user1['email'];
            
            $body=$message;
            $body=str_replace('<p>', '', $body);
            $body=str_replace('</p>', '', $body);
                
            $strong_title='';
            $title=$link_id;
            
            $url1='';
            $url1_title='';
            
            $from_email=env('MAIL_FROM_ADDRESS');
            $from_name=env('MAIL_FROM_NAME');
            
            $data2=array(
                'email'=>$email,
                'from_email'=>$from_email,
                'from_name'=>$from_name,
                'title'=>$title,
                'strong_title'=>$strong_title,
                'body'=>$body,
                'url1'=>$url1,
                'url1_title'=>$url1_title,
            );
            
            Mail::send('email_templates.notification', $data2, function($message) use($email, $from_email, $from_name, $title) {
                $message->from($from_email, $from_name);
                $message->to($email);
                $message->subject($title);
                //$message->attach($pathToFile);
            });
            //send email END
        }
        
        else if($type=='4')
        {
            //new order placed, send alert to customer, and admin

            $site=\App\Models\Site::limit(1)->get();
            $site=collect($site)->first();
            $emails=array();
            if($site->email_alerts!='') $emails=explode(',', $site->email_alerts);

            $user1=DB::select("SELECT id, first_name, last_name, email, profile_image FROM users WHERE id=:user1 LIMIT 1", ['user1'=>$user1]);
            $user1=collect($user1)->first();

            $user2=DB::select("SELECT id, first_name, last_name, email, profile_image FROM users WHERE id=:user2 LIMIT 1", ['user2'=>$user2]);
            $user2=collect($user2)->first();

            //send email START
            $name=$user1->first_name.' '.$user1->last_name;
            $email=$user1->email;
            //App::setLocale($user2->lang);

            $donations=\App\Models\Donation::where([['paid', '1'], ['transaction_id', $link_id]])->orderBy('id', 'DESC')->get();
            $hui_si_tang=\App\Models\Transaction::where([['paid', '1'], ['transaction_id', $link_id]])->orderBy('id', 'DESC')->get();
            $lamps=\App\Models\LampBooking::where([['paid', '1'], ['transaction_id', $link_id]])->get();
            
            $transactions=array(); $i=0;
            
            $sub_total=0;
            foreach($donations as $item)
            {
                $transactions[$i]['product']='Donation';
                $transactions[$i]['total']=$item->total;
                
                $sub_total+=$transactions[$i]['total'];
                
                $i++;
            }
            foreach($hui_si_tang as $item)
            {
                $transactions[$i]['product']='Hui-Si Tang Membership: '.$item->account_no.' x '.$item->years.' year(s)';
                $transactions[$i]['total']=$item->total;
                
                $sub_total+=$transactions[$i]['total'];
                
                $i++;
            }
            foreach($lamps as $item)
            {
                $lamp=\App\Models\Lamp::select('title')->where('id', $item->lamp_id)->limit(1)->get();
                $lamp=collect($lamp)->first();
                
                $transactions[$i]['product']=$lamp->title.' x '.$item->years.' year(s) <br>
(Number: '.$item->lamp_number.')';
                $transactions[$i]['total']=$item->total;
                
                $sub_total+=$transactions[$i]['total'];
                
                $i++;
            }
            $tax=0;

            $body='';

            $link_id=explode('_', $link_id)[0];
            $title='Payment Successfull | '.$link_id;

            $url1='';
            $url1_title='';
            $url2='';
            $url2_title='';

            $from_email=env('MAIL_FROM_ADDRESS');
            $from_name=env('MAIL_FROM_NAME');

            $data2=array(
                'transaction_id' => $link_id,
                'transactions'=>$transactions,
                'sub_total'=>$sub_total,
                'tax'=>$tax,
                'site' => $site,
                'user1'=>$user1,
                'order'=>$link_id,
                'email'=>$email,
                'from_email'=>$from_email,
                'from_name'=>$from_name,
                'title'=>$title,
                'body'=>$body,
                'url1'=>$url1,
                'url1_title'=>$url1_title,
                'url2'=>$url2,
                'url2_title'=>$url2_title,
            );

            //email alert to customer
            Mail::send('email_templates.invoice', $data2, function($message) use($email, $from_email, $from_name, $title) {
                $message->from($from_email, $from_name);
                $message->to($email);
                $message->subject($title);
                //$message->attach($pathToFile);
            });

            $title='New Order Received';
            $url1=url('admin/manage-orders');
            $url1_title='Manage Orders';
            
            //email alert to admin
            $data2=array(
                'transaction_id' => $link_id,
                'transactions'=>$transactions,
                'sub_total'=>$sub_total,
                'tax'=>$tax,
                'user1'=>$user1,
                'site' => $site,
                'order'=>$link_id,
                'email'=>$email,
                'from_email'=>$from_email,
                'from_name'=>$from_name,
                'title'=>$title,
                'body'=>$body,
                'url1'=>$url1,
                'url1_title'=>$url1_title,
                'url2'=>$url2,
                'url2_title'=>$url2_title,
            );
            if(!empty($emails)) {
            foreach($emails as $email) {
                if($email=='') continue;
                $email=trim($email);

            Mail::send('email_templates.invoice', $data2, function($message) use($email, $from_email, $from_name, $title) {
                $message->from($from_email, $from_name);
                $message->to($email);
                $message->subject($title);
                //$message->attach($pathToFile);
            });
            }
            }
            //send email END
        }
        else if($type=='14')
        {
            //reset your password

            //send email START
            $user1=DB::select("SELECT id, first_name, last_name, email, profile_image FROM users WHERE email=:email LIMIT 1", ['email'=>$user2]);
            $user1=collect($user1)->first();

            $user2=DB::select("SELECT id, first_name, last_name, email, profile_image FROM users WHERE id=:user2 LIMIT 1", ['user2'=>$user2]);
            $user2=collect($user2)->first();

            //send email START
            $name=$user1->first_name.' '.$user1->last_name;
            $email=$user1->email;
            //App::setLocale($user2->lang);

            $body="Hello ".$name.",
            <br><br>It looks like you've lost your password. Please follow the link below to reset your account password, ignore if not requested by you.";

            $strong_title='';
            $title='Reset your password!';

            $url1=url('reset-password/'.$user1->email.'/'.$link_id);
            $url1_title='Reset password';
            $url2='';
            $url2_title='';

            $from_email=env('MAIL_FROM_ADDRESS');
            $from_name=env('MAIL_FROM_NAME');

            $site=\App\Models\Site::find(1);
            $data2=array(
                'email'=>$email,
                'site' => $site,
                'from_name'=>$from_name,
                'from_email'=>$from_email,
                'title'=>$title,
                'strong_title'=>$strong_title,
                'body'=>$body,
                'url1'=>$url1,
                'url1_title'=>$url1_title,
                'url2'=>$url2,
                'url2_title'=>$url2_title,
            );

            Mail::send('email_templates.notification', $data2, function($message) use($email, $from_email, $from_name, $title) {
                $message->from($from_email, $from_name);
                $message->to($email);
                $message->subject($title);
                //$message->attach($pathToFile);
            });
            //send email END
        }
    }

    function time_ago( $time )
    {
        $out    = ''; // what we will print out
        $now    = time(); // current time
        $diff   = $now - $time; // difference between the current and the provided dates

        if( $diff < 60 ) // it happened now
            return TIMEBEFORE_NOW;

        elseif( $diff < 3600 ) // it happened X minutes ago
            return str_replace( '{num}', ( $out = round( $diff / 60 ) ), $out == 1 ? TIMEBEFORE_MINUTE : TIMEBEFORE_MINUTES );

        elseif( $diff < 3600 * 24 ) // it happened X hours ago
            return str_replace( '{num}', ( $out = round( $diff / 3600 ) ), $out == 1 ? TIMEBEFORE_HOUR : TIMEBEFORE_HOURS );

        elseif( $diff < 3600 * 24 * 2 ) // it happened yesterday
            return TIMEBEFORE_YESTERDAY;

        else // falling back on a usual date format as it happened later than yesterday
            return strftime( date( 'Y', $time ) == date( 'Y' ) ? TIMEBEFORE_FORMAT : TIMEBEFORE_FORMAT_YEAR, $time );
    }

    public function remove_extra_words($request, $string)
    {
        $string=preg_replace('/\ba(\W|$)/i', '', $string);
        $string=preg_replace('/\ban(\W|$)/i', '', $string);

        return $string;
    }

    public function compress_image($source, $destination, $quality) {

    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source);

    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source);

    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source);

    imagejpeg($image, $destination, $quality);

    return $destination;
    }

     public static function instance()
     {
         return new common_functions();
     }

     public function emojiTranslater($content){


        $content = preg_replace('/:grinning-face:/i', '<i data-emoji=":grinning-face:" class="js_emoji twa twa-2x twa-grinning-face"></i>', $content);
        $content = preg_replace('/:grinning-face-with-big-eyes:/i', '<i data-emoji=":grinning-face-with-big-eyes:" class="js_emoji twa twa-2x twa-grinning-face-with-big-eyes"></i>', $content);

        return $content;

    }
}
?>
