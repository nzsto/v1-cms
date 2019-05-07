// 点击汉字验证码 内嵌式 
function getImg(){
  $('#getVerify').html("正在验证");
  $('#clicaptcha-submit-info').clicaptcha({
    src: 'public/verifycode/clicaptcha.php',
    callback: function(data){
      if(data){
        $('#getVerify').html("验证通过");
        $('#getVerify').attr("onclick","");
        $('#getVerify').css("background","#fff url(public/verifycode/gou.png) no-repeat 70px -4px");
        $(".login form").css('height','auto');
        var sub = $('#sub').attr("onclick");
        if (sub == "") {
          submitLoginForm();
        }
      }else{
        $('#getVerify').css("background","none");
        $(".number").attr("style","none");
      }
    }
  });
  $(".login form").css('height','380px');
};
function subForm(){
  var judge = $('#getVerify').attr("onclick");
  if (judge == "getImg()") {
    $('#sub').attr("onclick","");
    $(".code-box").attr("style","display: block;");
    getImg();
  }else{
    submitLoginForm();
  }
}
function submitLoginForm() {
    var self = $("#loginform");
    $.post(self.attr("action"), self.serialize(), success, "json");
    return false;
    function success(data) {
        if (data.status==1) {
            layer.msg(data.info);
            setTimeout(function(){
                var suburl = $(".suburl").attr("id");
                window.location.href = suburl;
            },500);
        } else {
            layer.msg(data.info);
            $('#getVerify').html("点击完成验证");
            $('#getVerify').attr("onclick","getImg()");
            $('.sub').attr("onclick","subForm()");
            $('#getVerify').css("background","none");
            $(".number").attr("style","none");
        }
    };
}
var i = 1;
document.onclick=function(ev){
    var oBox=document.getElementById('number1');
    var oEvent = ev||event;
    
    var scrollTop=document.documentElement.scrollTop || document.body.scrollTop;
    var scrollLeft=document.documentElement.scrollLeft || document.body.scrollLeft;
    var numberX = scrollLeft + oEvent.clientX;
    var numberY = scrollTop + oEvent.clientY;

    //计算大图片的坐标范围
    var bigImg = $("#bigImg");
    var offsetBigImg = bigImg.offset();
    var bigImgMinX = offsetBigImg.left;
    var bigImgMaxX = bigImgMinX + bigImg.width();
    var bigImgMinY = offsetBigImg.top;
    var bigImgMaxY = bigImgMinY + bigImg.height();
    
    //验证码获取按钮坐标范围
    var getVerify = $("#getVerify");
    var offsetGetVerify = getVerify.offset();
    var getVerifyMinX = offsetGetVerify.left;
    var getVerifyMaxX = getVerifyMinX + getVerify.innerWidth();
    var getVerifyMinY = offsetGetVerify.top;
    var getVerifyMaxY = getVerifyMinY + getVerify.innerHeight();
    
    //验证码刷新按钮坐标范围
    var refreshImg = $(".clicaptcha-refresh-btn");
    var offsetrefreshImg = refreshImg.offset();
    var refreshImgMinX = offsetrefreshImg.left;
    var refreshImgMaxX = refreshImgMinX + refreshImg.width();
    var refreshImgMinY = offsetrefreshImg.top;
    var refreshImgMaxY = refreshImgMinY + refreshImg.height();
    
    //点击非大图区域取消验证码区域坐标计算
    var loginform = $(".login-body");
    var offsetLoginform = loginform.offset();
    var LoginformMinX = offsetLoginform.left;
    var LoginformMaxX = LoginformMinX + loginform.innerWidth();
    var LoginformMinY = offsetLoginform.top;
    var LoginformMaxY = LoginformMinY + loginform.innerHeight();
    
    //获取验证码按钮和刷新验证区域
    if ((numberX > getVerifyMinX && numberX < getVerifyMaxX && numberY > getVerifyMinY && numberY < getVerifyMaxY) || (numberX >refreshImgMinX && numberX < refreshImgMaxX && numberY > refreshImgMinY && numberY < refreshImgMaxY)) {
        i = 1;
        $(".number").attr("style","none");
        $(".code-box").attr("style","display: block;");
    }

    //取消验证码区域设定 当正在验证时 点击取消验证
    var judgeSuccess = $('#getVerify').html();
    if (judgeSuccess == '正在验证') {
        if (numberX < LoginformMinX - 10 || numberX > LoginformMaxX + 10 || numberY < LoginformMinY -10 || numberY > LoginformMaxY + 10) {
            i = 1;
            $(".number").attr("style","none");
            $(".code-box").attr('style','display: none;');
            $(".login form").attr("style","position: relative;height: auto;");
            $('#getVerify').html("点击完成验证");
            $('#sub').attr('onclick','subForm()');
            $('#getVerify').attr('onclick','getImg()');
        }
    }

    //点击验证码大图片时
    if (numberX >bigImgMinX && numberX < bigImgMaxX && numberY > bigImgMinY && numberY < bigImgMaxY) {
        if (i == 1) {
            var oBox=document.getElementById('number1');
            $("#number1").css('display','block');
            i += 1;
        }else if(i == 2) {
            var oBox=document.getElementById('number2');
            $("#number2").css('display','block');
            i += 1;
        }else if(i == 3) {
            var oBox=document.getElementById('number3');
            $("#number3").css('display','block');
            i += 1;
        }else if(i == 4) {
            var oBox=document.getElementById('number4');
            $("#number4").css('display','block');
            i = 1;
        }
        oBox.style.left=oEvent.clientX + scrollLeft -10 + 'px';   //clientX可视区内的水平坐标
        oBox.style.top=oEvent.clientY + scrollTop -10 + 'px';   //clientY......垂直... 
    }
};