<div class="row gy-3">
                                                       
                                                        <div class="col-12 col-lg-6">
                                                            <div>
                                                                <label class="form-label fw-semibold">Date Of Purchase / Lease:</label>
                                                                @php
                                                                $purchase_date = str_replace('.', '-',  $displays->purchase_date);
                                                                @endphp
                                                                <input class="form-control" type="date" value="{{$purchase_date}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Initial Value: </label>
                                                                <input class="form-control" type="text" value="{{$displays->initial_value}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Expected value at warranty period end: </label>
                                                                <input class="form-control" type="text" value="{{$displays->expected_value}}"></div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Annual straight line depreciation: </label>
                                                                <input class="form-control" type="text" value="{{$displays->annual_straight_line}}"></div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Monthly straight line depreciation: </label>
                                                                <input class="form-control" type="text" value="{{$displays->monthly_straight_line}}" placeholder=""></div>
                                                        </div>
                                                         <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Current value:</label>
                                                                <input class="form-control" type="text" value="{{$displays->current_value}}" placeholder=""></div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div><label class="form-label fw-semibold">Expected replacement date:</label>
                                                                <input class="form-control" type="date" value="{{$displays->expected_replacement_date}}"></div>
                                                        </div>
                                                        <div class="col-12 col-lg-12 mt-3">
                                                        <button class="btn btn-info rounded-pill btn-sm mb-3">Save Changes</button>
                                                        </div>
                                                    </div>