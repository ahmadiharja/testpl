<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationSettingsController extends Controller
{
    private function optionPayloadHasMeaningfulValues($value)
    {
        if ($value === null || $value === '') {
            return false;
        }

        $decoded = $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return trim($value) !== '';
            }
        }

        if (is_array($decoded)) {
            foreach ($decoded as $key => $item) {
                if ((string) $key !== '' || (string) $item !== '') {
                    return true;
                }
            }

            return false;
        }

        return (string) $decoded !== '';
    }

    private function mergeOptionPayloads($existing, $incoming)
    {
        if (!$this->optionPayloadHasMeaningfulValues($existing)) {
            return $incoming;
        }

        if (!$this->optionPayloadHasMeaningfulValues($incoming)) {
            return $existing;
        }

        if (!is_string($existing) || !is_string($incoming)) {
            return $existing;
        }

        $existingDecoded = json_decode($existing, true);
        $incomingDecoded = json_decode($incoming, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($existingDecoded) || !is_array($incomingDecoded)) {
            return $existing;
        }

        $merged = $existingDecoded;
        foreach ($incomingDecoded as $key => $value) {
            if (!array_key_exists($key, $merged) || $merged[$key] === '' || $merged[$key] === null) {
                $merged[$key] = $value;
            }
        }

        return json_encode($merged, JSON_UNESCAPED_UNICODE);
    }

    protected function managedWorkstationIdsForRequest(Request $request, $id)
    {
        $role = $request->session()->get('role');
        $userId = $request->session()->get('id');
        $user = \App\Models\User::find($userId);
        $ids = $this->findWs($id);

        if ($role === 'super') {
            return collect($ids)->map(fn ($item) => (int) $item)->values();
        }

        if (!$user || $role === 'user') {
            abort(403);
        }

        $workstations = \App\Models\Workstation::with('workgroup.facility')
            ->whereIn('id', $ids)
            ->get();

        if ($workstations->count() !== count($ids)) {
            abort(404);
        }

        foreach ($workstations as $workstation) {
            $facilityId = optional(optional($workstation->workgroup)->facility)->id;
            if ((int) $facilityId !== (int) $user->facility_id) {
                abort(404);
            }
        }

        return $workstations->pluck('id')->map(fn ($item) => (int) $item)->values();
    }

    public $color_convert = [
        '0' => '5001',
        '1' => '5501',
        '2' => '6502',
        '3' => '7504',
        '4' => '9297',            
    ];
    
    function cdTofL($cd) {
        return round($cd/3.4262591, 2);
    }
    
    function fLtocd($fL) {
        return round($fL*3.4262591, 2);
    }

    public $fields = array('Language',
                'units',
                'LumUnits',
                'AmbientLight',
                'AmbientLight_u',
                'AmbientStable',
                'PutDisplaysToEnergySaveMode',
                'StartEnergySaveMode',
                'EndEnergySaveMode',
                'CalibrationPresents',
                'CalibrationType',
                'Gamma',
                'ColorTemperature',
                'AdjustColorTemperature',
                'ColorY',
                'ColorX',
                'ColorTemperatureAdjustment',
                'ColorTemperatureAdjustment_ext',
                'WhiteLevel_u_extcombo',
                'WhiteLevel',
                'WhiteLevel_u_input',
                'SetWhiteLevel',
                'BlackLevel_u_extcombo',
                'BlackLevel',
                'SetBlackLevel',
                'BlackLevel_u_input',
                'gamut_name',
                'CreateICCICMProfile',
                'UsedClassificationForLastScheduling',
                'UsedRegulation',
                'UsedRegulationForLastScheduling',
                'UsedClassification',
                'bodyRegion',
                'AutoDailyTests',
                'Facility',
                'Department',
                'Room',
                'ResponsiblePersonName',
                'ResponsiblePersonCity',
                'ResponsiblePersonAddress',
                'ResponsiblePersonEmail',
                'ResponsiblePersonPhoneNumber'
        );

    public function app_settings(Request $request, $strId)
    {
        $user_id=$request->session()->get('id');
        $user=\App\Models\User::find($user_id);

        list($type, $id) = explode('-', $strId, 2);
        $listWs = [];
        // get list of ws
        if ($type == 'fa') {
            $fc = \App\Models\Facility::find($id);  
            $listWs = $fc->workstations;
        } else if ($type == 'wg') {
            $wg = \App\Models\Workgroup::find($id);  
            $listWs = $wg->workstations;
        } else if ($type == 'ws') {
            $listWs = \App\Models\Workstation::where('id', $id)->get();
        } else if ($type == 'list') {
            $ids = collect(explode(',', $id))
                ->filter()
                ->map(fn ($item) => (int) $item)
                ->values();
            $listWs = \App\Models\Workstation::whereIn('id', $ids)->get();
        }
        $allData = [];
        $allOptions = [];
        $allSnapshots = [];
        foreach($listWs as $ws) {   
            $data = $ws->preferences->pluck('value', 'name')->toArray();
            $options = $ws->settings_names->pluck('setting_value', 'setting_name')->toArray();
            $data['workgroup_id'] = $ws->workgroup_id;
            $data['name'] = $ws->name;
            $allSnapshots[] = $data;

            $facility_id = $request->session()->get('role') === 'super'
                ? optional($ws->workgroup)->facility_id
                : $user->facility_id;
            $workgroups = \App\Models\Workgroup::when($facility_id, function($q) use ($facility_id) {
                return $q->where('facility_id','=',$facility_id);
            })->pluck('name','id')->toArray();

            $lumUnit = $ws->preference('LumUnits');

            // for BlackLevel_u_extcombo
            $this->converPrefToOptions($options, $ws, 'BlackLevel_u_extcombo', $lumUnit);
            $this->converPrefToOptions($options, $ws, 'WhiteLevel_u_extcombo', $lumUnit);
            $this->converPrefToOptions($options, $ws, 'ColorTemperatureAdjustment_extcombo', 'K');

            $options['workgroup_id'] = json_encode($workgroups);

            if (count($allData) == 0) {
                $allData = $data;
                $allOptions = $options;
            } else {
                $allData = array_intersect($allData, $data);
                foreach ($options as $optionKey => $optionValue) {
                    $allOptions[$optionKey] = $this->mergeOptionPayloads(
                        $allOptions[$optionKey] ?? null,
                        $optionValue
                    );
                }
            }
            
        }

        $mixedFields = [];
        if (count($allSnapshots) > 1) {
            $fieldPool = [];
            foreach ($allSnapshots as $snapshot) {
                $fieldPool = array_unique(array_merge($fieldPool, array_keys($snapshot)));
            }

            foreach ($fieldPool as $field) {
                $values = array_map(function($snapshot) use ($field) {
                    return array_key_exists($field, $snapshot) ? (string) $snapshot[$field] : '__missing__';
                }, $allSnapshots);

                if (count(array_unique($values)) > 1) {
                    $mixedFields[] = $field;
                }
            }
        }

        $scopeTypeMap = [
            'fa' => 'facility',
            'wg' => 'workgroup',
            'ws' => 'workstation',
            'list' => 'workstation-list',
        ];

        return response()->json(
        [
            'data' => $allData,
            'options' => $allOptions,
            'workstations' => $listWs,
            'meta' => [
                'scope' => $strId,
                'scopeType' => $scopeTypeMap[$type] ?? 'unknown',
                'workstationCount' => count($listWs),
                'singleWorkstation' => count($listWs) === 1,
                'mixedFields' => $mixedFields,
            ],
        ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function findWs($id)
    {
        if (Str::startsWith($id, 'fa-')){
            
            $id = str_replace('fa-','',$id);
            $f = \App\Models\Facility::find($id);
            $w = $f->workstations;
           
        }
        else if (Str::startsWith($id, 'wg-')){
            
            $id = str_replace('wg-','',$id);
            $wg = \App\Models\Workgroup::find($id);
            $w = $wg->workstations;
           
        }
        else if (Str::startsWith($id, 'list-')) {
            $id = str_replace('list-','',$id);
            return collect(explode(',', $id))
                ->filter()
                ->map(fn ($item) => (int) $item)
                ->values();
        }
        else {
             
            $id = str_replace('ws-','',$id);
            // $w = Workstation::find($id);
            $ids = [];
            
            array_push($ids,$id);
            
            return $ids;
        }
        return $w->pluck('id');
    }

    public function getCategories(Request $request)
    {
        $id = $request->get('id');
        $type = "";
        $regulation = $request->get('regulation');

        if (Str::startsWith($id, 'fa-')){
            
            $id = str_replace('fa-','',$id);
            $f = \App\Models\Facility::find($id);
            $w = $f->workstations;
            $setting = $w->first()->settings_names()->where('setting_name', $regulation)->first();
        }
        else if (Str::startsWith($id, 'wg-')){
            
            $id = str_replace('wg-','',$id);
            $wg = \App\Models\Workgroup::find($id);
            $w = $wg->workstations;
            
            $setting = $w->first()->settings_names()->where('setting_name', $regulation)->first();
        }
        else if (Str::startsWith($id, 'list-')) {
            $id = str_replace('list-','',$id);
            $ids = collect(explode(',', $id))
                ->filter()
                ->map(fn ($item) => (int) $item)
                ->values();
            $w = \App\Models\Workstation::whereIn('id', $ids)->get();
            $setting = $w->first()?->settings_names()->where('setting_name', $regulation)->first();
        }
        else {
            $id = str_replace('ws-','',$id);
            $w = \App\Models\Workstation::find($id);
            
            $setting = $w->settings_names()->where('setting_name', $regulation)->first();
        }
        $res = array();
        if ($setting) {
            $data = json_decode($setting->setting_value,true);
            foreach ($data as $k=>$v) {
                $res[] = array('key'=>$k, 'value'=>$v);
            }
        }
        return $res;  
    }

    public function saveapp(Request $request, $id){
        // $id = str_replace('ws-','',$id);
        $ids = $this->managedWorkstationIdsForRequest($request, $id);
        foreach($ids as $id){
            $ws = \App\Models\Workstation::find($id);
            if ($ws) {
                if ($request->filled('name')) {
                    $ws->name = $request->input('name');
                    $ws->save();
                }
                $ambient = $request->input('AmbientLight');
                if ($request->input('LumUnits') == 'fL') {
                    // 1 foot-lambert [fL] = 3,4262590996323 candela/meter² [cd/m²]
                    $ambient = $this->fLtocd($ambient);
                }
                if ($request->input('Language')) {
                    $ws->updatePreference('Language', $request->input('Language'));
                }
                if ($request->input('LumUnits')) {
                    $ws->updatePreference('LumUnits', $request->input('LumUnits'));
                }
                if ($request->input('units')) {
                    $ws->updatePreference('units', $request->input('units'));
                }
                if ($request->input('AmbientLight')) {
                    $ws->updatePreference('AmbientLight', $ambient);
                }
                if ($request->input('AmbientStable')) {
                    $ws->updatePreference('AmbientStable', $request->input('AmbientStable'));
                }
                if ($request->input('StartEnergySaveMode')) {
                    $ws->updatePreference('StartEnergySaveMode', $request->input('StartEnergySaveMode'));
                }
                
                if ($request->input('EndEnergySaveMode')) {
                    $ws->updatePreference('EndEnergySaveMode', $request->input('EndEnergySaveMode'));
                }
                
                if ($request->has('PutDisplaysToEnergySaveMode')) {
                    $ws->updatePreference('PutDisplaysToEnergySaveMode', $request->boolean('PutDisplaysToEnergySaveMode') ? 'true' : 'false');
                }
                
                if ($request->session()->get('role') !== 'user' && $request->input('workgroup_id') && $ws->workgroup_id != $request->input('workgroup_id')) {
                    $ws->workgroup_id = $request->input('workgroup_id');
                    $ws->save();
                }
            }
           
        }
      
        return 'OK';
    }
    public function savelocation(Request $request, $id){
        // $id = str_replace('ws-','',$id);
        $ids = $this->managedWorkstationIdsForRequest($request, $id);
        foreach($ids as $id){
            $ws = \App\Models\Workstation::find($id);
            if ($ws) {
                $ws->updatePreference('Facility', $request->input('Facility'));
                $ws->updatePreference('Department', $request->input('Department'));
                $ws->updatePreference('Room', $request->input('Room'));
                $ws->updatePreference('ResponsiblePersonName', $request->input('ResponsiblePersonName'));
                $ws->updatePreference('ResponsiblePersonCity', $request->input('ResponsiblePersonCity'));
                $ws->updatePreference('ResponsiblePersonAddress', $request->input('ResponsiblePersonAddress'));
                $ws->updatePreference('ResponsiblePersonEmail', $request->input('ResponsiblePersonEmail'));
                $ws->updatePreference('ResponsiblePersonPhoneNumber', $request->input('ResponsiblePersonPhoneNumber'));
            }
           
        }
      
        return 'OK';
    }

    public $x = 0.0;
    public $y = 0.0;


    public function Ttoxy($t){
        if ($t < 4000 || $t > 25000){
            $this->x = 0;
            $this->y = 0;
            return ;
        }
        
        if ($t <= 7000){
            $this->x = -4.6070e9/($t*$t*$t) + 2.9678e6/($t*$t) + 99.11/$t + 0.244063;   
        }
        else {
            $this->x = -2.0064e9/($t*$t*$t) + 1.9018e6/($t*$t) + 247.48/$t + 0.237040;
        }

        $this->y = -3*$this->x*$this->x + 2.87*$this->x - 0.275;
    }
    public function savedc(Request $request, $id){
        $ids = $this->managedWorkstationIdsForRequest($request, $id);
        foreach($ids as $id){
            $ws = \App\Models\Workstation::find($id);
            if ($ws) {
               
                $whiteLevel = $request->input('WhiteLevel');
                $blackLevel = $request->input('BlackLevel');
                $colorTemp = $request->input('ColorTemperatureAdjustment');
                if ($colorTemp == '20') {
                    $colorTemp = $request->input('ColorTemperatureAdjustment_ext');
                    $ws->addToListPreference('ColorTemperatureAdjustment_extcombo', $colorTemp);
                } 
                $ws->updatePreference('CalibrationType', $request->input('CalibrationType'));
                $ws->updatePreference('Gamma', $request->input('Gamma'));
                $ws->updatePreference('gamut_name', $request->input('gamut_name'));
                $ws->updatePreference('CreateICCICMProfile', $request->input('CreateICCICMProfile')?'true':'false');
                $ws->updatePreference('CalibrationPresents', $request->input('CalibrationPresents'));
                $ws->updatePreference('SetBlackLevel', $request->input('SetBlackLevel'));                
                $ws->updatePreference('SetWhiteLevel', $request->input('SetWhiteLevel'));                
                $ws->updatePreference('AdjustColorTemperature', $colorTemp == "native"?"false":"true");    
               
                if ($colorTemp != 'native') {
                    $ws->updatePreference('ColorTemperatureAdjustment',  $colorTemp);    
                }
                $ws->updatePreference('WhiteLevel', $whiteLevel);     
                $ws->updatePreference('BlackLevel', $blackLevel);     

                if ($whiteLevel) {
                    $ws->addToListPreference('WhiteLevel_u_extcombo', $whiteLevel);
                }
                if ($blackLevel) {
                    $ws->addToListPreference('BlackLevel_u_extcombo', $blackLevel);
                }
                
               
            }
            
        }
        
        return 'OK';
    }

    public function saveqa(Request $request, $id){
    // $id = str_replace('ws-','',$id);
        $ids = $this->managedWorkstationIdsForRequest($request, $id);
        foreach($ids as $id){
            $ws = \App\Models\Workstation::find($id);
            if ($ws) {
                $ws->updatePreference('UsedClassificationForLastScheduling', $request->input('UsedClassificationForLastScheduling'));
                $ws->updatePreference('UsedRegulationForLastScheduling', $request->input('UsedRegulationForLastScheduling'));
                $changed = $ws->updatePreference('UsedRegulation', $request->input('UsedRegulation'));
                // If regulation changed, then force update classification
                $ws->updatePreference('UsedClassification', $request->input('UsedClassification'), $changed);
                $ws->updatePreference('bodyRegion', $request->input('bodyRegion'));
                $ws->updatePreference('AutoDailyTests', $request->input('AutoDailyTests')?true:false);
            }
            
        }
        
        return 'OK';
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update json
     */
    public function updatejson(Request $request,$settingname){

        return 1;
    }

    public function convertKey($c)
    {
        $color_convert = [
            '0' => '5001',
            '1' => '5501',
            '2' => '6502',
            '3' => '7504',
            '4' => '9297',            
        ];

        foreach($color_convert as $key => $value){
            if ($c == $key)
                $c = $value;
        }
        return $c;
    }

    private function converPrefToOptions(&$options, $ws, $name, $unit)
    {
        $combo = $ws->preference($name);
        if ($combo == '') return;

        $array = explode('|',$combo);
        $newArray = [];
        foreach ($array as $item) {
            $newArray[(string)$item] = $unit=='fL'
                                            ?$this->cdtofL($item):
                                            (
                                                $unit=='K'?$item.$unit:$item
                                            );
        }
        $newArray['custom'] = 'custom';  
        $options[$name] = json_encode($newArray,JSON_UNESCAPED_UNICODE);
    }
}
