var count = 0;
$("#left_menu").click(
    function () {
            $("#nav").removeClass("nav-left-0");
            $("#nav").removeClass("nav-right-to-left");
            $("#left").removeClass("left-content");
            $("#right").removeClass("right-content");
            $("#left").removeClass("left-to-left");
            $("#right").removeClass("right-to-left");
            $("#nav").removeClass("nav-up-to-down");
            $("#rightMask").removeClass("rightMask-0");
            $("#rightMask").removeClass("rightMask-to-right");
            $("#rightMask").css("display", "block");
            $("#rightMask").addClass("rightMask-to-right");
            $("#nav").addClass("nav-left-to-right");
            $("#left").addClass("left-to-right");
            $("#right").addClass("right-to-right");
            $("#rightMask").css("display", "block");
            $("#right").css("overflow", "hidden");
            $("#nav").css("display", "none");
        }
);
$("#rightMask,#leftClose").click(function () {
        $("#nav").removeClass("nav-left-0");
        $("#nav").removeClass("nav-left-to-right");
        $("#left").removeClass("left-content");
        $("#right").removeClass("right-content");
        $("#left").removeClass("left-to-right");
        $("#right").removeClass("right-to-right");
        $("#nav").removeClass("nav-up-to-down");
        $("#nav").addClass("nav-right-to-left");
        $("#left").addClass("left-to-left");
        $("#right").addClass("right-to-left");
        $("#rightMask").css("display", "none");
        $("#right").css("overflow", "visible");
        $("#nav").css("display", "block");
});

function moveMask(){
    $("#nav").removeClass("nav-left-0");
    $("#nav").removeClass("nav-left-to-right");
    $("#left").removeClass("left-content");
    $("#right").removeClass("right-content");
    $("#left").removeClass("left-to-right");
    $("#right").removeClass("right-to-right");
    $("#nav").removeClass("nav-up-to-down");
    $("#left").addClass("left-content");
    $("#right").addClass("right-content");
    $("#rightMask").css("display", "none");
    $("#right").css("overflow", "visible");
    $("#nav").css("display", "block");
}

$(".fun-list ul li div").mouseenter(function () {
    var this_id = $(this).attr("id");
    var mask_id = "#mask" + this_id;
    $(mask_id).removeClass("up-to-down");
    $(mask_id).addClass("down-to-up");
});
$(".fun-list ul li div").mouseleave(function () {
    var this_id = $(this).attr("id");
    var mask_id = "#mask" + this_id;
    $(mask_id).removeClass("down-to-up");
    $(mask_id).addClass("up-to-down");
});

var scrollFunc = function(e) {
    var direct = 0;
    e = e || window.event;
    var t1 = document.getElementById("wheelDelta");
    var t2 = document.getElementById("detail");
    if (e.wheelDelta) {
        if (e.wheelDelta > 0) {
            $("#nav").removeClass("nav-00");
            $("#nav").removeClass("nav-70");
            $("#nav").addClass("nav-up-to-down");
        }
        else if (e.wheelDelta < 0) {
            $("#nav").removeClass("nav-00");
            $("#nav").removeClass("nav-up-to-down");
            $("#nav").addClass("nav-70");
        }
        t1.value = e.wheelDelta;
    } else if (e.detail) {  
        if (e.detail > 0) {
            $("#nav").removeClass("nav-00");
            $("#nav").removeClass("nav-up-to-down");
            $("#nav").addClass("nav-70");
        }
        else if (e.detail < 0) {          
            $("#nav").removeClass("nav-00");
            $("#nav").removeClass("nav-70");
            $("#nav").addClass("nav-up-to-down");
        }
    }
    ScrollText(direct);
};
if (document.addEventListener) {
    document.addEventListener('DOMMouseScroll', scrollFunc, false);
}
window.onmousewheel = document.onmousewheel = scrollFunc;


