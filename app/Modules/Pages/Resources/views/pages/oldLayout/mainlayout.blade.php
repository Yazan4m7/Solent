
<html lang="en">
<head>
    @include('layout.partials.head')
    @yield("head")
    @include('layout.partials.header')
    <style>

    </style>
</head>
<body class="sticky-header">

<section>
@include('layout.partials.korvionSidebar')

<!-- body content start-->
    <div class="body-content">
        <!-- header section start-->
        @if (config('site_vars.environment') == 'testing')
        <div style="pointer-events: none;opacity:0.2;z-index:10000;position: fixed;bottom: 30px;right:20px;color:red;font-size: 55px;font-weight: bolder"> {{strtoupper(config('site_vars.environment') . ' ' . 'environment') }}</div>
        @endif
        <div class="container-fluid">
            <div class="page-head" style="text-align: center">
                <h4 class="my-2">
                    @yield('title')
                    </h4>
            </div>
            <div class="rowContainerCustom" style="margin-left: 0px;">
                <div class="row">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            <div class="alert-icon"><i class="flaticon-success"></i></div>
                            <div class="alert-text">{{session("success")}}</div>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            <div class="alert-icon"><i class="flaticon-danger"></i></div>
                            <div class="alert-text">{{session("error")}}</div>
                        </div>
                    @endif
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                  </div>
            @yield('content')
            </div>
            <!--end Right Slidebar-->
            <!-- <footer class="footer">

             </footer>-->
            <!--footer section end-->
        </div><!--end container-->




        <!-- Right Slidebar start -->
        <div class="sb-slidebar sb-right sb-style-overlay">
            <div class="right-bar slimscroll">
                <span class="r-close-btn sb-close"><i class="fa fa-times"></i></span>

                <ul class="nav nav-tabs nav-justified-">
                    <li class="">
                        <a href="#chat" class="active" data-toggle="tab">Chat</a>
                    </li>
                    <li class="">
                        <a href="#activity" data-toggle="tab">Activity</a>
                    </li>
                    <li class="">
                        <a href="#settings" data-toggle="tab">Settings</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="chat">
                        <div class="online-chat">
                            <div class="online-chat-container">
                                <div class="chat-list">
                                    <form class="search-content" action="index.html" method="post">
                                        <input type="text" class="form-control" name="keyword" placeholder="Search...">
                                        <span class="search-button"><i class="ti ti-search"></i></span>
                                    </form>
                                </div>
                                <div class="side-title-alt">
                                    <h2>online</h2>
                                </div>

                                <ul class="team-list chat-list-side">
                                    <li>
                                        <a href="#" class="ml-3">
                                                    <span class="thumb-small">
                                                        <img class="rounded-circle" src="{{asset('assets/images/users/avatar-1.jpg')}}" alt="">
                                                        <i class="online dot"></i>
                                                    </span>
                                            <div class="inline">
                                                <span class="name">Alison Jones</span>
                                                <small class="text-muted">Start exploring</small>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="ml-3">
                                                    <span class="thumb-small">
                                                        <img class="rounded-circle" src="{{asset('assets/images/users/avatar-3.jpg')}}" alt="">
                                                        <i class="online dot"></i>
                                                    </span>
                                            <div class="inline">
                                                <span class="name">Jonathan Smith</span>
                                                <small class="text-muted">Alien Inside</small>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="ml-3">
                                                    <span class="thumb-small">
                                                        <img class="rounded-circle" src="{{asset('assets/images/users/avatar-4.jpg')}}" alt="">
                                                        <i class="away dot"></i>
                                                    </span>
                                            <div class="inline">
                                                <span class="name">Anjelina Doe</span>
                                                <small class="text-muted">Screaming...</small>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="ml-3">
                                                    <span class="thumb-small">
                                                        <img class="rounded-circle" src="{{asset('assets/images/users/avatar-5.jpg')}}" alt="">
                                                        <i class="busy dot"></i>
                                                    </span>
                                            <div class="inline">
                                                <span class="name">Franklin Adam</span>
                                                <small class="text-muted">Don't lose the hope</small>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="ml-3">
                                                    <span class="thumb-small">
                                                        <img class="rounded-circle" src="{{asset('assets/images/users/avatar-6.jpg')}}" alt="">
                                                         <i class="online dot"></i>
                                                    </span>
                                            <div class="inline">
                                                <span class="name">Jeff Crowford </span>
                                                <small class="text-muted">Just flying</small>
                                            </div>
                                        </a>
                                    </li>
                                </ul>

                                <div class="side-title-alt mb-3">
                                    <h2>Friends</h2>
                                </div>
                                <ul class="list-unstyled friends">
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-7.jpg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-8.jpg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-9.jpg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-10.jpg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-2.jpg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-1.jpg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-3.jpg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <img class="rounded-circle" src="{{asset('assets/images/users/avatar-4.jpg')}}" alt="">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane " id="activity">

                        <div class="aside-widget">
                            <div class="side-title-alt">
                                <h2>Recent Activity</h2>
                            </div>
                            <ul class="team-list chat-list-side info">
                                <li>
                                            <span class="thumb-small">
                                                <i class="fa fa-pencil"></i>
                                            </span>
                                    <div class="inline">
                                        <span class="name">Mary Brown open a new company</span>
                                        <span class="time">just now</span>
                                    </div>
                                </li>
                                <li>
                                            <span class="thumb-small">
                                                <i class="fa fa-user-plus"></i>
                                            </span>
                                    <div class="inline">
                                        <span class="name">Mary Brown send a new message </span>
                                        <span class="time">50 min ago</span>
                                    </div>
                                </li>
                                <li>
                                            <span class="thumb-small">
                                                <i class="fa fa-wrench"></i>
                                            </span>
                                    <div class="inline">
                                        <span class="name">Holly Cobb Uploaded 6 new photos.</span>
                                        <span class="time">3 Week Ago</span>
                                    </div>
                                </li>
                                <li>
                                            <span class="thumb-small">
                                                <i class="fa fa-users"></i>
                                            </span>
                                    <div class="inline">
                                        <span class="name">Mary Brown open a new company.</span>
                                        <span class="time">1 Month Ago</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="aside-widget">

                            <div class="side-title-alt">
                                <h2>Events</h2>
                            </div>
                            <ul class="team-list chat-list-side info statistics border-less-list">
                                <li>
                                    <div class="inline">
                                        <p class="mb-1">Jamie Miller</p>
                                        <span class="name">Contrary to popular belief, Lorem Ipsum is not simply random text.</span>
                                        <span class="time text-muted">2 Week Ago</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="inline">
                                        <p class="mb-1">Robert Jones</p>
                                        <span class="name">Lorem Ipsum is simply dummy text of the printing and typesetting .</span>
                                        <span class="time text-muted">1 Month Ago</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane " id="settings">
                        <div class="side-title-alt">
                            <h6 class="mb-0">Account Setting</h6>
                        </div>
                        <ul class="team-list chat-list-side info statistics border-less-list setting-list">
                            <li>
                                <div class="inline">
                                    <span class="name">Auto updates</span>
                                </div>
                                <span class="thumb-small">
                                            <input type="checkbox" checked data-plugin="switchery" data-color="#079c9e" data-size="small"/>
                                        </span>
                            </li>
                            <li>
                                <div class="inline">
                                    <span class="name">Show offline Contacts</span>
                                </div>
                                <span class="thumb-small">
                                            <input type="checkbox" checked data-plugin="switchery" data-color="#079c9e" data-size="small"/>
                                        </span>
                            </li>

                            <li>
                                <div class="inline">
                                    <span class="name">Location Permission</span>
                                </div>
                                <span class="thumb-small">
                                            <input type="checkbox" checked data-plugin="switchery" data-color="#079c9e" data-size="small"/>
                                        </span>
                            </li>
                        </ul>

                        <div class="side-title-alt">
                            <h6 class="mb-0">General Setting</h6>
                        </div>
                        <ul class="team-list chat-list-side info statistics border-less-list setting-list">
                            <li>
                                <div class="inline">
                                    <span class="name">Show me Online</span>
                                </div>
                                <span class="thumb-small">
                                            <input type="checkbox" checked data-plugin="switchery" data-color="#079c9e" data-size="small"/>
                                        </span>
                            </li>
                            <li>
                                <div class="inline">
                                    <span class="name">Status visible to all</span>
                                </div>
                                <span class="thumb-small">
                                            <input type="checkbox" checked data-plugin="switchery" data-color="#079c9e" data-size="small"/>
                                        </span>
                            </li>

                            <li>
                                <div class="inline">
                                    <span class="name">Notifications</span>
                                </div>
                                <span class="thumb-small">
                                            <input type="checkbox" checked data-plugin="switchery" data-color="#079c9e" data-size="small"/>
                                        </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!--end body content-->
    <!--footer section start-->

</section>
</body>
@include('layout.partials.footer-scripts')


@yield('scripts')
</html>
