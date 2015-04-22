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
			$this->output('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">');
		}
		
		function head_script()
		{
			qa_html_theme_base::head_script();
			
			$this->output('
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
				</script>');

		}
		function head_css()
		{
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
			
			$this->output('<DIV CLASS="qa-top-header">', '');
			$this->nav('user');
			$this->output('</DIV>', '');

			$this->header();
			$this->output('<DIV CLASS="qa-body-wrapper">', '');

			$this->widgets('full', 'top');
			
			$this->output('<DIV CLASS="qa-sub-nav">');
			$this->nav_user_search();
			$this->nav('sub');
			$this->output('</DIV>');
			
			$this->widgets('full', 'high');
			$this->sidepanel();
			$this->main();
			$this->widgets('full', 'low');
			$this->output('</DIV> <!-- END body-wrapper -->');
			
			$this->footer();
			$this->widgets('full', 'bottom');

			$this->body_suffix();
		}
		
		function header()
		{
			$this->output('<DIV CLASS="qa-header">');
			
			$this->logo();
			$this->nav_main_sub();
			$this->header_clear();
			
			$this->output('</DIV> <!-- END qa-header -->', '');
		}
		
		function nav_user_search()
		{
			$this->search();
		}
		
		function nav_main_sub()
		{
			$this->nav('main');
		}

		function nav($navtype, $level=null)
		{
			$navigation=@$this->content['navigation'][$navtype];
			if ($navtype=='main'){
				$this->output('<nav id="mobilenav"><a href="#" class="menu_show_hide">Menu</a></nav>');
			}
			
			
			if (($navtype=='user') || isset($navigation)) {
				$this->output('<DIV CLASS="qa-nav-'.$navtype.'">');
				
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
	
				$this->output('</DIV>');
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
			$this->output('<DIV CLASS="qa-wrap-footer">');
			
			qa_html_theme_base::footer();
			
			$this->output('</DIV> <!-- END qa-footer -->', '');
		}
		
	}

/*
	Omit PHP closing tag to help avoid accidental output
*/