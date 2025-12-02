
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <!--page style css-->
    <link rel="stylesheet" href="/css/style.css">

  </head>
  <style>
 .checked{
  filter: invert(26%) sepia(73%) saturate(492%) hue-rotate(133deg) brightness(94%) contrast(86%);

}
  </style>
  <body>

  @php
  $startingPosition = 310;
  $imageSize = 50;
  $decrement = 45;
  $teeth = 0;
  $imageSizeL = 43;
  $imageSizeM = 35;
  $leftPadding=70;
  @endphp
    <div class="main-body" style="padding-top: 50px;width:200px;height:500px">



    <img class="teeth" alt="1" src="assets/teethPics/1.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition - 61}}px;left: {{25+  $leftPadding}}px;">
    @php $teeth = 1; @endphp
    <img class="teeth" alt ="2" src="assets/teethPics/2.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition- 100 }}px;left:{{ 29+  $leftPadding}}px;">
    @php $teeth = 2; @endphp
    <img class="teeth" alt ="3" src="assets/teethPics/3.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition- 139 }}px;left:{{ 34+ $leftPadding}}px;">
    @php $teeth = 3; $decrement = $decrement-1.5; @endphp
    <img class="teeth" alt ="4" src="assets/teethPics/4.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition- 172}}px;left:{{ 39+  $leftPadding}}px;">
    @php $teeth = 4; @endphp
    <img class="teeth" alt ="5" src="assets/teethPics/5.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition- 207 }}px;left:{{ 46+  $leftPadding}}px;">
    @php $teeth = 5; @endphp
    <img class="teeth" alt ="6" src="assets/teethPics/6.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition-235 }}px;left:{{ 66+  $leftPadding}}px;">
    @php $teeth = 6; @endphp
    <img class="teeth" alt ="7" src="assets/teethPics/7.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition-260 }}px;left: {{89+  $leftPadding}}px;">
    @php $teeth = 7; @endphp
    <img class="teeth" alt="8" src="assets/teethPics/8.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition - $decrement *$teeth +30 }}px;left: {{125+  $leftPadding}}px;">
    @php $teeth = 8; @endphp
    <img class="teeth" alt ="9" src="assets/teethPics/9.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition- $decrement *$teeth +72}}px;left: {{173+  $leftPadding}}px;">
    @php $teeth = 9; @endphp
    <img class="teeth" alt ="10" src="assets/teethPics/10.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition- 260 }}px;left: {{215+  $leftPadding}}px;">
    @php $teeth = 5; @endphp
    <img class="teeth" alt ="11" src="assets/teethPics/11.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition- 235}}px;left: {{250+  $leftPadding}}px;">
    @php $teeth = 4; @endphp
    <img class="teeth" alt ="12" src="assets/teethPics/12.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition- 200}}px;left: {{260+  $leftPadding}}px;">
    @php $teeth = 3; @endphp
    <img class="teeth" alt ="13" src="assets/teethPics/13.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition- 169 }}px;left: {{265+  $leftPadding}}px;">
    @php $teeth = 2; @endphp
    <img class="teeth" alt ="14" src="assets/teethPics/14.png" height={{$imageSizeL }}px style="  position: absolute; top: {{$startingPosition-138 }}px;left: {{270+  $leftPadding}}px;">
    @php $teeth = 1; @endphp
    <img class="teeth" alt ="15" src="assets/teethPics/15.png" height={{$imageSizeL }}px style="  position: absolute; top: {{$startingPosition- 100 }}px;left: {{270+  $leftPadding}}px;">
    @php $teeth = 0; @endphp
    <img class="teeth" alt ="16" src="assets/teethPics/16.png" height={{$imageSizeL }}px style="  position: absolute; top: {{$startingPosition- 63 }}px;left: {{270+  $leftPadding}}px;">
    @php $teeth = 16; @endphp


    @php
  $startingPosition = 330;
  $imageSize = 50;
  $decrement = 45;
  $teeth = 0;
  $imageSizeL = 43;
  $imageSizeM = 35;
  $leftPadding=70;
  @endphp
    <div class="main-body" style="padding-top: 50px;width:200px;height:500px">
    <h2 style="padding-left:300%" id="teethSelectedH2"></h2>



    <img class="teeth" alt="17" src="assets/teethPics/17.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition + 0 }}px;left: {{272+  $leftPadding}}px;">
    @php $teeth = 1; @endphp
    <img class="teeth" alt ="18" src="assets/teethPics/18.png" height={{$imageSizeL+2}}px style="  position: absolute; top: {{$startingPosition+ 41 }}px;left:{{ 272+  $leftPadding}}px;">
    @php $teeth = 2; @endphp
    <img class="teeth" alt ="19" src="assets/teethPics/19.png" height={{$imageSizeL+2}}px style="  position: absolute; top: {{$startingPosition+ 80 }}px;left:{{ 268+ $leftPadding}}px;">
    @php $teeth = 3; $decrement = $decrement-1.5; @endphp
    <img class="teeth" alt ="20" src="assets/teethPics/20.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 120 }}px;left:{{ 258+  $leftPadding}}px;">
    @php $teeth = 4; @endphp
    <img class="teeth" alt ="21" src="assets/teethPics/21.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+153 }}px;left:{{ 245+  $leftPadding}}px;">
    @php $teeth = 5; @endphp
    <img class="teeth" alt ="22" src="assets/teethPics/22.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 182}}px;left:{{ 227+  $leftPadding}}px;">
    @php $teeth = 6; @endphp
    <img class="teeth" alt ="23" src="assets/teethPics/23.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition + 195 }}px;left: {{203+  $leftPadding}}px;">
    @php $teeth = 7; @endphp
    <img class="teeth" alt="24" src="assets/teethPics/24.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition +  200 }}px;left: {{168+  $leftPadding}}px;">
    @php $teeth = 8; @endphp
    <img class="teeth" alt ="25" src="assets/teethPics/25.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition + 200}}px;left: {{134+  $leftPadding}}px;">
    @php $teeth = 9; @endphp
    <img class="teeth" alt ="26" src="assets/teethPics/26.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 197 }}px;left: {{104+  $leftPadding}}px;">
    @php $teeth = 5; @endphp
    <img class="teeth" alt ="27" src="assets/teethPics/27.png" height={{$imageSizeL-3}}px style="  position: absolute; top: {{$startingPosition+ 185 }}px;left: {{68+  $leftPadding}}px;">
    @php $teeth = 4; @endphp
    <img class="teeth" alt ="28" src="assets/teethPics/28.png" height={{$imageSizeL-3}}px style="  position: absolute; top: {{$startingPosition+ 158}}px;left: {{46+  $leftPadding}}px;">
    @php $teeth = 3; @endphp
    <img class="teeth" alt ="29" src="assets/teethPics/29.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 125}}px;left: {{39+  $leftPadding}}px;">
    @php $teeth = 2; @endphp
    <img class="teeth" alt ="30" src="assets/teethPics/30.png" height={{$imageSizeL+4 }}px style="  position: absolute; top: {{$startingPosition+ +80 }}px;left: {{34+  $leftPadding}}px;">
    @php $teeth = 1; @endphp
    <img class="teeth" alt ="31" src="assets/teethPics/31.png" height={{$imageSizeL+6 }}px style="  position: absolute; top: {{$startingPosition+ $decrement *$teeth -10 }}px;left: {{29+  $leftPadding}}px;">
    @php $teeth = 0; @endphp
    <img class="teeth" alt ="32" src="assets/teethPics/32.png" height={{$imageSizeL+3 }}px style="  position: absolute; top: {{$startingPosition+ $decrement *$teeth -5 }}px;left: {{25+  $leftPadding}}px;">
    @php $teeth = 16; @endphp

</div>
</body>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script>
    const teethSelected = [];
$(".teeth").click(function() {
    if ($(this).hasClass("checked")) {
        $(this).removeClass("checked");
        var teethNumber = $(this).attr("alt");
        const index = teethSelected.indexOf(teethNumber);
        console.log(teethNumber);
        if (index > -1) {
        teethSelected.splice(index, 1);
        }
        document.getElementById("teethSelectedH2").innerHTML = teethSelected;
    } else {
        var teethNumber = $(this).attr("alt");
        teethSelected.push(teethNumber);
        $(this).addClass("checked");
        document.getElementById("teethSelectedH2").innerHTML = teethSelected;
    }

});
</script>
</html>