//---------轮播图片----------------
function secondShow(this_id) {
    if (this_id == "01") {
        $("#secondTitel").html("我是标题1我是标题1我是标题1");
        $("#secondContent").html("我是内容1我是内容1我是内容1我是内容1我是内容1我是内容1我是内容1");
    }
    else if (this_id == "02") {
        $("#secondTitel").html("我是标题2我是标题2");
        $("#secondContent").html("我是内容2我是内容2我是内容2我是内容2我是内容2我是内容2我是内容2我是内容2");
    }
    else if (this_id == "03") {
        $("#secondTitel").html("我是标题3我是标题3");
        $("#secondContent").html("我是内容3我是内容3我是内容3我是内容3我是内容3我是内容3我是内容3我是内容3我是内容3我是内容3");
    }
    var _id = "./images/bg" + this_id + ".jpg";
    $("#secondPhoto").attr("src", _id);
    $(".js-flickity,.fun,.union,.develop").css("display", "none");
    $(".second-content").css("display", "block");
    var fontHeight = $("#secondFont").height();
    $(".link").css("top", 570 + fontHeight + "px");
    $(".footer").css("top", 570 + fontHeight + 180 + "px");
};
$("#download,#one").click(function () {
    moveMask();
    secondShow("01");
});
$("#join,#two").click(function () {
    moveMask();
    secondShow("02");
});
$("#about,#three").click(function () {
    moveMask();
    secondShow("03");
});
//---------"首页"、"功能"、"联盟"点击事件----------------
$("#syLink,#syLink2").click(function () {
    $(".js-flickity,.fun,.union,.develop").css("display", "block");
    $(".link").css("top", imgHeight + 1090 + 900 + 860 + "px");
    $(".footer").css("top", imgHeight + 1090 + 900 + 860 + 180 + "px");
    $(".second-content").css("display", "none");
    $('html,body').animate({ scrollTop: 0 }, 800);
});
$("#funLink,#funLink2").click(function () {
    $(".js-flickity,.fun,.union,.develop").css("display", "block");
    $(".second-content").css("display", "none");
    $('html,body').animate({ scrollTop: $('.fun').offset().top - 42 }, 800);
    $(".link").css("top", imgHeight + 1090 + 900 + 860 + "px");
    $(".footer").css("top", imgHeight + 1090 + 900 + 860 + 180 + "px");
});
$("#unionLink,#unionLink2").click(function () {
    $(".js-flickity,.fun,.union,.develop").css("display", "block");
    $(".link").css("top", imgHeight + 1090 + 900 + 860 + "px");
    $(".footer").css("top", imgHeight + 1090 + 900 + 860 + 180 + "px");
    $(".second-content").css("display", "none");
    $('html,body').animate({ scrollTop: $('.union').offset().top-42 }, 800);
});

//-----------功能分类点击事件---------------
$(".fun-list ul li div").click(function () {
    var _id = $(this).attr("id");
    var str = "./images/" + _id + ".png";
    $(".fun-list").css("display", "none");
    $(".fun-content").css("display", "block");
    $("#funContent").attr("src", str);
    $(".union").css("top", imgHeight + 850 + "px");
    $(".develop").css("top", imgHeight + 850 + 900 + "px");
    $(".link").css("top", imgHeight + 850 + 900 + 770 + "px");
    $(".footer").css("top", imgHeight + 850 + 900 + 770 + 180 + "px");
});
$("#funClose").click(function () {
    $(".fun-content").css("display", "none");
    $(".fun-list").css("display", "block");
    $(".union").css("top", imgHeight + 1090 + "px");
    $(".develop").css("top", imgHeight + 1090 + 900 + "px");
    $(".link").css("top", imgHeight + 1090 + 900 + 770 + "px");
    $(".footer").css("top", imgHeight + 1090 + 900 + 770 + 180 + "px");
});
$("#funLeft").click(function() {
    var a = $("#funContent").attr("src"),b;
    var arr = a.split(".png");
    b = arr[0].split("./images/");
    if (b[1] <= 1) {
        b[1] = 1;
        $("#funContent").attr("src","./images/1.png");
    }
    else if (b[1] >1) {
        --b[1];
        a = "./images/" + b[1] + ".png";
        $("#funContent").attr("src", a);
    }
});
$("#funRight").click(function () {
    var a = $("#funContent").attr("src"), b;
    var arr = a.split(".png");
    b= arr[0].split("./images/");
    if (b[1] >= 6) {
        b[1] = 6;
        $("#funContent").attr("src", "./images/6.png");
    }
    else if (b[1] <6) {
        ++b[1];
        a = "./images/" + b[1] + ".png";
        $("#funContent").attr("src", a);
    }
});
//-----------联盟点击事件---------------
$("#unionWhite").mousedown(function (event) {
    var x, y, value;
    x = this.offsetLeft;
    value = event.clientX - this.offsetLeft;
    if ((0 <= value) && (value < 75)) {
        $("#drag").css("margin-left", "-245px");
        $("#photo").attr("src", "./images/change1.png");
        $(".union-ul li").css("color", "gray");
        $("#cy").css("color", "black");
    }
    else if ((75 <= value) && (value < 225)) {
        $("#drag").css("margin-left", "-105px");
        $("#photo").attr("src", "./images/change2.png");
        $(".union-ul li").css("color", "gray");
        $("#fw").css("color", "black");
    }
    else if ((225 <= value) && (value < 375)) {
        $("#drag").css("margin-left", "35px");
        $("#photo").attr("src", "./images/change3.png");
        $(".union-ul li").css("color", "gray");
        $("#cl").css("color", "black");
    }
    else if ((375 <= value) && (value < 525)) {
        $("#drag").css("margin-left", "175px");
        $("#photo").attr("src", "./images/change4.png");
        $(".union-ul li").css("color", "gray");
        $("#tz").css("color", "black");
    }
});
var imgHeight;
function winResize() {
    var broWidth;
    location.reload(true);
    broWidth = document.body.clientWidth;
    imgHeight = 1135 * broWidth / 2396 ;
    $(".js-flickity").css("height", imgHeight + "px");
    $(".fun").css("top", imgHeight);
    $(".union").css("top", imgHeight + 1090 + "px");
    $(".develop").css("top", imgHeight + 1090 + 900 + "px");
    $(".link").css("top", imgHeight + 1090 + 900 + 770 + "px");
    $(".footer").css("top", imgHeight + 1090 + 900 + 770 + 180 + "px");
}