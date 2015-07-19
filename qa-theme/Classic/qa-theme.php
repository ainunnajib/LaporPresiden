<?php
$theme_dir = dirname( __FILE__ ) . '/';
$theme_url = qa_opt('site_url') . 'qa-theme/' . qa_get_site_theme() . '/';
qa_register_layer('/qa-admin-options.php', 'Theme Options', $theme_dir , $theme_url );

//var_dump($theme_dir);

	class qa_html_theme extends qa_html_theme_base
	{

		function head_metas()
		{
			qa_html_theme_base::head_metas();
			$this->output('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
			$this->output('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">');
			$this->output('<meta name="description" content="">');
			$this->output('<meta name="author" content="">');
		}
		
		function head_script()
		{
			qa_html_theme_base::head_script();
			
			/*$this->output('
				<script type="text/javascript">
				$(document).ready(function(){
					$(".menu_show_hide").click(function(){
					$(".qa-nav-main").slideToggle();
					});

				$(window).resize(function() {
					if ($(window).width()>720) {$(".qa-nav-main").show();}
				});
				}
				);
				</script>');*/

		}
		function head_css()
		{
		    $this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'bower_components/bootstrap/dist/css/bootstrap.min.css'.'"/>');
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'bower_components/metisMenu/dist/metisMenu.min.css'.'"/>');
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'bower_components/font-awesome/css/font-awesome.min.css'.'"/>');
			
			if (qa_opt('qat_compression')==2) //Gzip
				$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'qa-styles-gzip.php'.'"/>');
			elseif (qa_opt('qat_compression')==1) //CSS Compression
				$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'qa-styles-commpressed.css'.'"/>');
			else // Normal CSS load
				$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.$this->css_name().'"/>');
			
			if (isset($this->content['css_src']))
				foreach ($this->content['css_src'] as $css_src)
					$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$css_src.'"/>');
					
			if (!empty($this->content['notices']))
				$this->output(
					'<STYLE><!--',
					'.qa-body-js-on .qa-notice {display:none;}',
					'//--></STYLE>'
				);
		}		
		

		function body_content()
		{
			$this->body_prefix();
			$this->notices();
			$logoshow=qa_opt('logo_show');
			$logourl=qa_opt('logo_url');
			$logoheight=qa_opt('logo_height');
			
			$this->output('<nav class="navbar navbar-red navbar-fixed-top">', '');
				$this->output('<div class="container">', '');
					$this->output('<div class="navbar-header">', '');
						$this->output('<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
									<span class="sr-only">Toggle navigation</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								  </button>', '');
				    	$this->output('<a class="navbar-brand" style="padding-left:5px;padding-top:0px;z-index:1000000;" href="#">
										 <img src="'.$logourl.'" id="logo3" border="0" alt="'.qa_html(qa_opt('site_title')).'" style="height:'.$logoheight.'px;">
									  </a>', '');
					$this->output('</DIV>', '');
					$this->output('<div id="navbar" class="navbar-collapse collapse">', '');
						$this->output('<ul class="nav navbar-top-links navbar-right" id="side-menu2">', '');
							$this->nav2('user');
							$this->nav_main_sub3();
						$this->output('</ul>', '');
					$this->output('</DIV>', '');
				$this->output('</DIV>', '');	
			$this->output('</nav>', '');

			$this->header();
			
			
			
			$this->output('<div id="wrapper">', '');
			$this->output('<div class="row">', '');
				$this->output('<div class="col-md-3">', '');
				
				$this->output('<div class="sidebar transparent" role="navigation">
									<ul class="nav " id="side-menu">', '');
				
				$this->nav_main_sub();
				
				
				$this->output('</ul></div>', '');
				
				$this->output('<div class="sidebar transparent" role="navigation">
									<div class="sidebar-nav">', '');
				$this->output('<div class="panel panel-default">', '');
					$this->output('<div class="panel-heading">', '');
					$this->output("Kategori");
					$this->output('</div>');
					$this->output('<div class="panel-body">', '');
						$this->nav('cat', 1);
					$this->output('</div>');
				$this->output('</div>');
				$this->output('</div></div>', '');
				
				$this->output('</div>');
				
				
				$this->output('<div class="col-md-6">', '');
				    $this->output('<div id="createNewAsk" class="row">', '');
						$this->output('<div class="col-md-12">', '');
								$headingcontent='<div class="row">
														<div class="col-md-8"><h3 style="padding:0px;margin:5px;">Ayo berpartisipasi</h3></div>
														<div class="col-md-4">
															<a class="btn btn-default btn-block" href="/ask"><i class="fa fa-pencil-square-o"></i> Lapor</a>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															  <div class="sidebar-search" style="padding-top:10px;">
																   <form method="get" action="'.qa_path_html('search').'">
																		<div class="input-group custom-search-form">								
																			<input type="text" name="q" class="form-control" placeholder="Cari Laporan">
																			<span class="input-group-btn">
																				<button class="btn btn-default" type="submit">
																					<i class="fa fa-search"></i>
																				</button>								
																			</span>
																		</div>
																	</form>
															  </div>
														</div>
													</div>';
								$this->panel("panel-red",true,$headingcontent,false,"",false,"");
						$this->output('</div>');
					$this->output('</div>');
					$this->output('<div class="row">', '');
						$this->output('<div class="col-md-12">', '');
							$this->output('<ul class="sub-nav">', '');
						   		$this->nav2('sub');
						   	$this->output('</ul>', '');
						$this->output('</div>');
					$this->output('</div>');
					$this->main();
				$this->output('</div>');
				
				
				$this->output('<div class="col-md-3">', '');
					$this->sidebar2();
					$this->output('<div class="row" style="padding-bottom:20px;text-align:center;">');
						$this->output('<div class="col-xs-12">', '');
						$this->output('<div id="fb-root"></div>
										<script>(function(d, s, id) {
										  var js, fjs = d.getElementsByTagName(s)[0];
										  if (d.getElementById(id)) return;
										  js = d.createElement(s); js.id = id;
										  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=814638221947104";
										  fjs.parentNode.insertBefore(js, fjs);
										}(document, "script", "facebook-jssdk"));</script>');
						$this->output('<div class="fb-page" style="width:100%;" data-href="https://www.facebook.com/LaporPresiden" data-width="100%" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-show-posts="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/LaporPresiden"><a href="https://www.facebook.com/LaporPresiden">Lapor Presiden</a></blockquote></div></div>');
						$this->output('</div>');
					$this->output('</div>');
					
					$this->output('<div class="row">');
						$this->output('<div class="col-xs-12">', '');
						$this->output_raw(@$this->content['sidepanel']);
		                $this->feed();
						$this->output('</div>');
					$this->output('</div>');
					
					
						
					
					
					
					
				$this->output('</div>');
			$this->output('</div>');
			$this->output('</div> <!-- END body-wrapper -->');
			
			
			
			
			
			$this->output('<div class="footer">');
				$this->footer();
			$this->output('</div>');
			
			
			
			
			
			
			
			$this->widgets('full', 'bottom');

			$this->body_suffix();
		}
		
		
		function header()
		{
			//$this->output('<DIV CLASS="qa-header">');
			//$this->logo();
			//$this->nav_main_sub();
			//$this->header_clear();
			//$this->output('</DIV> <!-- END qa-header -->', '');
		}
		
		function nav_user_search()
		{
			$this->search();
		}
		
		function nav_main_sub()
		{
			$this->nav2('main');
		}
		function nav_main_sub3()
		{
			$this->nav3('main');
		}
		function nav3($navtype, $level=null)
		{
			$navigation=@$this->content['navigation'][$navtype];
			$liStyle="";
			
			if (($navtype=='user') || isset($navigation)) {
				if ($navtype=='user'){
					$liStyle="dropdown";
					$this->logged_in();
				}else if ($navtype=='main'){
					$liStyle="dropdown small-show";
				}else if ($navtype=='sub'){
					$liStyle="sub";
				}else{
				    $liStyle=$navtype;
				}
					
				// reverse order of 'opposite' items since they float right
				foreach (array_reverse($navigation, true) as $key => $navlink)
					if (@$navlink['opposite']) {
						unset($navigation[$key]);
						$navigation[$key]=$navlink;
					}
				
				$this->set_context('nav_type', $navtype);
				$this->nav_list2($navigation, $liStyle, $level);
					
			}
		}
		
		function nav2($navtype, $level=null)
		{
			$navigation=@$this->content['navigation'][$navtype];
			$liStyle="";
			
			if (($navtype=='user') || isset($navigation)) {
				if ($navtype=='user'){
					$liStyle="dropdown";
					$this->logged_in();
				}else if ($navtype=='main'){
					$liStyle="";
				}else if ($navtype=='sub'){
					$liStyle="sub";
				}else{
				    $liStyle=$navtype;
				}
				$this->set_context('nav_type', $navtype);
				$this->nav_list2($navigation, $liStyle, $level);
			}
		}

		function nav($navtype, $level=null)
		{
			$navigation=@$this->content['navigation'][$navtype];
			if ($navtype=='main'){
				//$this->output('<div></div>');
			}
			
			
			if (($navtype=='user') || isset($navigation)) {
				//$this->output('<DIV CLASS="qa-nav-'.$navtype.'">');
				
				if ($navtype=='user')
					$this->logged_in();
					
				// reverse order of 'opposite' items since they float right
				foreach (array_reverse($navigation, true) as $key => $navlink)
					if (@$navlink['opposite']) {
						unset($navigation[$key]);
						$navigation[$key]=$navlink;
					}
				
				$this->set_context('nav_type', $navtype);
				$this->nav_list($navigation, 'nav-'.$navtype, $level);
				$this->nav_clear($navtype);
				
				$this->clear_context('nav_type');
			}
		}

		function nav_item($key, $navlink, $class, $level=null)
		{
			$this->output('<LI CLASS="qa-'.$class.'-item'.(@$navlink['opposite'] ? '-opp' : '').
				(@$navlink['selected'] ? (' qa-'.$class.'-item-selected') : '').
				(@$navlink['state'] ? (' qa-'.$class.'-'.$navlink['state']) : '').' qa-'.$class.'-'.$key.'">');
			$this->nav_link($navlink, $class);
			
			if (count(@$navlink['subnav']))
				$this->nav_list($navlink['subnav'], $class, 1+$level);
			
			if ($class=='nav-cat'){
				
					$neaturls=qa_opt('neat_urls');
					$url=qa_opt('site_url');
					$mainkey=$key;
					if ($key=='all')$key='.rss';else $key='/'.$key.'.rss';
					
					switch ($neaturls) {
						case QA_URL_FORMAT_INDEX:
								$url.='index.php/feed/questions'.$key;
							break;
							
						case QA_URL_FORMAT_NEAT:
							$url.='feed/questions'.$key;
							break;
							
						case QA_URL_FORMAT_PARAM:
							$url.='?qa=feed/questions'.$key;
							break;
							
						default:
							$url.='index.php?qa=feed&qa_1=questions&qa_2='.$mainkey.'.rss';
						
						case QA_URL_FORMAT_PARAMS:
							$url.='?qa=feed&qa_1=questions&qa_2='.$mainkey.'.rss';
							break;
					}
					$this->output('<A HREF="'.$url.'" CLASS="qa-cat-feed-link"><DIV CLASS="qa-feed-cat"></DIV></A>');
				}
			
			$this->output('</LI>');
		}
		
		function view_count($post)
		{
			// do nothing
		}
		function theme_view_count($post)
		{
			qa_html_theme_base::view_count($post);
		}
		
		function post_meta_flags($post, $class)
		{ 
			$this->theme_view_count($post);
			qa_html_theme_base::post_meta_flags($post, $class);
		}
		function attribution()
		{
			// Please don't remove these links
			$this->output(
				'<DIV CLASS="qa-attribution">',
				'and <A HREF="http://qa-themes.com/shop/esteem-theme" title="Responsive Q2A Esteem Theme">Esteem Theme</A>',
				'</DIV>'
			);
			qa_html_theme_base::attribution();
		}
		function footer()
		{
			// qa_html_theme_base::footer();
			$this->output('<div class="footer-top">
               <div class="container">
                  <div class="row">
                     <section class="col-lg-3 col-md-3 col-xs-12 col-sm-3 footer-one">
                        <h4>Hubungi Kami</h4>
                        <p>
                        <ul>
                            <li><a href="/feedback">Kirim Komentar atau Masukan</a></li>
                        </ul>
                        </p>
                     </section>
                     <section class="col-lg-3 col-md-3 col-xs-12 col-sm-3 footer-two">
                        <h4>Powered by</h4>
                        <p>
                        <ul>
                            <li><a href="http://www.question2answer.org/">Question2Answer</a></li>
                            <li><a href="http://cloudkilat.com">CloudKilat</a></li>
                        </ul>
                        </p>
                     </section>
                     <section class="col-lg-3 col-md-3 col-xs-12 col-sm-3 footer-three">
                        <h4>Privasi Dan Kebijakan</h4>
                        <p>
                        <ul>
                            <li><a href="/privasi-dan-kebijakan">Privasi Dan Kebijakan</a></li>
                        </ul>
                        </p>
                        
                     </section>
                     <section class="col-lg-3 col-md-3 col-xs-12 col-sm-3 footer-four">
                        <h4>Tim Lapor Presiden</h4>
                        <p>
                        <ul>
                            <li><a href="/tim-lapor-presiden">Tim Lapor Presiden</a></li>
                        </ul>
                        </p>
                     </section>
                  </div>
               </div>
            </div>');
			$this->output('<div class="footer-bottom">
               <div class="container">
                  <div class="row">
                     <div class="col-lg-6 col-md-6 col-xs-12 col-sm-6 "> ©Copyright 2015. </div>
                     <div class="col-lg-6 col-md-6 col-xs-12 col-sm-6 ">
                        <ul class="social social-icons-footer-bottom">
                           <li class="facebook"><a href="https://www.facebook.com/LaporPresiden" data-toggle="tooltip" title="Facebook Lapor Presiden" style="cursor: pointer;" data-original-title="Facebook"><i class="fa fa-facebook"></i></a></li>
                           <li class="twitter"><a  href="https://twitter.com/LaporPresiden" data-toggle="tooltip" title="Twitter Lapor Presiden" style="cursor: pointer;" data-original-title="Twitter"><i class="fa fa-twitter"></i></a></li>
                           <li><a href="https://play.google.com/store/apps/details?id=org.laporpresiden.android" target="blank"><span class="apps"></span></a></li>
						</ul>
                     </div>
                  </div>
               </div>
            </div>');
			
			
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'bower_components/font-awesome/css/font-awesome.min.css'.'"/>');
			$this->output('<script src="'.$this->rooturl.'bower_components/jquery/dist/jquery.min.js"></script>');
			$this->output('<script src="'.$this->rooturl.'bower_components/bootstrap/dist/js/bootstrap.min.js"></script>');
			$this->output('<script src="'.$this->rooturl.'bower_components/metisMenu/dist/metisMenu.min.js"></script>');
			$this->output('<script src="'.$this->rooturl.'dist/js/classic3.js"></script>');
			
		}
		
	}

/*
	Omit PHP closing tag to help avoid accidental output
*/
