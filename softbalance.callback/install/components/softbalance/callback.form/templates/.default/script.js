/**
 * Created by PhpStorm.
 * User: chernenko Nikolay ( wedoca@gmail.com )
 * Date: 19.08.14
 * Time: 11:06
 */
$(function(){

	$("body").append($("#callbackForm"));
	$("body").append('<div id="callback_mask"></div>');
	$("#callbackLink").on("click",function(){
		$("#callbackForm,#callback_mask").show();
	});

	$("#callback_mask,#callbackForm .close").on("click",function(){
		$("#callbackForm,#callback_mask").hide();
	});

	$("#callbackForm").submit(function(){
		var formFields = $(this).serializeArray(),
			data = {
				"json_request_callback_form":"Y",
				"form":formFields
			}

		$.when(startTask(data)).done(function(jsonObj){
			if(jsonObj.error){
				$("#callbackForm input").removeClass("error");
				$("#callbackForm>div").empty();
				for(var i in jsonObj.error){
					console.log(jsonObj.error[i]);
					$("#callbackForm input[name="+jsonObj.error[i].name+"]").addClass("error");
					$("#callbackForm>div").append("<p>"+jsonObj.error[i].message+"</p>");
				}
			}
			if(jsonObj.complete){
				$("#callbackForm>div").append("<span>"+jsonObj.ok+"</span>");
				setTimeout(function(){$("#callbackForm,#callback_mask").fadeOut(500);},3000);
			}
		});


		return false;
	});

	function startTask(data){return $.getJSON("",data);}
});