$("document").ready(function(){
	$(".unblock").click(function(){
		$.post("demo_test_post.asp",
		{
		  name: "Donald Duck",
		  city: "Duckburg"
		},
		function(data,status){
			alert("Data: " + data + "\nStatus: " + status);
		});
	});
});