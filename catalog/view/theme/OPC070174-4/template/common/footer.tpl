<footer>
 	<div class="content_footer_top"><?php echo $footertop; ?> </div>
  	<div id="footer" >
   	  <div class="container">
			<div class="col-sm-3 column first">
				<div class="content_footer_left"><?php echo $footerleft; ?> </div>
			</div>
			
      		<?php if ($informations) { ?>
			<div class="col-sm-3 column second">
        		<h5><?php echo $text_information; ?></h5>
        			<ul class="list-unstyled">
          				<?php foreach ($informations as $information) { ?>
          					<li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
        				<?php } ?>
  		 					<!--<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
							<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>-->
        			</ul>
     		 </div>
    	  	<?php } ?>
    
      		<div class="col-sm-3 column third">
        		<h5><?php echo $text_extra; ?></h5>
        			<ul class="list-unstyled">
					  <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
					<!--<li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
					  <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>-->
					  <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
					   <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
					   <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
        			</ul>
      		</div>
			
	 	  <div class="col-sm-3 column fourth">
			 <div class="content_footer_right"><?php echo $footerright; ?> </div>
		  </div>
    </div>
</div>    
</footer>
<div class="footer-bottom">
<div class="copyright-container container">
	<div class="footer-container">
		<div id="bottomfooter">
			<ul>
				<li class="first"><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
				
				<li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
				
				<li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>   
				
				<li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li> 
				
				<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
				
				<li class="last"><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
			
			</ul>

		</div>

		<div class="powered"><?php echo $powered; ?></div> 	
	</div> 
	<div class="content_footer_bottom"><?php echo $footerbottom; ?> </div>
</div>
</div>


<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//--> 

<!-- Theme created by Welford Media for OpenCart 2.0 www.welfordmedia.co.uk -->
<span class="grid_default_width" style="display: none; visibility: hidden;" ></span>
</body></html>