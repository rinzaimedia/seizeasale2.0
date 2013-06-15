 </div>
 </div>
            </div>
        </div>
    </div>
    <div id="Footer">
    	<p><a href="/page/merchant-faq">Merchants</a>|<a href="/page/how-it-works">How it works</a>|<a href="<?php echo base_url()."home/faq"?>">FAQ</a>|<a href="#">Promote your business</a>|<a href="#">Press</a></p>
        <p><a href="/page/contact">Contact</a>|<a href="/page/terms-of-use">Terms of Use</a>|<a href="/page/privacy-policy">Privacy Policy</a></p>
    </div>
</div>
<?php
	if($this->config->item("google_analytics")!="" )
		echo $this->config->item("google_analytics");
?>
</body>
</html>