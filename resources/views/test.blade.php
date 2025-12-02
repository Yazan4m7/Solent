

<html lang="en">
<head>
    <title>LightGallery</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/lightgallery/1.3.9/css/lightgallery.min.css" rel="stylesheet">
    <style>body{background-color:#152836}h2{color:#fff;margin-bottom:40px;text-align:center;font-weight:100;}</style>
    <script src="{{asset('https://code.jquery.com/jquery-3.6.0.js')}}" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset('https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css')}}" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l')" crossorigin="anonymous">

        <!------ Include the above in your HEAD tag ---------->

</head>

<style>
    body{
        background-color: #152836
    }
    h2{color:#fff;margin-bottom:40px;text-align:center;font-weight:100;}
    .demo-gallery > ul {
        margin-bottom: 0;
    }
    .demo-gallery > ul > li {
        float: left;
        margin-bottom: 15px;
        margin-right: 20px;
        width: 200px;
    }
    .demo-gallery > ul > li a {
        border: 3px solid #FFF;
        border-radius: 3px;
        display: block;
        overflow: hidden;
        position: relative;
        float: left;
    }
    .demo-gallery > ul > li a > img {
        -webkit-transition: -webkit-transform 0.15s ease 0s;
        -moz-transition: -moz-transform 0.15s ease 0s;
        -o-transition: -o-transform 0.15s ease 0s;
        transition: transform 0.15s ease 0s;
        -webkit-transform: scale3d(1, 1, 1);
        transform: scale3d(1, 1, 1);
        height: 100%;
        width: 100%;
    }
    .demo-gallery > ul > li a:hover > img {
        -webkit-transform: scale3d(1.1, 1.1, 1.1);
        transform: scale3d(1.1, 1.1, 1.1);
    }
    .demo-gallery > ul > li a:hover .demo-gallery-poster > img {
        opacity: 1;
    }
    .demo-gallery > ul > li a .demo-gallery-poster {
        background-color: rgba(0, 0, 0, 0.1);
        bottom: 0;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        -webkit-transition: background-color 0.15s ease 0s;
        -o-transition: background-color 0.15s ease 0s;
        transition: background-color 0.15s ease 0s;
    }
    .demo-gallery > ul > li a .demo-gallery-poster > img {
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        opacity: 0;
        position: absolute;
        top: 50%;
        -webkit-transition: opacity 0.3s ease 0s;
        -o-transition: opacity 0.3s ease 0s;
        transition: opacity 0.3s ease 0s;
    }
    .demo-gallery > ul > li a:hover .demo-gallery-poster {
        background-color: rgba(0, 0, 0, 0.5);
    }
    .demo-gallery .justified-gallery > a > img {
        -webkit-transition: -webkit-transform 0.15s ease 0s;
        -moz-transition: -moz-transform 0.15s ease 0s;
        -o-transition: -o-transform 0.15s ease 0s;
        transition: transform 0.15s ease 0s;
        -webkit-transform: scale3d(1, 1, 1);
        transform: scale3d(1, 1, 1);
        height: 100%;
        width: 100%;
    }
    .demo-gallery .justified-gallery > a:hover > img {
        -webkit-transform: scale3d(1.1, 1.1, 1.1);
        transform: scale3d(1.1, 1.1, 1.1);
    }
    .demo-gallery .justified-gallery > a:hover .demo-gallery-poster > img {
        opacity: 1;
    }
    .demo-gallery .justified-gallery > a .demo-gallery-poster {
        background-color: rgba(0, 0, 0, 0.1);
        bottom: 0;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        -webkit-transition: background-color 0.15s ease 0s;
        -o-transition: background-color 0.15s ease 0s;
        transition: background-color 0.15s ease 0s;
    }
    .demo-gallery .justified-gallery > a .demo-gallery-poster > img {
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        opacity: 0;
        position: absolute;
        top: 50%;
        -webkit-transition: opacity 0.3s ease 0s;
        -o-transition: opacity 0.3s ease 0s;
        transition: opacity 0.3s ease 0s;
    }
    .demo-gallery .justified-gallery > a:hover .demo-gallery-poster {
        background-color: rgba(0, 0, 0, 0.5);
    }
    .demo-gallery .video .demo-gallery-poster img {
        height: 48px;
        margin-left: -24px;
        margin-top: -24px;
        opacity: 0.8;
        width: 48px;
    }
    .demo-gallery.dark > ul > li a {
        border: 3px solid #04070a;
    }
    .home .demo-gallery {
        padding-bottom: 80px;
    }
</style>

<body class="home">
<div class="container" style="margin-top:40px;">
    <h2>Jquery LightGallery</h2>
    <div class="demo-gallery">
        <ul id="lightgallery" class="list-unstyled row">
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-sub-html="<h4>Fading Light</h4><p>Classic view from Rigwood Jetty on Coniston Water an old archive shot similar to an old post but a little later on.</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-1.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-2.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-13.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-4.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-sub-html="<h4>Fading Light</h4><p>Classic view from Rigwood Jetty on Coniston Water an old archive shot similar to an old post but a little later on.</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-1.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-2.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-13.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-4.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-sub-html="<h4>Fading Light</h4><p>Classic view from Rigwood Jetty on Coniston Water an old archive shot similar to an old post but a little later on.</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-2.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-1.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-2.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-13.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-sub-html="<h4>Fading Light</h4><p>Classic view from Rigwood Jetty on Coniston Water an old archive shot similar to an old post but a little later on.</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-4.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-1.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-2.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-13.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/4-1600.jpg" data-sub-html="<h4>Fading Light</h4><p>Classic view from Rigwood Jetty on Coniston Water an old archive shot similar to an old post but a little later on.</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-4.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/1-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-1.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/2-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-2.jpg">
                </a>
            </li>
            <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-src="https://sachinchoolur.github.io/lightgallery.js/static/img/13-1600.jpg" data-sub-html="<h4>Bowness Bay</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy I was passing the right place at the right time....</p>">
                <a href="">
                    <img class="img-responsive" src="https://sachinchoolur.github.io/lightgallery.js/static/img/thumb-13.jpg">
                </a>
            </li>
        </ul>
    </div>
</div>
<script src="{{asset('assets/js/lightgallery.js')}}"></script>
<script>

    $(document).ready(function(){
        $('#lightgallery').lightGallery();
    });
</script>
</body>
</html>
