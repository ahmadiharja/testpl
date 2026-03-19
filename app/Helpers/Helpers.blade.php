<?php
    function cdTofL($cd) {
        return round($cd/3.4262591, 2);
    }
    
    function fLtocd($fL) {
        return round($fL*3.4262591, 2);
    }

    function appVersion() {
        // todo: env file version
        $version = File::get(base_path().'/version.txt');
        return $version;
    }

    /**
	 * Get MAC Address using PHP
	 * @return string
	 */
    function getMacAddress(){

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
    ?>