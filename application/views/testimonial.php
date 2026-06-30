        <h2 class="page-title">ScanLink for all business large and small...</h2>
				<p class="text-bold">Meet a few of our customers, hear their stories, and imagine what ScanLink can do for you</p>   
        <br/><br/>
        <section class="testimonial-row clearfix">
					<?php
					for ($i=0; $i<count($testimonials); $i++)
					{
						if ($i>0 && ($i)%2 == 0)
						{
							print "</section>";
							print '<section class="testimonial-row clearfix">';
						}
						print '<section class="testi-block">'.$testimonials[$i]["video"].'</section>';
					}
					?>
       </section>