//var _url = "http://www.test.54qnck.com/";
//var _url = "http://www.54qnck.com/";
var _url = "http://www.qnck.dev/";
function GetQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}