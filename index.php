<html>
	<head>
		<title>aChat</title>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"/></script>
		<script type="text/javascript" src="js/jquery.cookie.js"></script>
		<script type="text/javascript" src="http://www.itsyndicate.ca/gssi/jquery/jquery.crypt.js"/></script>
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	<body>

		<header>
			<h1>aChat</h1>
		</header>
		<section id="chatScreen" class='loading'>
			<ul style='display:none;' >
				
			</ul>
		</section>

		<section id="userPanel">
			<form id="chatForm" action="javascript:void()">
				<span>Nickname:</span><input type="text" id="nickname" name="nickname" value="myNick"/>
				<span>ChatRoom:</span><input type="text" id="chatRoomName" name="chatRoomName" value="chatRoomName"/>
				<span>EncryptionKey:</span><input type="text" id="encryptionKey" name="encryptionKey" value="mySecretEencryptionKey"/>
				<span>Message: </span><textarea id="message" name="message" value="messaggio!" rows=4 col=20></textarea>
				<input class="button red" type="submit" id="sendMessage" name="send" value="Invia" />
			</form>
		</section>
	</body>
	<script type="text/javascript">
		function updateChat(){
			var chatList = $('#chatScreen ul');
			$.ajax("chat.php?a=get&chatroomname=" + $().crypt({method:"sha1",source:$("#chatRoomName").val()}) , 
				{
					success: function(data){
						data = $.parseJSON(data);
						if(	data && data.length && 
							data.length !== $('#chatScreen li').length){
							$('#chatScreen').addClass('loading');
							chatList.hide();
							chatList[0].innerHTML = "";
							for(var i =0, msg; (msg = data[i]); i++){
								if(msg.message !== null && msg.message !== ""){
									var li = $("<li/>");
									var time = isNaN(msg.timestamp) ? "--" : new Date(msg.timestamp*1000);
									time = time.getMonth() + "/" + time.getDay() + "-" + time.getHours() + ":" + time.getSeconds();
									li.text($().crypt({
										method:"xteab64dec",
										source: msg.message,
										strKey:$('#encryptionKey').val()
									}));
									$("<span class='nickname'/>").text(msg.nickname+":").prependTo(li);
									$("<span class='date'/>").text(time).prependTo(li);
									li.appendTo(chatList);
								}
							}
							//$('#chatScreen').height(chatList.height());
							$('#chatScreen').removeClass('loading');
							chatList.show();
							chatList[0].scrollTop = chatList[0].scrollHeight;
 							}
					}
				}
			);
			$('#chatScreen').height(chatList.height());
		}

		$(document).ready(function(){
			$("textarea").keyup(function(){
				if($(this).val() !== ""){
					$("input[type='submit']").removeClass('red').addClass('green');
				}else{
					$("input[type='submit']").removeClass('green').addClass('red');
				}
			});
			$("#encryptionKey").blur(function(){
				$('#chatScreen ul').empty();
				updateChat();
			});
			$('#chatForm').submit(function(e){
				
				$("input,textarea").each(function(index,elm){ 
					if($(elm).val() === ""){
						$(elm).css('border','2px solid red');
						$("input[type='submit']").removeClass('green').addClass('red');
						$(elm).blur(function(){
							if($(this).val() !== ""){
								$(elm).css('border','');
							}else{
								$("input[type='submit']").removeClass('green').addClass('red');
							}
						});
						return false;
					}
				});

				var messageToSend = $("#message").val();
				var nickName = $("#nickname").val();
				var secretKey = $('#encryptionKey').val();
				var chatRoomName = $('#chatRoomName').val();
				$.cookie("nickname",nickName);
				messageToSend =  $().crypt({
					method:"xteab64enc",
					source: messageToSend,
					strKey:secretKey
				}) ;
				chatRoomName = $().crypt({method:"sha1",source:chatRoomName}); 
				$.ajax("chat.php?op=send&chatroomname=" + chatRoomName  + "&message=" + messageToSend,{
					success: function(data){
					// check if send was succesfull
				}});
				e.preventDefault();
				return false;
			})
			setInterval(updateChat,500);
		});
	</script>
</html>
	