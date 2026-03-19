<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use DB;
use Config;
use App\Models\Setting;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function getSetting($name) {
        $s = DB::table('settings')->where('name', $name)->first();
        return $s?$s->value:'';
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        try {
            if (\Schema::hasTable('settings')) {
                $config = Config::get('app');   

                $settings = DB::table('settings')->get();
                foreach ($settings as $setting) {
                    if ($setting->value) {
                        if ($setting->type == 'file') {
                            $config[$setting->name] = Storage::url($setting->value);
                        } else {
                            $config[$setting->name] = $setting->value;
                        }
                    }
                }
                        
                Config::set('app', $config);
            }
            
            if (\Schema::hasTable('smtp_settings')) {
                $smtp_settings = DB::table('smtp_settings')->first();
                if ($smtp_settings) //checking if table is not empty
                {
                
                    if ($smtp_settings->username && $smtp_settings->username != '') {
                        $config = Config::get('mail');
                        $config['driver'] = $smtp_settings->driver;
                        $config['host'] = $smtp_settings->host;
                        $config['port'] = $smtp_settings->port;
                        $config['encryption'] = $smtp_settings->encryption;
                        $config['username'] = $smtp_settings->username;
                        $config['password'] = $smtp_settings->password;
                        if ($smtp_settings->senderemail!='') {
                            $config['from']['address'] = $smtp_settings->senderemail;
                        }

                        if ($smtp_settings->sendername!='') {
                            $config['from']['name'] = $smtp_settings->sendername;
                        } else {
                            $appConfig = Config::get('app');   
                            $config['from']['name'] = $appConfig['name'] . ' Administrator';
                        }
                        Config::set('mail', $config);
                    }
                    
                }
            }

            
        } catch (\PDOException $ex) {
            // do nothing
        }
    }
}
