$(function(){
	$('input').blur(function(){
		var e=$(this);
		if(e.attr("data-validate")){
			e.closest('.field').find(".input-help").remove();
			var $checkdata=e.attr("data-validate").split(',');
			var $checkvalue=e.val();
			var $checkstate=true;
			var $checktext="";
			if(e.attr("placeholder")==$checkvalue){$checkvalue="";}
			if($checkvalue!="" || e.attr("data-validate").indexOf("required")>=0){
				for(var i=0;i<$checkdata.length;i++){
					var $checktype=$checkdata[i].split(':');
					if(! $pintuercheck(e,$checktype[0],$checkvalue)){
						$checkstate=false;
						$checktext=$checktext+"<li>"+$checktype[1]+"</li>";
					}
				}
			};
			if($checkstate){
				e.closest('.form-group').removeClass("check-error");
				e.parent().find(".input-help").remove();
				e.closest('.form-group').addClass("check-success");
			}else{
				e.closest('.form-group').removeClass("check-success");
				e.closest('.form-group').addClass("check-error");
				// e.closest('.field').append('<div class="input-help"><ul>'+$checktext+'</ul></div>');
			}
		}
	});
});