<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.
**/

defined('_FINDEX_') or die('Access Denied');

if(isset($article-> category)) :
	$category 	= $article-> category;
	$readmore	= $article-> readmore;
	$catlink	= $article-> catlink;
	$comment	= $article-> comment;
	$pagelink 	= $article-> pglink;
	$perrows 	= $article-> perrows;
	$author 	= $article-> author;
	$title		= $article-> title;
	$image  	= $article-> image;
	$text 	 	= $article-> content;
	$desc 		= $article-> desc;
	$panel 		= $article-> panel;
	$tags		= $article-> tags;
	$intro		= $article-> intro;
	
	if($desc AND _FEED_ != 'rss') :	?>
	
		<?php if(defined('Apps_Title')) : ?>
			<h1 class='title'><?php echo Apps_Title; ?></h1>
		<?php endif; ?>
				
		<!-- Article Main Warp -->
		<div id="article">
			<?php for($i=0; $i < $perrows ;$i++) : ?>				
			<!-- Article Main Box -->	
			<div class="article-box">	
				<h2 class="title"><?php echo $title[$i]; ?></h2>	
				<?php if(!empty($article->show_panel)) {
					echo "<div class='article-panel'>$panel[$i]</div>";
				} ?>
				<!-- Article Item Body -->
				
				<div class="article-blog">
					<?php if($image[$i]) : ?>
					<img src="<?php echo $image[$i]; ?>" alt="<?php echo $title[$i]; ?>" class="image-intro" style="max-width:<?php echo $article->imgW;?>px; max-height:<?php echo $article->imgH; ?>px" width="<?php echo $article->imgW;?>" />
					<?php endif; ?>
					<div class="article-intro">
						<?php echo $text[$i]; ?>						
						<div class="article-more">
							<?php echo $readmore[$i]; ?>
						</div>	
					</div>	
				</div>	
				
				<div class="clear"></div>
			</div>	
			<?php endfor; ?>	
			
			<!-- RSS Feed Icon -->
			<?php if($article->show_rss) : ?>
				<a href="<?php echo $article-> rssLink ; ?>" title="Read <?php echo $category[0]; ?>'s RSS Feed" class="article-rss">RSS</a>	
			<?php endif; ?>
			
			<!-- Pagelinks -->
			<?php if(!empty($pagelink)) : ?>
			<div class="article-pagelink pagination">
				<?php echo $pagelink; ?>
			</div>
			<?php endif; ?>	
		</div>
		
		
	<!-- RSS Feed File Generator -->	
	<?php elseif(_FEED_ == 'rss') : 
	
		// create simplexml object 
		$xml = new SimpleXMLElement("<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'    xmlns:sy='http://purl.org/rss/1.0/modules/syndication/'    xmlns:admin='http://webns.net/mvcb/'    xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'    xmlns:content='http://purl.org/rss/1.0/modules/content/'></rss>"); 

		// add channel information 
		$xml->addChild('channel'); 
		$xml->channel->addChild('title', $article -> rssTitle);
		$xml->channel->addChild('link', $article -> rssLink); 
		$xml->channel->addChild('description', $article -> rssDesc); 
		$xml->channel->addChild('pubDate', date(DATE_RSS)); 
		// query database for article data 

		for($i=0; $i < $perrows ;$i++)  { 
			// add item element for each article 
			$item = $xml->channel->addChild('item'); 
			$item->addChild('title', $article -> ftitle[$i]); 
			$item->addChild('link', $article -> link[$i]); 
			$item->addChild('description', $desc[$i]); 
			$item->addChild('pubDate', $article -> ftime[$i]); 
		} 
		// save the xml to a file
		Header('Content-type: text/xml');

		print str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $xml->asXML()); ?>
		
	<?php endif; ?>
	
<?php else : ?>
<!-- if the articles in the category is empty -->
	<?php if(defined('Apps_Title')) : ?>
			<h2><?php echo Apps_Title; ?></h2>
	<?php endif; ?>
	<h3><?php echo Category_is_empty;?></h3>
<?php endif; ?>