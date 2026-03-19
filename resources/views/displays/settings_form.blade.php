                                        <div class="row gy-3">
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">Ignore Display</label>
                                                                <div class="form-check mb-0">
                                                                    <input class="form-check-input" type="checkbox" id="formCheck-2" @if($row['exclude']==1) checked @endif>
                                                                    <label class="form-check-label fw-semibold" for="formCheck-2">Exclude Display from Testing / Calibration</label></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">Calibration Upload</label>
                                                                
                                                                <div class="form-check mb-0">
                                                                    <input class="form-check-input" type="checkbox" id="formCheck-3">
                                                                    <label class="form-check-label fw-semibold" for="formCheck-3">Use graphicboard LUTs only</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">Save Calibration to</label>
                                                                
                                                                <select class="form-select">
                                                                    <option value="12" selected="">Select</option>
                                                                    <option value="13">Danh</option>
                                                                    <option value="14">Danh</option>
                                                                </select>
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">Used Sensor</label>
                                                                <div class="form-check mb-0">
                                                                    <input class="form-check-input" type="checkbox" id="formCheck-4" @if($row['InternalSensor']==true) checked @endif>
                                                                    <label class="form-check-label fw-semibold" for="formCheck-4">Use internal sensor if possible</label></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Display Model</label><input class="form-control" type="text" name="displayModel" value="{{$row['Model']}}" required></div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Display Serial Number</label><input class="form-control" type="text" name="displaySerialNo" value="{{$row['SerialNumber']}}"></div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Display Manufacturer</label><input class="form-control" type="text" name="displayManufacturer" value="{{$row['Manufacturer']}}" ></div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Inventory Number</label>
                                                                <input class="form-control" type="text" name="inventoryNo" value=""></div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Type of Display {{$row['TypeOfDisplay']}}</label>
                                                                <select class="form-select" name="display_type">
                                                                    @php
                                                                    $display_type=json_decode($settings['TypeOfDisplay'], 1);
                                                                    @endphp
                                                                    @foreach($display_type as $value)
                                                                    <option value="{{$value}}" @if($row['TypeOfDisplay']==$value) selected @endif>{{$value}}</option>
                                                                    @endforeach
                                                                </select>
                                                               
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Display Technology {{$row['DisplayTechnology']}}</label>
                                                                
                                                                <select class="form-select" name="display_tech">
                                                                    @php
                                                                    $display_tech=json_decode($settings['DisplayTechnology'], 1);
                                                                    @endphp
                                                                    @foreach($display_tech as $value)
                                                                    <option value="{{$value}}" @if($row['DisplayTechnology']==$value) selected @endif >{{$value}}</option>
                                                                    @endforeach
                                                                </select>
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Screen Size</label>
                                                                <input class="form-control" type="text" name="screenSize" value="{{$row['ScreenSize']}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Revolution (h/v)</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-text form-control-wrapper form-control-icon-start form-control-icon-end position-relative p-0">
                                                                        <input class="form-control" type="text" autocomplete="off" name="revolutionH" value="{{$row['ResolutionHorizontal']}}" >
                                                                        <span class="position-absolute position-absolute-end top-50 translate-middle-y">px</span></div><span class="bg-transparent input-group-text">X</span>
                                                                    <div class="input-group-text form-control-wrapper form-control-icon-start form-control-icon-end position-relative p-0">
                                                                        <input class="form-control" type="text" autocomplete="off" name="revolutionV" value="{{$row['ResolutionVertical']}}">
                                                                        <span class="position-absolute position-absolute-end top-50 translate-middle-y">px</span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Backlight Stabilization</label>
                                                               
                                                                <select class="form-select" name="backlightStablization">
                                                                    @php
                                                                    $stablization=json_decode($settings['BacklightStabilization'], 1);
                                                                    @endphp
                                                                    @foreach($stablization as $value)
                                                                    <option value="{{$value}}" @if($row['BacklightStabilization']==$value) selected @endif>{{$value}}</option>
                                                                    @endforeach
                                                                </select>
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div>
                                                                @php
                                                                $row['InstalationDate'] = str_replace('.', '-', $row['InstalationDate']);
                                                                @endphp
                                                                <label class="form-label fw-semibold">Installation Date</label>
                                                                <input class="form-control" type="date" name="instalationDate" value="{{$row['InstalationDate']}}">
                                                            </div>
                                                        </div>
                                                    
                                                    <div class="col-12 col-lg-6 mt-3">
                                                        <button class="btn btn-info rounded-pill btn-sm mb-3">Save Changes</button>
                                                    </div>
</div>