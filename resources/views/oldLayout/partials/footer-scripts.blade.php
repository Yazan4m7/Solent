<!-- Bootstrap core JavaScript
================================================= -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{{asset('https://code.jquery.com/jquery-3.6.0.js')}}" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="{{asset('https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js')}}" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="{{asset('https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js')}}" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<script src="{{asset('https://code.jquery.com/ui/1.12.1/jquery-ui.js')}}" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="{{asset('assets/js/moment-with-locales.min.js')}}"></script>
<script src="{{asset('assets/js/fontawesome-iconpicker.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.js')}}"></script>
<script src="https://kit.fontawesome.com/b0187a4476.js" crossorigin="anonymous"></script>
<script src="{{asset('assets/js/modernizr.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('assets/js/slidebars.min.js')}}"></script>
<script src="{{asset('assets/js/chart.js')}}"></script>
<script src="{{asset('assets/js/sweetalert2.min.js')}}"></script>
<script>

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    let datePickersInPage =  $('.SDTP');
    $(document).ready(function() {
        $('.body-content').click(function(e) {
            var body = jQuery('body');
            var bodyposition = body.css('position');

            if(bodyposition != 'relative') {

            } else {
                if(body.hasClass('sidebar-open'))
                    body.removeClass('sidebar-open');
            }
        });
        if(datePickersInPage.length < 0)
            setDateTimePickersInPage();
        if(datePickersInPage.length != 0) {
            $.datetimepicker.setDateFormatter('moment');
            $.datetimepicker.setDateFormatter({
                parseDate: function (date, format) {
                    var d = moment(date, format);
                    return d.isValid() ? d.toDate() : false;
                },

                formatDate: function (date, format) {
                    return moment(date).format(format);
                },

                //Optional if using mask input
                formatMask: function (format) {
                    return format
                        .replace(/Y{4}/g, '9999')
                        .replace(/Y{2}/g, '99')
                        .replace(/M{2}/g, '19')
                        .replace(/D{2}/g, '39')
                        .replace(/H{2}/g, '29')
                        .replace(/m{2}/g, '59')
                        .replace(/s{2}/g, '59');
                }
            });
            datePickersInPage.each(function () {
                let loadedDate = new Date($(this).val());
                let formattedForPicker = moment(loadedDate).format('DD MMM, YYYY hh:mm a');
                $(this).val(formattedForPicker);
            });
            datePickersInPage.each(function () {
                $(this).datetimepicker({
                    format: 'DD MMM, YYYY hh:mm a',
                    formatTime: 'hh:mm a',
                    formatDate: 'DD MMM, YYYY',
                    step: 30
                });
            });
            $('form').submit(formatDateForSubmittion);
        }

        $(".clearOnAll").on("changed.bs.select", function(e, clickedIndex, isSelected, oldValue) {
            if (clickedIndex == null && isSelected == null) {
                var selectedItems = ($(this).selectpicker('val') || []).length;
                var allItems = $(this).find('option:not([disabled])').length;
                if (selectedItems == allItems) {
                    console.log('seleted all');
                } else {
                    console.log('deseleted all');
                }
            } else {
                var selectedD = $(this).find('option').eq(clickedIndex).text();
                //console.log('selectedD: ' + selectedD +  ' oldValue: ' + oldValue);
                if (selectedD =="All"){
                    $(this).val('all');
                    $(this).selectpicker('refresh');

                }else{
                    $(this).children("option[value='all']").prop("selected", false);
                    $(this).selectpicker('refresh');
                }

            }
        });

//        $(".dateTimePickerS").on("change", function() {
//            this.setAttribute(
//                "data-date",
//                moment(this.value, "YYYY-MM-DD")
//                    .format( this.getAttribute("data-date-format") )
//            )
//        }).trigger("change")
        $('.reOverlay').hover(
            function(){
                // When hover the #slide_img img hide the div.shadow
                $('.reOverlay').hide();
            },function(){
                // When out of hover the #slide_img img show the div.shadow
                $('.reOverlay').show();
            }
        );
    } );

    function setDateTimePickersInPage(){
        datePickersInPage =  $('.SDTP');
    }

    function formatDateForSubmittion(){
        if(datePickersInPage.length < 0)
            setDateTimePickersInPage();
        datePickersInPage.each(function() {
            var d = $(this).datetimepicker('getValue');
            var dateFormattedForDB = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate() + ' ' + d.getHours() + ":" + d.getMinutes();
            console.log("Date value: " + d + ' formatted : ' + dateFormattedForDB);
            $(this).val(dateFormattedForDB);
        });
    }

    $('.globalTable').DataTable({
        "pageLength": 25,
        "searching": false,
        "lengthChange": false,
        "columnDefs": [
            { targets: [0], visible: false},
        ],
    });


    {{--function PrintLabel({{$jobs}})--}}
    {{--{--}}

        {{--//height=192,width=288--}}
        {{--var mywindow = window.open('', 'PRINT', 'height=600,width=800');--}}
        {{--mywindow.document.write(`--}}

    {{--<style>--}}
    {{--@media all{--}}
      {{--#kt-invoice__head {}--}}
      {{--.kt-invoice__item {display:none;}--}}

                {{--}--}}

        {{--body {--}}
            {{--font-family: Arial;--}}
            {{--font-weight : bold;--}}
        {{--}--}}


        {{--.tablesHeaders{--}}
            {{--font-size:12px;--}}
            {{--color:black;--}}

        {{--}--}}


        {{--.tableContent{--}}
            {{--font-size:13px;--}}
            {{--color:black;--}}

        {{--}--}}

        {{--hr.solid {--}}
            {{--border-top: 1px solid #bbb;--}}
            {{--width:95%;--}}
        {{--}--}}

        {{--.headerTitle{--}}
            {{--color:black;--}}
        {{--}--}}

        {{--#tableTail{--}}
            {{--padding-left:1px;--}}
            {{--padding-right:2px;--}}
            {{--width:100%;--}}
            {{--position: absolute;--}}
            {{--bottom: 3px;--}}
        {{--}--}}
        {{--.jobcolor--}}
        {{--{--}}
            {{--padding-left:0px;--}}
            {{--padding-right:5px;--}}
        {{--}--}}
        {{--.paddingLeft--}}
        {{--{--}}
            {{--padding-left:2px;--}}

        {{--}--}}
        {{--</style>--}}
        {{--</head>--}}
        {{--<body>--}}

        {{--<div id="kt-invoice__head" style="height:50px; overflow: hidden; position: relative;padding:0px;">--}}


            {{--<div style="float:right;text-align:right; padding-right:4px;padding-top:2px;width:35%">--}}
            {{--<p style="font-size: 9px;font-weight:bold;color:black;text-align:right;margin:0px">{{str_replace('T',' ',now())}}</p>--}}
            {{--<p style="font-size: 8px;font-weight:bold;color:black;text-align:right;margin:0px">{{$case->case_id}}</p>--}}
            {{--<div style="padding-top:5px;">--}}
                {{--@if($isRemake)--}}
                {{--<text style="border:1px; border-style:solid;padding:1px;">RM</text>--}}
                {{--@endif--}}
                {{--@if($isRedo)--}}
                {{--<text style="border:1px; border-style:solid;padding:1px;">RD</text>--}}
                {{--@endif--}}

            {{--</div>--}}

            {{--</div>--}}


            {{--<div id="headerInfo" style="width:60%;color:black;float:left;">--}}

            {{--<table >--}}
            {{--<tr style="color:black;font-weight:bold;">--}}
            {{--<th style="width:20%;text-align:left;font-size:12px;"><b>Dr:</b></th>--}}
        {{--<th style="width:80%;font-size:12px;text-align:left;"><b>{{$case->client->name}}</b> </th>--}}
            {{--</tr>--}}
            {{--<tr style="color:black;font-weight:bold;">--}}
            {{--<th style="width:30%;font-size:12px;text-align:left;">Patient:</th>--}}
        {{--<th style="width:70%;text-align:left;font-size:12px;">{{$case->patient_name}}</th>--}}
            {{--</tr>--}}
            {{--</table>--}}

            {{--</div>--}}



            {{--</div>--}}

            {{--<div id="jobs" style="width:100%;display: flex;  ">--}}
            {{--<table class="table"  style="width: 100%;height: 100%;border-spacing: 1px 1px; margin: 0 auto;align-self: center;padding-top:5px;margin:0;padding-right:2px;">--}}
            {{--<thead>--}}
            {{--<tr>--}}
            {{--<th class="tablesHeaders" style="text-align:left" width="200"> Job Type</th>--}}
        {{--<th class="tablesHeaders" style="text-align:left" width="80;padding-left:0px">Material</th>--}}
            {{--<th class="tablesHeaders jobcolor" style="text-align:left;padding-left:0px" width="40">Color</th>--}}
            {{--<th class="tablesHeaders" style="text-align:left;" width="20">Qty</th>--}}

            {{--</tr>--}}

            {{--</thead>--}}

            {{--<tbody>--}}

                {{--@foreach($jobs as $job)--}}
            {{--<tr style="text-align:center">--}}

            {{--</tr>--}}
                {{--@endforeach--}}


            {{--</tbody>--}}
            {{--</table>--}}

            {{--<div id="tableTail">--}}


            {{--</div>--}}


            {{--</div>--}}

            {{--</body></html>--}}
            {{--`);--}}
        {{--mywindow.document.close(); // necessary for IE >= 10--}}
        {{--mywindow.focus(); // necessary for IE >= 10*/--}}
        {{--setTimeout(function(){ mywindow.print(); /*mywindow.close();*/},1000);--}}

        {{--return true;--}}
    {{--}--}}
</script>

