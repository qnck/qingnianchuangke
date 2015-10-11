<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <link rel="stylesheet" href="./css/wheel.css" media="screen">
    <link rel="stylesheet" type="text/css" href="./css/ck.css"/>
    <script src="./js/jquery.min.js"></script>
    
</head>
<body onresize="winResize()">
        <div id="left" class="left-content">
            <div style="text-align: left;margin: 20px 0 0 10px;">
                <img id="leftClose" src="./images/left-close.png">
            </div>
            <div>
                <img  src="./images/logo2.png" style="margin-left:0px;">
            </div>
            <div>
                <img style="width:200px;z-index: 300;position: absolute;left: 23px;"src="./images/butt-bg.png"/>
                <div id="download" style="z-index: 301;position: absolute;margin:12px 0 0 0px;font-size: 15px;color: white;">
                    下载应用
                </div>
            </div>
            <div>
                <ul id="left-ul" style="margin-top:60px">
                    <li id="about" style="border-top:solid 1px rgb(99, 99, 102)">
                        关于我们
                    </li>
                    <li id="join">
                        成为创客
                    </li>
                    <li id="recruit">
                        公司招聘
                    </li>
                    <li id="law">
                        法律相关
                    </li>
                </ul>
                <ul id="left-contact">
                    <li style="padding-top: 50px;">
                        <div>
                            <img style="width: 130px"src="./images/wxscan.png"/>
                        </div>
                        <div style="color: #e77817">
                            青创微信服务平台
                        </div>
                    </li>
                    <li>
                        <div style="margin-top: 20px;">
                            <img style="width:200px;z-index: 300;position: absolute;left: 30px;"src="./images/contact-border.png"/>
                            <div style="z-index: 301;position: absolute;margin:12px 0 0 8px;">
                                联系方式
                            </div>
                        </div>
                        <p style="font-size: 30px;margin-top: 70px;">028-84515536</p>
                        <p>----</p>
                        <p style="font-size: 16px">
                            QQ 340246677
                        </p>
                        <p>
                            ----
                        </p>
                        <p style="font-size: 18px;line-height: 30px">
                            桐梓林北路
                        </p>
                        <p style="font-size: 18px;line-height: 30px">
                            中华园中苑16号3栋2单元A座
                        </p>
                    </li>
                </ul>
            </div>
        </div>
        <div id="rightMask" class="rightMask-0"></div>
        <div id="right" class="right-content">
            <div class="nav nav-00" id="nav">
                <div>
                    <div id="left_menu" style="color:white;font-weight: bold;margin-left: 40px;min-width: 70px;font-size: 15px;">
                        <img src="./images/left_menu.png" style="margin-right: 5px;color:white"/><span id="option">选项</span>
                    </div>
                </div>
                <div style="text-align: center;width: 30%;min-width: 135px;">
                    <img src="./images/logo.png" style="margin-top: 15px;"/>
                </div>
                <div class="nav-right">
                    <ul>
                        <li id="syLink">
                            首页
                        </li>
                        <li id="funLink">
                            应用功能
                        </li>
                        <li id="unionLink">
                            <div>创客联盟</div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="js-flickity" data-js-module="hero-gallery">
                <div >
                    <img id="one" src="./images/bg01.jpg"/>
                </div>
                <div>
                    <img id="two" src="./images/bg02.jpg"/>
                </div>
                <div>
                    <img id="three" src="./images/bg03.jpg"/>
                </div>
            </div>
            <div class="second-content">
                <div>
                    <img id="secondPhoto" src="./images/bg01.jpg"/>
                </div>
                <div id="secondFont">
                    <p id="secondTitel">我是标题，而且要长长长长，还不够长</p>
                    <p id="secondContent">
                        我是内容1111，我要换行，换行换行，我要换行，换行换行，我要换行，换行换行，我要换行，我要换行，换行换行，我要换行，换行换行，我要换行，换行换行，我要换行，我要换行，换行换行，我要换行，换行换行，我要换行，换行换行，我要换行，
                    </p>
                </div>
            </div>
            <div class="fun">
                <div class="fun-font">
                    <p style="margin-bottom:10px;">
                        <img src="./images/function.png"/>
                    </p>
                    <p style="font-size: 37px;line-height: 37px;height: 51px;">
                        青年创 应用程序
                    </p>
                    <p style="font-size: 14px;height: 10px">
                        打造方便、实用、有趣的使用体验
                    </p>
                </div>
                <div class="fun-list">
                    <ul>
                        <li >
                            <div id="1" style="background: url(./images/A.png)">
                                <div></div>
                                <div id="mask1">
                                    <p>便捷开店</p>
                                    <img src="./images/up.png"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div id="2" style="background: url(./images/B.png)">
                                <div></div>
                                <div id="mask2">
                                    <p>店铺分类</p>
                                    <img src="./images/up.png"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div id="3" style="background: url(./images/D.png)">
                                <div></div>
                                <div id="mask3" >
                                    <p>校园社交</p>
                                    <img src="./images/up.png"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div id="4" style="background: url(./images/C.png)">
                                <div></div>
                                <div id="mask4">
                                    <p>财务报表</p>
                                    <img src="./images/up.png"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div id="5" style="background: url(./images/E.png)">
                                <div></div>
                                <div id="mask5">
                                    <p>个性设置</p>
                                    <img src="./images/up.png"/>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div id="6" style="background: url(./images/F.png)">
                                <div></div>
                                <div id="mask6">
                                    <p>分享推广</p>
                                    <img src="./images/up.png"/>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="fun-content">
                    <div>
                        <img id="funClose" src="./images/fun-close.png"/>
                    </div>
                        <img id="funContent"/>
                        <img id="funLeft" src="./images/fun-left.png"/>
                        <img id="funRight" src="./images/fun-right.png"/>
                </div>
            </div>
            <div class="union">
                <div class="union-font">
                    <p style="margin-bottom:10px;">
                        <img src="./images/union.png"/>
                    </p>
                    <p style="font-size: 37px;line-height: 37px;height: 51px;">
                        足够努力 梦想从不遥远
                    </p>
                    <p style="font-size: 14px;height: 27px">
                        青年创 全心支持每一位校园创客
                    </p>
                    <p>
                        <img src="./images/union-bg.png" style="width:700px;margin:178px 0 0 -350px;position: absolute;z-index: 101;left:50%;top:0;"/>
                        <img id="photo" src="./images/change1.png" style="width: 1000px;margin: 178px 0 0 -500px;height: 520px;position: absolute;z-index: 102;left: 50%;top:0"/>
                    </p>
                </div>
                <div style="height: 300px;width: 100%;position:absolute;top:720px;left:50%;margin: 0 0 0 -50%">
                    <p style="text-align:center;font-size:12px">我是文字而且居中对齐</p>
                    <ul class="union-ul">
                        <li id="cy" style="color:black;">
                            创业者
                        </li>
                        <li id="fw">
                            服务者
                        </li>
                        <li id="cl">
                            创乐者
                        </li>
                        <li id="tz">
                            投资者
                        </li>
                    </ul>
                    <img src="./images/union-change.png" style="margin:80px 0 0 -221px;position: absolute;left:50%;top:30px;z-index: 100"/>
                    <div id="unionWhite"style="margin:80px 0 0 -221px;position: absolute;left:50%;top:30px;z-index: 101;width: 442px;height: 23px;background: rgba(0,0,0,0) ;cursor:pointer"></div>
                    <img id="drag" src="./images/union-button.png" style="margin:53px 0 0 -250px;top:30px;position: absolute;left:50%;z-index: 102"/>
                </div>
            </div>
            <div class="develop">
                <div style="width:100%;position: absolute;z-index: 101;text-align: center;margin:70px 0 0 0">
                    <img src="./images/develop.png"/>
                    <div style="color:white;font-size: 37px;text-align:center;margin:10px 0 15px 0;padding: 0;line-height:30px">立足川内 辐射全国</div>
                    <div style="color:white;font-size: 16px;text-align:center;">青年创客 全心支持每一位校园创客</div>
                </div>
                <img src="./images/map.png" style="width:1000px;margin:5px 0 0 -500px;position: absolute;z-index: 100;left:50%"/>
                <img src="./images/register.png" style="position: absolute;z-index: 101;left: 50%;margin-left:-118px;bottom:-26px;cursor: pointer"/>
            </div>
            <div class="link">
                <ul class="menu01">
                    <li id="syLink2"style="width: 22%">
                        首页
                    </li>
                    <li style="width: 5%;font-size: 28px;color:#d7394f ">
                        •
                    </li>
                    <li id="funLink2"style="width: 34%">
                        应用功能
                    </li>
                    <li style="width: 5%;font-size: 28px;color:#d7394f ">
                        •
                    </li>
                    <li id="unionLink2"style="width: 34%">
                        创客联盟
                    </li>
                </ul>
                <ul class="menu02">
                    <li>
                        成为创客
                    </li>
                    <li>
                        公司招聘
                    </li>
                    <li>
                        法律相关
                    </li>
                    <li>
                        微信平台
                    </li>
                    <li>
                        关于我们
                    </li>
                </ul>
            </div>
            <div class="footer">
                <p style="margin-top:55px;">成都青年创客科技有限公司</p>
                <p><span>Copyright</span> <span style="font-size: 20px;">&copy</span> <span>54qnck.com,ALL Rights Reserved.</span></p>
            </div>
        </div>
</body>
    <script src="./js/chuangke.js"></script>
    <script src="js/wheel.js"></script>
    <script>
        var broWidth;
        broWidth = document.body.clientWidth;
        var imgHeight = 1135 * broWidth / 2396;
        $(".js-flickity").css("height", imgHeight + "px");
        $(".fun").css("top", imgHeight);
        $(".union").css("top", imgHeight+1140+"px");
        $(".develop").css("top", imgHeight + 1140 + 940 + "px");
        $(".link").css("top", imgHeight + 1140 + 940 + 770 + "px");
        $(".footer").css("top", imgHeight + 1140 + 940 + 770 +180+ "px");
    </script>
</html>
