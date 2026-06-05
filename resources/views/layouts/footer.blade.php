<footer class="footer">
    <script>
        // Show the spinner as soon as the page starts loading
        // window.addEventListener('beforeunload', function() {
        //     document.getElementById('loadingOverlay').style.display = 'flex';  // Show overlay before the page unloads
        // });

        // Show the spinner once the page is loaded
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         hideLoadingOverlay();  // Hide the overlay after 3 seconds
        //     }, 3000);  // Delay for demonstration; adjust as needed
        // });
        //Show the loading overlay when the page loads (on refresh)
        // window.addEventListener('load', function() {
        //     document.getElementById('loadingOverlay').style.display = 'flex';
        // });

        // Hide the loading overlay after a short delay (you can adjust this as needed)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         document.getElementById('loadingOverlay').style.display = 'none';
        //     }, 2000); // 2 seconds delay (adjust as needed)
        // });
    </script>

{{--   --}}
    <!-- Core Libraries -->
    <script src="{{ asset('white') }}/js/core/jquery.min.js"></script>
    <script src="{{ asset('white') }}/js/core/popper.min.js"></script>
    <script src="{{asset('https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js')}}" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>

    <!-- Moment.js (needed by several plugins) -->
    <script src="{{asset('assets/js/moment-with-locales.min.js')}}"></script>

    <!-- jQuery Plugins & Extensions -->
    <script src="{{asset('assets/js/jquery.datetimepicker.full.js')}}"></script>
    <script src="{{asset('assets/js/jquery.imagesloader-1.0.1.js')}}"></script>
    <script src="{{asset('assets/js/jquery.repeater.js')}}" defer></script>
    <script src="{{asset('assets/js/lightgallery.js')}}"></script>

    <!-- Bootstrap Plugins -->
    <script src="{{ asset('white') }}/js/plugins/bootstrap-notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <!-- DataTables Core & Extensions (keep together) -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>

    <!-- Font Awesome (Kit disabled - 403 error, using CDN versions loaded in pages) -->
    {{-- <script src="https://kit.fontawesome.com/b0187a4476.js" crossorigin="anonymous"></script> --}}
    <script src="{{asset('assets/js/fontawesome-iconpicker.js')}}"></script>

    <!-- Third-party Utilities -->
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha512-a9NgEEK7tsCvABL7KqtUTQjl69z7091EVPpw5KxPlZ93T141ffe1woLtbXTX+r2/8TtTvRX/v4zTL2UlMUPgwg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="{{ asset('assets') }}/js/ysh-custom-js/v3scripts.js" defer></script>
    <!-- UI Components & Features -->
    <script src="{{asset('assets/js/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sidebar-scroll.js')}}"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js" integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Theme & Custom Scripts (load last) -->
    <script src="{{ asset('white') }}/js/white-dashboard.min.js?v=1.0.0"></script>
    <script src="{{ asset('white') }}/js/theme.js"></script>
    <script src="{{ asset('assets') }}/js/ysh-custom-js/ysh-fuzzy-tools.js"></script>
    <script>
        $(document).ready(function () {
            $().ready(function () {
                $sidebar = $('.sidebar');
                $navbar = $('.navbar');
                $main_panel = $('.main-panel');

                $full_page = $('.full-page');

                $sidebar_responsive = $('body > .navbar-collapse');
                sidebar_mini_active = true;
                white_color = false;

                window_width = $(window).width();

                fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

                $('.fixed-plugin a').click(function (event) {
                    if ($(this).hasClass('switch-trigger')) {
                        if (event.stopPropagation) {
                            event.stopPropagation();
                        } else if (window.event) {
                            window.event.cancelBubble = true;
                        }
                    }
                });
                console.log('Plugin');

                $('.fixed-plugin .background-color span').click(function () {
                    $(this).siblings().removeClass('active');
                    $(this).addClass('active');

                    var new_color = $(this).data('color');

                    if ($sidebar.length != 0) {
                        $sidebar.attr('data', new_color);
                    }

                    if ($main_panel.length != 0) {
                        $main_panel.attr('data', new_color);
                    }

                    if ($full_page.length != 0) {
                        $full_page.attr('filter-color', new_color);
                    }

                    if ($sidebar_responsive.length != 0) {
                        $sidebar_responsive.attr('data', new_color);
                    }
                });

                $('.switch-sidebar-mini input').on("switchChange.bootstrapSwitch", function () {
                    var $btn = $(this);

                    if (sidebar_mini_active == true) {
                        $('body').removeClass('sidebar-mini');
                        sidebar_mini_active = false;
                        whiteDashboard.showSidebarMessage('Sidebar mini deactivated...');
                    } else {
                        $('body').addClass('sidebar-mini');
                        sidebar_mini_active = true;
                        whiteDashboard.showSidebarMessage('Sidebar mini activated...');
                    }

                    // we simulate the window Resize so the charts will get updated in realtime.
                    var simulateWindowResize = setInterval(function () {
                        window.dispatchEvent(new Event('resize'));
                    }, 180);

                    // we stop the simulation of Window Resize after the animations are completed
                    setTimeout(function () {
                        clearInterval(simulateWindowResize);
                    }, 1000);
                });

                $('.switch-change-color input').on("switchChange.bootstrapSwitch", function () {
                    var $btn = $(this);

                    if (white_color == true) {
                        $('body').addClass('change-background');
                        setTimeout(function () {
                            $('body').removeClass('change-background');
                            $('body').removeClass('white-content');
                        }, 900);
                        white_color = false;
                    } else {
                        $('body').addClass('change-background');
                        setTimeout(function () {
                            $('body').removeClass('change-background');
                            $('body').addClass('white-content');
                        }, 900);

                        white_color = true;
                    }
                });

                $('.light-badge').click(function () {
                    $('body').addClass('white-content');
                });

                $('.dark-badge').click(function () {
                    $('body').removeClass('white-content');
                });
            });
            document.querySelectorAll('td.clickable[data-toggle="modal"][data-target^="#waitingDialogdelivery"]').forEach(el => {

                el.addEventListener('click', function () {
                    const target = this.getAttribute('data-target'); // e.g. "#waitingDialogdelivery175"
                    const match = target.match(/#waitingDialogdelivery(\d+)/);
                    console.log("row clicked, match:", match);
                    if (match) {
                        console.log("setting lastCaseClickedCase:", match);
                        window.lastCaseClickedCase = parseInt(match[1]);
                        console.log("Clicked case ID:", window.lastCaseClickedCase);
                    }
                });
            });

        });
    </script>
    <script>

        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        let datePickersInPage = $('.SDTP');
        $(document).ready(function () {
            $('.body-content').click(function (e) {
                var body = jQuery('body');
                var bodyposition = body.css('position');

                if (bodyposition != 'relative') {


                } else {
                    if (body.hasClass('sidebar-open'))
                        body.removeClass('sidebar-open');

                }
            });
            if (datePickersInPage.length < 0)
                setDateTimePickersInPage();
            if (datePickersInPage.length != 0) {
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
                        step: 30,
                        widgetPositioning: {
                            horizontal: 'right',
                            vertical: 'top'
                        }

                    });
                });
                $('form').submit(formatDateForSubmittion);
            }


            $(".clearOnAll").on("changed.bs.select", function (e, clickedIndex, isSelected, oldValue) {
                    console.log('clear on all');
                    if (clickedIndex == null && isSelected == null) {
                        var selectedItems = ($(this).selectpicker('val') || []).length;
                        var allItems = $(this).find('option:not([disabled])').length;
                        if (selectedItems == allItems) {
                            console.log('selected all');
                        } else {
                            console.log('deselected all');
                        }
                    } else {

                        var selectedD = $(this).find('option').eq(clickedIndex).text().trim();
                        console.log('selectedD');
                        console.log(selectedD);
                        //console.log('selectedD: ' + selectedD +  ' oldValue: ' + oldValue);
                        if (selectedD == "All" || selectedD == "All") {
                            $(this).val('all');
                            $(this).selectpicker('refresh');
                            console.log('inside if all')
                        } else {
                            $(this).children("option[value='all']").prop("selected", false);
                            $(this).selectpicker('refresh');
                        }
                    }
                }
            );


            $('.reOverlay').hover(
                function () {
                    // When hover the #slide_img img hide the div.shadow
                    $('.reOverlay').hide();
                }, function () {
                    // When out of hover the #slide_img img show the div.shadow
                    $('.reOverlay').show();
                }
            );
        });

        function setDateTimePickersInPage() {
            datePickersInPage = $('.SDTP');
        }

        function formatDateForSubmittion() {
            if (datePickersInPage.length < 0)
                setDateTimePickersInPage();
            datePickersInPage.each(function () {
                var d = $(this).datetimepicker('getValue');
                var dateFormattedForDB = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate() + ' ' + d.getHours() + ":" + d.getMinutes();
                console.log("Date value: " + d + ' formatted : ' + dateFormattedForDB);
                $(this).val(dateFormattedForDB);
            });
        }


        let tables = $('.globalTable');
        if (tables) {
            tables.DataTable({
                "pageLength": 25,
                "searching": false,
                "lengthChange": false,

                "columnDefs": [
                    {targets: [0], visible: false}
                ]


            });
            tables.addClass("hover compact  stripe");
        }
        var tables2 = $('.casesFlowTable');
        if (tables2) {
            tables2.DataTable({
                "pageLength": 25,
                "searching": false,
                "lengthChange": false,
                "fixedHeader": true,

            });
            tables2.addClass("nowrap compact  stripe");
        }

        //            var myVar;
        //
        //            function myFunction() {
        //            myVar = setTimeout(showPage, 3000);
        //        }
        //
        //            function showPage() {
        //            document.getElementById("loader").style.display = "none";
        //            document.getElementById("myDiv").style.display = "block";
        //        }


    </script>

    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     // Select all forms on the page
        //     const forms = document.querySelectorAll('form');

        //     // Loop through each form
        //     forms.forEach(function (form) {
        //         // Listen for the submit event on the form
        //         form.addEventListener('submit', function (event) {
        //             // Prevent the default form submission (if you want to handle it with AJAX, etc.)
        //             // event.preventDefault();

        //             // Select all submit buttons within the current form
        //             const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

        //             // Disable all submit buttons
        //             submitButtons.forEach(function (button) {
        //                 button.disabled = true; // Disable the button
        //                 button.innerHTML = `<div class="spinner-grow text-success" role="status"><span class="sr-only">Submitting...</span></div>`; // Optionally change button text
        //             });

        //             // Simulate form submission (e.g., using AJAX)

        //         });
        //     });
        // });
    </script>
    <script>
        const navbarToggle = document.querySelector('.navbar-toggle');
        const overlay = document.getElementById('overlay');
        if (navbarToggle) {
            navbarToggle.addEventListener('click', function () {
                const isOpen = navbarToggle.classList.toggle('toggled');

                document.documentElement.classList.toggle('nav-open', isOpen);

                if (overlay) {
                    overlay.classList.toggle('active', isOpen);
                }
            });
        }


        // // Close overlay and remove "toggled" class when overlay is clicked
        // overlay.addEventListener('click', function () {
        //     console.log("overlay clicked");
        //     // $('.bodyClick').remove();
        //     document.documentElement.classList.remove('nav-open');
        //     navbarToggle.classList.remove('toggled'); // Remove "toggled" class
        //     overlay.classList.remove('active'); // Hide overlay
        // });
    </script>

    <ul class="nav">
        <small id="live-time" style="font-size: 12px;"></small>

    </ul>

    <div class="copyright">
        {{ now()->year }}
        <a target="_blank">©  <b style="color: var(--brand-primary, #c89b3c)"> Korvion </b></a>

    </div>
    <script>
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('live-time').textContent = timeStr;
        }

        updateTime(); // initial
        setInterval(updateTime, 1000); // update every second
    </script>
    <script>
        if (typeof $.fn.dataTable === 'undefined' && typeof jQuery.fn.dataTable !== 'undefined') {
            $.fn.dataTable = jQuery.fn.dataTable;
            $.fn.DataTable = jQuery.fn.DataTable;
        }
    </script>
    {{--/////////////////////////////////////////////////////////////////////--}}
    @stack('js'){{--  //////////////////  JAVASCRIPT STACK ///////////////--}}
    {{--/////////////////////////////////////////////////////////////////////--}}

</footer>
