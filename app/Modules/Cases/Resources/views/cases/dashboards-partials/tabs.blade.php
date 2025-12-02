<div role="tablist" aria-label="Fashion Trends" style="margin-left: 1%;">
    <button href="{{$stage['numericStage']}}" role="tab" class="innerActiveBtn innerBtn"
            aria-selected="false" aria-controls="{{'active-'.$key}}"
            id="{{'active-'.$key .'label'}}"
            tabindex="-1" onclick="setInnerTab(this)" data-stageid="{{$key}}">

                                    <span
                                        class="badge bg-info m-1 activeBadge">{{count($stage['activeCases'])}} </span>
        <span class="phaselabel activeTabText"> Active</span>
    </button>
    <button href="{{$stage['numericStage']}}" role="tab" class="innerWaitingBtn innerBtn"
            aria-selected="false" aria-controls="{{'waiting-'.$key}}"
            id="{{'waiting-'.$key .'label'}}"
            tabindex="-1" onclick="setInnerTab(this)" data-stageid="{{$key}}">
                                    <span
                                        class="badge bg-info m-1 waitingBadge">{{count($stage['waitingCases'])}} </span>
        <span
            class="phaselabel waitingtabText"> Waiting</span></button>
</div>
