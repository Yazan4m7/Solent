@props(['case', 'stageType' => '3dprinting'])

<div id="YSH-slide-overlay-{{$case->id}}" class="YSH-slide-overlay"
     onclick="YSH_closeSlidePanel({{$case->id}})">
    <div id="YSH-slide-panel-{{$case->id}}" class="YSH-slide-panel">
        <div class="YSH-slide-header">
            <h5>Case Completion</h5>
            <button type="button" class="YSH-close-slide"
                    onclick="YSH_closeSlidePanel({{$case->id}})">&times;
            </button>
        </div>
        <div class="YSH-slide-grid">
            <div class="YSH-slide-body">
                <div class="form-group row" style="margin-bottom: 0px">
                    <div class="form-group col-6" style="margin-bottom: 0px">
                        <label>Doctor:</label>
                        <h5><b>{{$case->client?->name}}</b></h5>
                    </div>
                    <div class="form-group col-6" style="margin-bottom: 0px">
                        <label>Patient:</label>
                        <h5><b>{{$case->patient_name}}</b></h5>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-12">
                        <label><b>Jobs:</b></label><br>
                        @php
                            // Convert stage type to stage number
                            $stageNumber = match($stageType) {
                                'milling' => 2,
                                '3dprinting' => 3,
                                'sintering' => 4,
                                'pressing' => 5,
                                'delivery' => 8,
                                default => 3
                            };
                        @endphp
                        @foreach($case->jobs->where('stage', $stageNumber) as $job)
                            @php
                                $unit = explode(', ',$job->unit_num);
                            @endphp
                            <span>
                        {{$job->unit_num}} - {{$job->jobType->name ?? "No Job Type"}} - {{$job->material->name ?? "no material"}}
                                {{$job->color == '0' ? "" : " - " . $job->color}}
                                {{$job->style == 'None' ? "" : " - " . $job->style}}
                                {{ isset($job->implantR) && $job->jobType->id == 6 ? (" - Implant Type: " . $job->implantR->name) : "" }}
                                {{ isset($job->abutmentR) && $job->jobType->id == 6 ? (" Abutment Type: " . $job->abutmentR->name) : "" }}
                        <br>
                    </span>
                        @endforeach
                    </div>
                </div>

                @if(count($case->notes) > 0)
                    <hr>
                    <label><b>Notes:</b></label><br>
                    @foreach($case->notes as $note)
                        <div class="form-control"
                             style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px">
                                                                    <span
                                                                        class="noteHeader">{{ '[' . substr($note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] :' }}</span><br>
                            <span class="noteText">{{$note->note}}</span>
                        </div>
                    @endforeach
                @endif

            </div>
            <div class="modal-footer fullBtnsWidth">
                <div class="row btnsRow"
                     style=" margin-right: 0px; margin-left: 0px;width:100%">
                    <div class="col-md-6 col-sm-12 padding5px">
                        <a href="{{route('view-case', ['id' => $case->id, 'stage' => 3  ])}}">
                            <button type="button" class="btn btn-info "><i
                                    class="fas fa-eye"></i> View
                            </button>
                        </a>
                    </div>

                    @php
                        $permissions = Cache::get('user'.Auth()->user()->id);
                        $canEditCase = false;
                        if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 102))))
                        $canEditCase = true;
                    @endphp
                    <div class="col-md-6 col-sm-12 padding5px"><a
                            href="{{route('edit-case-view',$case->id)}}">
                            <button type="button"
                                    class="btn btn-warning " {{$canEditCase ? '' : 'disabled'}}>
                                <i class="fas fa-edit"></i> Edit Case
                            </button>
                        </a></div>

                    <div class="col-12 padding5px">
                        <button type="button" class="btn btn-secondary "
                                data-dismiss="modal" style="width:100%">
                            Cancel
                        </button>
                    </div>
                </div>


            </div>

        </div>

    </div>
</div>

