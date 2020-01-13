
    //各频道筛选分类 
	$(function(){
		var hideList_height = $(document).height();
		$(".de_hide_list").css("height",hideList_height+"px");
		
		$(".de_mall-cate>li").each(function(index){
			var $this = $(this);
			$this.bind({
				click:function(e){
					e.stopPropagation();
					// 初始化
					$(".de_abbr").removeClass("webkit-box");
					$(".de_hide_list").hide()
					$("#second_list>ul").hide();
					if(!($this.hasClass("cur"))){
						$this.addClass("cur").siblings().removeClass("cur");
						$(".de_hide_list").show().find(".de_abbr").eq(index).addClass("webkit-box").find("#second_list>ul").eq(index).show();
						$("#first_list li").each(function(index){
							var $this = $(this);
							$this.click(function(e){
								e.stopPropagation();
								$(".second_list>ul").hide();
								$this.addClass("select").siblings().removeClass("select");
								$(".second_list>ul").eq(index).show();
							})
						})
					}
					else{
						$this.removeClass("cur");
						$(".de_abbr").eq(index).removeClass("webkit-box");
					}
				} ,
				change:function(){
					
				}
			});
		});
		$(".de_abbr").bind("click",function(e){
			e.stopPropagation();
		});
		$(document).click(function(){
			$(".de_mall-cate>li").removeClass("cur");
			$(".de_abbr").removeClass("webkit-box");
			$(".de_hide_list").hide();
			$("#second_list>ul").hide();
		})
	})