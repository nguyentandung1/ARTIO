<footer style = "background-color: #00BFFF;">
		<div class="container">
			<div class="footer-widget">
				<div class="widget" style = "border-radius: 12px">
					<div class="widget-heading">
						<h3>Important Link</h3>
					</div>
					<div class="widget-content">
						<ul>
							<li><a href="about.php" style = "font-family:candara;font-weight:bold;font-size:16px;">About</a></li>
							<li><a href="Instructions.php" style = "font-family:candara;font-weight:bold;font-size:16px;">Instructions</a></li>
							<li><a href="term.php" style = "font-family:candara;font-weight:bold;font-size:16px;">Terms & Conditions</a></li>
						</ul>
					</div>
				</div>
				<div class="widget" style="padding: 8px; border-radius: 12px">
				<div class="widget-content">
					<div class="column">
						<ul class="column-list">
							<li>Nguyễn Tấn Dũng</li>
						</ul>
					</div>
				</div>
			</div>

				<div class="widget" style = "border-radius: 12px">
					<div class="widget-heading" style = "display: flex; border: none;">
						<h3 style="flex:1; padding: 5px 0 0 0px; margin: 8px 0;  border-bottom: none;">Follow us</h3>
						
						<div class="follow" style="flex:1.5;">
							<ul>
								<li><a href="https://www.facebook.com/dungxbeeboy"><img src="img/icons/facebook.png"></a></li>
								<li><a href="#"><img src="img/icons/twitter.png"></a></li>
								<li><a href="#"><img src="img/icons/instagram.png"></a></li>
							</ul>
											
						</div>
					</div>
					
					<div class="widget-heading">
						<h3 style="margin: 8px 0;">Subscribe for Team Tech</h3>
					</div>
					<div class="widget-content">
						<div class="subscribe">
							<form>
								<div class="form-group" >
									<input type="text" class="form-control" name="subscribe" placeholder="Enter Email" 
									style="font-family: MyCustomFont, Arial, sans-serif; font-size: 0.95em; padding-right:30px">
									<img src="img/icons/paper_plane.png" 
									>
								</div>
							</form>
						</div>						
					</div>
				</div>
			</div> <!-- Footer Widget -->
<script src="<?php echo $js; ?>popper.min.js"></script>
<script src="<?php echo $js; ?>bootstrap.min.js"></script>
<script src="<?php echo $js; ?>frontend.js"></script>

<script>
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="6fxWDnlH2AgXKUBBf-3qg";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>

</body>
<style>

input:-ms-input-placeholder {
    color: #999; /* Màu của placeholder */
}

.widget-content {
    display: flex; /* Sử dụng flexbox để xây dựng 2 cột */
	font-family: MyCustomFont, Arial, sans-serif;
}

.column {
    flex: 1; /* Cột co giãn để chiếm phần bằng nhau của không gian */
}

.column-list {
    list-style: none; /* Loại bỏ dấu chấm đầu dòng */
    padding: 0; /* Xóa lề nội dung */
    margin: 0; /* Xóa lề ngoài */
}

.column-list li {
    line-height: 20px; /* Chiều cao dòng */
	padding: 5px 5px; /* Thêm lề cho mỗi mục */
}

.column-list li:last-child {
    border-bottom: none; /* Loại bỏ đường viền dưới cho mục cuối cùng */
}
li {
	/* font-family: MyCustomFont, Arial, sans-serif; */
	font-family:candara;
	font-weight: bold;
	/* font-size: 0.95em */
}

</style>

</html>