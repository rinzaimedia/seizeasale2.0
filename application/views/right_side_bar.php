<!-- Side bar -->
<div id="Side_Bar" class="clsFloatRight">
    <div class="clsSide_Add">
        <?php

        $banner_query = $this->common_model->getTableData("ad_banner","1");

        $banners=$banner_query->result();

            if(count($banners)>0)
            {
                $count = rand(0,count($banners)-1);


                        echo $banners[$count]->code;

                        break;

            }
        ?>

        <!--<a href="#"><img src="<?php echo base_url()?>images/add.png" alt="image" /></a>-->
    </div>
    <div class="clsFace_Likers">
    	<?php
        	echo fan_box();
		?>
        
    </div>
</div>
<style type="text/css">
.connect_widget
{
	background-color:#F1F5F2 !important;
}
</style>
<!-- End of Side Bar -->