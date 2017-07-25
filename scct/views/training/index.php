<?php $this->title = 'Training'; ?>
<style>
#embedVideo{
	padding: 0 8% 8% 3%;
}
.intro {
	padding-bottom: 0;
}
.video-js{
	height: 150px;
	width: 100% !important;
}
.fancybox{
	width: 100%;
}
.videoTitle{
	text-align: center;
	margin-bottom: 4%;
    margin-top: 1%;
}
.video{
	background: #fff;
    /* padding-bottom: 20px; */
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.15);
    width: 25%;
    margin: 1%;
    float: left;	
	
}
ul{
text-align: left;
}
</style>
<div class="site-index">
    <div class="jumbotron intro">
        <h2>Training Section</h2>
    </div>
    <div class="body-content">
        <div class="row">
			<div class="col-md-12" id="embedVideo">
				<h2>Website Videos</h2>
				<article class="video">
					<figure>
						<video id="video1" class="fancybox fancybox.iframe video-js vjs-default-skin" 
							poster=""
							data-setup='{"controls" : true, "autoplay" : false, "preload" : "auto"}'>
							<source src="/videos/DispatchAssigned_07_18_17.mp4" type="video/mp4"-->
						</video>
					</figure>
					<h4 class="videoTitle">Dispatch & Assigned</h4>
			    </article>
				<article class="video">
					<figure>
						<video id="video1" class="fancybox fancybox.iframe video-js vjs-default-skin" 
							poster=""
							data-setup='{"controls" : true, "autoplay" : false, "preload" : "auto"}'>
							<source src="/videos/Reports_07_19_17.mp4" type="video/mp4"-->
						</video>
					</figure>
					<h4 class="videoTitle">Reports</h4>
			    </article>
			</div>
			<div class="col-md-12" id="embedVideo">
				<h2>Mobile - Coming Soon</h2>
			</div>				
        </div>
    </div>
</div>